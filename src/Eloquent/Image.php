<?php

namespace BenBjurstrom\Glint\Eloquent;

use BenBjurstrom\CloudflareImages\CloudflareImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property bool $is_draft
 * @property string $cloudflare_id
 * @property ?string $blur_hash
 * @property string $model_id
 * @property string $model_type
 * @property string $type_id
 * @property string $verified_at
 * @property ?string $upload_url
 * @property \Carbon\Carbon $created_at
 */
class Image extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'images';

    protected $guarded = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array<string>
     */
    protected $with = ['type'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    |
    */

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ImageType::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    |
    */

    public function scopeTypeFilter(Builder $query, null|string|ImageType $type = null): Builder
    {
        if (is_string($type)) {
            $query->whereHas('type', function ($query) use ($type) {
                return $query->where('name', $type);
            });
        }

        if ($type instanceof ImageType) {
            $query->where('type_id', $type->id);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    |
    |
    */

    protected function uploadUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (isset($attributes['is_draft']) && $attributes['is_draft'] === false) {
                    return null;
                }

                $cloudflareId = $attributes['cloudflare_id'];
                $accountId = config('services.cloudflare.account_hash');

                return sprintf('https://upload.imagedelivery.net/%s/%s', $accountId, $cloudflareId);
            },
        );
    }

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

    /*
     * NOTE: When issuing a mass delete query via Eloquent, the saved,
     * updated, deleting, and deleted model events will not be dispatched for
     * the affected models. This is because the models are never actually
     * retrieved when performing mass updates or deletes.
     */
    protected static function booted(): void
    {
        static::deleting(function (Image $image) {
            app(CloudflareImages::class)
                ->images()
                ->delete($image->cloudflare_id);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Public Methods
    |--------------------------------------------------------------------------
    |
    |
    */

    public function updateDraft(): Image
    {
        if (! $this->is_draft) {
            return $this;
        }

        $response = app(CloudflareImages::class)
            ->images()
            ->get($this->id);

        if ($response->isDraft) {
            return $this;
        }

        $this->is_draft = false;
        $this->save();

        return $this;
    }
}
