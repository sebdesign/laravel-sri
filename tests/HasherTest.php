<?php

namespace Sebdesign\SRI\Test;

use PHPUnit\Framework\Constraint\RegularExpression;
use Sebdesign\SRI\Hasher;

class HasherTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_information_about_a_given_integrity()
    {
        // arrange

        $this->app['config']->set('sri.delimiter', '_');

        $hasher = $this->app->make(Hasher::class);

        // act

        $info = $hasher->info('sha256-foo_sha384-bar');

        // assert

        $this->assertEquals(['sha256', 'sha384'], $info);
    }

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
     */
    public function it_does_not_accept_invalid_algorithms()
    {
        // arrange

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('sha1024');

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

        $this->assertThat($hash, new RegularExpression('/^sha256-.+ sha384-.+$/'));

        // act

        $hash = $hasher->make($css, ['algorithms' => ['sha384', 'sha512']]);

        // assert

        $this->assertThat($hash, new RegularExpression('/^sha384-.+ sha512-.+$/'));
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

        $this->assertThat($hash, new RegularExpression('/^sha256-.+_sha384-.+$/'));

        // act

        $hash = $hasher->make($css, ['delimiter' => ':']);

        // assert

        $this->assertThat($hash, new RegularExpression('/^sha256-.+:sha384-.+$/'));
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
