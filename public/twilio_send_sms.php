<?php
 

    require "inc/twilio/Services/Twilio.php";
 
    // Twilio AccountSid and AuthToken 
    // TODO:  move to env var's
    $AccountSid = "ACef393eb2920ecf8b1719213552535227";
    $AuthToken = "517083e2d3a0c27e2185fc3cd1e678f3";
 
    // instantiate a new Twilio Rest Client
    $client = new Services_Twilio($AccountSid, $AuthToken);
 
 



    public function send_sms_message($from_cl_tid, $recipient_uids, $message){
        // make an array of people we know, to send them a message. 
        // Feel free to change/add your own phone number and name here.
        $people = array(
            "+17327717293" => "Eddie White",
            "+16465040127" => "Eddie Google",
        );
     
        
        foreach ($people as $number => $name) {
     
            $sms = $client->account->messages->sendMessage(
                // 'From' number  
                "754-600-4893", 
                // the number we are sending to - Any phone number
                $number,
                // the sms body
                "Hey $name, Monkey Party at 6PM. Bring Bananas!"
            );
         
       }
}


