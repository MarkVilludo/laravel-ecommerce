<?php

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\PackageItem;


class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packages')->truncate();
        //future additional fields , availability (kung kaninong user type lang ipapakita yung package), Tax
        //Eyes sub categories
        //Package 1
        Package::create(array(
            'name' => 'Make up Kit',
            'code' => 'MAKEUP1012',
            'description'    => 'Make up kit just only for you.',
            'price'    => 200,
            'status'    => 1, //1-Publish or 0 - unpublished 
            'created_at' => date('Y-m-d H:i:s')
        ));

      	DB::table('package_items')->truncate();
  	 	PackageItem::insert([
	  	 	[
	            'package_id' => 1,
	            'product_id' => 1,
	            'variant_id' => 1,
	            'quantity' => 1,
	            'created_at' => date('Y-m-d H:i:s'),
	        ],[
	            'package_id' => 1,
	            'product_id' => 2,
	            'variant_id' => 1,
	            'quantity' => 2,
	            'created_at' => date('Y-m-d H:i:s'),
	        ],
	        [
	            'package_id' => 1,
	            'product_id' => 3,
	            'variant_id' => 1,
	            'quantity' => 2,
	            'created_at' => date('Y-m-d H:i:s'),
	        ],
	        [
	            'package_id' => 1,
	            'product_id' => 4,
	            'variant_id' => 1,
	            'quantity' => 1,
	            'created_at' => date('Y-m-d H:i:s'),
	        ]
    	]);
    }
}
