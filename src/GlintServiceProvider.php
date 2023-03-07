<?php

namespace BenBjurstrom\Glint;

use BenBjurstrom\CloudflareImages\CloudflareImages;
use BenBjurstrom\Glint\Commands\GlintBlurCommand;
use BenBjurstrom\Glint\Commands\GlintCleanCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GlintServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->bind(CloudflareImages::class, function () {
            return new CloudflareImages(
                apiToken: config('services.cloudflare.api_token'),
                accountId: config('services.cloudflare.account_id'),
                signingKey: config('services.cloudflare.signing_key'),
            );
        });
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('glint')
            ->hasMigrations('create_image_types_table', 'create_images_table')
            ->runsMigrations()
            ->hasCommands(GlintCleanCommand::class, GlintBlurCommand::class);
    }
}
