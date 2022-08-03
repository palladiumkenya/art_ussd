<?php
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');
include_once('sms_gateway.php');
class GenerateSecretPin {
    public function process($ussdSession) {
        $menuItems = new MenuItems();
        $menuSuffix = "\n00 Home";
        $params = explode("*", $ussdSession->ussdProcessString);
        $lastSelection = trim($params[count($params) - 1]);
            if(MenuItems::MAINMENU_REQ == $ussdSession->previousFeedbackType){
                $ussdSession = $menuItems->setGenPassword($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
             } elseif (MenuItems::GENERATE_PIN_REQ == $ussdSession->previousFeedbackType) {
                $displayedClinicCodesArray = explode("#", UssdSession::getUserParam(UssdSession::GENERATE_PIN_ID, $ussdSession->userParams));
                $clinicalTypeSize = count($displayedClinicCodesArray);
                if (is_numeric($lastSelection) && $lastSelection > 0 && $lastSelection <= $clinicalTypeSize) {
                    if (is_numeric($lastSelection) && $lastSelection == 1) {
                          $userParams = $ussdSession->userParams . UssdSession::GENERATE_PIN_ID . "=" . $clinicalTypeSize . "*";
                           $ussdSession->userParams = $userParams;
                          $reply = "END Your request was sent successfully. Check your SMS. In case of any queries call 0800722440 for free. " . self::generatePin($ussdSession);
                    }else{
                         $reply = "END You have declined pin generation. Thank you"; 
                    }
                } else {
                    $ussdSession = $menuItems->setGenPassword($ussdSession);
                    $reply = "CON INVALID INPUT. Select from 1-" . $clinicalTypeSize . ".\n" . $ussdSession->currentFeedbackString;
                }
            } else {
                    $reply = "END Connection error. Please try again.";
            }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;           
    }

    function generatePin($ussdSession){
       // $ussdUser = new FacilityQuery();
        //print_r($ussdSession); exit();
        $pin=generatePin($ussdSession->msisdn);
        //$ussdUser->msisdn = $ussdSession->msisdn;
       // $ussdUser->pin = rand(1000,9999);
        if($pin>0){
           // return "You have Generated Secret pin successfully!";  
            $send_msg= new _sender();
            
                $msg="Dear Provider, your secret pin is ". $pin ." .The pin will be required when accessing patient details. Kindly note pin expires after 7 days. MOH";
                $resurn_msg=$send_msg->sendSMS($_ENV['SENDER_URL'],
                $msg,
                $ussdSession->msisdn, 
                $_ENV['SHORTCODE'],
                $_ENV['API-TOKEN']); 
            
        } else {
            return "END There was an error in your request. Please try again.";           
        }
    }

  
}