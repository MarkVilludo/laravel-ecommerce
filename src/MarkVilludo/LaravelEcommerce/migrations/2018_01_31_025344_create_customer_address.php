<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_address', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('user_id')->index();
            $table->integer('default_shipping')->default(0);
            $table->integer('default_billing')->default(0);
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->integer('order_id')->nullable()->index(); // Different order per billing address .
            $table->text('complete_address');
            $table->string('barangay')->nullable()->index();
            $table->string('city_id')->nullable()->index();
            $table->string('province_id')->nullable()->index();
            $table->string('country_id')->nullable()->index();
            $table->string('zip_code')->nullable()->index();
            $table->string('mobile_number')->nullable()->index();
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
        Schema::dropIfExists('customer_address');
    }
}
