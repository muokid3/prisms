<?php
/**
 * Created by PhpStorm.
 * User: muoki
 * Date: 2019-10-09
 * Time: 15:46

 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

function send_sms($senderid ,$message,$phone,$orderId){

    $token = Config::get('prisms.TOKEN');

//    Log::info("Token:".$token);

    $DELIVERY_REPORT = Config::get('prisms.DELIVERY_REPORT');

    Log::info("DELIVERY_REPORT:".$DELIVERY_REPORT);


    $headers =  array( "Content-type: application/json", "Accept: application/json", "Authorization: Bearer ". $token );
    $update_request = array( "sender" =>   $senderid,
        "message" => $message,
        "phone"=> $phone,
        "correlator" => $orderId ,
        "endpoint"=>$DELIVERY_REPORT);

    Log::info("Sending SMS payload:::::::");
    Log::info(json_decode(json_encode($update_request),true));


    //$headers = json_encode($headers);
    //echo $headers; exit;

    //convert request to a json format.
    $update_request = json_encode($update_request);
    //echo $update_request; exit;
    //SMS API endpoint
    $bongatechEndPoint = "https://bulk.bongatech.co.ke/api/v1/send-sms";

    //echo $update_request;
    $curl = curl_init();


    // Set some options - we are passing in a user agent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers ,
        CURLOPT_URL => $bongatechEndPoint,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $update_request
    ));

    // Send the request & save response to $resp
    $response = curl_exec($curl);
    //echo $response;
    /* if (curl_errno($curl)) {
                 $response = array(
                     "ResponseCode" => "5001",
                     "ResponseDescription" => curl_error($curl)
                 );
                 return $response;
     }*/

    //echo $response;
    // Close request to clear up some resources
    curl_close($curl);

    // convert response back to json
    $result_sms = json_decode($response, true);

    Log::info("Bongatech Response:::::::");

    Log::info($result_sms);

    return $result_sms;



}

