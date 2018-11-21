<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('name')->index();
            $table->string('code')->nullable()->index();
            $table->string('description')->nullable()->index();
            $table->integer('sub_category_id')->nullable()->index();
            $table->integer('child_sub_category_id')->nullable()->index();
            $table->string('warranty',64)->nullable()->index();
            $table->string('warranty_type',64)->nullable()->index();
            $table->integer('status')->index(); //for product availability Active or hidden store
            $table->decimal('price', 8,2)->index();
            $table->string('file_name', 191)->nullable()->index();
            $table->text('path')->nullable();

            $table->integer('updated_by')->nullable()->index(); //track users modified data
            
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
        Schema::dropIfExists('packages');
    }
}
