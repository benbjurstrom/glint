<?php

namespace BenBjurstrom\Glint;

use BenBjurstrom\Glint\Eloquent\Image;
use BenBjurstrom\Glint\Eloquent\ImageType;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class Glint
{
    public static function prepareMetadata(Model $model, ImageType $type): array
    {
        return [
            'application' => config('app.name'),
            'environment' => app()->environment(),
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getKey(),
            'type_id' => $type->id,
        ];
    }

    public static function saveImage(Model $model, string $cloudflareId, ImageType $type, bool $is_draft = false): Image
    {
        $image = Image::create([
            'id' => Uuid::uuid7(),
            'cloudflare_id' => $cloudflareId,
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getKey(),
            'type_id' => $type->id,
            'blur_hash' => null,
            'is_draft' => $is_draft,
        ]);

        self::addBlur($image);

        return $image;
    }

    public static function addBlur(Image $image): Image
    {
        if ($image->is_draft) {
            return $image;
        }

        $accountId = config('services.cloudflare.account_hash');
        $url = sprintf('https://imagedelivery.net/%s/%s/blur4x3', $accountId, $image->cloudflare_id);
        $fileContents = file_get_contents($url);

        if (! $fileContents) {
            Log::error('Could not get file contents from '.$url);

            return $image;
        }

        try {
            $image->blur_hash = Blur::getHashFromString($fileContents);
            $image->save();
        } catch(Exception $e) {
            Log::error($e->getMessage());
        }

        return $image;
    }
}
