<?php
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');
include_once('sms_gateway.php');
class ReferralServicesAction {
    public function process($ussdSession) {
        $menuItems = new MenuItems();
        $menuSuffix = "\n00 Home";
        $params = explode("*", $ussdSession->ussdProcessString);
        $lastSelection = trim($params[count($params) - 1]);
            if(MenuItems::MAINMENU_REQ == $ussdSession->previousFeedbackType){
                $ussdSession = $menuItems->setReferralServicesRequest($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
            } elseif (MenuItems::REFERRAL_SERVICES_REQ == $ussdSession->previousFeedbackType) {
                if (is_numeric($lastSelection) && $lastSelection >=1 && $lastSelection <= 4) {
                    $userParams = $ussdSession->userParams . UssdSession::REFERRAL_SERVICES_ID . "=" . $lastSelection . "*";
                    $ussdSession->userParams = $userParams;
                    if("1"==$lastSelection){//Initial Referal
                        $ussdSession = $menuItems->setInitialReferralRequest($ussdSession);
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    }elseif("2"== $lastSelection){//Accept Referrals
                        $ussdSession = $menuItems->setAcceptReferralRequest($ussdSession);    
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    }elseif("3"== $lastSelection){//Get Patient Details  
                        $ussdSession = $menuItems->setPatientDetailsRequest($ussdSession);    
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;  
                     }elseif("4"== $lastSelection){//Transit Clients  
                        $ussdSession = $menuItems->setTransitClientRequest($ussdSession);    
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;                     
                    }else{
                        $ussdSession = $menuItems->setReferralServicesRequest($ussdSession);
                        $reply = "CON INVALID INPUT. Only number 1-4 allowed.\n" . $ussdSession->currentFeedbackString . $menuSuffix;              
                    }
                } else {
                    $reply = "END Connection error. Please try again.";
                }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;
              
         
           } elseif (MenuItems::INITIAL_REF_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 1]);
                if (isValidIdCCCNumber($cccNumber) && !empty(checkCccNumber($cccNumber)) ){
                    $userParams = UssdSession::INITIAL_REF_CCC_NUMBER_ID . "=" . $cccNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setFacilityInitMFLCodeRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setInitialReferralRequest($ussdSession);
                        $reply = "CON The CCC Number is INVALID .\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::INIT_MFL_CODE_REQ == $ussdSession->previousFeedbackType) {
                $mflCode = trim($params[count($params) - 1]);

                if (isValidIdMFLCode($mflCode) && !empty(checkMflCode($mflCode)) ){
                    $userParams = $ussdSession->userParams . UssdSession::INIT_MFL_CODE_ID . "=" . $mflCode . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setAppoinmentDateRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setFacilityInitMFLCodeRequest($ussdSession);
                        $reply = "CON The MFL Code is INVALID.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::APPOINTMENT_DATE_REQ == $ussdSession->previousFeedbackType) {
                $days = trim($params[count($params) - 1]);
                if (isAppointmentDate($days)) {
                    $userParams = $ussdSession->userParams . UssdSession::APPOINTMENT_DATE_ID . "=" . $days . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setCurrentRegimentRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setAppoinmentDateRequest($ussdSession);
                        $reply = "CON The appointment date is INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }

           } elseif (MenuItems::CURRENT_REGIMEN_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 4]);
                $mflCode = trim($params[count($params) - 3]);
                $apptDate = trim($params[count($params) - 2]);
                $regiment = trim($params[count($params) - 1]);
                
                 if (is_numeric($regiment)) {
                    $userParams = $ussdSession->userParams . UssdSession::CURRENT_REGIMEN_ID . "=" . $regiment . "*";
                    $ussdSession->userParams = $userParams;
                

                    $details_facility=initiate_referal($ussdSession, $cccNumber, $mflCode,$apptDate,$regiment);
                   //$provider_facility=initiate_referals_details($ussdSession, $cccNumber, $mflCode,$regiment);
                    
                    $reply = "END Client with UPN ".$cccNumber. " was succesfully reffered to  ".$details_facility[0]['facility_name'].". CCC Clinic Number ".$details_facility[0]['telephone'].". In case of any queries call 0800722440 for free!";
                     $send_msg= new _sender();
                       $msg =  "Client with UPN ".$cccNumber. " was succesfully reffered to  ".$details_facility[0]['facility_name'].". CCC Clinic Number ".$details_facility[0]['telephone'].". MOH";

                          $resurn_msg=$send_msg->sendSMS($_ENV['SENDER_URL'],
                          $msg,
                          $details_facility[0]['telephone'], 
                        $_ENV['SHORTCODE'],
                          $_ENV['API-TOKEN'],'REF_INITIATE',$ussdSession );


                         $resurn_msg=$send_msg->sendSMS($_ENV['SENDER_URL'],
                          $msg,
                          $ussdSession->msisdn ,
                          $_ENV['SHORTCODE'],
                          $_ENV['API-TOKEN'],'REF_INITIATE',$ussdSession );


                
                } else {
                    $ussdSession = $menuItems->setCurrentRegimentRequest($ussdSession);
                        $reply = "CON The current regimen is INVALID .\n" . $ussdSession->currentFeedbackString;
                } 

         //    /////// Second option    
         } elseif (MenuItems::ACCEPT_REF_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 1]);
                if (isValidIdCCCNumber($cccNumber) && !empty(checkCccNumber($cccNumber)) ){
                    $userParams = UssdSession::ACCEPT_REF_CCC_NUMBER_ID . "=" . $cccNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setMoreOptionsRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setAcceptReferralRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::MORE_OPTIONS_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 2]);
                $displayedClinicCodesArray = explode("#", UssdSession::getUserParam(UssdSession::MORE_OPTIONS_ID, $ussdSession->userParams));
                $clinicalTypeSize = count($displayedClinicCodesArray);
                if (is_numeric($lastSelection) && $lastSelection > 0 && $lastSelection <= $clinicalTypeSize) {

                    if (is_numeric($lastSelection) && $lastSelection == 1) {
                        $userParams = $ussdSession->userParams . UssdSession::MORE_OPTIONS_ID . "=" . $clinicalTypeSize . "*";
                        $ussdSession->userParams = $userParams;
                        $details_facility = saveAcceptRef($ussdSession, $cccNumber);   

                        //print_r($details_facility);exit();                
                    
                        $reply = "END Client with UPN ".$cccNumber. " was succesfully reffered to  ".$details_facility[0]['facility_name'].". CCC Clinic Number ".$details_facility[0]['telephone'].". In case of any queries call 0800722440 for free!";
                        $send_msg= new _sender();
                        $msg =  "Client with UPN ".$cccNumber. " was succesfully reffered to  ".$details_facility[0]['facility_name'].". CCC Clinic Number ".$details_facility[0]['telephone'].". MOH";

                          $resurn_msg=$send_msg->sendSMS($_ENV['SENDER_URL'],
                          $msg,
                          $details_facility[0]['telephone'], 
                          $_ENV['SHORTCODE'],
                          $_ENV['API-TOKEN'],'REF_ACCEPT',$ussdSession);

                          $resurn_msg=$send_msg->sendSMS($_ENV['SENDER_URL'],
                          $msg,
                          $ussdSession->msisdn ,
                          $_ENV['SHORTCODE'],
                          $_ENV['API-TOKEN'],'REF_ACCEPT',$ussdSession);

                    }else{


                         $reply = "END You have declined with UPN ".$cccNumber.". In case of any queries call 0800722440 for free.MOH"; 

                         

                         //
                    }
                } else {
                    $ussdSession = $menuItems->setMoreOptionsRequest($ussdSession);
                    $reply = "CON INVALID INPUT. Select from 1-" . $clinicalTypeSize . ".\n" . $ussdSession->currentFeedbackString;
                }

     ///// Third Option - Search Patient 
            } elseif (MenuItems::PATIENT_DETAILS_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $phone = trim($params[count($params) - 1]);
                 if (isValidIdCCCNumber($phone)) {
                    $userParams = $ussdSession->userParams . UssdSession::PATIENT_DETAILS_CCC_NUMBER_ID . "=" . $phone . "*";
                    $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setPatientDetailsRequest($ussdSession);
                        $reply = "CON The CCC Number you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                } 

           } elseif (MenuItems::SECRET_PIN_REQ == $ussdSession->previousFeedbackType) {
                      $cccNumber = trim($params[count($params) - 2]); 
                       $pin = trim($params[count($params) - 1]); 
                       $ratesList = getDateCreated($ussdSession->msisdn);
                        if(empty($ratesList[0]->created_date))
                            {
                               $dateCreated  ='1900-01-01';
                            }else
                            {
                               $dateCreated =$ratesList[0]->created_date; 
                            }                       
                       $dateCreated1 = date('Y-m-d', strtotime($dateCreated));
                       $now = date('Y-m-d');  
                       $dateDiff=date_diff(date_create($dateCreated1),date_create($now))->format('%a');
                    
                      if($dateDiff < 7){
                              if ((isValidPIN($pin)) && ($pin==$ratesList[0]->pin)) {
                                $userParams = $ussdSession->userParams . UssdSession::SECRET_PIN_ID . "=" . $cccNumber . "*";
                                $ussdSession = $menuItems->setSearchPatientDetailsRequest($ussdSession,$cccNumber);
                                $reply = "END " . $ussdSession->currentFeedbackString;          
                               } else {
                                    $reply = "CON The Pin you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                               } 

                     }else{
                       $reply = "END Your secret pin is expired.\n Please generate new pin";
                     }     

///Fouth option - Transit Client
           } elseif (MenuItems::TRANSIT_CLIENT_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 1]);
                if (isValidIdCCCNumber($cccNumber) && !empty(checkCccNumber($cccNumber)) ){

                    // print_r($demi); exit();
                    $userParams = UssdSession::TRANSIT_CLIENT_CCC_NUMBER_ID . "=" . $cccNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setNumberOfDaysRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setTransitClientRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters or does not exist.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::NUMBER_OF_DAYS_REQ == $ussdSession->previousFeedbackType) {
                $numberOfDays = trim($params[count($params) - 1]);
                $cccNumber = trim($params[count($params) - 2]);
               
                 if (isValidNumberOfdays($numberOfDays)) {
                    $userParams = $ussdSession->userParams . UssdSession::NUMBER_OF_DAYS_ID . "=" . $numberOfDays . "*";
                    $ussdSession->userParams = $userParams;


                    $details_facility=transit($ussdSession, $cccNumber, $numberOfDays);
                    $provider_facility=get_provider_location_details($ussdSession, $cccNumber, $numberOfDays);
                    
                   $reply = "END Your have succesfully recorded transit details of client with UPN ".$cccNumber.". In case of any queries call 0800722440 for free!";
                          $send_msg= new _sender();
                       $msg =  "Dear Provider, your client with UPN  ".$cccNumber." has received a refill at ".$provider_facility[0]['facility_name']." to last  them ".$numberOfDays."  days. CCC Mobile Number  is ".$provider_facility[0]['telephone']." .MOH";

                          $resurn_msg=$send_msg->sendSMS($_ENV['SENDER_URL'],
                          $msg,
                          $details_facility[0]['telephone'], 
                          $_ENV['SHORTCODE'],
                          $_ENV['API-TOKEN'],'REF_TRANSIT',$ussdSession);
                } else {
                    $ussdSession = $menuItems->setNumberOfDaysRequest($ussdSession);
                        $reply = "CON The Number you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                } 


            } else {
                    $reply = "END Connection error. Please try again.";
            }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;           
    }


    function secretPin($ussdSession){
        $ussdUser = new UssdFacility();
         $ussdUser->msisdn = $ussdSession->msisdn;
        $ussdUser->cccNumber = UssdSession::getUserParam(UssdSession::PATIENT_DETAILS_CCC_NUMBER_ID, $ussdSession->userParams);
        $ussdUser->pin = UssdSession::getUserParam(UssdSession::SECRET_PIN_ID, $ussdSession->userParams);
        if(secretPin($ussdUser)){
            return "You have send a request successfully!";  
        } else {
            return "There was an error in your request. Please try again.";           
        }
    }



    function saveAcceptRef($ussdSession){
        $ussdUser = new UssdFacility();
         $ussdUser->msisdn = $ussdSession->msisdn;
        $ussdUser->cccNumber = UssdSession::getUserParam(UssdSession::ACCEPT_REF_CCC_NUMBER_ID, $ussdSession->userParams);
        $ussdUser->optionType = UssdSession::getUserParam(UssdSession::MORE_OPTIONS_ID, $ussdSession->userParams);
        if(secretPin($ussdUser)){
            return "You have send a request successfully!";  
        } else {
            return "There was an error in your request. Please try again.";           
        }
    }
  
}