<?php

namespace App\Http\Controllers\API;

use App\AllocationList;
use App\Http\Controllers\Controller;
use App\RedcapSite;
use App\Sent;
use App\SiteContact;
use App\User;
use App\UserGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use IU\PHPCap\RedCapProject;

class RemindersController extends Controller
{
    public function day30()
    {
        //send reminders today for tomorrow's day30
        //today is day 29

        $admissionDate = Carbon::now()->subDays(29);

        $allocations = AllocationList::whereDate('date_randomised',$admissionDate)->get();
        $ug = UserGroup::where('name','clinician')->first();

        foreach ($allocations as $allocation){
            //notify all clinicians in this site

            if (is_null($ug))
                $recipients = User::where('site_id',$allocation->site_id)->get();
            else
                $recipients = User::where('site_id',$allocation->site_id)->where('user_group',$ug->id)->get();


            foreach ($recipients as $recipient){

                $scheduledDate = Carbon::parse($allocation->date_randomised)->addDays(30)->isoFormat('MMM Do YYYY');

                $message = "Hi ".$recipient->title." ".$recipient->first_name.", This is a reminder to do a day-30 review".
                    " for the patient with the study ID: ".$allocation->participant_id." scheduled for ".
                    $scheduledDate.
                    "\r\n".
                    ". Good day.";

                $result_sms = send_sms("SEARCHTrial",$message,$recipient->phone_no,rand());

                $sent = new Sent();
                $sent->timestamp = Carbon::now();
                $sent->destination = $recipient->phone_no;
                $sent->text = $message;
                $sent->status = $result_sms["message"];
                $sent->unique_id = array_key_exists('data', $result_sms) ? $result_sms["data"]["uniqueId"] : null;
                $sent->saveOrFail();
            }
        }
    }

    public function day5()
    {
        //send reminders today for tomorrow's day5
        //today is day 4

        $admissionDate = Carbon::now()->subDays(4);

        $allocations = AllocationList::whereDate('date_randomised',$admissionDate)->get();
        $ug = UserGroup::where('name','clinician')->first();

        foreach ($allocations as $allocation){
            //notify all clinicians in this site

            if (is_null($ug))
                $recipients = User::where('site_id',$allocation->site_id)->get();
            else
                $recipients = User::where('site_id',$allocation->site_id)->where('user_group',$ug->id)->get();


            foreach ($recipients as $recipient){

                $scheduledDate = Carbon::parse($allocation->date_randomised)->addDays(30)->isoFormat('MMM Do YYYY');

                $message = "Hi ".$recipient->title." ".$recipient->first_name.", This is a reminder to do a day-5 review".
                    " for the patient with the study ID: ".$allocation->participant_id." scheduled for ".
                    $scheduledDate.
                    "\r\n".
                    "Good day.";

                $result_sms = send_sms("SEARCHTrial",$message,$recipient->phone_no,rand());

                $sent = new Sent();
                $sent->timestamp = Carbon::now();
                $sent->destination = $recipient->phone_no;
                $sent->text = $message;
                $sent->status = $result_sms["message"];
                $sent->unique_id = array_key_exists('data', $result_sms) ? $result_sms["data"]["uniqueId"] : null;
                $sent->saveOrFail();
            }
        }

    }

