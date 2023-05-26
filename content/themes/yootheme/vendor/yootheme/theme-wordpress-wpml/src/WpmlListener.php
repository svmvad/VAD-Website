<?php

namespace YOOtheme\Theme\Wordpress;

class WpmlListener
{
    /**
     * Skip filtering of nav menus by WPML
     *
     * @param \WP_Term[] $menus An array of menu objects.
     * @param array $args An array of arguments used to retrieve menu objects.
     */
    public static function getNavMenus($menus, $args)
    {
        if (is_customize_preview()) {
            return get_terms($args);
        }
        return $menus;
    }

    /**
     * Skip WPML filters to retrieve terms of all languages.
     *
     * @param array $args An array of arguments used to retrieve term objects.
     */
    public static function getTermsArgs($args)
    {
        if (is_customize_preview()) {
            return $args + ['wpml_skip_filters' => true];
        }
        return $args;
    }
}
