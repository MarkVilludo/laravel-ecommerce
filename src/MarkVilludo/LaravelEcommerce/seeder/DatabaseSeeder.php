<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Permissions / Role database seeder
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        
        //Voucher seeder
        $this->call(VoucherSeeder::class);
        
        //Calling additional database table seeder
        $this->call(CategoriesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(PackageTableSeeder::class);

        //Order status initial database seeder
        $this->call(OrderStatusTableSeeder::class);

        //Seeder for countries, provinces and cities
        $this->call(CountryTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);

        //Journal category
        $this->call(JournalCategoryTableSeeder::class);

        
    }
}
