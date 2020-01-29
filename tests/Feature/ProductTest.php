<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test adding a single product and retrieving it.
     *
     * @return void
     */
    public function testSingleProduct()
    {
        $this->seed();
        $this->withoutMiddleware();
        $this->json('POST', '/api/products',
            [
                'title' => 'Product 1',
                'sku' => 'sku0001',
                'type' => 'simple',
                'attributes' => [
                    'description' => 'This is a test object',
                    'price' => 2,
                    'stock' => 3,
                    'status' => 'active'
                ]
            ])
            ->assertJson([
                'sku' => 'sku0001',
            ]);

        $this->json('GET', '/api/products/1')
            ->assertJson([
                'attributes' => [
                    [
                        'name' => 'description',
                        'value' => 'This is a test object'
                    ],
                    [
                        'name' => 'price',
                        'value' => 2
                    ],
                    [
                        'name' => 'stock',
                        'value' => 3
                    ],[
                    'name' => 'status',
                        'value' => 'active'
                    ],
                ],
            ]);
    }

    /**
     * Test adding a configurable product, for this let's add 2 simple products, a configurable product, then linking
     * them together and retrieving the result
     */
    public function testConfigurableProduct()
    {
        $this->seed();
        $this->withoutMiddleware();
        $this->json('POST', '/api/products',
            [
                'title' => 'Simple Product 1',
                'sku' => 'sku00S1',
                'type' => 'simple',
                'attributes' => [
                    'description' => 'This is a simple test object',
                    'price' => 2,
                    'stock' => 30,
                    'size'  => 'M',
                    'status' => 'active'
                ]
            ])
            ->assertJson([
                'sku' => 'sku00S1',
            ]);

        $this->json('POST', '/api/products',
            [
                'title' => 'Simple Product 2',
                'sku' => 'sku00S2',
                'type' => 'simple',
                'attributes' => [
                    'description' => 'This is a simple test object',
                    'price' => 3,
                    'stock' => 10,
                    'size'  => 'L',
                    'status' => 'active'
                ]
            ])
            ->assertJson([
                'sku' => 'sku00S2',
            ]);

        $this->json('POST', '/api/products',
            [
                'title' => 'Configurable Product',
                'sku' => 'sku00C1',
                'type' => 'configurable',
                'attributes' => [
                    'description' => 'This is a configurable test object',
                    'status' => 'active'
                ]
            ])
            ->assertJson([
                'sku' => 'sku00C1',
            ]);

        $this->json('POST', '/api/products/link/3/1')
            ->assertJson([
                'product_id' => 3,
                'linked_product_id' => 1
            ]);

        $this->json('POST', '/api/products/link/3/2')
                ->assertJson([
                'product_id' => 3,
                'linked_product_id' => 2
            ]);

        $this->json('GET', '/api/products/3')
            ->assertJson([
                'sku' => 'sku00C1',
                'linked_products' => [
                    [
                        'sku' => 'sku00S1'
                    ],
                    [
                        'sku' => 'sku00S2'
                    ]
                ]
            ]);
    }

    /**
     * Test updating a product and its attributes and assert that the new values are retrieved.
     */
    public function testUpdateSingleProduct()
    {
        $this->seed();
        $this->withoutMiddleware();
        $this->json('POST', '/api/products',
            [
                'title' => 'Product 1',
                'sku' => 'sku0001',
                'type' => 'simple',
                'attributes' => [
                    'description' => 'This is a test object',
                    'price' => 2,
                    'stock' => 3,
                    'status' => 'active'
                ]
            ])
            ->assertJson([
                'sku' => 'sku0001',
            ]);

        $this->json('PUT', '/api/products/1',
            [
                'title' => 'New Product 1',
                'sku' => 'sku0011',
                'type' => 'simple',
                'attributes' => [
                    'description' => 'This is a new test object',
                    'price' => 20,
                    'stock' => 1,
                    'status' => 'active'
                ]
            ])
            ->assertJson([
                'title' => 'New Product 1',
                'sku' => 'sku0011',
                'type' => 'simple',
                'attributes' => [
                    [
                        'name' => 'description',
                        'value' => 'This is a new test object'
                    ],
                    [
                        'name' => 'price',
                        'value' => 20
                    ],
                    [
                        'name' => 'stock',
                        'value' => 1
                    ],[
                        'name' => 'status',
                        'value' => 'active'
                    ],
                ],
            ]);
    }

    /**
     * Test deleting products and it's attributes.
     */
    public function testDestroyProduct()
    {
        $this->seed();
        $this->withoutMiddleware();
        $this->json('POST', '/api/products',
            [
                'title' => 'Product 1',
                'sku' => 'sku0001',
                'type' => 'simple',
                'attributes' => [
                    'description' => 'This is a test object',
                    'price' => 2,
                    'stock' => 3,
                    'status' => 'active'
                ]
            ])
            ->assertJson([
                'sku' => 'sku0001',
            ]);

        $this->json('DELETE', '/api/products/1')
            ->assertNoContent();

        $this->json('GET', '/api/products/1')
            ->assertStatus(404);
    }
}
