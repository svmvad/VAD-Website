<?php

namespace Netdust\VAD\Services\WooCommerce;

use Netdust\Utils\ServiceProvider;
use Netdust\VAD\Services\WooCommerce\Shortcodes\OrderWidget_shortcode;
use Netdust\VAD\Services\WooCommerce\Shortcodes\ProductDownloads_shortcode;
use Netdust\VAD\Services\WooCommerce\Discount\VAD_WooCommerce_Discount;
use Netdust\VAD\Services\WooCommerce\Hooks\VAD_WooCommerce_Template_hooks;
use Netdust\VAD\Services\WooCommerce\Shortcodes\CrossSellOverview_shortcode;

/**
 * @todo no-index op filter pagina's
 */
class VAD_WooCommerce extends ServiceProvider
{
    public function register() {

        if ( ! defined( 'WC_PLUGIN_FILE' ) ) {
          //  return;
        }

        $this->container->register( VAD_WooCommerce_Discount::class );

    }

    public function boot( )
    {
        add_post_type_support( 'product', 'excerpt' );
        add_post_type_support( 'product', 'sort' );
        // Settings page
        //$this->add_menupages();
        $this->add_hooks();
    }

    protected function add_hooks() {

        add_filter(
            'woocommerce_get_breadcrumb',
            $this->container->callback(VAD_WooCommerce_Template_hooks::class, 'custom_breadcrumb'),
            20, 2
        );

        ( new OrderWidget_shortcode( 'showorder'  ) )->do_actions();
        ( new CrossSellOverview_shortcode( 'cross-sell-overview'  ) )->do_actions();
        ( new ProductDownloads_shortcode( 'product-download-list'  ) )->do_actions();

    }

    protected function add_menupages() {

        app()->admin_pages()->add('woocommerce-page', [
            "class" => "Netdust\VAD\Factories\VAD_AdminPage",
            "args" => [
                'page_title' => 'VAD Catalogus',
                'menu_title' => 'VAD Catalogus',
                'capability' => 'read',
                'menu_slug' => 'vad-catalogus',
                'layout' => 'custom',
                'icon' => app()->css_url() . '/img/vad.png',
                'position' => 4,
                'sections' => [[
                    "class"       => "Netdust\VAD\Services\WooCommerce\Admin\VAD_AdminPage_SettingsWooCommerce",
                    "args"        => [
                        'id'          => 'settings-section',
                        'name'        => 'Settings',
                        'options_key' => 'vad_doorverwijsgids',
                        'fields'      => [
                            'test_setting' => [
                                'class' => 'Netdust\Loaders\Admin\Factories\SettingsFields\Number',
                                'args'  => [ 50, [
                                    'name'        => 'max_resultaten',
                                    'description' => 'Geef het maximum aantal resultaten aan voor een zoekopdracht',
                                    'label'       => 'Max. resultaten',
                                ] ],
                            ],
                        ],
                    ]
                ]]
            ]
        ]);


    }
}
