<?php

use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vouchers')->truncate();
        DB::table('voucher_bind')->truncate();

        // pool for voucher
        DB::table('vouchers')->insert([
            [
                'id' => 1,
                'code' => '100OFF',
                'name' => '100off',
                'description' => '100off',
                'uses' => 0,
                'max_uses' => 5,
                'max_uses_user' => 1,
                'type' => 1,
                'discount_amount' => 100,
                'is_fixed' => 1,
                'starts_at' => '2018-03-03',
                'expires_at' => '2018-03-06',
                'max_amt_cap' => 100,
                'min_amt_availability' => 1000,
                'is_enabled' => 1,
                'model' => null,
            ],[
                'id' => 2,
                'code' => '500OFF',
                'name' => '500off',
                'description' => '500off',
                'uses' => 0,
                'max_uses' => 10,
                'max_uses_user' => 2,
                'type' => 1,
                'discount_amount' => 500,
                'is_fixed' => 1,
                'starts_at' => '2018-03-03',
                'expires_at' => '2018-03-06',
                'max_amt_cap' => 500,
                'min_amt_availability' => 2000,
                'is_enabled' => 1,
                'model' => 1,
            ],[
                'id' => 3,
                'code' => '10%OFF',
                'name' => '10%off',
                'description' => '10%off',
                'uses' => 0,
                'max_uses' => 15,
                'max_uses_user' => 1,
                'type' => 1,
                'discount_amount' => 10,
                'is_fixed' => 2,
                'starts_at' => '2018-03-03',
                'expires_at' => '2018-03-06',
                'max_amt_cap' => 250,
                'min_amt_availability' => 1500,
                'is_enabled' => 1,
                'model' => 1,
            ],[
                'id' => 4,
                'code' => '11%OFF',
                'name' => '10%off',
                'description' => '10%off',
                'uses' => 0,
                'max_uses' => 15,
                'max_uses_user' => 1,
                'type' => 1,
                'discount_amount' => 10,
                'is_fixed' => 2,
                'starts_at' => '2018-03-03',
                'expires_at' => '2018-03-06',
                'max_amt_cap' => 250,
                'min_amt_availability' => 1500,
                'is_enabled' => 1,
                'model' => 2,
            ],
        ]);
        // pool for voucher binds
        DB::table('voucher_bind')->insert([
            [
                'voucher_id' => 2,
                'model' => 'App\Product',
                'key_name' => 'product_id',
                'fk_id' => 1,
            ],[
                'voucher_id' => 3,
                'model' => 'App\Product',
                'key_name' => 'product_id',
                'fk_id' => 5,
            ],[
                'voucher_id' => 4,
                'model' => 'App\Category',
                'key_name' => 'category_id',
                'fk_id' => 1,
            ],
        ]);

    }
}
