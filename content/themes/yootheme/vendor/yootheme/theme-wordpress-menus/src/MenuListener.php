<?php

namespace YOOtheme\Theme\Wordpress;

use YOOtheme\Arr;
use YOOtheme\Config;
use YOOtheme\View;

class MenuListener
{
    /**
     * Register navigation menus.
     *
     * @link https://developer.wordpress.org/themes/functionality/navigation-menus
     *
     * @param Config $config
     */
    public static function registerMenus(Config $config)
    {
        foreach ($config('theme.menus') as $id => $name) {
            register_nav_menu($id, __($name));
        }
    }

    /**
     * Filters the arguments used to display a navigation menu.
     *
     * @link https://developer.wordpress.org/reference/hooks/wp_nav_menu_args/
     *
     * @param Config $config
     * @param View   $view
     * @param mixed  $args
     * @return array
     */
    public static function filterMenuArgs(Config $config, View $view, $args)
    {
        return array_replace($args, [
            'walker' => new MenuWalker($view, $config),
            'container' => false,
            'fallback_cb' => false,
            'items_wrap' => '%3$s',
            'position' => get_current_sidebar(),
        ]);
    }

    /**
     * Filters the arguments for the Navigation Menu widget.
     *
     * @link https://developer.wordpress.org/reference/hooks/widget_nav_menu_args/
     *
     * @param mixed $nav_menu_args
     * @param mixed $nav_menu
     * @param mixed $args
     * @param mixed $instance
     */
    public static function filterWidgetMenuArgs($nav_menu_args, $nav_menu, $args, $instance)
    {
        $menuArgs = [];
        foreach ($instance['_theme'] ?? [] as $key => $value) {
            if (str_starts_with($key, 'menu_')) {
                $menuArgs[substr($key, 5)] = $value;
            }
        }

        return $nav_menu_args + $menuArgs;
    }

    /**
     * Filters the sorted list of menu item objects before generating the menu's HTML.
     *
     * @since 3.1.0
     *
     * @param array     $sorted_menu_items The menu items, sorted by each menu item's menu order.
     * @param \stdClass $args              An object containing wp_nav_menu() arguments.
     */
    public static function getNavMenuObjects($sorted_menu_items, $args)
    {
        $active = false;
        foreach ($sorted_menu_items as $item) {
            $classes = empty($item->classes) ? [] : (array) $item->classes;

            // Unset active class for posts_page if currently on none blog page
            if (
                in_array('current_page_parent', $classes) &&
                $item->object_id == get_option('page_for_posts') &&
                !is_singular('post') &&
                !is_category() &&
                !is_tag() &&
                !is_date() &&
                get_query_var('post_type') !== 'post'
            ) {
                unset($classes[array_search('current_page_parent', $classes)]);
            }

            $item->classes = $classes;

            // set current
            $item->active =
                !empty($item->active) ||
                ($item->url == 'index.php' && (is_home() || is_front_page())) ||
                (is_page() && in_array($item->object_id, get_post_ancestors(get_the_ID()))) ||
                preg_match(
                    '/\bcurrent-([a-z]+-ancestor|menu-(item|parent))\b/',
                    implode(' ', $item->classes)
                );

            if ($item->active) {
                static::setParentItemActive($sorted_menu_items, $item);
            }

            $active = $active || $item->active;
        }

        if (!$active) {
            foreach ($sorted_menu_items as $item) {
                $item->active = preg_match(
                    '/\bcurrent_page_(item|parent)\b/',
                    implode(' ', $item->classes)
                );

                if ($item->active) {
                    static::setParentItemActive($sorted_menu_items, $item);
                }
            }
        }

        return $sorted_menu_items;
    }

    protected static function setParentItemActive($items, $item)
    {
        $current = $item;
        while (
            $parent = Arr::find($items, function ($item) use ($current) {
                return $item->ID === (int) $current->menu_item_parent;
            })
        ) {
            $parent->active = true;
            $current = $parent;
        }
    }
}
