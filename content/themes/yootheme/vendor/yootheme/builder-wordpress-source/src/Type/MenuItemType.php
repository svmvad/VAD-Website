<?php

namespace YOOtheme\Builder\Wordpress\Source\Type;

use YOOtheme\Config;
use function YOOtheme\app;
use function YOOtheme\trans;

class MenuItemType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Title'),
                        'filters' => ['limit'],
                    ],
                ],
                'image' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],
                'icon' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Icon'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],
                'subtitle' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Subtitle'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],
                'url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Link'),
                    ],
                ],
                'active' => [
                    'type' => 'Boolean',
                    'metadata' => [
                        'label' => trans('Active'),
                    ],
                ],
                'type' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Type'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::type',
                    ],
                ],
            ],
            'metadata' => [
                'type' => true,
                'label' => trans('Menu Item'),
            ],
        ];
    }

    public static function data($item, $args, $context, $info)
    {
        return app(Config::class)->get("~theme.menu.items.{$item->ID}.{$info->fieldName}");
    }

    public static function type($item)
    {
        if ($item->type === 'custom') {
            if ($item->url === '#' && preg_match('/---+/i', $item->title)) {
                return 'divider';
            }
            if ($item->url === '#') {
                return 'heading';
            }
        }

        return '';
    }
}
