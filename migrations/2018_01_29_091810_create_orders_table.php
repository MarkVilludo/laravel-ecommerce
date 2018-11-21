<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index(); //Customer
            $table->string('number')->nullable()->index();
            $table->string('descriptions')->nullable()->index();
            $table->decimal('sub_total',8,2)->nullable()->index();
            $table->decimal('total_amount',8,2)->nullable()->index();
            $table->decimal('grand_total',8,2)->nullable()->index();
            $table->decimal('balance',8,2)->nullable()->index();
            // $table->string('payment_status')->nullable()->index(); //
            $table->decimal('discount',8,2)->nullable()->index();
            $table->decimal('voucher_discount',8,2)->nullable()->index();
            $table->integer('status_id')->nullable()->default(0)->index(); //processing, delivered, shipping, complete or cancelled
            $table->decimal('shipping_fee',8,2)->nullable()->index();
            $table->decimal('promotions',8,2)->nullable()->index();
            // $table->integer('shipping_status')->nullable()->default(0)->index();
            $table->integer('customer_billing_address_id')->nullable()->index();
            $table->integer('customer_shipping_address_id')->nullable()->index();
            $table->integer('fulfill')->nullable()->default(0)->index(); // unfulfilled orders / fulfill (0 / 1)
            $table->text('remarks')->nullable();
            $table->text('reason_for_cancellation')->nullable();
            $table->dateTime('date_cancelled')->nullable(); //cancelled date

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
        Schema::dropIfExists('orders');
    }
}
