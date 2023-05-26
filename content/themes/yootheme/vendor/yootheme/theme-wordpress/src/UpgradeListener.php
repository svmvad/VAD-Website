<?php

namespace YOOtheme\Theme\Wordpress;

use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Wordpress\Update;

class UpgradeListener
{
    public static function initUpdate(Config $config, Update $update)
    {
        $update->register(Path::basename('~theme'), 'theme', $config('theme.update'), [
            'key' => $config('~theme.yootheme_apikey'),
            'stability' => $config('~theme.minimum_stability'),
        ]);

        // @link https://developer.wordpress.org/reference/hooks/upgrader_pre_install/
        add_filter(
            'upgrader_pre_install',
            function ($return, $package) {
                if (!is_wp_error($return)) {
                    static::move($package);
                }

                return $return;
            },
            10,
            2
        );

        // @link https://developer.wordpress.org/reference/hooks/upgrader_post_install/
        add_filter(
            'upgrader_post_install',
            function ($return, $package) {
                if (!is_wp_error($return)) {
                    static::move($package, true);
                }

                return $return;
            },
            10,
            2
        );
    }

    public static function move($package, $reverse = false)
    {
        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;

        $themeDir = Path::get('~theme');
        $name = $package['theme'] ?? '';
        $content = $wp_filesystem->wp_content_dir();

        if ($name != basename($themeDir)) {
            return;
        }

        $paths = [$themeDir, "{$content}/upgrade"];

        [$source, $target] = $reverse ? array_reverse($paths) : $paths;

        $files = array_merge(glob("{$source}/fonts/*"), glob("{$source}/css/theme*.css"));

        foreach ($files as $file) {
            // skip theme.update.css
            if (strpos($file, 'update.css')) {
                continue;
            }

            $filename = ltrim(substr($file, strlen($source)), '\\/');
            $directory = dirname("{$target}/{$filename}");

            if (!$wp_filesystem->is_dir($directory)) {
                $wp_filesystem->mkdir($directory);
            }

            $wp_filesystem->move($file, "{$target}/{$filename}", true);
        }
    }

    /**
     * @link https://developer.wordpress.org/reference/hooks/wp_prepare_themes_for_js/
     */
    public static function disableAutoUpdate($prepared_themes)
    {
        $name = Path::basename('~theme');
        if (!empty($prepared_themes[$name]['autoupdate']['supported'])) {
            $prepared_themes[$name]['autoupdate']['supported'] = false;
        }

        return $prepared_themes;
    }
}
