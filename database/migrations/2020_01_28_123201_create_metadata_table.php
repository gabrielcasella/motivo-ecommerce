<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetadataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_metadata', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('attribute_id');
            $table->string('data_type', 100);
            $table->boolean('is_required');
            $table->boolean('is_searchable');
            $table->boolean('is_visible');
            $table->timestamps();
            $table->foreign('attribute_id')->references('id')->on('attributes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_metadata');
    }
}
