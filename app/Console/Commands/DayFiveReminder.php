<?php

namespace App\Console\Commands;

use App\RedcapSite;
use App\Sent;
use App\SiteContact;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use IU\PHPCap\RedCapProject;

class DayFiveReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:day5';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Day 5 reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiUrl = 'https://searchtrial.kemri-wellcome.org/api/';

        //$apiToken = '8E77FB323E730636E6204C516ECC74B3'; //dkimego
        $apiToken = '4CF1C475E6A78AED79DE826D2FB4E403'; //hsutoken

        $project = new RedCapProject($apiUrl, $apiToken);
        //$projectInfo = $project->exportProjectInfo();

        $records = $project->exportRecords(
            'php',
            null,
            null,//[520045],
            ["id","date_today","study_id","hosp_id","ipno","date_adm"],
            null,
            ['preliminary_arm_1']

        );


        foreach ($records as $record){
            $date_adm = $record['date_adm'];
            //$ipno = $record['ipno'];
            $hosp_id = $record['hosp_id'];
            $study_id = $record['study_id'];


            if (Carbon::parse($date_adm)->addDays(5)->isCurrentDay()){
                $scheduledDate = Carbon::parse($date_adm)->addDays(5)->isoFormat('MMM Do YYYY');


                $site = RedcapSite::where('redcap_hospital_id',$hosp_id)->first();
                $siteName = is_null($site) ? "NULL" : $site->redcap_hospital_name;
                $id = is_null($site) ? 0 : $site->id;


                Log::info("DATE ADM::".$date_adm." HOSP ID::".$hosp_id." HOSP::".$siteName." STUDY ID::".$study_id );


                $siteContacts = SiteContact::where('redcap_site_id', $id)->get();
                Log::info("sitecontact count:::".$siteContacts->count());
                foreach ($siteContacts as $siteContact){

                    if ($siteContact->user_group == 4){
                        //clerk
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-5 review entry".
                            " for the patient with the study ID: ".$study_id." scheduled for today ".
                            $scheduledDate.
                            "\r\n".
                            ".Good day.";
                    }else{
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-5 review".
                            " for the patient with the study ID: ".$study_id." scheduled for today ".
                            $scheduledDate.
                            "\r\n".
                            ".Good day.";
                    }



                    $result_sms = send_sms("SEARCHTrial",$message,$siteContact->contact_phone_no,rand());

                    $sent = new Sent();
                    $sent->timestamp = Carbon::now();
                    $sent->destination = $siteContact->contact_phone_no;
                    $sent->text = $message;
                    $sent->status = $result_sms["message"];
                    $sent->unique_id = array_key_exists('data', $result_sms) ? $result_sms["data"]["uniqueId"] : null;
                    $sent->saveOrFail();

                }
            }





        }

        //return 0;
    }
}
