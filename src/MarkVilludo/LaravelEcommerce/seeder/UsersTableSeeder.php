<?php

// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $newUser = new User;
        $newUser->email = 'mark.villudo@synergy88digital.com';
        $newUser->first_name = 'Mark';
        $newUser->last_name = 'Villudo';
        $newUser->password = bcrypt('password');
        $newUser->save();

        //Assign role for initial users
        $newUser->assignRole(['Super Admin', 'Admin']);

        $newUser = new User;
        $newUser->email = 'jes.dolfo@synergy88digital.com';
        $newUser->first_name = 'Master';
        $newUser->last_name = 'Jes';
        $newUser->password =  bcrypt('password');
        $newUser->save();
        //Assign role for initial users

        $newUser->assignRole('Admin');
        
    }
}
