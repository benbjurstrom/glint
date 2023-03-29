<?php

namespace BenBjurstrom\Glint\Eloquent;

use BenBjurstrom\CloudflareImages\CloudflareImages;
use BenBjurstrom\Glint\Glint;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasImages
{
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    |
    */

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'model');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    |
    */

    //

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    |
    |
    */

    protected static function booted(): void
    {
        static::deleting(function () {
            $this->removeAllImages();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Public Methods
    |--------------------------------------------------------------------------
    |
    |
    */

    public function addImage(string $fileContents, string $fileName, string|ImageType $type): Image
    {
        if (is_string($type)) {
            $type = ImageType::firstOrCreate(['name' => $type]);
        }

        $response = app(CloudflareImages::class)
            ->images()
            ->withMetadata(Glint::prepareMetadata($this, $type))
            ->create($fileContents, $fileName);

        return Glint::saveImage($this, $response->id, $type);
    }

    public function removeImage(string $id): void
    {
        app(CloudflareImages::class)
            ->images()
            ->delete($id);
    }

    public function removeAllImages(): void
    {
        $this->images()->each(function (Image $image) {
            $image->delete();
        });
    }

    public function addImageFromUrl(string $url, string|ImageType $type): Image
    {
        if (is_string($type)) {
            $type = ImageType::firstOrCreate(['name' => $type]);
        }

        $response = app(CloudflareImages::class)
            ->images()
            ->withMetadata(Glint::prepareMetadata($this, $type))
            ->createFromUrl($url);

        return Glint::saveImage($this, $response->id, $type);
    }

    public function addImageFromDraft(string|ImageType $type): Image
    {
        if (is_string($type)) {
            $type = ImageType::firstOrCreate(['name' => $type]);
        }

        $response = app(CloudflareImages::class)
            ->images()
            ->withMetadata(Glint::prepareMetadata($this, $type))
            ->getUploadUrl();

        return Glint::saveImage($this, $response->id, $type, $response->uploadUrl);
    }
}
