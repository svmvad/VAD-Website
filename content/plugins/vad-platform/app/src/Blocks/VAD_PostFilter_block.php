<?php

namespace Netdust\VAD\Blocks;

use Netdust\Traits\Cache;
use Netdust\Traits\Setters;
use Netdust\Traits\Templates;
use Netdust\Utils\Logger\Logger;

class VAD_PostFilter_block extends VAD_ACFBlock
{
    use Cache;

    public string $extra_searchfield = '';

    public string $action = 'vad_filter_search';
    public string $nonce = 'vad_filter_search-nonce';

    protected string $filter_posttype = 'post';
    protected string $filteritem_layout = 'design';
    protected string $filter_excl_categories= '';

    public function get_field( $name, $default=[] ) {
        if( !empty( get_field( $name ) ) )
            return get_field( $name );

        if( $name=='filter_posttype' && ! empty( $_REQUEST['post'] ) ) {
            return $_REQUEST['post'];
        }

        return $this->{$name};
    }

    public function add_ajax() {
        add_action('wp_ajax_'.$this->action, [$this, 'filter_products_ajax']);
        add_action('wp_ajax_nopriv_'.$this->action, [$this, 'filter_products_ajax']);
    }

    public function add_noindexmeta_tags() {

        if ( (is_home()||is_archive()) && count( $this->get_query() ) > 0 && !array_key_exists('post-type', $this->get_query()) ) {
            echo '<meta name="robots" content="noindex, follow" />' . "\n";
        }

    }

    public function do_actions() {
        add_action( 'wp_head', [$this,'add_noindexmeta_tags']);
        parent::do_actions();
        $this->add_ajax();
    }

    public function block_actions( ) {

        if( is_admin() ) {
            echo '<div style="width:100%;height:75px;background:#f4f6f7;text-align: center;line-height: 75px"><h4>Posttype Filter</h4></divstyle>';
        }
        else {
            $this->echo_template();
        }
    }

    public function as_shortcode( $atts ) {

        $this->filter_posttype = $atts['posttype'] ?? 'posts';
        $this->filterresult_layout = $atts['filterresult_layout'] ?? '3-row';
        $this->filteritem_layout = $atts['filteritem_layout'] ?? 'design';
        $this->filter_excl_categories = $atts['excl_categories'] ?? '';

        $this->extra_searchfield = $atts['extra_searchfield'] ?? $this->extra_searchfield;

        return parent::as_shortcode( $atts );
    }

    public function echo_template( ) {

        $excl_categories = explode(',', $this->get_field('filter_excl_categories' ) );
        $exclude = array_merge( array( 'post_tag', 'post_format'), $excl_categories );

        $taxonomies = $this->get_taxonomies( $this->get_field('filter_posttype' ), $exclude );

        $results = $this->get_results( $taxonomies, false );

        $this->enqueue( $taxonomies );

        echo $this->get_template($this->name, [
            'taxonomies'=>$taxonomies,
            'results'=>$results,
            'defaults'=>[],
            's'=>$_REQUEST['s']??''
        ]);
    }


    public function get_filters( $taxonomies ){

        $filters = $this->get_query();

        error_log(print_r( $filters, true ) );

        $filters_arr=[];
        foreach( $filters as $category => $terms ) {
            if( isset( $taxonomies[$category] ) ) {
                $terms_arr = explode(',', $terms);
                foreach ($terms_arr as $term_slug) {
                    if (isset($taxonomies[$category]['terms'][$term_slug])) {
                        $filters_arr[$taxonomies[$category]['tax']][] = $taxonomies[$category]['terms'][$term_slug];
                    }
                }
            }
        }

        error_log(print_r( $filters_arr, true ) );

        return $filters_arr;
    }

    public function get_results( $taxonomies, $json ) {

        $filters = $this->get_filters( $taxonomies );

        $results = $this->filter_products( $filters, $json );

        if( !$json ) return $results;

    }

    public function filter_products_ajax() {

        check_ajax_referer( $this->nonce, '_ajax_list_nonce' );

        $taxonomies = $this->get_taxonomies( $_REQUEST['post'] );

        $this->get_results( $taxonomies, true );

    }

