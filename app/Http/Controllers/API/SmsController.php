<?php

namespace App\Http\Controllers\API;

use App\AllocationList;
use App\Http\Controllers\Controller;
use App\Inbox;
use App\Sent;
use App\Site;
use App\Stratum;
use App\Study;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function receive(Request $request)
    {

        Log::info("Received payload from Bongatech: ".json_encode($request->all()));


        $platform = $request->platform ;
        $phone = $request->phone ;
        $short_code = $request->short_code ;
        $short_message = $request->short_message ;
        $link_id = $request->link_id ;
        $timeReceived = $request->timeReceived ;

        $msg = explode(" ", $short_message);

        if(strtolower($msg[0]) == "tools" || strtolower($msg[0]) == "utilities"){
            $status = "22";
            Log::info("A utilities/tools message is logged from ".$phone." at ".Carbon::now());
        } else{
            $status = "0";
            Log::info("A randomise message is logged from ".$phone." at ". Carbon::now());
        }

        $inbox = new Inbox();
        $inbox->timestamp = $timeReceived;
        $inbox->source = $phone;
        $inbox->text = $short_message;
        $inbox->short_code = $short_code;
        $inbox->status = $status;
        $inbox->saveOrFail();

        Log::info("Request successfully stored for the phone number: ".$phone." at ".Carbon::now());


        Log::info("Running artisan command");

        Artisan::call('sms:randomise');

        Log::info("Artisan command run complete");



        return response()->json([
            "response_code" => "0",
            "response_message" => "Successful"
        ]);



    }

    public function delivery_report(Request $request)
    {
        $phone = $request->phone ;
        $unique_id = $request->uniqueId ;
        $deliveryStatus = $request->deliveryStatus ;
        $deliveryTime = $request->deliveryTime ;

        Log::info("Received delivery report: ".json_encode($request->all()));

        $sent = Sent::where('unique_id', $unique_id)->first();

        if (!is_null($sent)){
            $sent->status = $deliveryStatus;
            $sent->delivery_time = $deliveryTime;
            $sent->update();

            Log::info("Delivery status successfully stored for the phone number: ".$phone." at ".Carbon::now());
        }else{
            Log::info("message with unique ID".$unique_id." not found");
        }



    }

    public function randomise(){
        $inboxes = Inbox::where('status',0)->limit(3)->get();

        foreach ($inboxes as $inbox){

            $id = $inbox->id;
            $source = $inbox->source;
            $text = $inbox->text;
            $superuser = Config::get('prisms.OPERATOR_SUPERUSER');

            $inbox->status = 29;
            $inbox->update();

            Log::info("A message from " . $source . " was updated successfully stored: Processing at " .Carbon::now());

//            $user = User::where('phone_no',$source)->first();

//            if (is_null($user)){
//                $reply = "The number " . $source . " does not belong to an active user who is authorised to randomise participants study on PRISMS. Contact " . Config::get('prisms.OPERATOR_SUPERUSER') . "for more details";
//            }else{
//            }

            $message = array_values(array_filter(explode(" ", $text)));
            print_r($message);
            echo sizeof($message);

            if (sizeof($message) <= 3 || sizeof($message) > 7) {

                $inbox->status = 44;
                $inbox->update();

                $reply = "Incorrect message format; use:" . "\r\n" . "\r\n" . "randomise [ipno] to [studyName] [sitePrefix]" . "\n" . "\n" . "or:" . "\n" . "\n" . "randomise [ipno] to [studyName] [sitePrefix] [phoneNO]" . "\r\n" . "\r\n" . "without the straight brackets. You may also add your phone number at the end of the message if using an authorised phone that does not belong to you.";

                Log::info("INCORRECT MESSAGE FORMAT: ".$reply. "\n");

            }
            elseif(strtolower($message[0]) != strtolower("randomise")) {

                $inbox->status = 44;
                $inbox->update();

                $reply = "Incorrect message format; use:" . "\r\n" . "\r\n" . "randomise [ipno] to [studyName] [sitePrefix]" . "\n" . "\n" . "or:" . "\n" . "\n" . "randomise [ipno] to [studyName] [sitePrefix] [phoneNO]" . "\r\n" . "\r\n" . "without the straight brackets. You may also add your phone number at the end of the message if using an authorised phone that does not belong to you.";
                Log::info("INCORRECT MESSAGE FORMAT: ".$reply. "\n");

            }
            else{

                $strat = "";

                if (sizeof($message) == 5) {
                    $ipno = $message[1];
                    $site = $message[4];
                    $study = $message[3];
                    $phone_no = $source;
                } elseif (sizeof($message) == 6) {
                    ##check if index 5 is stratum or phone number
                    $ipno = $message[1];
                    $site = $message[4];
                    $study = $message[3];
                    $i5 = $message[5];

//                    if (substr($i5, 0, 5) == "strat"){
//                        $str_arr = explode (":", $i5);
//                        $strat =  sizeof($str_arr) > 1 ? $str_arr[1] : "";
//                        $phone_no = $source;
//                    }

                    if ($i5 == "supportive"){
                        $strat =  "supportive";
                        $phone_no = $source;
                    } else{
                        $phone_no = $message[5];
                    }


                } elseif (sizeof($message) == 7) {
                    ##this one has stratum
                    $ipno = $message[1];
                    $site = $message[4];
                    $study = $message[3];
                    $phone_no = $message[5];
                    $i6 = $message[6];

//                    if (substr($i6, 0, 5) == "strat"){
//                        $str_arr = explode (":", $i6);
//                        $strat =  sizeof($str_arr) > 1 ? $str_arr[1] : "";
//                    }
                    if ($i6 == "supportive"){
                        $strat =  "supportive";
                    }

                } elseif (sizeof($message) == 4) {
                    $ipno = $message[1];
                    $study = $message[3];
                    $phone_no = $source;
                }

                if (substr($phone_no, 0, 1) === '0') {
                    $phone_no = "+254".ltrim($phone_no, "0");
                }

                if (substr($phone_no, 0, 1) === '2') {
                    $phone_no = "+".$phone_no;
                }

                Log::info("received randomisation request from phone ::::::::::::".$phone_no);

                $lookup_users = User::where('active',1)->where('phone_no', $phone_no)->first();


                if (!is_null($lookup_users)) {

                    $available = DB::table('user_permissions')
                        ->select('id')
                        ->where('group_id', '=', $lookup_users->user_group)
                        ->whereIn('permission_id', [10])
                        ->get();

                    if (count($available) > 0 || $lookup_users->user_group ==1 ){
                        //user has randomising permissions

                        $st = Study::where('study',$study)->first();
                        $stId = is_null($st) ? 0 : $st->id;

                        if ($strat == "supportive")
                            $stratum = Stratum::where('stratum','like','supportive%')->first();
                        else
                            $stratum = Stratum::where('stratum','like','antibiotic%')->first();

                        $stratumId = is_null($stratum) ? 1 : $stratum->id;

                        $select_ipnos = AllocationList::where('ipno',$ipno)
                            ->where('study_id',$stId)
                            ->where('stratum_id',$stratumId)
                            ->first();

                        if (!is_null($select_ipnos)) {
                            $reply = "The participant with the ipno " . $ipno . " is already allocated " . $select_ipnos->allocation . " by " .
                                optional($select_ipnos->staff)->first_name.' '. optional($select_ipnos->staff)->last_name . " at " . $select_ipnos->date_randomised;

                            Log::info("RANDOMISATION ATTEMPT TO REALLOCATE: ".$reply. "\n");

                        } else {
                            //randomising

                            $actualSite = Site::where('prefix',$site)->first();
                            $actualSitetId = is_null($actualSite) ? 0 : $actualSite->id;


                            $alloc_seq = AllocationList::selectRaw(" MIN(sequence) AS next_sequence")
                                ->whereNull('date_randomised')
                                ->where('study_id',$stId)
                                ->where('site_id',$actualSitetId)
                                ->where('stratum_id',$stratumId)
                                ->first();


                            if (is_null($alloc_seq)) {
                                $reply = "Random allocations to the " . $study . " study are no longer available. Please contact the study co-ordination centre.";

                                Log::info("RANDOMIZATION ALLOCATION LIST NOT AVAILABLE: ".$reply. "\n");

                            } else {

                                $next_sequence = $alloc_seq->next_sequence;

                                $lookup_allocation = AllocationList::where('sequence',$next_sequence)
                                    ->whereNull('date_randomised')
                                    ->where('study_id',$stId)
                                    ->where('site_id',$actualSitetId)
                                    ->where('stratum_id',$stratumId)
                                    ->first();

                                if (is_null($lookup_allocation)){
                                    $reply = "Random allocations to the " . $study . " study are no longer available. Please contact the study co-ordination centre.";

                                    Log::info("RANDOMIZATION ALLOCATION LIST NOT AVAILABLE: ".$reply. "\n");
                                }else{
                                    $next_allocation = $lookup_allocation->allocation;

                                    //check is supportive, maintain same participant ID given for antibiotic
                                    if ($strat == "supportive"){
                                        $existing_allocation = AllocationList::where('ipno',$ipno)
                                            ->whereNotNull('date_randomised')
                                            ->where('study_id',$stId)
                                            ->where('site_id',$actualSitetId)
                                            ->first();

                                        if (is_null($existing_allocation)){
                                            $reply = "Please randomise the participant to antibiotic arm first before attempting to randomise to supportive care arm";
                                            Log::info("NOT SUCCESSFUL. ATTEMPTED TO RANDOMIZE TO SUPPORTIVE BEFORE ANTIBIOTIC: ".$reply. "\n");

                                        }else{
                                            $participant_id = $existing_allocation->participant_id;

                                            $lookup_allocation->participant_id = $participant_id;
                                            $lookup_allocation->ipno = $ipno;
                                            $lookup_allocation->user_id = $lookup_users->id;
                                            $lookup_allocation->date_randomised = Carbon::now();
                                            $lookup_allocation->update();


                                            $reply = "Participant " . $ipno . " has been randomised to " . $next_allocation . " in the " . $study . " study and "
                                                . Stratum::find($stratumId)->stratum . ". The unique number for the participant is " .
                                                $participant_id . " . Randomised by " . $lookup_users->first_name.' '.$lookup_users->last_name . " at " . Carbon::now() . "." . "\r\n" . "#" . $next_sequence;

                                            Log::info("SUCCESSFUL RANDOMIZATION: ".$reply. "\n");
                                        }

                                    }else{

                                        $siteRand = AllocationList::where('site_id',$actualSitetId)
                                            ->whereNotNull('date_randomised')
                                            ->count();

                                        $siteRandCount = $siteRand+1;

                                        $participant_id = $actualSite->prefix . str_pad($siteRandCount,3,"0",STR_PAD_LEFT);

                                        $lookup_allocation->participant_id = $participant_id;
                                        $lookup_allocation->ipno = $ipno;
                                        $lookup_allocation->user_id = $lookup_users->id;
                                        $lookup_allocation->date_randomised = Carbon::now();
                                        $lookup_allocation->update();


                                        $reply = "Participant " . $ipno . " has been randomised to " . $next_allocation . " in the " . $study . " study and "
                                            . Stratum::find($stratumId)->stratum . ". The unique number for the participant is " .
                                            $participant_id . " . Randomised by " . $lookup_users->first_name.' '.$lookup_users->last_name . " at " . Carbon::now() . "." . "\r\n" . "#" . $next_sequence;

                                        Log::info("SUCCESSFUL RANDOMIZATION: ".$reply. "\n");
                                    }
                                }

                            }
                        }

                    }
                    else{
                        //user DOES NOT have randomising permissions
                        $reply = "You do not have the permissions to randomise participants study at " . strtoupper($site) . ". Contact " . Config::get('prisms.OPERATOR_SUPERUSER') . "for more details";
                        $adm_msg = "The number " . $source . " tried randomising participants at " . strtoupper($site) . ". The user does not have permissions";

                        Log::info("RANDOMISATION UPDATE: ".$adm_msg. "\n");

                        $result_sms = send_sms("SEARCHTrial",$adm_msg,Config::get('prisms.OPERATOR_SUPERUSER'),rand());

                        $sent = new Sent();
                        $sent->timestamp = Carbon::now();
                        $sent->destination = $superuser;
                        $sent->text = $adm_msg;
                        $sent->status = $result_sms["message"];
                        $sent->message_id = $id;
                        $sent->unique_id = array_key_exists('data', $result_sms) ? $result_sms["data"]["uniqueId"] : null;
                        $sent->saveOrFail();

                    }


                } else {

                    $reply = "The number " . $source . " does not belong to an active user who is authorised to randomise participants study at " . strtoupper($site) . ". Contact " . Config::get('prisms.OPERATOR_SUPERUSER') . "for more details";
                    $adm_msg = "The number " . $source . " tried randomising participants at " . strtoupper($site) . ". The user is unregistered";

                    Log::info("RANDOMISATION UPDATE: ".$adm_msg. "\n");

                    $result_sms = send_sms("SEARCHTrial",$adm_msg,Config::get('prisms.OPERATOR_SUPERUSER'),rand());

                    $sent = new Sent();
                    $sent->timestamp = Carbon::now();
                    $sent->destination = $superuser;
                    $sent->text = $adm_msg;
                    $sent->status = $result_sms["message"];
                    $sent->message_id = $id;
                    $sent->unique_id = array_key_exists('data', $result_sms) ? $result_sms["data"]["uniqueId"] : null;
                    $sent->saveOrFail();

                }

            }


            $result_sms = send_sms("SEARCHTrial",$reply,$source,rand());

            Log::info("RESULT SMS::::".json_encode($result_sms));

            $sent = new Sent();
            $sent->timestamp = Carbon::now();
            $sent->destination = $source;
            $sent->text = $reply;
            $sent->status = $result_sms["message"];
            $sent->message_id = $id;
            $sent->unique_id = array_key_exists('data', $result_sms) ? $result_sms["data"]["uniqueId"] : null;
            $sent->saveOrFail();

        }

    }
}
