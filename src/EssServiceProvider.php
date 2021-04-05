<?php

namespace Larapkg\Ejuess;

use Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class EssServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('ess', function ($app, $config) {
            $client = new EssClient(
                $config['key'],
                $config['secret'],
                $config['bucket'],
                $config['url']
            );

            return new Filesystem(new EssAdapter($client));
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}