<?php

namespace YOOtheme\Builder\Wordpress\Source\Type;

use function YOOtheme\trans;

class CustomMenuItemsQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'customMenuItems' => [
                    'type' => [
                        'listOf' => 'MenuItem',
                    ],

                    'args' => [
                        'id' => [
                            'type' => 'Int',
                        ],
                        'parent' => [
                            'type' => 'String',
                        ],
                        'heading' => [
                            'type' => 'String',
                        ],
                        'include_heading' => [
                            'type' => 'Boolean',
                            'defaultValue' => true,
                        ],
                        'ids' => [
                            'type' => [
                                'listOf' => 'String',
                            ],
                        ],
                    ],

                    'metadata' => [
                        'label' => trans('Custom Menu Items'),
                        'group' => trans('Custom'),
                        'fields' => [
                            'id' => [
                                'label' => trans('Menu'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [['evaluate' => 'this.$root.Menu.menus']],
                            ],
                            'parent' => [
                                'label' => trans('Parent Menu Item'),
                                'description' => trans(
                                    'Menu items are only loaded from the selected parent item.'
                                ),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['value' => '', 'text' => trans('Root')],
                                    ['evaluate' => 'this.$root.Menu.menuItems[id]'],
                                ],
                            ],
                            'heading' => [
                                'label' => trans('Limit by Menu Heading'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['value' => '', 'text' => trans('None')],
                                    ['evaluate' => 'this.$root.Menu.headingItems(id, parent)'],
                                ],
                            ],
                            'include_heading' => [
                                'description' => trans(
                                    'Only load menu items from the selected menu heading.'
                                ),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Include heading itself'),
                            ],
                            'ids' => [
                                'label' => trans('Select Manually'),
                                'description' => trans(
                                    'Select menu items manually. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple menu items.'
                                ),
                                'type' => 'select',
                                'options' => [['evaluate' => 'this.$root.Menu.menuItems[id]']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ],
                        ],
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root, array $args)
    {
        $items = wp_get_nav_menu_items($args['id']);

        _wp_menu_item_classes_by_context($items);

        apply_filters('wp_nav_menu_objects', $items, []);

        $found = false;
        return array_filter($items, function ($item) use ($args, &$found) {
            if (!empty($args['ids'])) {
                return in_array($item->ID, $args['ids']);
            }

            if (!empty($args['heading'])) {
                if (!$found) {
                    if ((string) $item->ID === $args['heading']) {
                        $found = $item;
                        return !empty($args['include_heading']);
                    }
                    return false;
                }

                if ($item->menu_item_parent !== $found->menu_item_parent) {
                    return false;
                }

                if (!($item->type === 'custom' && $item->url === '#')) {
                    return true;
                }

                return $found = false;
            }

            if (!empty($args['parent'])) {
                return $item->menu_item_parent === $args['parent'];
            }

            return $item->menu_item_parent === '0';
        });
    }
}
