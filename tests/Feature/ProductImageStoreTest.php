<?php

namespace Tests\Feature;

use App\Asset;
use App\Assets\Type\Image;
use App\Product;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\AssetTrait;
use Tests\Traits\ResponseTrait;

class ProductImageStoreTest extends TestCase
{
    use RefreshDatabase, ResponseTrait, AssetTrait;

    /**
     * @var Product
     */
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'filesystems' => [
                'default' => 'public',
                'max_size' => 1000,
            ],
        ]);

        $this->product = factory(Product::class)->create();
    }

    /** @test */
    public function validation_fails_with_empty_request(): void
    {
        $response = $this->postJson(route('product.store_image', $this->product->id));

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])],
        ]);
    }

    /** @test */
    public function validation_fails_with_empty_value(): void
    {
        $response = $this->postJson(route('product.store_image', $this->product->id), ['file' => '']);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])],
        ]);
    }

    /** @test */
    public function validation_fails_with_non_image_file_type(): void
    {
        Storage::fake();

        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => UploadedFile::fake()->create('file.pdf', 1000),
        ]);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.image', ['attribute' => 'file'])],
        ]);

        $this->assertCount(0, Asset::all());
        $this->assertCount(0, Storage::allFiles(Image::directory()));
    }

    /** @test */
    public function validation_fails_with_max_size_exceeded(): void
    {
        Storage::fake();

        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => UploadedFile::fake()->create('file.jpg', 1001, 'image/jpeg'),
        ]);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.max.file', ['attribute' => 'file', 'max' => 1000])],
        ]);

        $this->assertCount(0, Asset::all());
        $this->assertCount(0, Storage::allFiles(Image::directory()));
    }

    /** @test */
    public function upload_image_and_save_assets(): void
    {
        $this->withoutExceptionHandling();
        Storage::fake();

        $image1 = UploadedFile::fake()->image('image.jpg');
        $image2 = UploadedFile::fake()->image('image-2.png');

        $this->assertCount(0, $this->product->assets);

        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => $image1,
        ]);
        $this->product = $this->product->fresh('assets');

        $this->assertCount(1, $this->product->assets);

        $firstAsset = $this->product->assets->first();

        $this->assertResponseCreatedWithJson($response, [
            'id' => 1,
            'name' => 'image.jpg',
            'extension' => 'jpeg',
            'url' => $firstAsset->url,
            'variants' => $firstAsset->variants,
            'preload' => $this->preload($firstAsset),
        ]);

        Storage::assertExists($firstAsset->path);

        $this->assertEquals(Storage::url($firstAsset->path), $firstAsset->url);

        $this->assertDatabaseHas('assets', [
            'id' => 1,
            'assetable_id' => $this->product->id,
            'assetable_type' => Product::class,
            'type' => Image::class,
            'disk' => 'public',
            'visibility' => Filesystem::VISIBILITY_PUBLIC,
            'sort' => 1,
            'path' => $firstAsset->path,
            'original_name' => 'image.jpg',
            'mime' => 'image/jpeg',
            'size' => $firstAsset->size,
            'caption' => 'image.jpg',
            'variants' => ($firstVariants = $this->variants($firstAsset))->toJson(),
        ]);

        $firstVariants->each(function ($variant) {
            Storage::assertExists($variant['path']);
        });

        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => $image2,
        ]);
        $this->product = $this->product->fresh('assets');

        $this->assertCount(2, $this->product->assets);

        $secondAsset = $this->product->assets->sortByDesc('sort')->first();

        $this->assertResponseCreatedWithJson($response, [
            'id' => 2,
            'name' => 'image-2.png',
            'extension' => 'png',
            'url' => $secondAsset->url,
            'variants' => $secondAsset->variants,
            'preload' => $this->preload($secondAsset),
        ]);

        Storage::assertExists($firstAsset->path);
        Storage::assertExists($secondAsset->path);

        $this->assertNotEquals($secondAsset->url, $firstAsset->url);
        $this->assertEquals(Storage::url($secondAsset->path), $secondAsset->url);

        $this->assertDatabaseHas('assets', [
            'id' => 2,
            'assetable_id' => $this->product->id,
            'assetable_type' => Product::class,
            'type' => Image::class,
            'disk' => 'public',
            'visibility' => Filesystem::VISIBILITY_PUBLIC,
            'sort' => 2,
            'path' => $secondAsset->path,
            'original_name' => 'image-2.png',
            'mime' => 'image/png',
            'size' => $secondAsset->size,
            'caption' => 'image-2.png',
            'variants' => ($secondVariants = $this->variants($secondAsset))->toJson(),
        ]);

        $allVariants = $firstVariants->concat($secondVariants);
        self::assertCount(8, $allVariants);

        $secondVariants->each(function ($variant) {
            Storage::assertExists($variant['path']);
        });
    }
}
