<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('order_id')->index();
            $table->string('item')->index();
            $table->integer('status_id')->nullable()->default(1)->index(); //processing, delivered, shipping, complete or cancelled //default processing
            $table->integer('shipping_days')->nullable()->default(5); //processing, delivered, shipping, complete or cancelled //default processing
            $table->text('remarks')->nullable(); //remarks for replacement or cancell
            $table->dateTime('date_replaced')->nullable(); //replacement date
            $table->integer('product_id')->nullable()->index();
            $table->integer('package_id')->nullable()->index();
            $table->integer('variant_id')->nullable()->index();
            $table->boolean('is_replacement')->default(false)->nullable();
            $table->decimal('regular_price',8,2)->index();
            $table->decimal('selling_price',8,2)->index();
            $table->decimal('discount',8,2)->index();
            $table->integer('quantity')->index();
            
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
        Schema::dropIfExists('order_items');
    }
}
