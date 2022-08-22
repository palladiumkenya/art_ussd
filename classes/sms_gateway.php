<?php
include_once('./QueryManager.php');
//Central SMS sender class for the USSD ART & Referral Services
class _sender{
    public function sendSMS($url_gateway, $message,$destination, $shorcode, $key, $msg_type, $ussdUser){

        $url = $url_gateway;
    
        $curl = curl_init();
        $headers = [
            'Content-Type:application/json',
            'api-token:'.$key];
   
 
        $fields = array(
            'destination' => $destination,
            'msg' =>$message,
            'sender_id' => $destination,
            'gateway' => $shorcode
        );
         
        //$fields_string = http_build_query($fields);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         
        $data=curl_exec($curl);
        
         if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            $data=''; 
        }
        curl_close($curl);
      
         //Log Message
         log_sms_sent($ussdUser,$msg_type, $message, $data);
    
    }
}

?>