<?php

namespace Netdust\VAD\Services\CustomLogin;

use Netdust\Utils\ServiceProvider;

class NTDST_CustomLogin extends ServiceProvider
{
    public $logo_url = 'https://vad.be';
    public $logo_title = "Welkom terug";
    public $login_page_title;


    public function register() {

        $this->container->register( NTDST_CustomRegistration::class );

    }

    public function boot()
    {
        add_action('login_header', array($this, 'add_extra_div'));
        add_action('login_head', array($this, 'generate_css'), 15);
        add_action('login_footer', array($this, 'close_extra_div'));

        add_filter('login_headerurl', array($this, 'logo_url'), 99);
        add_filter('login_headertext', array($this, 'logo_title'), 99);
        add_filter('login_title', array($this, 'login_page_title'), 99);

        add_action( 'login_footer', array( $this, 'print_login_button' ) );
        add_action( 'wp_login_errors', array( $this, 'display_link_message' ), 99, 2 );
        add_action( 'login_form_lostpassword', array( $this, 'add_loginlink_to_payload' ), 99, 2 );
        add_filter( 'retrieve_password_message', array( $this, 'email_use_login_message' ), 10, 4 );
    }


    public function logo_url($url)
    {
        if ('' != $this->logo_url) {
            return esc_url($this->logo_url);
        }

        return $url;
    }

    public function logo_title($title)
    {
        if ( ( (!array_key_exists('action', $_REQUEST)||!isset($_REQUEST['action'])) && !isset($_REQUEST['checkemail']) ) || ( isset($_REQUEST['action']) && $_REQUEST['action']=='login') ) {
            return wp_kses_post($this->logo_title);
        }
        if ( isset($_REQUEST['checkemail']) && $_REQUEST['checkemail']=='confirm' ) {
            return 'Ga naar je inbox';
        }

        return $title;
    }

    public function login_page_title($title)
    {
        if ( ( (!array_key_exists('action', $_REQUEST)||!isset($_REQUEST['action'])) && !isset($_REQUEST['checkemail']) ) || ( isset($_REQUEST['action']) && $_REQUEST['action']=='login') ) {
            return esc_html($this->login_page_title);
        }

        return $title;
    }

    public function add_extra_div()
    {
        ?>
        <div class="ml-container">
        <div class="ml-extra-div"></a></div>
        <div class="ml-form-container">
        <?php
    }

    public function close_extra_div()
    {
        if ( ( (!array_key_exists('action', $_REQUEST)||!isset($_REQUEST['action'])) && !isset($_REQUEST['checkemail']) ) || ( isset($_REQUEST['action']) && $_REQUEST['action']=='login') ) {
            $regurl = sprintf( '<a class="button small" href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register' ) );
            ?>
            <a class="subnav" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?' ); ?></a>
            <hr/>
            <div class="reg_footer">
                <h2>Ik ben nieuw hier</h2>
                <p><?php echo $regurl; ?></p>
                <div class="subnav">
                    <a class="el-link" href="https://www.vad.be/privacy" target="_blank">Privacy</a> | <a class="el-link" href="https://www.vad.be/disclaimer" target="_blank">Disclaimer</a>
                </div>
            </div>
            <?php
        }

