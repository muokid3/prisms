<?php

namespace App\Console\Commands;

use App\RedcapSite;
use App\Sent;
use App\SiteContact;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use IU\PHPCap\RedCapProject;

class DayNinentyReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:day90';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Day 90 reminder';
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
        $apiToken = 'FE1D8D5FC99D535DA5EA73370CF95050'; //hsutoken

        $project = new RedCapProject($apiUrl, $apiToken);

        $records = $project->exportRecords(
            'php',
            null,
            null,//[520045],
            ["id","aetiology_enrolled"],
            null,
            ['aetiology_study_arm_1']

        );


        $ids = array();
        foreach ($records as $record){
            $id = $record['id'];
            $aetiologyEnrolled = $record['aetiology_enrolled'];

            //only send for aetiology enrolled patients
            if ($aetiologyEnrolled == 1){
                $ids[] = $id;
            }
        }


        $records2 = $project->exportRecords(
            'php',
            null,
            $ids,
            ["id","date_today","study_id","hosp_id","ipno","date_adm"],
            null,
            ['preliminary_arm_1']

        );

        foreach ($records2 as $record) {
            $date_adm = $record['date_adm'];
            $hosp_id = $record['hosp_id'];
            $study_id = $record['study_id'];

            info($record);

            if (Carbon::parse($date_adm)->addDays(90)->isCurrentDay()){
                $scheduledDate = Carbon::parse($date_adm)->addDays(90)->isoFormat('MMM Do YYYY');

                $site = RedcapSite::where('redcap_hospital_id',$hosp_id)->first();
                $siteName = is_null($site) ? "NULL" : $site->redcap_hospital_name;
                $id = is_null($site) ? 0 : $site->id;



                $siteContacts = SiteContact::where('redcap_site_id', $id)->get();
                foreach ($siteContacts as $siteContact){

                    if ($siteContact->user_group == 4){
                        //clerk
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-90 review entry".
                            " for the patient with the study ID: ".$study_id." scheduled for today ".
                            $scheduledDate.
                            "\r\n".
                            ".Good day.";
                    }else{
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-90 review".
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
    }
}
