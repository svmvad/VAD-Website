<?php

namespace Netdust\VAD\Services\CustomLogin;

use Netdust\Utils\ServiceProvider;

class NTDST_CustomRegistration extends ServiceProvider
{
    public $logo_url = 'https://vad.be';
    public $logo_title = "Welkom op onze site";
    public $login_page_title;


    public function register() {

    }

    public function boot()
    {
        add_filter('login_headerurl', array($this, 'logo_url'), 99);
        add_filter('login_headertext', array($this, 'logo_title'), 99);
        add_filter('login_title', array($this, 'login_page_title'), 99);

        add_action( 'register_form', [$this,'custom_register_extra_fields'] );
        add_filter( 'registration_errors', [$this,'custom_register_extra_fields_validation'], 10, 3 );
        add_action( 'user_register', [$this,'custom_register_extra_fields_save'] );
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
        if ( ( (!array_key_exists('action', $_REQUEST)||!isset($_REQUEST['action'])) ) || ( isset($_REQUEST['action']) && $_REQUEST['action']=='register') ) {
            return wp_kses_post($this->logo_title);
        }
        return $title;
    }

    public function login_page_title($title)
    {
        if ( ( (!array_key_exists('action', $_REQUEST)||!isset($_REQUEST['action'])) ) || ( isset($_REQUEST['action']) && $_REQUEST['action']=='register') ) {
            return esc_html($this->login_page_title);
        }

        return $title;
    }

// Add a new field to the registration form
    public function custom_register_extra_fields() {
        ?>
        <p>
            <label for="phone_number">Phone Number<br/>
                <input type="text" name="phone_number" id="phone_number" class="input" value="<?php echo ( ! empty( $_POST['phone_number'] ) ) ? esc_attr( $_POST['phone_number'] ) : ''; ?>" />
            </label>
        </p>
        <?php
    }

// Validate and save the custom field value during registration
    public function custom_register_extra_fields_validation( $errors, $sanitized_user_login, $user_email ) {
        if ( empty( $_POST['phone_number'] ) ) {
            $errors->add( 'phone_number_error', __( '<strong>ERROR</strong>: Please enter your phone number.', 'text-domain' ) );
        }

        return $errors;
    }

// Save the custom field value to the user meta after registration
    public function custom_register_extra_fields_save( $user_id ) {
        if ( ! empty( $_POST['phone_number'] ) ) {
            update_user_meta( $user_id, 'phone_number', sanitize_text_field( $_POST['phone_number'] ) );
        }
    }


}