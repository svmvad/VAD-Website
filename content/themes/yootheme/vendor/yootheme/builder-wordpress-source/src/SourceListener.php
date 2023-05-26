<?php

namespace YOOtheme\Builder\Wordpress\Source;

use YOOtheme\Config;
use YOOtheme\Http\Request;
use YOOtheme\Str;
use function YOOtheme\trans;

class SourceListener
{
    public static function initSource($source)
    {
        $query = [
            Type\DateQueryType::config(),
            Type\UserQueryType::config(),
            Type\SiteQueryType::config(),
            Type\SearchQueryType::config(),
        ];

        $types = [
            ['Date', Type\DateType::config()],
            ['Search', Type\SearchType::config()],
            ['Site', Type\SiteType::config()],
            ['User', Type\UserType::config()],
            ['Attachment', Type\AttachmentType::config()],
        ];

        foreach (Helper::getPostTypes() as $type) {
            $query[] = Type\PostQueryType::config($source, $type);
            $types[] = [Str::camelCase($type->name, true), Type\PostType::config($type)];
        }

        $types[] = ['MenuItem', Type\MenuItemType::config()];

        foreach (Helper::getTaxonomies() as $taxonomy) {
            $query[] = Type\TaxonomyQueryType::config($source, $taxonomy);
            $types[] = [
                Str::camelCase($taxonomy->name, true),
                Type\TaxonomyType::config($taxonomy),
            ];
        }

        $query[] = Type\CustomMenuItemsQueryType::config();
        $query[] = Type\CustomUserQueryType::config();

        foreach ($query as $args) {
            $source->queryType($args);
        }

        foreach ($types as $args) {
            $source->objectType(...$args);
        }
    }

