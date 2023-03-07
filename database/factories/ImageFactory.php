<?php

namespace BenBjurstrom\Glint\Database\Factories;

use BenBjurstrom\Glint\Eloquent\Image;
use BenBjurstrom\Glint\Eloquent\ImageType;
use BenBjurstrom\Glint\Tests\Support\TestModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'cloudflare_id' => $this->faker->uuid,
            'model_type' => TestModel::class,
            'model_id' => TestModel::firstOrFail()->id,
            'type_id' => ImageType::firstOrCreate([
                'name' => 'default',
            ])->id,
        ];
    }
}
