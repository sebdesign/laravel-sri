<?php

namespace Sebdesign\SRI;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class Hasher implements HasherContract
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $supportedAlgorithms = [];

    /**
     * Constructor.
     *
     * @param array $supportedAlgorithms
     * @param array $options
     */
    public function __construct(array $supportedAlgorithms, array $options)
    {
        $this->supportedAlgorithms = $supportedAlgorithms;
        $this->options = $options;
    }

    /**
     * Hash the given file.
     *
     * @param  string  $file
     * @param  array   $options
     * @return string
     */
    public function make($file, array $options = [])
    {
        $options = $this->getOptions($options);

        return collect($this->getAlgorithms($options))
            ->map(function ($algorithm) use ($file) {
                $digest = base64_encode(hash_file($algorithm, $file, true));

                return $algorithm.'-'.$digest;
            })
            ->implode($options['delimiter']);
    }

    /**
     * Check the given file against a hash.
     *
     * @param  string  $file
     * @param  string  $integrity
     * @param  array   $options
     * @return bool
     */
    public function check($file, $integrity, array $options = [])
    {
        return $this->make($file, $options) === $integrity;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $integrity
     * @param  array   $options
     * @return bool
     */
    public function needsRehash($integrity, array $options = [])
    {
        $options = $this->getOptions($options);

        $algorithms = collect(explode($options['delimiter'], $integrity))
            ->map(function ($hash) {
                return head(explode('-', $hash));
            });

        return $this->getAlgorithms($options) != $algorithms->all();
    }

    /**
     * Replace the given options with the defaults.
     *
     * @param  array  $options
     * @return array
     */
    protected function getOptions(array $options)
    {
        return array_replace_recursive($this->options, $options);
    }

    /**
     * Get the hashing algorithms from the options.
     *
     * @param  array  $options
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getAlgorithms(array $options)
    {
        if (!isset($options['algorithms'])
        || !is_array($options['algorithms'])
        || empty($options['algorithms'])) {
            throw new \InvalidArgumentException('No hashing algorithms are set.');
        }

        if ($notSupported = array_diff($options['algorithms'], $this->supportedAlgorithms)) {
            throw new \InvalidArgumentException(sprintf(
                "The hashing algorithms [%s] are not supported.",
                implode(', ', $notSupported)
            ));
        }

        return $options['algorithms'];
    }
}
