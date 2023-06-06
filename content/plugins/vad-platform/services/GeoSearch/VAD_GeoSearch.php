<?php

namespace Netdust\VAD\Services\GeoSearch;

use Netdust\Service\Pages\Admin\AdminPage;
use Netdust\Service\Posts\Post;
use Netdust\Utils\ServiceProvider;


/**
 * @todo enkel hulpverlening laten ranken, de rest niet, en op deze pagina alle fiches oplijsten
 */

class VAD_GeoSearch extends ServiceProvider
{

    public function register() {

        $this->container->bind(
            'geo_search_filter',
            VAD_GeoSearch_Filter::class, ['do_actions']
        );
    }

    public function boot() {
        $this->add_hooks();
        $this->add_posttypes();
        $this->add_menupages();
        $this->add_shortcodes();
    }

    protected function add_hooks() {
        add_filter( 'enter_title_here', function( $title ){
            $screen = get_current_screen();

            if  ( 'vad--hulpverlening' == $screen->post_type
                || 'vad--vroeginter' == $screen->post_type
                || 'vad--preventie' == $screen->post_type) {
                $title = 'Organisatienaam';
            }

            return $title;
        });
    }

    protected function add_posttypes() {

        $this->app(Post::Class)->add(
            'hulpverlening', [
            'type'=>'vad--hulpverlening',
            'args'=>array(
                'labels'      => array(
                    'name'          => __( 'Hulpverlening', 'vad_platform' ),
                    'singular_name' => __( 'Hulpverlening', 'vad_platform' ),
                    'add_new_item'          => __( 'Voeg nieuwe fiche toe', 'vad_platform' ),
                    'add_new'               => __( 'Voeg nieuwe fiche toe', 'vad_platform' ),
                    'new_item'              => __( 'Nieuwe fiche', 'vad_platform' ),
                    'edit_item'             => __( 'Fiche aanpassen', 'vad_platform' ),
                    'update_item'           => __( 'Update fiche', 'vad_platform' )
                ),
                'public'      => true,
                'has_archive' => 'doorverwijsgids/hulpverlening',
                'rewrite'     => array( 'slug' => 'doorverwijsgids/hulpverlening' ),
                'show_in_rest' => false,
                'supports' => array('title'),

                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu'=>'vad-doorverwijs'
            )]
        );
        $this->app(Post::Class)->add(
            'preventie', [
                'type'=>'vad--preventie',
                'args'=>array(
                'labels'      => array(
                    'name'          => __( 'Preventie fiches', 'vad_platform' ),
                    'singular_name' => __( 'Preventie fiche', 'vad_platform' ),
                    'add_new_item'          => __( 'Voeg nieuwe fiche toe', 'vad_platform' ),
                    'add_new'               => __( 'Voeg nieuwe fiche toe', 'vad_platform' ),
                    'new_item'              => __( 'Nieuwe fiche', 'vad_platform' ),
                    'edit_item'             => __( 'Fiche aanpassen', 'vad_platform' ),
                    'update_item'           => __( 'Update fiche', 'vad_platform' )
                ),
                'public'      => true,
                'has_archive' => 'doorverwijsgids/preventie',
                'rewrite'     => array( 'slug' => '/doorverwijsgids/preventie' ),
                'show_in_rest' => false,
                'supports' => array('title'),

                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu'=>'vad-doorverwijs'
            )]
        );
        $this->app(Post::Class)->add(
            'vroeginterventie', [
                'type'=>'vad--vroeginter',
                'args'=>array(
                'labels'      => array(
                    'name'          => __( 'Vroeginterventie fiches', 'vad_platform' ),
                    'singular_name' => __( 'Vroeginterventie fiche', 'vad_platform' ),
                    'add_new_item'          => __( 'Voeg nieuwe fiche toe', 'vad_platform' ),
                    'add_new'               => __( 'Voeg nieuwe fiche toe', 'vad_platform' ),
                    'new_item'              => __( 'Nieuwe fiche', 'vad_platform' ),
                    'edit_item'             => __( 'Fiche aanpassen', 'vad_platform' ),
                    'update_item'           => __( 'Update fiche', 'vad_platform' )
                ),
                'public'      => true,
                'has_archive' => 'doorverwijsgids/vroeginterventie',
                'rewrite'     => array( 'slug' => '/doorverwijsgids/vroeginterventie' ),
                'show_in_rest' => true,
                'supports' => array('title'),

                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu'=>'vad-doorverwijs'
            )]
        );
    }
    protected function add_menupages() {

        $this->app(AdminPage::class)->add(
            'geodir-page', [
            'page_title' => 'VAD Doorverwijs',
            'menu_title' => 'VAD Doorverwijs',
            'capability' => 'read',
            'menu_slug' => 'vad-doorverwijs',
            'icon' => app()->css_url() . '/img/vad.png',
            'position' => 4,
            'template_root' =>$this->app()->dir() . '/app/templates'
        ]);

    }

