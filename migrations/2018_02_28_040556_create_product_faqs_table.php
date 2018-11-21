<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_faqs', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('product_id')->index(); //for references each product
            $table->integer('user_id')->nullabe()->index(); //avaialable for guest.
            $table->text('title');
            $table->text('description')->nullabe(); //answers
            $table->integer('helpful')->default(0)->nullabe()->index(); // count users toggle this functions (increment per user)
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
        Schema::dropIfExists('product_faqs');
    }
}
