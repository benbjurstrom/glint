<?php

namespace BenBjurstrom\Glint\Commands;

use BenBjurstrom\CloudflareImages\CloudflareImages;
use BenBjurstrom\Glint\Eloquent\Image;
use Illuminate\Console\Command;

class GlintCleanCommand extends Command
{
    public $signature = 'glint:clean {environment}';

    public $description = 'Checks cloudflare for unused images and deletes them';

    public function handle(): int
    {
        // 1. Check for any images that are in the database but not in cloudflare
        // a.
        // 2. CHeck for any images that are in cloudflare but not in the database
        // a. For each cloudflare result, if the Application Name and Environment Match
        // b. Check the database for that image id.
        // c. If doesn't exist then delete the cloudflare iamge.

        // Get the list of cloudflare images
        $response = app(CloudflareImages::class)
            ->images()
            ->list();

        $this->info('Found '.count($response->images).' images in cloudflare');

        // Loop through and if it exists in the database update the verified at field
        foreach ($response->images as $image) {
            $image = Image::query()->where('cloudflare_id', $image->id)->first();
            if ($image) {
                $this->info('Found image in database: '.$image->id);
                $image->verified_at = now();
                $image->save();
            }
        }

        return self::SUCCESS;
    }
}
