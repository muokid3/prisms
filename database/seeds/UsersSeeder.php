<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::statement('SET session_replication_role = \'replica\';');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
//        DB::statement('SET session_replication_role = \'origin\';');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user = new \App\User();
        $user->title = 'Mr.';
        $user->first_name = 'SUPER';
        $user->last_name = 'ADMIN';
        $user->user_group = 1; //super admin
        $user->phone_no = '254713653112';
        $user->site_id = 1; //all
        $user->email = "admin@prisms.com";
        $user->password = bcrypt("pass123");
        $user->save();
    }
}
