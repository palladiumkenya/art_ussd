<?php
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');
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
                if (isValidIdCCCNumber($cccNumber)) {
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
                if (isValidIdMFLCode($mflCode)) {
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
                $phone = trim($params[count($params) - 1]);
                 if (isValidPhone($phone)) {
                    $userParams = $ussdSession->userParams . UssdSession::CURRENT_REGIMEN_ID . "=" . $phone . "*";
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::initialReference($ussdSession);
                } else {
                    $ussdSession = $menuItems->setCurrentRegimentRequest($ussdSession);
                        $reply = "CON The current regimen is INVALID .\n" . $ussdSession->currentFeedbackString;
                } 

         //    /////// Second option    
         } elseif (MenuItems::ACCEPT_REF_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 1]);
                if (isValidIdCCCNumber($cccNumber)) {
                    $userParams = UssdSession::ACCEPT_REF_CCC_NUMBER_ID . "=" . $cccNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setMoreOptionsRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setAcceptReferralRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::MORE_OPTIONS_REQ == $ussdSession->previousFeedbackType) {
                $displayedClinicCodesArray = explode("#", UssdSession::getUserParam(UssdSession::MORE_OPTIONS_ID, $ussdSession->userParams));
                $clinicalTypeSize = count($displayedClinicCodesArray);
                if (is_numeric($lastSelection) && $lastSelection > 0 && $lastSelection <= $clinicalTypeSize) {

                    if (is_numeric($lastSelection) && $lastSelection == 1) {
                        $userParams = $ussdSession->userParams . UssdSession::MORE_OPTIONS_ID . "=" . $clinicalTypeSize . "*";
                        $ussdSession->userParams = $userParams;
                        $reply = "END " . self::saveAcceptRef($ussdSession);
                    }else{
                         $reply = "END You have declined Referral. Thank you"; 
                    }
                } else {
                    $ussdSession = $menuItems->setMoreOptionsRequest($ussdSession);
                    $reply = "CON INVALID INPUT. Select from 1-" . $clinicalTypeSize . ".\n" . $ussdSession->currentFeedbackString;
                }

     ///// Third Option
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

            // } elseif (MenuItems::SECRET_PIN_REQ == $ussdSession->previousFeedbackType) {
            //     $mflCode = trim($params[count($params) - 2]);
            //     error_log("[ERROR : " . date("Y-m-d H:i:s") . "] query from safaricom \nParams=" . print_r($mflCode, true), 3, LOG_FILE);
            //      if (isValidPIN($mflCode)) {
            //         $userParams = $ussdSession->userParams . UssdSession::SECRET_PIN_ID . "=" . $mflCode . "*";
            //         $ussdSession->userParams = $userParams;
                   
            //         $ussdSession = $menuItems->setSearchPatientDetailsRequest($ussdSession,$mflCode);
            //         $reply = "CON" . $ussdSession->currentFeedbackString. $menuSuffix;
            //     } else {
            //         $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
            //             $reply = "CON The code you entered is INVALID characters.\n" . $ussdSession->currentFeedbackString;
            //     } 

            // } elseif (MenuItems::SECRET_PIN_REQ == $ussdSession->previousFeedbackType) {
            //     $phone = trim($params[count($params) - 1]);
            //      if (isValidPIN($phone)) {
            //         $userParams = $ussdSession->userParams . UssdSession::SECRET_PIN_ID . "=" . $phone . "*";
            //         $ussdSession->userParams = $userParams;
            //         $reply = "END " . self::setSearchPatientDetailsRequest($ussdSession,$phone);
            //     } else {
            //         $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
            //             $reply = "CON The CCC Number you have entered is INVALID.\n" . $ussdSession->currentFeedbackString;
            //     } 
           // } elseif (MenuItems::PATIENT_DETAILS_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
           //      $cccNumber = trim($params[count($params) - 1]);
           //      if (isValidIdCCCNumber($cccNumber)) {
           //          $userParams = UssdSession::PATIENT_DETAILS_CCC_NUMBER_ID . "=" . $cccNumber . "*";
           //          $ussdSession->userParams = $userParams;
           //          $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
           //          $reply = "CON " . $ussdSession->currentFeedbackString;
           //      } else {
           //          $ussdSession = $menuItems->setPatientDetailsRequest($ussdSession);
           //              $reply = "CON The CCC Number you have entered is INVALID.\n" . $ussdSession->currentFeedbackString;
           //      }

           } elseif (MenuItems::SECRET_PIN_REQ == $ussdSession->previousFeedbackType) {
                      $cccNumber = trim($params[count($params) - 2]);
                       $phone = trim($params[count($params) - 1]); 
                       $ratesList = getDateCreated($ussdSession->msisdn);
                       $dateCreated = $ratesList[0]->created_date;            
                       $dateCreated1 = new DateTime($dateCreated);
                       $now = new DateTime();
                       $dateDiff = $dateCreated1->diff($now)->format("%d");  
                      if($dateDiff < 7){
                       
                      if (isValidPIN($cccNumber)) {
                        $userParams = $ussdSession->userParams . UssdSession::SECRET_PIN_ID . "=" . $cccNumber . "*";
                        $ussdSession = $menuItems->setSearchPatientDetailsRequest($ussdSession,$cccNumber);
                        $reply = "END" . $ussdSession->currentFeedbackString;          
                       } else {
                            $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
                            $reply = "CON The Pin you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                       } 

                     }else{
                       $reply = "END Your secret pin is expired.\n Please generate new pin";
                     }     

///Fouth option
           } elseif (MenuItems::TRANSIT_CLIENT_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 1]);
                if (isValidIdCCCNumber($cccNumber)) {
                    $userParams = UssdSession::TRANSIT_CLIENT_CCC_NUMBER_ID . "=" . $cccNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setNumberOfDaysRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setTransitClientRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::NUMBER_OF_DAYS_REQ == $ussdSession->previousFeedbackType) {
                $numberOfDays = trim($params[count($params) - 1]);
                 if (isValidNumberOfdays($numberOfDays)) {
                    $userParams = $ussdSession->userParams . UssdSession::NUMBER_OF_DAYS_ID . "=" . $numberOfDays . "*";
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::transit($ussdSession);
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



 

    function initialReference($ussdSession){
        $ussdUser = new UssdFacility();
        $ussdUser->msisdn = $ussdSession->msisdn;
        $ussdUser->cccNumber = UssdSession::getUserParam(UssdSession::INITIAL_REF_CCC_NUMBER_ID, $ussdSession->userParams);
        $ussdUser->mflCode = UssdSession::getUserParam(UssdSession::INIT_MFL_CODE_ID, $ussdSession->userParams);
        $ussdUser->daysOfAppointment = UssdSession::getUserParam(UssdSession::APPOINTMENT_DATE_ID, $ussdSession->userParams);
        $ussdUser->currentRegime = UssdSession::getUserParam(UssdSession::CURRENT_REGIMEN_ID, $ussdSession->userParams);
        if(initialReference($ussdUser)){
            return "You have send Initial Referral send successfully!";  
        } else {
            return "There was an error in your request. Please try again.";           
        }
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

    function transit($ussdSession){
        $ussdUser = new UssdFacility();
         $ussdUser->msisdn = $ussdSession->msisdn;
        $ussdUser->cccNumber = UssdSession::getUserParam(UssdSession::TRANSIT_CLIENT_CCC_NUMBER_ID, $ussdSession->userParams);
        $ussdUser->numberOfDrugs = UssdSession::getUserParam(UssdSession::NUMBER_OF_DAYS_ID, $ussdSession->userParams);
        if(transit($ussdUser)){
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