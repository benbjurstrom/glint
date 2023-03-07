# A Cloudflare Images library for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/benbjurstrom/glint.svg?style=flat-square)](https://packagist.org/packages/benbjurstrom/glint)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/glint/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/benbjurstrom/glint/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/benbjurstrom/glint/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/benbjurstrom/glint/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)


## ️⚠️ WARNING: This package is still in development and not ready for production use. ⚠️
For example, currently there's no option to use a custom model in place of the package's Image and ImageType models. I'll add that functionality once my implementation is stable.

## Installation

Install the package via composer:

```bash
composer require benbjurstrom/glint
```

Add cloudflare credentials to your services config file:
```bash
// config/services.php
'cloudflare' => [
    'account_hash' => env('CLOUDFLARE_IMAGES_ACCOUNT_HASH'),
    'account_id' => env('CLOUDFLARE_IMAGES_ACCOUNT_ID'),
    'api_token' => env('CLOUDFLARE_IMAGES_API_TOKEN'),
    'signing_key' => env('CLOUDFLARE_IMAGES_SIGNING_KEY'),
],
```
Then publish and run the migrations with:

```bash
php artisan vendor:publish --tag="glint-migrations"
php artisan migrate
```

## Usage


### Client upload

Since there's so many ways to handle authentication, authorization, and response formatting this is left to the user to implement. But an example controller might look something like this:
```php
    public function store(Request $request)
    {
        Gate::authorize('uploadImages', [
            $request->user()
        ]);

        $data = $request->validate([
            'type_id' => 'required|uuid|exists:image_types,id',
            'model_id' => 'required|uuid',
            'model_type' => 'required',
        ]);

        $modelName = Relation::getMorphedModel($data['model_type']) ?? $data['model_type'];
        $model = (new $modelName)->findOrFail($data['model_id']);
        throw_unless($model instanceof HasImagesInterface, 
            \Exception::class, 'Model does not implement HasImagesInterface'
        );

        $type = ImageType::findOrFail($data['type_id']);
        $image = $model->addImageFromDraft($type);

        return response()->json($image);
    }
```

## Credits

- [Ben Bjurstrom](https://github.com/benbjurstrom)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
