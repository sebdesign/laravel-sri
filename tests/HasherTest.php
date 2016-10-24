<?php

namespace Sebdesign\SRI\Test;

use Sebdesign\SRI\Hasher;

class HasherTest extends TestCase
{
    /**
     * @test
     */
    public function it_hashes_a_file()
    {
        // arrange

        $css = $this->getCss();
        $hasher = $this->app->make(Hasher::class);

        // act

        $hash = $hasher->make($css);

        // assert

        $this->assertStringStartsWith('sha256-', $hash);
    }

    /**
     * @test
     */
    public function it_accepts_different_algorithms()
    {
        // arrange

        $css = $this->getCss();
        $this->app['config']->set('sri.algorithms', ['sha384']);
        $hasher = $this->app->make(Hasher::class);

        // act

        $hash = $hasher->make($css);

        // assert

        $this->assertStringStartsWith('sha384-', $hash);

        // act

        $hash = $hasher->make($css, ['algorithms' => ['sha512']]);

        // assert

        $this->assertStringStartsWith('sha512-', $hash);
    }

    /**
     * @test
     * @expectedException         \InvalidArgumentException
     * @expectedExceptionMessage  sha1024
     */
    public function it_does_not_accept_invalid_algorithms()
    {
        // arrange

        $css = $this->getCss();
        $this->app['config']->set('sri.algorithms', ['sha512', 'sha1024']);
        $hasher = $this->app->make(Hasher::class);

        // act

        $hasher->make($css);
    }

    /**
     * @test
     */
    public function it_accepts_multiple_algorithms()
    {
        // arrange

        $css = $this->getCss();
        $this->app['config']->set('sri.algorithms', ['sha256', 'sha384']);
        $hasher = $this->app->make(Hasher::class);

        // act

        $hash = $hasher->make($css);

        // assert

        $this->assertRegExp('/^sha256-.+ sha384-.+$/', $hash);

        // act

        $hash = $hasher->make($css, ['algorithms' => ['sha384', 'sha512']]);

        // assert

        $this->assertRegExp('/^sha384-.+ sha512-.+$/', $hash);
    }

    /**
     * @test
     */
    public function it_accepts_a_different_delimiter()
    {
        // arrange

        $css = $this->getCss();
        $this->app['config']->set('sri.algorithms', ['sha256', 'sha384']);
        $this->app['config']->set('sri.delimiter', '_');
        $hasher = $this->app->make(Hasher::class);

        // act

        $hash = $hasher->make($css);

        // assert

        $this->assertRegExp('/^sha256-.+_sha384-.+$/', $hash);

        // act

        $hash = $hasher->make($css, ['delimiter' => ':']);

        // assert

        $this->assertRegExp('/^sha256-.+:sha384-.+$/', $hash);
    }

    /**
     * @test
     */
    public function it_checks_a_hash()
    {
        // arrange

        $css = $this->getCss();
        $hasher = $this->app->make(Hasher::class);

        // act

        $hash = $hasher->make($css);

        // assert

        $this->assertTrue($hasher->check($css, $hash));
        $this->assertFalse($hasher->check($css, $hash, ['algorithms' => ['sha512']]));
    }

    /**
     * @test
     */
    public function it_checks_if_the_hash_needs_rehash()
    {
        // arrange

        $css = $this->getCss();
        $hasher = $this->app->make(Hasher::class);

        // act

        $hash = $hasher->make($css, ['algorithms' => ['sha256', 'sha384']]);

        // assert

        $this->assertFalse($hasher->needsRehash($hash, [
            'algorithms' => ['sha256', 'sha384'],
        ]));

        $this->assertTrue($hasher->needsRehash($hash, [
            'algorithms' => ['sha384'],
        ]));

        $this->assertTrue($hasher->needsRehash($hash, [
            'algorithms' => ['sha256', 'sha384'],
            'delimiter' => ':',
        ]));
    }
}
