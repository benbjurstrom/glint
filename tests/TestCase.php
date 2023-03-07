<?php

namespace BenBjurstrom\Glint\Tests;

use BenBjurstrom\Glint\Eloquent\ImageType;
use BenBjurstrom\Glint\GlintServiceProvider;
use BenBjurstrom\Glint\Tests\Support\TestModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Ramsey\Uuid\Uuid;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'BenBjurstrom\\Glint\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            GlintServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('services.cloudflare.account_hash', env('CLOUDFLARE_IMAGES_ACCOUNT_HASH'));
        config()->set('services.cloudflare.account_id', env('CLOUDFLARE_IMAGES_ACCOUNT_ID'));
        config()->set('services.cloudflare.api_token', env('CLOUDFLARE_IMAGES_API_TOKEN'));
        config()->set('services.cloudflare.signing_key', env('CLOUDFLARE_IMAGES_SIGNING_KEY'));

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * @param  Application  $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('width')->nullable();
            $table->softDeletes();
        });

        TestModel::create([
            'id' => Uuid::uuid7(),
            'name' => 'test',
        ]);

        $migration = include __DIR__.'/../database/migrations/create_image_types_table.php.stub';
        $migration->up();

        ImageType::create([
            'id' => Uuid::uuid7(),
            'name' => 'default',
        ]);

        $migration = include __DIR__.'/../database/migrations/create_images_table.php.stub';
        $migration->up();
    }
}
