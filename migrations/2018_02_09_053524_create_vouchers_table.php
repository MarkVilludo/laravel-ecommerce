<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            // The voucher code
            $table->string('code')->index();

            // The human readable voucher code name
            $table->string('name')->index();

            // The description of the voucher - Not necessary
            $table->text('description')->nullable();

            // The number of uses currently
            $table->integer('uses')->unsigned()->nullable()->index();

            // The max uses this voucher has->index();
            $table->integer('max_uses')->unsigned()->nullable()->index();

            // How many times a user can use this voucher.
            $table->integer('max_uses_user')->unsigned()->nullable()->index();

            // The type can be: voucher, discount, sale. What ever you want.
            $table->tinyInteger('type')->unsigned()->index();

            // The amount to discount by (in pennies) in this example.
            $table->integer('discount_amount')->index();

            // Whether or not the voucher is a percentage or a fixed price.
            $table->boolean('is_fixed')->default( true )->index();

            // When the voucher begins
            $table->datetime('starts_at')->index();

            // When the voucher ends
            $table->datetime('expires_at')->nullable()->index();

            // Maximum amount capacity of total checkout price the discount can be on percentage basis
            $table->decimal('max_amt_cap', 10, 2)->nullable()->index();

            // Minimum amount of checkout price to use the voucher
            $table->decimal('min_amt_availability', 10, 2)->nullable()->index();

            // Pretty self explanatory
            $table->boolean('is_enabled')->default( true )->index();

            // where model where voucher is binded
            $table->tinyInteger('model')->nullable()->index();

            // You know what this is...
            $table->timestamps();

            // We like to horde data.
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
        Schema::dropIfExists('vouchers');
    }
}
