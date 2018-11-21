<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('product_id')->index();
            $table->integer('product_variant_id')->nullable()->index();
            $table->string('file_name')->index();
            $table->text('path');
            $table->integer('page_preview')->default(0)->index(); //Feature products on the homepage (1 or 0 true or false)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_images');
    }
}
