<?php

namespace Netdust\VAD\Pages;

use Netdust\Core\Request;
use Netdust\Service\Pages\VirtualPage;

class VAD_EditForm extends VirtualPage
{

    protected array $config;

    public function __construct()
    {
        parent::__construct( "temp", "temp", app()->tpl_dir() );
    }

    public function init( $template, $title) {

    }

    public function index(string $trailing, string $action, int $id ) {

        if( $action!='edit' ) {
            add_action( 'template_redirect',  [$this, 'custom_404_error'] );
        }

        if( is_user_logged_in()  ) {
            // we need to alter fluent forms request object
            wpFluentForm('request')->set( 'action', $action );
            wpFluentForm('request')->set( 'id', $id );

            $this->onRoute();
        }
        else {
            add_filter( 'login_redirect', [$this, 'redirect_after_login'], 10, 3 );
            add_action( 'template_redirect',  [$this, 'redirect_to_login'] );
        }
    }

    public function custom_404_error(  ) {
        global $wp_query;

        if ( ! is_admin() && $wp_query->is_404 ) {
            $wp_query->set_404();

            status_header( 404 );
            nocache_headers();

            include get_query_template( '404' );
            exit;
        }
    }

    public function redirect_to_login(  ) {
        remove_action( 'template_redirect', [$this, 'redirect_to_login']);
        $_SESSION['redirect_to'] = esc_url_raw( $_SERVER['REQUEST_URI'] );

        // Redirect to the login page
        wp_redirect( wp_login_url() );
    }
    public function redirect_after_login( $redirect_to, $request, $user ) {

        remove_filter( 'login_redirect', [$this, 'redirect_after_login']);

        // Check if the redirect session variable is set
        if ( isset( $_SESSION['redirect_to'] ) ) {
            $redirect_to = $_SESSION['redirect_to'];
            unset( $_SESSION['redirect_to'] ); // Clear the session variable
        }

        return $redirect_to;
    }

    public function get_Path() {
        return \Netdust\App::get( Request::class )->getPath();
    }

    public function get_Action() {
        return wpFluentForm('request')->get( 'action' );
    }

    public function get_ID() {
        return wpFluentForm('request')->get( 'id' );
    }

    public function config( string $key ) {
        return $this->config[$key];
    }

}