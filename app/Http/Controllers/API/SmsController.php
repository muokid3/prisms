<?php

namespace App\Http\Controllers\API;

use App\AllocationList;
use App\Http\Controllers\Controller;
use App\Inbox;
use App\Sent;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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

        Log::info("Request successfully stored for the phone number: ".$phone." at ".Carbon::now());

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

        Log::info("Received elivery report: ".json_encode($request->all()));

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

            $message = array_values(array_filter(explode(" ", $text)));
            print_r($message);
            echo sizeof($message);

            if (sizeof($message) <= 3 || sizeof($message) > 6) {

                $inbox->status = 44;
                $inbox->update();

                $reply = "Incorrect message format; use:" . "\r\n" . "\r\n" . "randomise [ipno] to [studyID] [siteID]" . "\n" . "\n" . "or:" . "\n" . "\n" . "randomise [ipno] to [studyID] [siteID] [phoneNO]" . "\r\n" . "\r\n" . "without the straight brackets. You may also add your phone number at the end of the message if using an authorised phone that does not belong to you.";

                Log::info("INCORRECT MESSAGE FORMAT: ".$reply. "\n");

            }elseif(strtolower($message[0]) != strtolower("randomise")) {

                $inbox->status = 44;
                $inbox->update();

                $reply = "Incorrect message format; use:" . "\r\n" . "\r\n" . "randomise [ipno] to [studyID] [siteID]" . "\n" . "\n" . "or:" . "\n" . "\n" . "randomise [ipno] to [studyID] [siteID] [phoneNO]" . "\r\n" . "\r\n" . "without the straight brackets. You may also add your phone number at the end of the message if using an authorised phone that does not belong to you.";
                Log::info("INCORRECT MESSAGE FORMAT: ".$reply. "\n");

            } else{
                if (sizeof($message) == 5) {
                    $ipno = $message[1];
                    $site = $message[4];
                    $study = $message[3];
                    $phone_no = $source;
                } elseif (sizeof($message) == 6) {
                    ##check the source in the database
                    $ipno = $message[1];
                    $site = $message[4];
                    $study = $message[3];
                    $phone_no = $message[5];
                } elseif (sizeof($message) == 4) {
                    $ipno = $message[1];
                    $study = $message[3];
                    $phone_no = $source;
                }

                //$lookup_users = "SELECT first_name, last_name, study FROM users WHERE active=1 AND phone_no='$phone_no'";
                $lookup_users = User::where('active',1)->where('phone_no', $phone_no)->first();


                if ($lookup_users->count() > 0) {
                    $select_ipnos = AllocationList::where('ipno',$ipno)->where('study',$study)->first();


                    if (!is_null($select_ipnos)) {
                        $reply = "The participant with the ipno " . $ipno . " is already allocated " . $select_ipnos->allocation . " by " .
                            optional($select_ipnos->staff)->first_name.' '. optional($select_ipnos->staff)->last_name . " at " . $select_ipnos->date_randomised;

                        Log::info("RANDOMISATION ATTEMPT TO REALLOCATE: ".$reply. "\n");

                    } else {
                        //randomising
                        $alloc_seq = AllocationList::selectRaw(" MIN(sequence) AS next_sequence")
                            ->whereNull('date_randomised')
                            ->where('study',$study)
                            ->first();

                        $next_sequence = $alloc_seq->next_sequence;

                        if (is_null($alloc_seq)) {
                            $reply = "Random allocations to the " . $study . " study are no longer available. Please contact the study co-ordination centre.";

                            Log::info("RANDOMIZATION ALLOCATION LIST NOT AVAILABLE: ".$reply. "\n");

                        } else {
                            $lookup_allocation = AllocationList::where('sequence',$next_sequence)
                                ->whereNull('date_randomised')
                                ->where('study',$study)
                                ->first();

                            $next_allocation = $lookup_allocation->allocation;

                            $participant_id = 'BLA' . mt_rand(100, 999);

                            $lookup_allocation->participant_id = $participant_id;
                            $lookup_allocation->ipno = $ipno;
                            $lookup_allocation->user_id = $lookup_users->id;
                            $lookup_allocation->date_randomised = Carbon::now();


                            $reply = "Participant " . $ipno . " has been randomised to " . $next_allocation . " in the " . $study . " study. The unique number for the participant is " .
                                $participant_id . " . Randomised by " . $lookup_users->first_name.' '.$lookup_users->last_name . " at " . Carbon::now() . "." . "\r\n" . "#" . $next_sequence;

                            Log::info("SUCCESSFUL RANDOMIZATION: ".$reply. "\n");
                        }
                    }


                } else {

                    $reply = "The number " . $source . " does not belong to an active user who is authorised to randomise participants study at " . strtoupper($site) . ". Contact " . Config::get('prisms.OPERATOR_SUPERUSER') . "for more details";
                    $adm_msg = "The number " . $source . " tried randomising participants at " . strtoupper($site) . ". The user is unregistered";

                    Log::info("RANDOMISATION UPDATE: ".$adm_msg. "\n");

                    $result_sms = send_sms("SEARCHTrial",$adm_msg,Config::get('prisms.OPERATOR_SUPERUSER'),rand());
                    $data = $result_sms["data"];

                    $sent = new Sent();
                    $sent->timestamp = Carbon::now();
                    $sent->destination = $superuser;
                    $sent->text = $adm_msg;
                    $sent->status = $result_sms["message"];
                    $sent->message_id = $id;
                    $sent->unique_id = $data["uniqueId"];
                    $sent->saveOrFail();

                }

            }


            $result_sms = send_sms("SEARCHTrial",$reply,$source,rand());
            $data = $result_sms["data"];

            $sent = new Sent();
            $sent->timestamp = Carbon::now();
            $sent->destination = $source;
            $sent->text = $reply;
            $sent->status = $result_sms["message"];
            $sent->message_id = $id;
            $sent->unique_id = $data["uniqueId"];
            $sent->saveOrFail();

        }

    }
}
