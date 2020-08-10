<?php

namespace Tests\Feature;

use App\Asset;
use App\Assets\Type\Image;
use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\ResponseTrait;

class ProductImageStoreTest extends TestCase
{
    use RefreshDatabase, ResponseTrait;

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
            ]
        ]);

        $this->product = factory(Product::class)->create();

    }

    /** @test */
    public function validation_fails_with_empty_request(): void {
        $response = $this->postJson(route('product.store_image', $this->product->id));

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])]
        ]);
    }

    /** @test */
    public function validation_fails_with_empty_value(): void {
        $response = $this->postJson(route('product.store_image', $this->product->id), ['file' => '']);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])]
        ]);
    }

    /** @test */
    public function validation_fails_with_non_image_file_type(): void {
        Storage::fake();

        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => UploadedFile::fake()->create('file.pdf', 1000)
        ]);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.image', ['attribute' => 'file'])]
        ]);

        $this->assertCount(0, Asset::all());
        $this->assertCount(0, Storage::allFiles(Image::directory()));
    }

    /** @test */
    public function validation_fails_with_max_size_exceeded(): void {
        Storage::fake();

        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => UploadedFile::fake()->create('file.jpg', 1001, 'image/jpeg')
        ]);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.max.file', ['attribute' => 'file', 'max' => 1000])]
        ]);

        $this->assertCount(0, Asset::all());
        $this->assertCount(0, Storage::allFiles(Image::directory()));
    }
}
