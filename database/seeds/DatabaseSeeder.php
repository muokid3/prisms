<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(SItesSeeder::class);
         $this->call(UserGroupSeeder::class);
         $this->call(PermissionsSeeder::class);
         $this->call(UsersSeeder::class);
         $this->call(RedcapHospitalsSeeder::class);
    }
}
