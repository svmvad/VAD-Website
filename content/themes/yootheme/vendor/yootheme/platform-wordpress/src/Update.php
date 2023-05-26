<?php

namespace YOOtheme\Wordpress;

class Update
{
    /**
     * @var array
     */
    public $themes = [];

    /**
     * @var array
     */
    public $plugins = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        foreach (['plugin', 'theme'] as $type) {
            $types = "{$type}s";

            // @link https://developer.wordpress.org/reference/hooks/site_transient_transient/
            add_filter("site_transient_update_{$types}", function ($transient) use ($types) {
                foreach ($this->{$types} as $update) {
                    $this->prepare($transient, $update);
                }

                return $transient;
            });

            // @link https://developer.wordpress.org/reference/hooks/pre_set_site_transient_transient/
            add_filter("pre_set_site_transient_update_{$types}", function ($transient) use (
                $types
            ) {
                foreach ($this->{$types} as $update) {
                    $this->check($transient, $update);
                }

                return $transient;
            });

            // @link https://developer.wordpress.org/reference/hooks/plugins_api/
            add_filter(
                "{$types}_api",
                function ($result, $action, $args) use ($type, $types) {
                    return $action == "{$type}_information" && isset($this->{$types}[$args->slug])
                        ? $this->fetchData($this->{$types}[$args->slug])
                        : false;
                },
                10,
                3
            );
        }
    }

    /**
     * Register plugin/theme update.
     *
     * @param string $name
     * @param string $type
     * @param string $remote
     * @param array  $options
     */
    public function register($name, $type, $remote, array $options = [])
    {
        $types = "{$type}s";
        $options = array_merge(compact('name', 'type', 'remote'), $options);

        if (empty($options['id'])) {
            $options['id'] = $type == 'plugin' ? "$name/$name.php" : $name;
        }

        $this->{$types}[$name] = $options;

        // check expiration
        if (
            isset($options['expiration']) &&
            ($transient = get_site_transient("update_{$types}")) &&
            isset($transient->response[$options['id']], $transient->last_checked) &&
            time() - $transient->last_checked > $options['expiration']
        ) {
            delete_site_transient("update_{$types}");
        }
    }

    /**
     * Prepare update data.
     *
     * @param mixed $transient
     * @param array $update
     */
    public function prepare($transient, array $update)
    {
        if (!isset($transient->response[$update['id']])) {
            return;
        }

        $data = (object) $transient->response[$update['id']];

        if (isset($update['key'])) {
            $data->package = add_query_arg(['key' => $update['key']], $data->package);
        }

        $transient->response[$update['id']] = $update['type'] == 'plugin' ? $data : (array) $data;
    }

    /**
     * Check if update is available.
     *
     * @param mixed $transient
     * @param array $update
     */
    public function check($transient, array $update)
    {
        $version = $transient->checked[$update['id']] ?? null;

        if (
            $version &&
            ($data = $this->fetchData($update)) &&
            version_compare($version, $data->version, '<')
        ) {
            $transient->response[$update['id']] =
                $update['type'] == 'plugin' ? $data : (array) $data;
        }
    }

    /**
     * Fetches the update data from remote server.
     *
     * @param array $update
     *
     * @return object|false
     */
    public function fetchData(array $update)
    {
        $url = parse_url($update['remote']);
        $remote = add_query_arg(['user-agent' => true], $update['remote']);

        if (
            ($response = wp_remote_retrieve_body(wp_remote_get($remote))) &&
            ($body = @json_decode($response))
        ) {
            if ($data = $this->latestVersion($update, $body->versions ?? [$body])) {
                $data->slug = $update['name'];
                $data->url = $data->url ?? "{$url['scheme']}://{$url['host']}";
                $data->sections = isset($data->sections) ? (array) $data->sections : [];
                $data->banners = isset($data->banners) ? (array) $data->banners : [];
                $data->new_version = $data->version;

                $type = $update['type'] ?? '';
                if (in_array($type, ['plugin', 'theme'])) {
                    $data->$type = $update['name'];
                }

                return $data;
            }
        }

        return false;
    }

    /**
     * Gets the latest version from version array.
     *
     * @param array $update
     * @param array $versions
     *
     * @return object|null
     */
    public function latestVersion(array $update, array $versions)
    {
        $stabilities = ['stable'];

        // add preferred stability
        if (isset($update['stability'])) {
            $stabilities[] = $update['stability'];
        }

        // sort versions, the newest first
        usort($versions, function ($a, $b) {
            return version_compare($a->version, $b->version) * -1;
        });

        // get the latest version with preferred stability
        foreach ($versions as $version) {
            if (
                isset($version->php_minimum) &&
                version_compare(PHP_VERSION, $version->php_minimum, '<')
            ) {
                continue;
            }

            if (empty($version->stability)) {
                $version->stability = 'stable';
            }

            if (in_array($version->stability, $stabilities, true)) {
                return $version;
            }
        }

        return null;
    }
}
