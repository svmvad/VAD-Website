<?php

namespace Netdust\VAD\Modules\BuddyBoss;



class VAD_BuddyBoss extends \Netdust\Factories\Module
{
    public function do_actions() {

        if ( ! defined( 'BP_VERSION' ) ) {
            return;
        }

        app()->shortcodes()->add( 'buddyboss_user_menu', [
                "class"=>"Netdust\VAD\Modules\BuddyBoss\shortcodes\BuddyBossUserMenu_shortcode",
                "args"=> [
                    'shortcode'=>'BuddyBossUserMenu'
                ]
            ]
        );

        //$notif = \Netdust\VAD\Modules\BuddyBoss\notifications\VAD_Admin_Notifications::instance();

        //add_action( 'init', [$this,'at_order_add_notification'], 10, 1 );


    }

    public function at_order_add_notification( ) {

        if ( bp_is_active( 'notifications' ) ) {
            ntdst_error_log('do notif');
            bp_notifications_add_notification(
                array(
                    'user_id' => 1,
                    //'item_id'           => $activity->id,
                    //'secondary_item_id' => $activity->user_id,
                    'component_name' => 'custom',
                    'component_action' => 'custom_action',
                    'date_notified' => bp_core_current_time(),
                    'is_new' => 1,
                )
            );
        }

    }

}