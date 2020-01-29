<?php

use Illuminate\Database\Seeder;

class MetadataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Start adding basic attributes
        $description_id = DB::table('attributes')->insertGetId([
            'name' => 'description',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        $price_id = DB::table('attributes')->insertGetId([
            'name' => 'price',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        $stock_id = DB::table('attributes')->insertGetId([
            'name' => 'stock',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        $status_id = DB::table('attributes')->insertGetId([
            'name' => 'status',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        $size_id = DB::table('attributes')->insertGetId([
            'name' => 'size',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        // Finish adding basic attributes

        // Start adding default metadata for the basic attributes
        DB::table('product_metadata')->insert([
            'attribute_id' => $description_id,
            'data_type' => 'text',
            'is_required' => true,
            'is_searchable' => false,
            'is_visible' => true,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('product_metadata')->insert([
            'attribute_id' => $price_id,
            'data_type' => 'decimal',
            'is_required' => false,
            'is_searchable' => true,
            'is_visible' => true,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('product_metadata')->insert([
            'attribute_id' => $stock_id,
            'data_type' => 'int',
            'is_required' => false,
            'is_searchable' => false,
            'is_visible' => true,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('product_metadata')->insert([
            'attribute_id' => $status_id,
            'data_type' => 'string',
            'is_required' => true,
            'is_searchable' => false,
            'is_visible' => true,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('product_metadata')->insert([
            'attribute_id' => $size_id,
            'data_type' => 'string',
            'is_required' => false,
            'is_searchable' => false,
            'is_visible' => true,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        // Finish adding default metadata for the basic attributes
    }
}
