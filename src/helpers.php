<?php

use Sebdesign\SRI\Hasher;

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
