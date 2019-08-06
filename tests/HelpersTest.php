<?php

namespace Sebdesign\SRI\Test;

use Sebdesign\SRI\Hasher;

class HelpersTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_the_hash()
    {
        $hasher = $this->app->make(Hasher::class);

        $integrity = integrity('app.css');

        $this->assertTrue($hasher->check($this->getCss(), $integrity));
    }

    /**
     * @test
     */
    public function it_generates_the_hash_if_it_does_not_exist()
    {
        $hasher = $this->app->make(Hasher::class);

        $integrity = integrity('app.js');

        $this->assertTrue($hasher->check($this->getJs(), $integrity));
    }

    /**
     * @test
     */
    public function it_accepts_options()
    {
        $hasher = $this->app->make(Hasher::class);

        rename(
            $this->getTestFilesDirectory('sri.json'),
            $this->getTestFilesDirectory('integrity.json')
        );

        $integrity = integrity('app.css', ['filename' => 'integrity.json']);

        $this->assertTrue($hasher->check($this->getCss(), $integrity));
    }

    /**
     * @test
     */
    public function it_fails_if_a_file_does_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $hasher = $this->app->make(Hasher::class);

        integrity('app.min.css');
    }
}