    protected function add_shortcodes() {

        add_shortcode( 'geo_search_filter', function( $atts) {
            return $this->app('geo_search_filter')->as_shortcode($atts);
        });

    }


    // km, breedte, lengte
    public function do_search( $distance_km, $latitude, $longitude ) {
        global $wpdb;

        $lat = "META1.meta_value";
        $long = "META2.meta_value";

        $sql = "SELECT wp_posts.*, ( 6371 * acos( cos( radians( $lat ) ) * cos( radians( $latitude ) ) * cos( radians( $longitude ) - radians( $long ) ) + sin( radians( $lat ) ) * sin(radians( $latitude )) ) ) AS distance
            FROM wp_posts 
                INNER JOIN wp_postmeta as META1 ON (wp_posts.ID = META1.post_id)  AND ( META1.meta_key = 'extra_breedtegraad' )
                INNER JOIN wp_postmeta as META2 ON (wp_posts.ID = META2.post_id)  AND ( META2.meta_key = 'extra_lengtegraad' )
	    WHERE 1=1 AND wp_posts.post_type = 'vad--hulpverlening' AND wp_posts.post_status = 'publish' AND META1.meta_value > 0
            ORDER BY distance ASC";

        $results = $wpdb->get_results( $sql, ARRAY_A );

        return $results;

    }

    /**
     * Returns openstreetmap address using latitude and longitude.
     *
     * @since 1.6.5
     * @package GeoDirectory
     * @param string $lat Latitude string.
     * @param string $lng Longitude string.
     * @return array|bool Returns address on success.
     */
    public function get_address_by_lat_lan($lat, $lng) {

        // we need the protocol to be "//" as a http site call to their https server fails. EDIT, it seems to require HTTPS now :/
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' . trim($lat) . '&lon=' . trim($lng) . '&zoom=16&addressdetails=1';


        $response = wp_remote_get($url);
        if ( is_wp_error( $response ) ) {
            return false;
        }

        $result = json_decode( wp_remote_retrieve_body( $response ) );

        if(!empty($result->address)){
            $address_fields = array('public_building', 'house', 'house_number', 'bakery', 'footway', 'street', 'road', 'village', 'attraction', 'pedestrian', 'neighbourhood', 'suburb');
            $formatted_address = (array)$result->address;

            foreach ( $result->address as $key => $value ) {
                if (!in_array($key, $address_fields)) {
                    unset($formatted_address[$key]);
                }
            }
            $result->formatted_address = !empty($formatted_address) ? implode(', ', $formatted_address) : '';

            return $result;
        }else{
            return false;
        }
    }

