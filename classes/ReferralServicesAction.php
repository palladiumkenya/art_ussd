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
                    $ussdSession = $menuItems->setFacilityMFLCodeRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setInitialReferralRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::MFL_CODE_REQ == $ussdSession->previousFeedbackType) {
                $mflCode = trim($params[count($params) - 1]);
                if (isValidIdMFLCode($mflCode)) {
                    $userParams = $ussdSession->userParams . UssdSession::MFL_CODE_ID . "=" . $mflCode . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setAppoinmentDateRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setFacilityMFLCodeRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
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
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }

           } elseif (MenuItems::CURRENT_REGIMEN_REQ == $ussdSession->previousFeedbackType) {
                $phone = trim($params[count($params) - 1]);
                 if (isValidPhone($phone)) {
                    $userParams = $ussdSession->userParams . UssdSession::CURRENT_REGIMEN_ID . "=" . $phone . "*";
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::initialReference($ussdSession);
                } else {
                    $ussdSession = $menuItems->setCurrentRegimentRequest($ussdSession);
                        $reply = "CON The Phone Number you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                } 

            /////// Second option    
     



     ///// Third Option
           } elseif (MenuItems::PATIENT_DETAILS_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 1]);
                if (isValidIdCCCNumber($cccNumber)) {
                    $userParams = UssdSession::PATIENT_DETAILS_CCC_NUMBER_ID . "=" . $cccNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setTransitClientRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }

           } elseif (MenuItems::SECRET_PIN_REQ == $ussdSession->previousFeedbackType) {
                $phone = trim($params[count($params) - 1]);
                 if (isValidPIN($phone)) {
                    $userParams = $ussdSession->userParams . UssdSession::SECRET_PIN_ID . "=" . $phone . "*";
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::secretPin($ussdSession);
                } else {
                    $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
                        $reply = "CON The Phone Number you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
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
                    $ussdSession = $menuItems->setPatientDetailsRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::NUMBER_OF_DAYS_REQ == $ussdSession->previousFeedbackType) {
                $numberOfDays = trim($params[count($params) - 1]);
                 if (isValidNumberOfdays($numberOfDays)) {
                    $userParams = $ussdSession->userParams . UssdSession::NUMBER_OF_DAYS_ID . "=" . $numberOfDays . "*";
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::numberOfDays($ussdSession);
                } else {
                    $ussdSession = $menuItems->setSecretPinRequest($ussdSession);
                        $reply = "CON The Number you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                } 


            } else {
                    $reply = "END Connection error. Please try again.";
            }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;           
    }

   public function secretPin($ussdSession)
   {
      
   }

      public function numberOfDays($ussdSession)
   {
      
   }

    function initialReference($ussdSession){
        $ussdUser = new UssdUser();
        $ussdUser->msisdn = $ussdSession->msisdn;
        $ussdUser->mflCode = UssdSession::getUserParam(UssdSession::MFL_CODE_ID, $ussdSession->userParams);
        if(mflCodeSend($ussdUser)){
            return "You have send MFL Code successfully!";  
        } else {
            return "There was an error in your request. Please try again.";           
        }
    }


  
}