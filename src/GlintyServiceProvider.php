<?php

namespace BenBjurstrom\Glinty;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BenBjurstrom\Glinty\Commands\GlintyCommand;

class GlintyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('glinty')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_glinty_table')
            ->hasCommand(GlintyCommand::class);
    }
}
