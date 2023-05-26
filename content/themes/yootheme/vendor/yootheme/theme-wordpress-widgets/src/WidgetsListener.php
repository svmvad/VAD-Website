<?php

namespace YOOtheme\Theme;

use YOOtheme\Arr;
use YOOtheme\Config;
use YOOtheme\Metadata;
use YOOtheme\Path;
use YOOtheme\Theme\Wordpress\FilterHelper;
use YOOtheme\View;
use function YOOtheme\app;

class WidgetsListener
{
    /**
     * @var string|null
     */
    public $style;

    /**
     * @var string|null
     */
    public $sidebar;

    /**
     * @var array
     */
    public $widgets = [];

    /**
     * @var array|null
     */
    public $position = [];

    /**
     * @var array
     */
    public $logos = [];

    /**
     * @var array
     */
    public $config;

    /**
     * Initialize widgets and sidebars.
     *
     * @link https://developer.wordpress.org/themes/functionality/sidebars
     * @link https://developer.wordpress.org/reference/hooks/widgets_init/
     *
     * @param Config $config
     */
    public function initWidgets(Config $config)
    {
        register_widget('BuilderWidget');
        register_widget('YOOtheme\Theme\BreadcrumbsWidget');

        $this->config = $config->loadFile(Path::get('../config/widgets.json'));

        foreach ($config('theme.positions') as $id => $name) {
            $this->registerSidebar($id, $name);
        }
    }

    public function isActiveSidebar($active, $sidebar)
    {
        return $active ||
            has_nav_menu($sidebar) ||
            ($sidebar == 'navbar-split' &&
                (has_nav_menu('navbar') || $this->getMenuWidgets('navbar'))) ||
            (in_array($sidebar, [
                'header-split',
                'dialog-push',
                'dialog-mobile-push',
                'header-push',
                'navbar-push',
            ]) &&
                !empty($this->widgets[$sidebar])) ||
            $this->hasHeaderSearchOrSocial($sidebar) ||
            $this->getLogo($sidebar, true) ||
            $this->hasToggle($sidebar);
    }

    public function beforeSidebar($sidebar)
    {
        $this->sidebar = $sidebar;
        if (
            !in_array($sidebar, [
                'header-split',
                'navbar-push',
                'dialog-push',
                'dialog-mobile-push',
            ])
        ) {
            $this->widgets[$sidebar] = [];
        }
    }

    public function afterSidebar(Config $config, View $view, $sidebar)
    {
        global $wp_registered_widgets;

        $items = $this->widgets[$sidebar];

        // Menu (Location) Widget
        if (has_nav_menu($sidebar)) {
            array_unshift(
                $items,
                $this->createMenuWidget($sidebar, $sidebar, get_nav_menu_locations()[$sidebar])
            );
        }

        if ($sidebar === 'navbar-split') {
            if (
                has_nav_menu('navbar') &&
                in_array($config('~theme.menu.positions.navbar.type'), ['', 'nav'])
            ) {
                array_unshift(
                    $items,
                    $this->createMenuWidget($sidebar, 'navbar', get_nav_menu_locations()['navbar'])
                );
            }

            foreach ($this->getMenuWidgets('navbar') as $id) {
                $widget = $wp_registered_widgets[$id];
                $settings = $widget['callback'][0]->get_settings();
                $params = Arr::get($settings, Arr::get($widget, 'params.0.number', 0), []);
                $menu = Arr::get($params, 'nav_menu');
                $type = Arr::get(
                    json_decode(Arr::get($params, '_theme', '{}'), true) ?: [],
                    'menu_type'
                );

                if ($menu && in_array($type, ['', 'nav'])) {
                    array_unshift($items, $this->createMenuWidget($sidebar, 'navbar', $menu));
                }
            }
        }

        // Logo Widget
        if ($content = $this->getLogo($sidebar)) {
            $widget = $this->createWidget([
                'id' => 'logo',
                'type' => 'logo',
                'content' => $content,
            ]);
            array_unshift($items, $widget);
        }

        // Search Widget
        foreach (['~theme.header.search', '~theme.mobile.header.search'] as $key) {
            $position = explode(':', $config($key, ''), 2);
            if ($sidebar == $position[0]) {
                $widget = $this->getWidget($sidebar, 'WP_Widget_Search');
                $position[1] == 'start'
                    ? array_unshift($items, $widget)
                    : array_push($items, $widget);
            }
        }

        // Social Widget
        foreach (['~theme.header.social', '~theme.mobile.header.social'] as $key) {
            $position = explode(':', $config($key, ''), 2);
            if (
                $sidebar == $position[0] &&
                ($content = trim($view('~theme/templates/socials', ['position' => $sidebar])))
            ) {
                $widget = $this->createWidget([
                    'id' => 'social',
                    'type' => 'social',
                    'content' => $content,
                ]);

                $position[1] == 'start'
                    ? array_unshift($items, $widget)
                    : array_push($items, $widget);
            }
        }

        // Dialog Toggle Widget
        foreach (['~theme.dialog.toggle', '~theme.mobile.dialog.toggle'] as $key) {
            $position = explode(':', $config($key, ''), 2);
            if (
                $sidebar == $position[0] &&
                ($content = trim($view('~theme/templates/header-dialog', ['position' => $sidebar])))
            ) {
                $widget = $this->createWidget([
                    'id' => 'dialog-toggle',
                    'type' => 'dialog-toggle',
                    'content' => $content,
                ]);

                $position[1] == 'start'
                    ? array_unshift($items, $widget)
                    : array_push($items, $widget);
            }
        }

        // Split Header Area
        if ($sidebar == 'header' && $config('~theme.header.layout') == 'stacked-center-c') {
            // Split Auto
            $index = $config('~theme.header.split_index') ?: ceil(count($items) / 2);

            if (!is_registered_sidebar('header-split')) {
                $this->registerSidebar('header-split', 'Header Split');
                $this->widgets['header-split'] = array_slice($items, $index);
            }
            $items = array_slice($items, 0, $index);
        }

        // Push Navbar Area
        if (
            $sidebar == 'navbar' &&
            $config('~theme.header.layout') == 'stacked-left' &&
            ($index = $config('~theme.header.push_index'))
        ) {
            if (!is_registered_sidebar('navbar-push')) {
                $this->registerSidebar('navbar-push', 'Navbar Push');
                $this->widgets['navbar-push'] = array_slice($items, $index);
            }
            $items = array_slice($items, 0, $index);
        }

        // Push Dialog Areas
        foreach (
            [
                'dialog' => '~theme.dialog.push_index',
                'dialog-mobile' => '~theme.mobile.dialog.push_index',
            ]
            as $key => $value
        ) {
            if ($sidebar == $key && ($index = $config($value))) {
                if (!is_registered_sidebar($key . '-push')) {
                    $this->registerSidebar($key . '-push', ucfirst($key) . ' Push');
                    $this->widgets[$key . '-push'] = array_slice($items, $index);
                }
                $items = array_slice($items, 0, $index);
            }
        }

        echo $view('~theme/templates/position', [
            'name' => $sidebar,
            'items' => $items,
            'style' => $this->style,
            'position' => $this->position,
        ]);

        $this->style = null;
        $this->sidebar = null;
        $this->position = null;
    }

