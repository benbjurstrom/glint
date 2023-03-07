<?php

namespace BenBjurstrom\Glint\Commands;

use BenBjurstrom\Glint\Eloquent\Image;
use BenBjurstrom\Glint\Glint;
use Illuminate\Console\Command;

class GlintBlurCommand extends Command
{
    public $signature = 'glint:blur';

    public $description = 'Adds blur hashes to image records where it\'s missing';

    public function handle(): int
    {
        $images = Image::query()->whereNull('blur_hash')->get();
        $this->info('Found '.$images->count().' images to blur');
        $images->each(function (Image $image) {
            $this->info('Blurring image: '.$image->id);
            $image->blur_hash = Glint::addBlur($image);
        });

        return self::SUCCESS;
    }
}
