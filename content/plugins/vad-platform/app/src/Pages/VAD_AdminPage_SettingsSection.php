<?php

namespace Netdust\VAD\Factories;


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class VAD_AdminPage_SettingsSection extends \Netdust\Loaders\Admin\Factories\AdminSection
{
    public function get_templates() {
        return [
            'admin-section' => [
                'override_visibility' => 'private',
            ],
            'admin-section-settingsbox'      => [
                'override_visibility' => 'private',
            ]
        ];
    }

    protected function get_template_group() {
        return 'admin/sections/settings';
    }


    protected function get_template_root_path() {
        return dirname(__DIR__, 2) . '/templates';
    }
}