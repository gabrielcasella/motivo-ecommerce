<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// TODO Add security middleware

// Get the list of all products in the database
Route::get('products', 'API\ProductController@index');

// Add a new product and its attributes
Route::post('products', 'API\ProductController@store');

// Link a simple product with a configurable product
Route::post('products/link/{id}/{linked_product_id}', 'API\ProductController@link');

// Show a product based on the product id
Route::get('products/{id}', 'API\ProductController@show');

// Update a product and its attributes
Route::put('products/{id}', 'API\ProductController@update');

// Delete a product and its attributes
Route::delete('products/{id}', 'API\ProductController@destroy');
