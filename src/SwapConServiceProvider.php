<?php

namespace CodeDistortion\SwapCon;

use Dotenv\Exception\InvalidPathException;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * SwapCon ServiceProvider for Laravel.
 */
class SwapConServiceProvider extends BaseServiceProvider
{
    /**
     * Service-provider register method.
     *
     * @return void
     */
    public function register() // PHP7.1 ): void
    {
        $this->app->bind('SwapCon', function () {
            return new SwapCon();
        });
    }

    /**
     * Service-provider boot method.
     *
     * @return void
     */
    public function boot() // PHP7.1 ): void
    {
        $this->initialiseConfig();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'SwapCon',
        ];
    }



    /**
     * Initialise the config settings/file.
     *
     * @return void
     */
    protected function initialiseConfig() // PHP7.1 ): void
    {
        // initialise the config
        $configPath = __DIR__.'/../config/config.php';
        try {
            $this->mergeConfigFrom($configPath, 'code-distortion.swapcon');
        } catch (InvalidPathException $e) {
        }

        // allow the default config to be published
        if ((!$this->app->environment('testing'))
        && ($this->app->runningInConsole())) {

            $this->publishes(
                [$configPath => config_path('code-distortion.swapcon.php'),],
                'config'
            );
        }
    }
}
