<?php

namespace BenBjurstrom\Glint\Eloquent;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasImagesInterface
{
    public function images(): MorphMany;

    public function addImage(string $fileContents, string $fileName, string|ImageType $type): Image;

    public function addImageFromUrl(string $url, string|ImageType $type): Image;

    public function addImageFromDraft(string|ImageType $type): Image;
}
