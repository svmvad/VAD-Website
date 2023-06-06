<?php

namespace Netdust\VAD\Services\MagicLink;


use Netdust\Utils\ServiceProvider;

class NTDST_MagicLink extends ServiceProvider
{

    static $TOKEN_USER_META = 'magic_login_token';
    static $CRON_HOOK_NAME  = 'magic_login_cleanup_expired_tokens';

    public $token_ttl                     = 5;

    public $login_email                   = '';


    public function register() {
        $this->login_email = $this->get_default_login_email_text();
    }

    public function boot() {
        $this->enqueue_scripts_and_styles();
        $this->add_hooks();
    }

    protected function add_hooks() {
        $hooks = new NTDST_MagicLink_Hooks();

        add_action( 'login_form_magic_login', array( $hooks, 'action_magic_login' ) );
        add_action( 'login_form_login', array( $hooks, 'maybe_redirect' ) );
        add_action( 'init', array( $hooks, 'handle_login_request' ) );
        add_action( static::$CRON_HOOK_NAME, array( $hooks, 'cleanup_expired_tokens' ) );
        //add_action( 'login_footer', array( $hooks, 'print_login_button' ) );
        //add_action( 'login_head', array( $hooks, 'login_css' ) );
    }


    /**
     * Create token
     *
     * @param object $user \WP_User object
     *
     * @return string
     */
    protected function create_user_token( $user ) {

        $tokens    = get_user_meta( $user->ID, static::$TOKEN_USER_META, true );
        $tokens    = is_string( $tokens ) ? array( $tokens ) : $tokens;
        $new_token = sha1( wp_generate_password() );

        $ip = sha1( $this->get_client_ip() );
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            $ip = 'cli';
        }

        if( !isset( $this->token_ttl ) ) {
            $this->token_ttl = 5;
        }

        $tokens[] = [
            'token'   => $new_token,
            'time'    => time(),
            'ip_hash' => $ip,
        ];

        update_user_meta( $user->ID, static::$TOKEN_USER_META, $tokens );

        if ( absint( $this->token_ttl ) > 0 ) { // eternal token
            wp_schedule_single_event( time() + ( $this->token_ttl * MINUTE_IN_SECONDS ), static::$CRON_HOOK_NAME, array( $user->ID ) );
        }

        return $new_token;
    }


    /**
     * Create login link for given user
     *
     * @param object $user WP_User object
     *
     * @return mixed|string
     */
    public function create_login_link( $user ) {
        $token = $this->create_user_token( $user );

        $query_args = array(
            'user_id'     => $user->ID,
            'token'       => $token,
            'magic-login' => 1,
        );

        if ( ! empty( $_REQUEST['http_referer'] ) ) {
            $query_args['redirect_to'] = esc_url_raw( $_REQUEST['http_referer'] );
        }

        $login_url = esc_url_raw( add_query_arg( $query_args, wp_login_url() ) );

        return $login_url;
    }

    /**
     * Get client raw ip
     * this should be hashed
     *
     * @return mixed
     */
    protected function get_client_ip() {
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Default login email message
     *
     * @return mixed|string|void
     */
    protected function get_default_login_email_text() {
        ob_start();
        include "templates/email.php";
        $email_text = ob_get_clean();

        return __( $email_text, 'magic-login' );
    }

    /**
     * Is plugin activated network wide?
     *
     * @param string $plugin_file file path
     *
     * @return bool
     * @since 1.0
     */
    public function is_network_wide( $plugin_file ) {
        if ( ! is_multisite() ) {
            return false;
        }

        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        return is_plugin_active_for_network( plugin_basename( $plugin_file ) );
    }

    /**
     * Get login link
     *
     * @return mixed|string
     */
    public function get_magic_login_url() {
        return esc_url_raw( site_url( 'wp-login.php?action=magic_login', 'login_post' ) );
    }

    /**
     * Get user tokens
     *
     * @param int  $user_id       User ID
     * @param bool $clear_expired flag for clean-up expired tokens
     *
     * @return array|mixed
     */
    public function get_user_tokens( $user_id, $clear_expired = false ) {
        $tokens = get_user_meta( $user_id, static::$TOKEN_USER_META, true );
        $tokens = is_string( $tokens ) ? array( $tokens ) : $tokens;

        if ( $clear_expired ) {
            $ttl      = absint( $this->token_ttl );

            if ( 0 === $ttl ) { // means token lives forever till used
                return $tokens;
            }

            foreach ( $tokens as $index => $token_data ) {
                if ( empty( $token_data ) ) {
                    unset( $tokens[ $index ] );
                    continue;
                }

                if ( time() > absint( $token_data['time'] ) + ( $ttl * MINUTE_IN_SECONDS ) ) {
                    unset( $tokens[ $index ] );
                }
            }
            update_user_meta( $user_id, static::$TOKEN_USER_META, $tokens );
        }

        return $tokens;
    }


    /**
     * Get default redirect url for given user
     *
     * @param \WP_User $user User object
     *
     * @return string|void
     */
    public function get_user_default_redirect( $user ) {
        if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {
            $redirect_to = user_admin_url();
        } elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {
            $redirect_to = get_dashboard_url( $user->ID );
        } elseif ( ! $user->has_cap( 'edit_posts' ) ) {
            $redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
        } else {
            $redirect_to = admin_url();
        }

        return $redirect_to;
    }

    /**
     * Delete all token meta
     */
    public function delete_all_tokens() {
        global $wpdb;

        return $wpdb->delete(
            $wpdb->usermeta,
            [
                'meta_key' => static::$TOKEN_USER_META,
            ]
        );
    }

    /**
     * Allowed intervals for TTL.
     *
     * @return array
     * @since 1.2
     */
    public function get_allowed_intervals() {
        return [
            'MINUTE' => esc_html__( 'Minute(s)', 'magic-login' ),
            'HOUR'   => esc_html__( 'Hour(s)', 'magic-login' ),
            'DAY'    => esc_html__( 'Day(s)', 'magic-login' ),
        ];
    }

    /**
     * Convert minutes to possible time format
     *
     * @param int $timeout_in_minutes TTL in minutes
     *
     * @return array
     * @since 1.2
     */
    public function get_ttl_with_interval( $timeout_in_minutes ) {
        $ttl      = $timeout_in_minutes;
        $interval = 'MINUTE';

        if ( $ttl > 0 ) {
            if ( 0 === (int) ( $ttl % 1440 ) ) {
                $ttl      = $ttl / 1440;
                $interval = 'DAY';
            } elseif ( 0 === (int) ( $ttl % 60 ) ) {
                $ttl      = $ttl / 60;
                $interval = 'HOUR';
            }
        }

        return array(
            $ttl,
            $interval,
        );
    }


    protected function enqueue_scripts_and_styles()
    {

    }

}