<?php

namespace App\Http\Controllers\API;

use App\AllocationList;
use App\Http\Controllers\Controller;
use App\Sent;
use App\User;
use App\UserGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

}
