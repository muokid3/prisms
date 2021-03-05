<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
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
        DB::table('permissions')->truncate();
//        DB::statement('SET session_replication_role = \'origin\';');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permission = new \App\Permission();
        $permission->name = 'Manage Users';
        $permission->save();

        $permission = new \App\Permission();
        $permission->name = 'Upload a sequence';  //2
        $permission->save();

        $permission = new \App\Permission();
        $permission->name = 'Generate a sequence'; //3
        $permission->save();

        $permission = new \App\Permission();
        $permission->name = 'Randomization Log'; //4
        $permission->save();

        $permission = new \App\Permission();
        $permission->name = 'SMS Log'; //5
        $permission->save();

        $permission = new \App\Permission();
        $permission->name = 'Manage studies'; //6
        $permission->save();


        $permission = new \App\Permission();
        $permission->name = 'Manage Sites'; //7
        $permission->save();




    }
}
