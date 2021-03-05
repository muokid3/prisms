<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SItesSeeder extends Seeder
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
        DB::table('sites')->truncate();
//        DB::statement('SET session_replication_role = \'origin\';');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $site = new \App\Site();
        $site->site_name = 'NAIROBI';
        $site->save();

        $site = new \App\Site();
        $site->site_name = 'UKUNDA';
        $site->save();

        $site = new \App\Site();
        $site->site_name = 'NAIVASHA';
        $site->save();



    }
}
