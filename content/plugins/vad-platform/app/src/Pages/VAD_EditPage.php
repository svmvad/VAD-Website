<?php

namespace Netdust\VAD\Factories;

class VAD_EditPage extends  \Netdust\Loaders\PostTemplate\Factories\PostTemplate_Instance {

    public function do_actions(){
        parent::do_actions();
        add_action ('magic_login_redirect', array($this, 'login_redirect'), 10, 2);
        add_action ('netdust-before_template', array($this, 'maybe_redirect'), 10, 1 );
        add_action('fluentform_before_insert_submission', array($this,'handle_submission'), 10, 3 );


        add_action( 'pre_get_posts', function ( $wp ){
            if( isset( $wp->query['edit'] ) ) {
                $data = $this->get_post_data( );
                $wp->query_vars['post_type'] = $data->post_type;
                $wp->query_vars['title'] = $data->post_title;
            };
            return $wp;
        } );
    }

    public function maybe_redirect( $template ){

        if( $template == $this->template && !is_user_logged_in() ) {
            wp_redirect( add_query_arg(
                ['redirect_to'=>home_url( $_SERVER['REQUEST_URI'] )],
                app('magiclink')->get_magic_login_url() )
            );
            die;
        }
    }

    public function login_redirect( $redirect_to, $user ){

        $url = 'members/' . $user->user_nicename;
        return $_REQUEST['redirect_to'] ?? home_url( user_trailingslashit( $url ) );

    }

    public function do_template( ){

        $forms = [
            'vad--research' => 4,
            'vad--vacancy' => 5,
        ];

        if( ($data = $this->get_post_data()) != null )
        {
            $admins = get_field( 'research_admins', $data->ID );

            if( in_array( get_current_user_id(), $admins ) || current_user_can( 'manage_options' ) ) {

                echo $this->get_template('editpage', [
                    'title' => $data->post_title,
                    'description' =>'Je kan hier de informatie verder updaten.',
                    'content' => do_shortcode('[fluentform id="'.$forms[$data->post_type].'"]')
                ] );
            }
            else {
                echo $this->get_template('editpage', [
                    'title' => 'Er liep iets mis',
                    'content' => 'Op deze pagina kan je gegevens aanpassen, maar het lijkt erop dat je geen toestemming hebt hiervoor. Als dit een vergissing blijkt te zijn, gelieve contact op te nemen met ons.'
                ] );

            }
        }
        else
        echo 'we have a problem';

    }

    protected function get_post_data( ) {

        $args = array(
            'name'           => $_REQUEST['item'],
            'post_type'      => array( 'vad--research','vad--vacancy' ),
            'post_status'    => 'publish',
            'posts_per_page' => 1
        );
        $posts = get_posts( $args );

        if( count( $posts ) > 1 ) {
            return null;
        }

        return $posts[0];

    }

    public function get_form( ) {

        do_action('vad_vormingen-before_form_template', null );

        echo $this->get_template('layout', [
            'title' => $this->title,
            'content' => do_shortcode('[fluentform id="'.$this->form.'"]'),
            'widgets' => ''
        ] );

    }

    public function handle_submission( $insertData, $data, $form ) {

        if($form->id != $this->form) {
            return;
        }


        do_action('vad_vormingen-before_form_submit', $data );

        // submit or do not submit but redirect
        if( isset( $data['refer_url'] ) ){
            wp_send_json_success([
                'result' => [
                    'redirectUrl' => $data['refer_url'],
                    'message' => 'We hebbende informatie juist ontvangen, even geduld...'
                ]
            ]);
        }
    }

    public function get_templates() {
        return [
            'editpage' => [
                'override_visibility' => 'private',
            ],
            'parts/editform' => [
                'override_visibility' => 'private',
            ],

        ];
    }

    protected function get_template_group() {
        return 'front';
    }


    protected function get_template_root_path() {
        return dirname(__DIR__, 2) . '/templates';
    }

}