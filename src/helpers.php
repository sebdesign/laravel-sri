<?php

use Sebdesign\SRI\Hasher;

if (! function_exists('elixir')) {
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param  string  $file
     * @param  string  $buildDirectory
     * @return string
     *
     * @throws \InvalidArgumentException
     *
     * @deprecated Use Laravel Mix instead.
     */
    function elixir($file, $buildDirectory = 'build')
    {
        static $manifest = [];
        static $manifestPath;

        if (empty($manifest) || $manifestPath !== $buildDirectory) {
            $path = public_path($buildDirectory.'/rev-manifest.json');

            if (file_exists($path)) {
                $manifest = json_decode(file_get_contents($path), true);
                $manifestPath = $buildDirectory;
            }
        }

        $file = ltrim($file, '/');

        if (isset($manifest[$file])) {
            return '/'.trim($buildDirectory.'/'.$manifest[$file], '/');
        }

        $unversioned = public_path($file);

        if (file_exists($unversioned)) {
            return '/'.trim($file, '/');
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}

if (! function_exists('integrity')) {
    /**
     * Get the integrity hash for a file.
     *
     * @param  string  $file
     * @param  array   $options
     * @return string
     * @throws \InvalidArgumentException
     */
    function integrity($file, array $options = [])
    {
        static $store;
        static $hashes = [];

        $options = array_replace_recursive(config('sri'), $options);
        $path = base_path($options['path']).DIRECTORY_SEPARATOR.$options['filename'];

        if (empty($hashes) || $store !== $path) {
            if (file_exists($path)) {
                $hashes = json_decode(file_get_contents($path), true);
                $store = $path;
            }
        }

        $file = public_path($file);

        return collect($hashes)
            ->flip()
            ->map('base_path')
            ->flip()
            ->get($file, function () use ($file, $options) {
                if (file_exists($file)) {
                    return app(Hasher::class)->make($file, $options);
                }

                throw new \InvalidArgumentException("File {$file} not defined in {$options['filename']}.");
            });
    }
}
