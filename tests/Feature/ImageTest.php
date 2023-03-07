<?php

use BenBjurstrom\Glint\Eloquent\Image;
use BenBjurstrom\Glint\Eloquent\ImageType;
use BenBjurstrom\Glint\Tests\Support\TestModel;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| Relationships
|--------------------------------------------------------------------------
|
|
*/

it('retrieves the morphTo model', function () {
    $image = Image::factory()->create();

    $result = $image->model;
    expect($result)->toBeInstanceOf(TestModel::class);
});

/*
|--------------------------------------------------------------------------
| Scopes
|--------------------------------------------------------------------------
|
|
*/

it('filters images by type', function () {
    $type = ImageType::create([
        'id' => Uuid::uuid7(),
        'name' => 'avatar',
    ]);
    $image = Image::factory()->create([
        'type_id' => $type->id,
    ]);

    // extra image to make sure we're not getting all images
    Image::factory()->create();

    $result = Image::query()->typeFilter('avatar')->get();
    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toEqual($image->id);
});

/*
|--------------------------------------------------------------------------
| Accessors
|--------------------------------------------------------------------------
|
|
*/

/*
|--------------------------------------------------------------------------
| Mutators
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

it('deletes a cloudflare image when image record is deleted', function () {
    $image = Image::factory()->create();

    Event::fake();
    $image->delete();
    Event::assertDispatched('eloquent.deleted: BenBjurstrom\Glint\Eloquent\Image');

    $this->assertDatabaseMissing('images', [
        'id' => $image->id,
    ]);
});
