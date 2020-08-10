<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

}
