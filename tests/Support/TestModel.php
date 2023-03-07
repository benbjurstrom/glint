<?php

namespace BenBjurstrom\Glint\Tests\Support;

use BenBjurstrom\Glint\Eloquent\HasImages;
use BenBjurstrom\Glint\Eloquent\HasImagesInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model implements HasImagesInterface
{
    use HasImages, HasUuids;

    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}