    public function parseSidebarStyle($title, $raw)
    {
        global $wp_registered_sidebars;

        if (strpos($raw, ':')) {
            [$name, $style] = explode(':', $raw, 2);

            if (isset($wp_registered_sidebars[$name])) {
                $this->style = $style;
                return $name;
            }
        }

        return $title;
    }

    /**
     * @param Config      $config
     * @param array|false $instance
     * @param \WP_Widget  $widget
     * @param array       $args
     *
     * @return array|false
     */
    public function displayWidget(Config $config, $instance, $widget, $args)
    {
        // store sidebar in case another sidebar is rendered within this widget
        $sidebar = $this->sidebar;

        if ($instance === false || ($sidebar === null && empty($args['yoo_element']))) {
            return $instance;
        }

        $type = strtr(str_replace('nav_menu', 'menu', $widget->id_base), '_', '-');

        // Prepare widget theme settings
        $instance['_theme'] =
            ($args['_theme'] ?? []) +
            json_decode($instance['_theme'] ?? '{}', true) +
            array_map(fn($field) => $field['default'] ?? '', $this->config['fields']);

        // Set settings in config for rendering chrome (templates/position.php and templates/module.php)
        $config->update(
            "~theme.modules.{$widget->id}",
            fn($values) => ['is_list' => $this->isListWidget($type)] +
                $instance['_theme'] +
                ($values ?: [])
        );

        // Ignore wpautop filter for text-widgets in header position
        if (
            in_array($sidebar, [
                'navbar',
                'navbar-split',
                'navbar-push',
                'navbar-mobile',
                'header',
                'header-split',
                'header-mobile',
                'toolbar-left',
                'toolbar-right',
                'logo',
                'logo-mobile',
            ])
        ) {
            $restore = FilterHelper::remove('widget_text_content', 'wpautop');
        }

        ob_start();
        $widget->widget($args, $instance);
        $output = ob_get_clean();

        if (isset($restore)) {
            $restore();
        }

        preg_match(
            '/' .
                preg_quote($args['before_widget'], '/') .
                '(.*)' .
                preg_quote($args['after_widget'], '/') .
                '/s',
            $output,
            $content
        );
        preg_match(
            '/' .
                preg_quote($args['before_title'], '/') .
                '(.*?)' .
                preg_quote($args['after_title'], '/') .
                '/s',
            $output,
            $title
        );

        $content = $content ? $content[1] : $output;

        if ($title) {
            $content = str_replace($title[0], '', $content);
        }

        // add 'uk-panel' to text widget content div class
        if ($type === 'text') {
            $content = substr_replace(
                $content,
                'uk-panel ',
                strpos($content, 'class="textwidget"') + strlen('class="'),
                0
            );
        }

        if (!isset($widget->widget_cssclass)) {
            $widget->widget_cssclass = '';
        }

        $this->widgets[$sidebar][] = $this->createWidget([
            'id' => $widget->id,
            'type' => $type,
            'title' => $title ? $title[1] : '',
            'content' => $content,
            'instance' => $instance,
            'attrs' => [
                'id' => $widget->id,
                'class' => [trim("widget widget_{$widget->id_base} {$widget->widget_cssclass}")],
            ],
        ]);

        $this->sidebar = $sidebar;

        return false;
    }

