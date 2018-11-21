<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChildSubCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_sub_categories', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('sub_category_id')->index();
            $table->integer('category_id')->nullable()->index();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->integer('status')->nullable()->default(1)->index(); //Active or hidden
            $table->string('file_name')->nullable()->index();
            $table->text('path')->nullable();
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
        Schema::dropIfExists('child_sub_categories');
    }
}
