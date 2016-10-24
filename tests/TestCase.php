<?php

namespace Sebdesign\SRI\Test;

use File;
use Orchestra\Testbench\TestCase as Orchestra;
use Sebdesign\SRI\SubresourceIntegrityServiceProvider;

abstract class TestCase extends Orchestra
{

    public function setUp()
    {
        parent::setUp();

        $this->setUpTempTestFiles();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SubresourceIntegrityServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory($this->getTempDirectory());

        $app->setBasePath($this->getTestFilesDirectory());

        $app->singleton('path.public', function () {
            return $this->getTestFilesDirectory();
        });

        $config = $app['files']->getRequire(__DIR__ . '/../config/sri.php');

        $app['config']->set('sri', $config);
    }

    protected function setUpTempTestFiles()
    {
        $this->initializeDirectory($this->getTestFilesDirectory());
        File::copyDirectory(__DIR__.'/testfiles', $this->getTestFilesDirectory());
    }

    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory);
    }

    public function getTempDirectory($suffix = '')
    {
        return __DIR__.'/temp'.($suffix == '' ? '' : '/'.$suffix);
    }

    public function getTestFilesDirectory($suffix = '')
    {
        return $this->getTempDirectory().'/testfiles'.($suffix == '' ? '' : '/'.$suffix);
    }

    public function getCss()
    {
        return $this->getTestFilesDirectory('app.css');
    }

    public function getJs()
    {
        return $this->getTestFilesDirectory('app.js');
    }
}