        ?>
        </div></div>
        <?php
    }


    function display_link_message( $errors, $redirect_to ) {
        if( isset($_REQUEST['checkemail']) &&  $_REQUEST['checkemail']=='confirm'
        &&  isset($_REQUEST['loginlink']) && $_REQUEST['loginlink']==1
        ) {
            $errors = new \WP_Error();
            $errors->add(
                'confirm',
                'Ga naar je inbox voor je login link, gebruik deze eenmalig om je aan te melden.',
                'message'
            );

        }

        return $errors;
    }

    function print_login_button() {
        $registration_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
        $lostpassword_url = add_query_arg( ['loginlink'=>1], wp_lostpassword_url($registration_redirect) );
        ?>
        <script type="text/javascript" defer='defer'>
            (function ($) {
                $('#lostpasswordform').submit(function () {
                    if($(document.activeElement).hasClass("link"))
                    {
                        $(this).append($.map([{name:'loginlink',value:1}], function (param) {
                            return   $('<input>', {
                                type: 'hidden',
                                name: param.name,
                                value: param.value
                            })
                        }))
                    }

                });
                document.getElementById('lostpasswordform').insertAdjacentHTML(
                    'beforeend',
                    '<div id="continue-with-magic-login" class="continue-with-magic-login" style="display:inline-block;font-size:small;text-align:right;width:100%;">of ' +
                    '<input type="submit"  class="link" value="stuur me een loginlink">'+
                    '</div>'
                );
            }(jQuery || window.jQuery));
        </script>
        <?php

    }

    function add_loginlink_to_payload( )
    {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'lostpassword'
            && isset($_REQUEST['loginlink']) && $_REQUEST['loginlink'] == 1) {
            $_REQUEST['redirect_to'] = 'wp-login.php?checkemail=confirm&loginlink=1';
        }
    }

    function email_use_login_message( $message, $key, $user_login, $user_data ) {

        if ( isset($_REQUEST['loginlink']) && $_REQUEST['loginlink']==1 ) {

            $link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );

            $link = $this->app('magiclink')->create_login_link( $user_data );

            $message = '<p>Iemand heeft een login link aangevraagd voor je account</p>';
            $message .= '<p>Als dit een vergissing is mag je deze email verwijderen.</p>';
            $message .= '<p>' . sprintf( 'Gebruik <a href="%s">deze link</a> om in te loggen.', $link ) . '</p>';

            $message = bp_email_core_wp_get_template( $message, $user_data );

            add_filter( 'wp_mail_content_type', 'bp_email_set_content_type' ); // add this to support html in email

            if ( has_filter( 'wp_mail_content_type', 'pmpro_wp_mail_content_type' ) ) {
                remove_filter( 'wp_mail_content_type', 'pmpro_wp_mail_content_type' );
            }

            return $message;
        }

        return $message;
    }

    /**
     * Output the inline CSS
     */
    public function generate_css()
    {
        $logo_height = 80;

        $background_size = '100';
        $background_image = '';

        $logo_css = '.login h1 a{width:100%;height:100%;text-indent: unset;background-position:top center !important;padding-top:' . (30 + absint($logo_height)) . 'px; background-size: ' . $background_size . '; margin-top: -' . (15 + absint($logo_height)) . 'px; position:relative;background-image:url(' . $background_image . ')}';

        ?>
        <style type="text/css">
            <?php echo $logo_css; ?>

            body.login {
                background: rgba(171,202,204,.15);
            }
            body.login form {
                border:0;
                clip-path: polygon(0% 0,100% 0,100% calc(93%),calc(93%) 100%,0 100%);
            }
            body.login form .dashicons-visibility:before {
                color:#2a4b6d;
            }

            body.login h1 a, h2{
                color:#2a4b6d;
                text-align: center;
                font-size: inherit;
                font-weight: inherit;
            }
            .ml-form-container h2 {
                margin: 20px 0 !important
            }

            .login a.subnav {
                font-size: small;
                padding-top: 10px;
            }
            .login div.subnav {
                font-size: small;
                padding-top: 40px;
                text-align: center;
            }
            .login .button-large {
                background: #c96858;
                color: white;
                border-radius: 70px;
                border: 0;
                padding: 2px 20px !important;
            }
            .login .button.small {
                border-radius: 70px;
                background: transparent;
                color: #2a4b6d;
                border: 2px solid #2a4b6d;
                padding: 2px 40px;
                width: 300px;
                text-align: center;
            }

            .login #login_error, .login .message, .login .success {
                border-left: 4px solid #2a4b6d;
            }


            hr {
                width: 100%;
                max-width: 320px;
                margin-top: 40px !important
            }

            .privacy-policy-page-link, .language-switcher, #backtoblog, #nav {
                display: none;
            }
            .language-switcher{z-index:9;margin:0;}
            .login label{display:inline-block;}
            .login h1 a{ background-image: none !important;text-indent: unset;width:auto !important;height: auto !important; }
            #login form p label br{display:none}
            .ml-container #login{ position:relative;padding: 0;width:100%;max-width:320px;margin:0;}
            #loginform,#registerform,#lostpasswordform{box-sizing: border-box;max-height: 100%;background-position: center;background-repeat: no-repeat;background-size: cover;}
            .ml-container{position:relative;min-height:100vh;display:flex;height:100%;min-width:100%;}
            .ml-container .ml-extra-div{background-position:center;background-size:cover;background-repeat:no-repeat}
            .ml-container .ml-extra-div{position:absolute;top:0;left:0;width:100%;height:100%;z-index: -1;}
            .ml-form-container{display:flex;align-items:center;justify-content:center;flex-flow:column;}
            .ml-container .ml-form-container{width:100%;min-height:100vh;display:flex;flex-flow:column;}
            .login form input{font-size: inherit !important; }
            .login input[type=text]:focus, .login input[type=search]:focus, .login input[type=radio]:focus, .login input[type=tel]:focus, .login input[type=time]:focus, .login input[type=url]:focus, .login input[type=week]:focus, .login input[type=password]:focus, .login input[type=checkbox]:focus, .login input[type=color]:focus, .login input[type=date]:focus, .login input[type=datetime]:focus, .login input[type=datetime-local]:focus, .login input[type=email]:focus, .login input[type=month]:focus, .login input[type=number]:focus, .login select:focus, .login textarea:focus{ box-shadow: none; }

            .continue-with-magic-login { padding-top:15px; } .continue-with-magic-login input { background: none; border:none; cursor: pointer; } .continue-with-magic-login input:hover{ text-decoration: underline; }
        </style>
        <?php


    }

}