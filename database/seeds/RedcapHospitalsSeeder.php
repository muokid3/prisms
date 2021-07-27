<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RedcapHospitalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('redcap_sites')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 45;
        $redcapSite->redcap_hospital_name = 'Naivasha Level 5 Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 51;
        $redcapSite->redcap_hospital_name = 'Kiambu Level 5 Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 52;
        $redcapSite->redcap_hospital_name = 'Machakos Level 5 Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 53;
        $redcapSite->redcap_hospital_name = 'Mama Lucy Kibaki Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 62;
        $redcapSite->redcap_hospital_name = 'Kisumu County Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 63;
        $redcapSite->redcap_hospital_name = 'Kakamega County General Teaching and Referral Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 66;
        $redcapSite->redcap_hospital_name = 'Busia County Referral Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 68;
        $redcapSite->redcap_hospital_name = 'Kitale County Referral Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 71;
        $redcapSite->redcap_hospital_name = 'Embu Level 5 Teaching and Referral Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 76;
        $redcapSite->redcap_hospital_name = 'Bungoma County Referral Hospital';
        $redcapSite->save();

        $redcapSite = new \App\RedcapSite();
        $redcapSite->redcap_hospital_id = 77;
        $redcapSite->redcap_hospital_name = 'Bugando Medical Centre (Tanzania site)';
        $redcapSite->save();
    }
}
