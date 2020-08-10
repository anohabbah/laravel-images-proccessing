<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;

use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function displays_product_list(): void
    {
        $products = factory(Product::class, 5)->create();

        $response = $this->get(route('product'));

        $response->assertStatus(Response::HTTP_OK);

        $products->each(function (Product $product) use ($response) {
            $response->assertSee(route('product.view', $product->id));
        });
    }

    /**
     * @test
     */
    public function displays_product_image_form(): void
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();

        $response = $this->get(route('product.view', $product->id));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($product->name);
    }
}
