<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_models', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('name')->index();
            $table->string('key_id')->index();
            $table->string('model')->index();
            $table->timestamps();
        });

        DB::table('voucher_models')->insert([
            [
                'id' => 1,
                'name' => 'Product',
                'key_id' => 'product_id',
                'model' => 'App\Product',
            ],[
                'id' => 2,
                'name' => 'Product Category',
                'key_id' => 'category_id',
                'model' => 'App\Category',
            ]
            // ,[
            //     'id' => 3,
            //     'name' => 'Payment Transaction',
            //     'key_id' => '',
            //     'model' => 'App\PaymentTransaction',
            // ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_models');
    }
}