    public static function initCustomizer(Config $config)
    {
        $archives = [];
        $templates = [];

        require_once ABSPATH . 'wp-admin/includes/translation-install.php';

        $translations = \wp_get_available_translations();
        $languages = [['text' => 'English (United States)', 'value' => 'en_US']];
        foreach (get_available_languages() as $lang) {
            if (isset($translations[$lang])) {
                $languages[] = ['text' => $translations[$lang]['native_name'], 'value' => $lang];
            }
        }

        foreach (Helper::getPostTypes() as $name => $type) {
            $templates["single-{$name}"] = [
                'label' => trans('Single %post_type%', [
                    '%post_type%' => $type->labels->singular_name,
                ]),
                'group' => trans('Single Post'),
            ];

            $taxonomies = get_object_taxonomies($name);

            sort($taxonomies);

            if ($taxonomies) {
                $label_lower = mb_strtolower($type->labels->name);

                $templates["single-{$name}"] += [
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'terms' => [
                                    'label' => trans('Limit by Terms'),
                                    'description' => trans(
                                        'The template is only assigned to %post_types_lower% with the selected terms. %post_types% from child terms are not included. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple terms.',
                                        [
                                            '%post_types_lower%' => $label_lower,
                                            '%post_types%' => $type->labels->name,
                                        ]
                                    ),
                                    'type' => 'select',
                                    'default' => [],
                                    'options' => array_map(function ($taxonomy) {
                                        return ['evaluate' => "config.taxonomies.{$taxonomy}"];
                                    }, $taxonomies),
                                    'attrs' => [
                                        'multiple' => true,
                                        'class' => 'uk-height-medium',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }

            if (count($languages) > 1) {
                $templates["single-{$name}"]['fieldset']['default']['fields'][
                    'locale'
                ] = static::getLocaleField();
            }

            if ($name === 'post' || $type->has_archive) {
                $archives["archive-{$name}"] = [
                    'label' => trans('%post_type% Archive', ['%post_type%' => $type->label]),
                    'group' => trans('Post Type Archive'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'pages' => static::getPageField(),
                                'locale' => static::getLocaleField(),
                            ],
                        ],
                    ],
                ];
            }
        }

        $archives['author-archive'] = [
            'label' => trans('Author Archive'),
            'group' => trans('Archive Pages'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];

        $archives['date-archive'] = [
            'label' => trans('Date Archive'),
            'group' => trans('Archive Pages'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'archive' => [
                            'label' => trans('Limit by Date Archive Type'),
                            'description' => trans(
                                'The template is only assigned to the selected pages.'
                            ),
                            'type' => 'select',
                            'options' => [
                                trans('Any') => '',
                                trans('Year Archive') => 'year',
                                trans('Month Archive') => 'month',
                                trans('Day Archive') => 'day',
                                trans('Time Archive') => 'time',
                            ],
                        ],
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];

        $templates += $archives;

        $taxonomies = [];
        $allTaxonomies = [['text' => trans('None'), 'value' => '']];

        foreach (Helper::getTaxonomies() as $name => $taxonomy) {
            $templates["taxonomy-{$name}"] = static::getTaxonomyArchive($taxonomy);
            $allTaxonomies[] = ['text' => $taxonomy->label, 'value' => $taxonomy->name];

            if ($terms = static::getTaxonomyTerms($taxonomy)) {
                $taxonomies[$name] = [
                    'label' => $taxonomy->label,
                    'options' => $terms,
                ];
            }
        }

        $templates['search'] = [
            'label' => trans('Search'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];

        $templates['error-404'] =
            [
                'label' => trans('Error 404'),
            ] +
            (count($languages) > 1
                ? [
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'locale' => static::getLocaleField(),
                            ],
                        ],
                    ],
                ]
                : []);

        $authors = [];
        foreach (
            get_users(['fields' => ['ID', 'display_name'], 'capability' => ['edit_posts']])
            as $user
        ) {
            $authors[] = ['text' => $user->display_name, 'value' => (int) $user->ID];
        }

        $roles = [];
        foreach (wp_roles()->get_names() as $id => $name) {
            $roles[] = ['text' => $name, 'value' => $id];
        }

        $config->add('customizer.templates', $templates);
        $config->add('customizer.taxonomies', $taxonomies);
        $config->add('customizer.allTaxonomies', $allTaxonomies);
        $config->add('customizer.authors', $authors);
        $config->add('customizer.languages', $languages);
        $config->add('customizer.roles', $roles);
    }

    public static function addPostTypeFilter(Request $request, $query)
    {
        if ($post_type = $request->getParam('post_type')) {
            return ['post_type' => [$post_type]] + $query;
        }

        return $query;
    }

    public static function getPageField()
    {
        return [
            'label' => trans('Limit by Page Number'),
            'description' => trans('The template is only assigned to the selected pages.'),
            'type' => 'select',
            'options' => [
                trans('All pages') => '',
                trans('First page') => 'first',
                trans('All except first page') => 'except_first',
            ],
        ];
    }

    public static function getLocaleField()
    {
        return [
            'label' => trans('Limit by Language'),
            'type' => 'select',
            'defaultIndex' => 0,
            'options' => [
                ['text' => __('All languages', 'yootheme'), 'value' => ''],
                ['evaluate' => 'config.languages'],
            ],
            'show' => '$customizer.languages[\'length\'] > 1 || lang',
        ];
    }

    public static function getTaxonomyArchive($taxonomy)
    {
        $label_lower = mb_strtolower($taxonomy->labels->name);
        $has_archive = $taxonomy->hierarchical
            ? trans('Child %taxonomies% are not included.', ['%taxonomies%' => $label_lower])
            : '';

        return [
            'label' => "{$taxonomy->labels->singular_name} Archive",
            'group' => trans('Taxonomy Archive'),
            'fieldset' => [
                'default' => [
                    'fields' => [
                        'terms' => [
                            'label' => $taxonomy->label,
                            'description' => trans(
                                'The template is only assigned to the selected %taxonomies%. %has_archive% Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple %taxonomies%.',
                                ['%taxonomies%' => $label_lower, '%has_archive%' => $has_archive]
                            ),
                            'type' => 'select',
                            'default' => [],
                            'options' => [
                                ['evaluate' => "config.taxonomies.{$taxonomy->name}.options"],
                            ],
                            'attrs' => [
                                'multiple' => true,
                                'class' => 'uk-height-small',
                            ],
                        ],
                        'pages' => static::getPageField(),
                        'locale' => static::getLocaleField(),
                    ],
                ],
            ],
        ];
    }

    public static function getTaxonomyTerms($taxonomy)
    {
        return static::getTerms($taxonomy, function ($term) {
            $name = html_entity_decode($term->name);
            $level = get_ancestors($term->term_id, $term->taxonomy);

            return ['value' => $term->term_id, 'text' => str_repeat('- ', count($level)) . $name];
        });
    }

    protected static function getTerms($taxonomy, callable $callback = null)
    {
        $terms = get_terms([
            'taxonomy' => $taxonomy->name,
            'hide_empty' => false,
        ]);

        $terms = _get_term_children(0, $terms, $taxonomy->name);

        return $callback ? array_map($callback, $terms) : $terms;
    }
}
