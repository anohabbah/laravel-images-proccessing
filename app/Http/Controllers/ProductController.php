<?php

namespace App\Http\Controllers;

use App\Product;

use Illuminate\View\View;
use Illuminate\Support\Collection;

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
            ->with('assets', new Collection);
    }
}
