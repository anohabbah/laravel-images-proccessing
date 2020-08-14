<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetCollection;
use App\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display records.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('product.index')
            ->with('products', Product::all());
    }

    /**
     * View record.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\View\View
     */
    public function view(Product $product): View
    {
        return view('product.product')
            ->with('product', $product)
            ->with('assets', AssetCollection::make($product->assets)->collection);
    }
}
