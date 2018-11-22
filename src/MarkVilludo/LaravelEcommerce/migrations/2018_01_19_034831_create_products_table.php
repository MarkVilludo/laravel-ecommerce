<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('name')->index();
            $table->integer('sub_category_id')->nullable()->index(); //Electronic device, Mens fashion, Women fashion, Grocery,pets, fashion accessories, etc
            $table->integer('child_sub_category_id')->nullable()->index(); //Product category ex Watch, Shirt, Dress, Shoes ,etc
            $table->integer('brand_id')->nullable()->index(); //
            $table->integer('merchant_id')->nullable(); //refers to user id of a merchant
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('warranty_type')->nullable();
            $table->string('warranty')->nullable();
            $table->string('model')->nullable();
            $table->string('tag_id')->nullable()->index();
            $table->integer('pre_order')->nullable(); //Make your product as Pre-order if you need more time to ship out. Else please ship with 3 days.
            $table->integer('days_to_ship')->nullable(); //Numbe of days to ship product

            $table->integer('minimum_purchase_quantity')->nullable()->index();//Minimun purchase quantity

            $table->decimal('regular_price',8,2);
            $table->decimal('selling_price',8,2);
            $table->decimal('shipping_fee',8,2)->nullable()->index();
            //product package details
            $table->string('package_height')->nullable();
            $table->string('package_length')->nullable();
            $table->string('package_width')->nullable();
            $table->string('package_weight')->nullable();
            $table->string('package_content')->nullable();
            $table->string('slug')->nullable()->index(); //Url of product
            // $table->enum('condition',['new','old'])->nullable()->index();
            $table->integer('status')->default(1)->nullable(); //for product availability Active or hidden store
            $table->integer('views')->nullable(); //for count number of view
            $table->double('ratings',10,2)->default(0)->nullable(); //for count number of ratings
            $table->integer('featured')->default(0)->nullable(); //Top picks product  1 - Top pick products
            $table->text('manual')->nullable(); //how to use
            $table->integer('fbt_id')->nullable()->index(); //Set of frequently bought together
            $table->integer('updated_by')->nullable()->index(); 
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
        Schema::dropIfExists('products');
    }
}
