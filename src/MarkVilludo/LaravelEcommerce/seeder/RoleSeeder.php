<?php

use Illuminate\Database\Seeder;
use MarkVilludo\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        //Seed initial user roles then assigned based from its access.
        Role::insert([
            ['name' => 'Super Admin', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Admin', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Customer', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Merchant', 'created_at' => date('Y-m-d H:i:s')]
        ]);    

        //Add initial access for each role

    }
}