    public function editScreen(Config $config, Metadata $metadata, $screen)
    {
        if (in_array($screen->base, ['customize', 'widgets'])) {
            $metadata->set(
                'script:widgets-data',
                sprintf('var $widgets = %s;', json_encode($this->config))
            );

            if ($screen->base === 'widgets') {
                $debug = $config('app.debug') ? '' : '.min';
                $metadata->set('script:uikit', ['src' => "~assets/uikit/dist/js/uikit{$debug}.js"]);
                $metadata->set('script:widgets', ['src' => Path::get('../app/widgets.min.js')]);
            }
        }
    }

    /**
     * @param \WP_Widget $widget
     * @param null       $return
     * @param array      $instance
     */
    public function editWidget($widget, $return, $instance)
    {
        echo sprintf(
            '<input type="hidden" name="%s" value="%s" data-widget>',
            $widget->get_field_name('_theme'),
            esc_attr($instance['_theme'] ?? '{}')
        );
    }

    public function updateWidget($instance, $new_instance)
    {
        if (isset($new_instance['_theme'])) {
            $instance['_theme'] = $new_instance['_theme'];
        }

        return $instance;
    }

    protected function createWidget($widget)
    {
        static $id = 0;

        return (object) array_merge(
            [
                'id' => 'tm-' . ++$id,
                'title' => '',
                'position' => $this->sidebar,
                'attrs' => ['class' => []],
            ],
            (array) $widget
        );
    }

    protected function registerSidebar($id, $name)
    {
        register_sidebar([
            'id' => $id,
            'name' => $name,
            'before_widget' => '<content>',
            'after_widget' => '</content>',
            'before_title' => '<title>',
            'after_title' => '</title>',
        ]);
    }

    protected function isListWidget($type)
    {
        return in_array($type, [
            'recent-posts',
            'pages',
            'recent-comments',
            'archives',
            'categories',
            'meta',
        ]);
    }

    protected function getLogo($sidebar, $check = false)
    {
        if (
            !in_array($sidebar, ['logo', 'logo-mobile', 'dialog', 'dialog-mobile']) ||
            ($check && str_starts_with($sidebar, 'dialog'))
        ) {
            return '';
        }

        if (!isset($this->logos[$sidebar])) {
            $this->logos[$sidebar] = trim(
                app(View::class)('~theme/templates/header-logo', ['position' => $sidebar])
            );
        }

        return $this->logos[$sidebar];
    }

    protected function hasToggle($sidebar)
    {
        $config = app(Config::class);

        foreach (
            ['~theme.dialog.toggle' => '', '~theme.mobile.dialog.toggle' => '-mobile']
            as $key => $suffix
        ) {
            $position = explode(':', $config($key, ''), 2);
            if ($position[0] === $sidebar && is_active_sidebar("dialog{$suffix}")) {
                return true;
            }
        }
        return false;
    }

    protected function hasHeaderSearchOrSocial($sidebar)
    {
        $config = app(Config::class);

        return Arr::some(
            ['header.search', 'header.social', 'mobile.header.search', 'mobile.header.social'],
            fn($key) => str_starts_with($config("~theme.{$key}", ''), "{$sidebar}:")
        );
    }

    protected function getMenuWidgets($sidebar)
    {
        $widgets = wp_get_sidebars_widgets();
        return isset($widgets[$sidebar])
            ? Arr::filter($widgets[$sidebar], function ($widget) {
                global $wp_registered_widgets;
                return Arr::get($wp_registered_widgets, "{$widget}.classname") ===
                    'widget_nav_menu';
            })
            : [];
    }

    protected function createMenuWidget($sidebar, $location, $menu)
    {
        $config = app(Config::class)("~theme.menu.positions.{$location}", []);

        return $this->getWidget(
            $sidebar,
            'WP_Nav_Menu_Widget',
            [
                'theme_location' => $location,
                'nav_menu' => $menu,
                '_theme' => json_encode(
                    array_combine(
                        array_map(fn($key) => "menu_{$key}", array_keys($config)),
                        $config
                    )
                ),
            ],
            $location
        );
    }

    protected function getWidget($sidebar, $type, $instance = [], $sidebarSettings = null)
    {
        global $wp_widget_factory, $wp_registered_sidebars, $wp_registered_widgets;

        static $i = 1;

        $widget = $wp_widget_factory->widgets[$type];

        // workaround for Elementor overwriting $widget->number in plugins/elementor/includes/widgets/wordpress.php:184
        $number = is_numeric($widget->number) ? $widget->number : count($wp_registered_widgets);

        $widget->_set($number + $i++);

        $this->displayWidget(
            app(Config::class),
            $instance,
            $widget,
            $wp_registered_sidebars[$sidebarSettings ?? $sidebar]
        );

        return end($this->widgets[$sidebar]);
    }
}
