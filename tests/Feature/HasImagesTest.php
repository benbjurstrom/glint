<?php

use BenBjurstrom\CloudflareImages\CloudflareImages;
use BenBjurstrom\CloudflareImages\Requests\PostUploadUrl;
use BenBjurstrom\Glint\Eloquent\Image;
use BenBjurstrom\Glint\Tests\Support\TestModel;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('retrieves an image', function () {
    $model = TestModel::firstOrFail();
    $image = Image::factory()->create(
        [
            'model_type' => TestModel::class,
            'model_id' => $model->id,
        ]
    );

    $result = $model->images->first();
    expect($result)->toBeInstanceOf(Image::class);
});

it('creates a draft image', function () {
    $mockClient = new MockClient([
        PostUploadUrl::class => MockResponse::fixture('postUploadUrl'),
    ]);

    $api = app(CloudflareImages::class);
    $api->withMockClient($mockClient);
    $this->app->bind(CloudflareImages::class, fn () => $api);

    $model = TestModel::firstOrFail();
    $image = $model->addImageFromDraft('default');

    expect($image->is_draft)->toBeTrue();
});
