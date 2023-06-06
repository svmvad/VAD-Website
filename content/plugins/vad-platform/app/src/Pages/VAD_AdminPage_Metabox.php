<?php

namespace Netdust\VAD\Factories;


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class VAD_AdminPage_Metabox extends \Netdust\Loaders\Admin\Factories\AdminSection
{

    /**
     * Settings screen ID
     *
     * @var string
     */
    protected $settings_screen_id = '';

    /**
     * This is used as the option_name when the settings
     * are saved to the options table.
     *
     * @var string $settings_section_key
     */
    protected $settings_metabox_key = '';

    /**
     * Holds the values for the fields. Read in from the wp_options item.
     *
     * @var array $setting_option_values Array of section values.
     */
    protected $setting_option_values = array();

    /**
     * Map internal settings field ID to legacy field ID.
     *
     * @var array $settings_fields_map
     */
    protected $settings_fields_map = array();

    /**
     * Current Post being edited.
     *
     * @var WP_Post|null $_post WP_Post object.
     */
    protected $_post = null; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

    /**
     * Current metabox
     *
     * @var object $_metabox Metabox object.
     */
    protected $_metabox = null; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore


    public function __construct( $args ) {

        parent::__construct( $args );

        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

        add_filter(
            $this->settings_screen_id . '_display_settings',
            array( $this, 'display_settings_filter' ),
            100,
            3
        );
    }

    /**
     * Initialize Metabox instance.
     *
     * @param WP_Post $post Post object instance to initialize instance.
     * @param boolean $force True to force init. This will load values and settings again.
     */
    public function init( $post = null, $force = false ) {
        if ( ( ! $post ) || ( ! is_a( $post, 'WP_Post' ) ) ) {
            return false;
        }

        if ( $post->post_type !== $this->settings_screen_id ) {
            return false;
        }

        $this->_post = $post;

        if ( true === $force ) {
            $this->settings_values_loaded = false;
            $this->settings_fields_loaded = false;
        }

        if ( ! $this->settings_values_loaded ) {
            $this->load_settings_values();
        }

        if ( ! $this->settings_fields_loaded ) {
            //$this->load_settings_fields();
        }
    }

    /**
     * Load the section settings values.
     */
    public function load_settings_values() {

        if ( ( is_admin() ) && ( function_exists( 'get_current_screen' ) ) ) {
            $screen = get_current_screen();
            if ( $screen->id !== $this->settings_screen_id ) {
                return false;
            }
        }

        if ( ( ! $this->_post ) || ( ! is_a( $this->_post, 'WP_Post' ) ) ) {
            return false;
        }

        if ( $this->_post->post_type !== $this->settings_screen_id ) {
            return false;
        }

        $setting_values = learndash_get_setting( $this->_post );

        if ( ! empty( $setting_values ) ) {
            foreach ( $this->settings_fields_map as $_internal => $_legacy ) {
                if ( isset( $setting_values[ $_legacy ] ) ) {
                    $this->setting_option_values[ $_internal ] = $setting_values[ $_legacy ];
                } else {
                    $this->setting_option_values[ $_internal ] = '';
                }
            }
        }

        $this->settings_values_loaded = true;

        return true;
    }

    /**
     * Show Settings Section meta box.
     *
     * @param WP_Post $post Post.
     * @param object  $metabox Metabox.
     */
    public function show_meta_box( $post = null, $metabox = null ) {
        if ( $post ) {
            $this->init( $post );
            $this->show_metabox_nonce_field();
            $this->show_settings_metabox( $this );
        }
    }

    /**
     * Output Metabox nonce field.
     */
    public function show_metabox_nonce_field() {
        wp_nonce_field( $this->settings_metabox_key, $this->settings_metabox_key . '[nonce]' );
    }

    /**
     * Verify Metabox nonce field POST value.
     */
    public function verify_metabox_nonce_field() {
        if ( ( isset( $_POST[ $this->settings_metabox_key ]['nonce'] ) ) && ( ! empty( $_POST[ $this->settings_metabox_key ]['nonce'] ) ) && ( wp_verify_nonce( esc_attr( $_POST[ $this->settings_metabox_key ]['nonce'] ), $this->settings_metabox_key ) ) ) {
            return true;
        }
    }

    /**
     * Show the meta box settings
     *
     * @param object $metabox Metabox.
     */
    public function show_settings_metabox( $metabox = null ) {
        if ( ( $metabox ) && ( is_object( $metabox ) ) ) {
            // If this section defined its own display callback logic.
            if ( ( isset( $metabox->settings_fields_callback ) ) && ( ! empty( $metabox->settings_fields_callback ) ) && ( is_callable( $metabox->settings_fields_callback ) ) ) {
                call_user_func( $metabox->settings_fields_callback, $this->settings_metabox_key );
            } else {

                 $this->show_settings_section_description();

                 echo '<div class="sfwd sfwd_options ' . esc_attr( $metabox->settings_metabox_key ) . '">';

                 $this->show_settings_metabox_fields( $metabox );

                 echo '</div>';

            }
        }
    }

    public function show_settings_section_description() {
        if ( ! empty( $this->settings_section_description ) ) {
            echo '<div class="ld-metabox-description">' . wp_kses_post( wpautop( $this->settings_section_description ) ) . '</div>';
        }
    }

    /**
     * Show Settings Section Fields.
     *
     * @param object $metabox Metabox.
     */
    protected function show_settings_metabox_fields( $metabox = null ) {
        if ( $metabox ) {
            LearnDash_Settings_Fields::show_section_fields( $metabox->setting_option_fields );
        }
    }

    /**
     * Get Settings Metabox Fields.
     *
     * @return array Array of settings fields.
     */
    public function get_settings_metabox_fields() {
        return $this->setting_option_fields;
    }

    public function get_templates() {
        return [
            'admin_section_metabox' => [
                'override_visibility' => 'private',
            ],
        ];
    }

    protected function get_template_group() {
        return 'admin/sections/settings';
    }


    protected function get_template_root_path() {
        return dirname(__DIR__, 2) . '/templates';
    }
}