    /**
     * Get GPS info for the address using OpenStreetMap Nominatim API.
     *
     * @since 2.0.0.66
     *
     * @param array|string $address Array of address element or full address.
     * @param bool $wp_error Optional. Whether to return a WP_Error on failure. Default false.
     * @return bool|\WP_Error GPS data or WP_Error on failure.
     */
    public function get_gps_from_address( $address, $wp_error = false ) {
        global $wp_version;

        if ( empty( $address ) ) {
            if ( $wp_error ) {
                return new \WP_Error( 'vad_platform-gps-from-address', __( 'Address must be non-empty.', 'vad_platform' ) );
            } else {
                return false;
            }
        }

        $extra_params = '';
        if ( is_array( $address ) ) {
            $address = wp_parse_args( $address, array(
                'street' => '',
                'city' => '',
                'region' => '',
                'country' => '',
                'zip' => '',
                'country_code' => '',
            ) );

            $_address = array();
            if ( trim( $address['street'] ) != '' ) {
                $_address[] = trim( $address['street'] );
            }
            if ( trim( $address['city'] ) != '' ) {
                $_address[] = trim( $address['city'] );
            }
            if ( trim( $address['region'] ) != '' ) {
                $_address[] = trim( $address['region'] );
            }
            if ( trim( $address['zip'] ) != '' ) {
                $_address[] = trim( $address['zip'] );
            }
            if ( trim( $address['country'] ) != '' ) {
                $_address[] = trim( $address['country'] );
            }

            // We must have at least 2 address items.
            if ( count( $_address ) < 2 ) {
                if ( $wp_error ) {
                    return new \WP_Error( 'vad_platform-gps-from-address', __( 'Not enough location info for address.', 'vad_platform' ) );
                } else {
                    return false;
                }
            }

            $search_address = implode( ', ', $_address );

            // Search within specific country code(s).
            if ( ! empty( $address['country_code'] ) ) {
                $extra_params .= '&countrycodes=' . ( is_array( $address['country_code'] ) ? implode( ',', $address['country_code'] ) : $address['country_code'] );
            }
        } else {
            if ( trim( $address ) == '' ) {
                if ( $wp_error ) {
                    return new \WP_Error( 'vad_platform-gps-from-address', __( 'Not enough location info for address.', 'vad_platform' ) );
                } else {
                    return false;
                }
            }

            $search_address = trim( $address );
        }

        $request_url = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1';
        $request_url .= '&q=' . $search_address;
        $request_url .= $extra_params;

        // If making large numbers of request please include an appropriate email address to identify requests.
        //$request_url .= '&email=' . get_option( 'admin_email' );

        $request_url = apply_filters( 'vad_platform_osm_gps_from_address_request_url', $request_url, $address );

        $args = array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking'    => true,
            'decompress'  => true,
            'sslverify'   => false,
        );
        $response = wp_remote_get( $request_url , $args );

        // Check for errors
        if ( is_wp_error( $response ) ) {
            if ( $wp_error ) {
                return new \WP_Error( 'vad_platform-gps-from-address', __( 'Failed to reach OpenStreetMap Nominatim server.', 'vad_platform' ) );
            } else {
                return false;
            }
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        $gps = array();
        if ( ! empty( $data ) && is_array( $data ) ) {
            if ( ! empty( $data[0]['lat'] ) && ! empty( $data[0]['lon'] ) ) {
                $details = $data[0];

                $gps['latitude'] = $details['lat'];
                $gps['longitude'] = $details['lon'];
            } else {
                if ( $wp_error ) {
                    $gps = new \WP_Error( 'vad_platform-gps-from-address', __( 'Listing has no GPS info, failed to retrieve GPS info from OpenStreetMap Nominatim server.', 'vad_platform' ) );
                } else {
                    $gps = false;
                }
            }
        } else {
            if ( $wp_error ) {
                $gps = new \WP_Error( 'vad_platform-gps-from-address', __( 'Failed to retrieve GPS info from OpenStreetMap Nominatim server.', 'vad_platform' ) );
            } else {
                $gps = false;
            }
        }

        return $gps;
    }


}