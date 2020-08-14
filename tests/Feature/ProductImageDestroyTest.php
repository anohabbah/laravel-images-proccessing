<?php

namespace Tests\Feature;

use App\Asset;
use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\AssetTrait;
use Tests\Traits\ResponseTrait;

class ProductImageDestroyTest extends TestCase
{
    use RefreshDatabase, ResponseTrait, AssetTrait;

    /**
     * @var Product
     */
    protected Product $product;

    /** @test */
    public function validation_fails_with_missing_file_field(): void
    {
        self::assertCount(0, $this->product->assets);

        $response = $this->deleteJson(route('product.destroy_image', $this->product->id));

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])],
        ]);
    }

    /** @test */
    public function validation_fails_with_empty_file_field(): void
    {
        self::assertCount(0, $this->product->assets);

        $response = $this->deleteJson(route('product.destroy_image', $this->product->id), ['file' => '']);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])],
        ]);
    }

    /** @test */
    public function validation_fails_with_invalid_file_field(): void
    {
        self::assertCount(0, $this->product->assets);

        $response = $this->deleteJson(route('product.destroy_image', $this->product->id), ['file' => 1]);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.exists', ['attribute' => 'file'])],
        ]);
    }

    /** @test */
    public function it_removes_images(): void
    {
        $this->withoutExceptionHandling();
        Storage::fake();

        self::assertCount(0, $this->product->assets);

        $image1 = UploadedFile::fake()->image('image.jpg');
        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => $image1,
        ]);

        $this->product = $this->product->fresh('assets');

        self::assertCount(1, $this->product->assets);

        $this->assertResponseCreated($response);

        /** @var Asset $asset */
        $asset = $this->product->assets->first();

        Storage::assertExists($asset->path);
        collect($asset->variants)->each(function ($variant) {
            Storage::assertExists($variant['path']);
        });

        self::assertEquals(Storage::url($asset->path), $asset->url);

        $response = $this->deleteJson(route('product.destroy_image', $this->product->id), [
            'file' => $asset->id,
        ]);

        $this->assertResponseOkWithJson($response, ['success' => true]);
        $this->product = $this->product->fresh('assets');
        self::assertCount(0, $this->product->assets);
        self::assertCount(0, Asset::all());

        Storage::assertMissing($asset->path);
        collect($asset->variants)->each(function ($variant) {
            Storage::assertMissing($variant['path']);
        });
    }

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
}