    public function get_taxonomies( $post_type, $exclude = [] ) {

        $cache = self::get_cache( 'vad_platform_get_taxonomies_' . $post_type );

        if ( ! empty( $cache ) ) {
            return $cache;
        }

        $taxonomies = [];

        $object_taxonomies = get_object_taxonomies(array('post_type' => $post_type ));

        foreach( $object_taxonomies as $taxonomy ) :
            if( in_array( $taxonomy, $exclude ) ) continue;

            $details = get_taxonomy( $taxonomy );
            $slug =  strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $taxonomy)));

            $terms = get_terms( $taxonomy );
            if( ! empty( $terms ) && count( $terms ) > 0 ) {

                $taxonomies[$slug] = [
                    'label'=> $details->label,
                    'tax'=> $taxonomy,
                    'terms'=>[]
                ];

                foreach( $terms as $term ) :
                    $taxonomies[$slug]['terms'][$term->slug]= $term->name;
                endforeach;
            }
        endforeach;

        self::create_cache( 'vad_platform_get_taxonomies_' . $post_type , $taxonomies );

        return $taxonomies;
    }

    public function get_query() {
        $filters = [];

        // if filter is in the URL ( tag archive )
        global $wp_query;
        if( !empty( $wp_query->query )) {
            foreach( $wp_query->query as $key=>$value ) {
                $filters[str_replace('_', '-', $key )] = $value;
            };
        }

        if( !empty( $_REQUEST['filter'] )) {
            $filters = array_merge($filters,array_map(function($item){ return $item[0];}, $_REQUEST['filter']));
        }
        else $filters = array_merge($filters,$_GET);

        // filter out the bad guys
        $tax = get_object_taxonomies(array('post_type' => $this->get_field('filter_posttype' ) ));
        $filters = array_filter($filters, function($item)  use ($tax){
            return in_array(str_replace('-', '_', $item ), $tax );
        }, ARRAY_FILTER_USE_KEY);

        return $filters;
    }

    public function enqueue( $taxonomies ) {
        // enqueue and localize script
        $script = app()->get('filter-js');
        $script->set_param(
            'ajaxurl', admin_url( 'admin-ajax.php' )
        );
        $script->set_param(
            'ajaxnonce', wp_create_nonce( $this->nonce )
        );
        $script->set_param(
            'action', $this->action
        );
        $script->set_param(
            'taxonomies', json_encode($taxonomies)
        );
        $script->set_param(
            'query', json_encode($this->get_query())
        );
        $script->set_param(
            'posttype', $this->get_field( 'filter_posttype' )
        );

        $script->enqueue();
    }



    public function filter_products( $filters=[], $json=true ) {

        do_action( 'postfilter:before_filter' );

        $filters = apply_filters( 'postfilter:filters', $filters );

        $posttype = $_REQUEST['post'] ?? $this->get_field('filter_posttype' );

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $args = [
            'post_type' => $posttype,
            'posts_per_page' => 15,
            'post_status'  => 'publish',
            'orderby'      => 'menu_order,title,date',
            'order'        => 'DESC',
            'paged'        => $paged
        ];

        if( ! empty( $_REQUEST['s'] ) ){
            $this->modify_acf_relationship_search_query();
            $args['s'] = $_REQUEST['s'];
        }

        $args = apply_filters( 'postfilter:query', $args );

        if( ! empty( $filters ) ) {
            $args['tax_query'] = [
                'relation' => 'AND'
            ];

            foreach ( $filters as $category => $terms ) {
                $args['tax_query'][] = [
                    'taxonomy' => $category,
                    'field' => 'name',
                    'terms' => $terms,
                    'operator' => 'IN',
                ];
            }
        }

        $hash = md5(json_encode($args));
        $cache = self::get_cache( $hash );

        if (  empty( $cache ) ) {

            $ajaxproducts = new \WP_Query($args);

            $response = [];
            if ( $ajaxproducts->have_posts() ) {
                while ( $ajaxproducts->have_posts() ) : $ajaxproducts->the_post();

                    $term_obj_list = get_the_terms( get_the_ID(), 'product_tag' );
                    $terms_string = join(', ', wp_list_pluck($term_obj_list, 'name'));

                    $response[] = $this->get_template('filter_resultitem', [
                        'title'=>get_the_title(),
                        'content'=>get_the_content(),
                        'excerpt'=>get_the_excerpt(),
                        'permalink'=>get_the_permalink(),
                        'image'=>get_the_post_thumbnail_url(),
                        'meta'=>$terms_string,
                    ]);
                endwhile;
            }

            $cache=[];
            $cache['data'] = $response;
            $cache['json'] = [
                'total' => $ajaxproducts->found_posts,
                'html' => $ajaxproducts->found_posts==0 ? '<h5>Jammer, we hebben geen resultaten gevonden.</h5>' : implode($response),
            ];

            self::create_cache( $hash, $cache );
        }

        if( !$json ) return $cache;
        else {
            wp_reset_postdata();
            wp_reset_query();

            die( json_encode($cache['json']) );
        }

    }

    /**
     * override
     */
    public function search_custom_meta_acf_alter_search($search,$qry) {
        global $wpdb;
        remove_filter('posts_search',[$this,'search_custom_meta_acf_alter_search'],1,2);
        $add = $wpdb->prepare("(CMS15.meta_key = '$this->extra_searchfield' AND CAST(CMS15.meta_value AS CHAR) LIKE '%%%s%%')",$qry->get('s'));
        $pat = '|\(\((.+)\)\)|';
        $search = preg_replace($pat,'(($1 OR '.$add.'))',$search);

        return $search;
    }

    public function search_custom_meta_acf_add_join($joins) {
        global $wpdb;
        remove_filter('posts_join',[$this,'search_custom_meta_acf_add_join']);
        return $joins . " INNER JOIN {$wpdb->postmeta} as CMS15 ON ({$wpdb->posts}.ID = CMS15.post_id)";
    }

    public function search_custom_meta_acf_distinct() {
        remove_filter('posts_distinct',[$this,'search_custom_meta_acf_distinct']);
        return "DISTINCT";
    }

    public function modify_acf_relationship_search_query( ) {
        if( !empty( $this->extra_searchfield ) ){
            add_filter('posts_join',[$this,'search_custom_meta_acf_add_join']);
            add_filter('posts_search',[$this,'search_custom_meta_acf_alter_search'],1,2);
            add_filter('posts_distinct', [$this,'search_custom_meta_acf_distinct']);
        }
    }


    /**
     * Fetches the template group name.
     *
     * @since 1.0.0
     *
     * @return string The template group name
     */
    public function get_template_group() {
        return parent::get_template_group() . '/filter';
    }
}