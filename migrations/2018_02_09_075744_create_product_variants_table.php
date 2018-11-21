<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('product_id')->index();
            $table->string('size')->nullable()->index();
            $table->string('grams')->nullable()->index();
            $table->string('barcode')->nullable()->index();
            $table->decimal('weight',8,2)->nullable()->index();
            $table->string('weight_unit')->nullable()->index();
            $table->string('sku')->nullable()->index();
            $table->integer('inventory')->index();
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
        Schema::dropIfExists('product_variants');
    }
}
