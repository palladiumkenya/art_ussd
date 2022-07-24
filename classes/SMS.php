<?php
//Central SMS sender class for the USSD ART & Referral Services
class _sender{
    public function sendSMS($url_gateway, $message,$destination, $shorcode, $key){

        $url = $url_gateway;
    
        $curl = curl_init();
        $headers = [
            'Content-Type:application/json',
            'api-token: $key'];
   
 
        $fields = array(
            'destination' => $destination,
            'msg' =>$message,
            'sender_id' => $destination,
            'gateway' => $shorcode
        );
         
        $fields_string = http_build_query($fields);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
         
        $data = curl_exec($curl);
        
         if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
           return $error_msg;
        }
       return $data;
    
    }
}

?>