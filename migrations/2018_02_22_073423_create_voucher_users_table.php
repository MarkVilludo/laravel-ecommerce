<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('voucher_users', function (Blueprint $table) {
          $table->bigIncrements('id')->index();

          //voucher id used
          $table->bigInteger('voucher_id')->unsigned()->index();

          // user id of the user who used the voucher
          $table->bigInteger('user_id')->unsigned()->index();

          // order id where the voucher is used
          $table->integer('order_id')->unsigned()->index();

          // date that the voucher is used
          $table->datetime('date_used')->index();

          //get number of uses of each voucher
          $table->integer('uses')->default(0)->nullable();

          // make user id and voucher id unique with each other
          $table->unique(['user_id','voucher_id']);

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
        Schema::dropIfExists('voucher_users');
    }
}