    public function redcap_day30()
    {
        $apiUrl = 'https://searchtrial.kemri-wellcome.org/api/';

        //$apiToken = '8E77FB323E730636E6204C516ECC74B3'; //dkimego
        $apiToken = 'FE1D8D5FC99D535DA5EA73370CF95050'; //hsutoken

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
                $scheduledDate = Carbon::parse($date_adm)->addDays(30)->isoFormat('MMM Do YYYY');

                $site = RedcapSite::where('redcap_hospital_id',$hosp_id)->first();
                $siteName = is_null($site) ? "NULL" : $site->redcap_hospital_name;
                $id = is_null($site) ? 0 : $site->id;


                Log::info("DATE ADM::".$date_adm." HOSP ID::".$hosp_id." HOSP::".$siteName." STUDY ID::".$study_id );


                $siteContacts = SiteContact::where('redcap_site_id', $id)->get();
                Log::info("sitecontact count:::".$siteContacts->count());
                foreach ($siteContacts as $siteContact){

                    if ($siteContact->user_group == 4){
                        //clerk
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-30 review entry".
                            " for the patient with the study ID: ".$study_id." scheduled for today ".
                            $scheduledDate.
                            "\r\n".
                            ".Good day.";
                    }else{
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-30 review".
                            " for the patient with the study ID: ".$study_id." scheduled for today ".
                            $scheduledDate.
                            "\r\n".
                            ".Good day.";
                    }



//                    $result_sms = send_sms("SEARCHTrial",$message,'+254713653112',rand());
                    $result_sms = send_sms("SEARCHTrial",$message,$siteContact->contact_phone_no,rand());

                    $sent = new Sent();
                    $sent->timestamp = Carbon::now();
                    $sent->destination = $siteContact->contact_phone_no;
                    $sent->text = $message;
                    $sent->status = $result_sms["message"];
                    $sent->unique_id = array_key_exists('data', $result_sms) ? $result_sms["data"]["uniqueId"] : null;
                    $sent->saveOrFail();

//                    Log::info($result_sms);

                }
            }





        }

        //Log::info($records);

        //print_r($records);

    }

    public function redcap_day29()
    {
        $apiUrl = 'https://searchtrial.kemri-wellcome.org/api/';

        //$apiToken = '8E77FB323E730636E6204C516ECC74B3'; //dkimego
        $apiToken = 'FE1D8D5FC99D535DA5EA73370CF95050'; //hsutoken

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


            if (Carbon::parse($date_adm)->addDays(29)->isCurrentDay()){
                $scheduledDate = Carbon::parse($date_adm)->addDays(30)->isoFormat('MMM Do YYYY');


                $site = RedcapSite::where('redcap_hospital_id',$hosp_id)->first();
                $siteName = is_null($site) ? "NULL" : $site->redcap_hospital_name;
                $id = is_null($site) ? 0 : $site->id;


                Log::info("DATE ADM::".$date_adm." HOSP ID::".$hosp_id." HOSP::".$siteName." STUDY ID::".$study_id );


                $siteContacts = SiteContact::where('redcap_site_id', $id)->get();
                Log::info("sitecontact count:::".$siteContacts->count());
                foreach ($siteContacts as $siteContact){

                    if ($siteContact->user_group == 4){
                        //clerk
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-30 review entry".
                            " for the patient with the study ID: ".$study_id." scheduled for tomorrow ".
                            $scheduledDate.
                            "\r\n".
                            ".Good day.";
                    }else{
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-30 review".
                            " for the patient with the study ID: ".$study_id." scheduled for tomorrow ".
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

        //Log::info($records);

        //print_r($records);

    }

    public function redcap_day5()
    {
        $apiUrl = 'https://searchtrial.kemri-wellcome.org/api/';

        //$apiToken = '8E77FB323E730636E6204C516ECC74B3'; //dkimego
        $apiToken = 'FE1D8D5FC99D535DA5EA73370CF95050'; //hsutoken

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

        //Log::info($records);

        //print_r($records);

    }

    public function redcap_day4()
    {
        $apiUrl = 'https://searchtrial.kemri-wellcome.org/api/';

        //$apiToken = '8E77FB323E730636E6204C516ECC74B3'; //dkimego
        $apiToken = 'FE1D8D5FC99D535DA5EA73370CF95050'; //hsutoken

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


            if (Carbon::parse($date_adm)->addDays(4)->isCurrentDay()){
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
                            " for the patient with the study ID: ".$study_id." scheduled for tomorrow ".
                            $scheduledDate.
                            "\r\n".
                            ".Good day.";
                    }else{
                        $message = "Hi ".$siteContact->contact_first_name.", This is a reminder to do a day-5 review".
                            " for the patient with the study ID: ".$study_id." scheduled for tomorrow ".
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

        //Log::info($records);

        //print_r($records);

    }

}
