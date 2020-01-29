<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeIntsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_ints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('product_id');
            $table->bigInteger('value');
            $table->timestamps();
            $table->foreign('attribute_id')->references('id')->on('attributes');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unique(['attribute_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_ints');
    }
}
