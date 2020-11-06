<?php

namespace App\Providers;

use App\Services\CustomEntrust;
use Zizaco\Entrust\EntrustServiceProvider;
use Zizaco\Entrust\MigrationCommand;

class CustomEntrustServiceProvider extends EntrustServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEntrust();

        $this->registerCommands();

        $this->mergeConfig();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerEntrust()
    {
        $this->app->bind('entrust', function ($app) {
            return new CustomEntrust($app);
        });

        $this->app->alias('entrust', CustomEntrust::class);
    }


    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->singleton('command.entrust.migration', function ($app) {
            return new MigrationCommand();
        });
    }

    /**
     * Merges user's and entrust's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            base_path('vendor/zizaco/entrust/src/config') . '/config.php', 'entrust'
        );
    }
}
