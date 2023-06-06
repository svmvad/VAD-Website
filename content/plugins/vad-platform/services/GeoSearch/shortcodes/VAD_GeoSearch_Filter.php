<?php

namespace Netdust\VAD\Modules\GeoSearch\shortcodes;


class VAD_GeoSearch_Filter extends \Netdust\VAD\Blocks\VAD_PostFilter_block
{

    public $nonce = 'vad_doorverwijsgids-nonce';
    public $action = 'vad_doorverwijsgids_search';
    public $extra_searchfield = 'extra_postcodes';

    public function do_actions() {
        //
    }

    protected function load_csv_into_select() {

        if( !empty($this->filter_posttype) && empty($_REQUEST['postcode']) ){
            $_REQUEST['postcode']=1000;
        }

        $r = array_map('str_getcsv', file(app()->url() . '/modules/GeoSearch/assets/postcodes.csv'));
        foreach( $r as $k => $d ) { $r[$k] = array_combine($r[0], $r[$k]); }
        $csv = array_values(array_slice($r,1));

        $options = '<option value="" disabled selected hidden>Zoek op stad of postcode</option>';
        foreach ( $csv as $key => $value ) {
            $options .= '<option '. (!empty($_REQUEST['postcode']) && $_REQUEST['postcode']==$value['postcode']?'selected':'') .' value="'.$value['postcode'].'">'.$value['stad'].' ('. $value['postcode'] .')</option>';
        }

        return $options;
    }

    public function as_shortcode( $atts )
    {

        $this->enqueue_dropdown( );

        $options = $this->load_csv_into_select();

        $this->filter_posttype = $atts['posttype'] ?? 'posts';
        $this->filteritem_layout = $atts['filteritem_layout'] ??'design';
        $this->filter_excl_categories = $atts['excl_categories'] ?? '';

        if( !empty( $atts['prefilter'] )  ) {

            return $this->get_template('prefilter', [
                'action' => 'doorverwijs_form_redirect',
                'postcodes'=>$options
            ]);

        }
        else if( !empty( $this->filter_posttype ) ){

            $excl_categories = explode(',', $this->get_field('filter_excl_categories' ) );
            $exclude = array_merge( array( 'post_tag', 'post_format'), $excl_categories );

            $taxonomies = $this->get_taxonomies( $this->get_field('filter_posttype' ), $exclude );

            $results = $this->get_results( $taxonomies, false );

            $this->enqueue( $taxonomies );

            return $this->get_template('filter', [
                'action' => 'doorverwijs_form',
                'taxonomies'=>$taxonomies,
                'results'=>$results,
                'defaults'=>[],
                'postcodes'=>$options,
                's'=>$_REQUEST['s']??''
            ]);
        }

    }

    public function add_ajax() {
        // redirect action from preview filter
        add_action('admin_post_nopriv_doorverwijs_form_redirect', [$this, 'handle_form_doorverwijs_form_redirect']);
        add_action('admin_post_doorverwijs_form_redirect', [$this, 'handle_form_doorverwijs_form_redirect']);

        // actuall filter
        parent::add_ajax();

    }


    /**
     * redirect to results page
     * @return void
     */
    public function handle_form_doorverwijs_form_redirect() {
        wp_redirect( site_url( '/doorverwijsgids/'.$_POST['theme'].'/?postcode='.$_POST['geo'] . ( !empty($_POST['trefwoord'])?'&s='.$_POST['trefwoord']:'' ) ) );
    }


    public function filter_products( $filters=[], $json=true ) {

        add_filter('vadplatform_postfilter_query', [$this,'modify_acf_relationship_query_param'] );
        $this->modify_acf_relationship_search_query();

        return parent::filter_products( $filters, $json );

    }

    /**
     * adding postcode to the query arguments
     */

    public function modify_acf_relationship_query_param( $args ) {
        remove_filter('vadplatform_postfilter_query', [$this,'modify_acf_relationship_query_param'] );

        if( !empty($_REQUEST['postcode']) && $this->get_field( 'filter_posttype' ) !== 'vad--hulpverlening' ) {
            $args['s'] = $_REQUEST['postcode'];
        }

        $args['posts_per_page'] = -1;
        unset ($args['paged'] );

        return $args;
    }

    /**
     * adding postcodes to the query
     */


    public function search_custom_meta_acf_alter_fields($fields,$qry) {
        global $wpdb;

        if( $this->get_field( 'filter_posttype' ) === 'vad--hulpverlening') {

            $latitude = 50.8427501;
            $longitude = 4.351549900000009;

            remove_filter('posts_join',[$this,'search_custom_meta_acf_alter_fields']);
            $fields .= " ,( 6371 * acos( cos( radians( META1.meta_value ) ) * cos( radians( $latitude ) ) * cos( radians( $longitude ) - radians( META2.meta_value ) ) + sin( radians( META1.meta_value ) ) * sin(radians( $latitude )) ) ) AS distance";
        }

        return $fields;
    }

    public function search_custom_meta_acf_add_join($joins) {
        global $wpdb;

        if( $this->get_field( 'filter_posttype' ) === 'vad--hulpverlening') {
            remove_filter('posts_join',[$this,'search_custom_meta_acf_add_join']);

            $joins .= $joins . " INNER JOIN {$wpdb->postmeta} as META1 ON ({$wpdb->posts}.ID = META1.post_id) AND ( META1.meta_key = 'extra_breedtegraad' )";
            $joins .= " INNER JOIN {$wpdb->postmeta} as META2 ON ({$wpdb->posts}.ID = META2.post_id) AND ( META2.meta_key = 'extra_lengtegraad' )";

            return $joins;

        }

        return parent::search_custom_meta_acf_add_join($joins);
    }

    public function modify_acf_relationship_search_query( ) {
        if( $this->get_field( 'filter_posttype' ) === 'vad--hulpverlening') {
            add_filter('posts_join',[$this,'search_custom_meta_acf_add_join']);
            add_filter('posts_fields',[$this,'search_custom_meta_acf_alter_fields'],1,2);
            add_filter('posts_distinct', [$this,'search_custom_meta_acf_distinct']);
        }
        else parent::modify_acf_relationship_search_query();
    }

    public function enqueue_dropdown( ) {

        app()->scripts()->add(
            'postcodes-js', [
                'handle'      => 'postcodes-js',
                'src'         => app()->url() . '/modules/GeoSearch/assets/geo_search.js',
                'dependencies'=> 'choises-js',
            ]
        );

        $script = app()->scripts()->get( 'postcodes-js' );

        $script->register();
        $script->enqueue();

    }


    /**
     * @inheritDoc
     */
    protected function get_template_root_path() {
        return dirname(__DIR__) . '/templates';
    }

    public function get_templates() {
        return [
            'filter'         => [
                'override_visibility' => 'private',
            ],
            'prefilter'         => [
                'override_visibility' => 'private',
            ],
            'filter-form'         => [
                'override_visibility' => 'private',
            ],
            'filter-result' => [
                'override_visibility' => 'private',
            ],
            'filter-resultitem' => [
                'override_visibility' => 'private',
            ],
        ];
    }

    /**
     * Fetches the template group name.
     *
     * @since 1.0.0
     *
     * @return string The template group name
     */
    protected function get_template_group() {
        return 'filter';
    }
}