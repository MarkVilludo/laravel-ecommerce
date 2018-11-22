<?php

use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_status')->truncate();
        OrderStatus::insert([
        	['name' => 'Processing'],
            ['name' => 'Shipping'],
            ['name' => 'Delivered'],
            ['name' => 'Complete'],
        	['name' => 'Cancelled'],
            ['name' => 'Replacement'],
        	['name' => 'Return / Refund']
        ]);
    }
}
