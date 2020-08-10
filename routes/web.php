<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'ProductController@index')->name('product');
Route::get('/{product}', 'ProductController@view')->name('product.view');
Route::post('/{product}', 'ProductImageController@store')->name('product.store_image');
Route::delete('/{product}', 'ProductImageController@destroy')->name('product.destroy_image');
