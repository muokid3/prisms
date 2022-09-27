<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (is_null(\App\User::where('email','')->first())){
            $user = new \App\User();
            $user->title = 'Mr.';
            $user->first_name = 'KWTRP';
            $user->last_name = 'ADMIN';
            $user->user_group = 1; //super admin
            $user->phone_no = '254711111111';
            $user->site_id = 1; //all
            $user->email = "kwtrpadmin@kemri-wellcome.org";
            $user->password = bcrypt("kwtrpAdmin2022");
            $user->save();
        }

    }
}
