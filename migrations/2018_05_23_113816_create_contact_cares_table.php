<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactCaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_cares', function (Blueprint $table) {
            $table->increments('id');
            $table->string('contact_number', 32);
            $table->string('email', 50);
            $table->string('shipping_concern_email', 50);
            $table->string('pr_media_inquiry_email', 50);
            $table->string('partnership_business_inquery_email', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_cares');
    }
}
