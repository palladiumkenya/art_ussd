<?php
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');
class ClientAccessAction {
    public function process($ussdSession) {
        $menuItems = new MenuItems();
        $menuSuffix = "\n00 Home";
        $params = explode("*", $ussdSession->ussdProcessString);
        $lastSelection = trim($params[count($params) - 1]);
            if(MenuItems::MAINMENU_REQ == $ussdSession->previousFeedbackType){
                $ussdSession = $menuItems->setUniquePatientIdRequest($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
           } elseif (MenuItems::UNIQUE_PATIENT_ID_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $cccNumber = trim($params[count($params) - 1]);
                if (isValidIdCCCNumber($cccNumber)) {
                    $userParams = UssdSession::UNIQUE_PATIENT_ID_NUMBER_ID . "=" . $cccNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setDirectoryService($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setUniquePatientIdRequest($ussdSession);
                        $reply = "CON The Uniqie Patient Number is INVALID .\n" . $ussdSession->currentFeedbackString;
                }
         
            } elseif (MenuItems::DIRECTORY_SERVICE_REQ == $ussdSession->previousFeedbackType) {
                if (is_numeric($lastSelection) && $lastSelection >=1 && $lastSelection <= 2) {
                    $userParams = $ussdSession->userParams . UssdSession::DIRECTORY_SERVICE_ID . "=" . $lastSelection . "*";
                    $ussdSession->userParams = $userParams;
                    if ("1" == $lastSelection) {//Medication Directory
                        $ussdSession = $menuItems->setMedicationDelivery($ussdSession);
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    } elseif ("2" == $lastSelection) {//Facility Close by
                        $ussdSession = $menuItems->setMFLCodeRequest($ussdSession);
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    } else {
                        $ussdSession = $menuItems->setDirectoryService($ussdSession);
                        $reply = "CON INVALID INPUT. Only number 1-2 allowed.\n" . $ussdSession->currentFeedbackString . $menuSuffix;
                    }
                } else {
                    $reply = "END Connection error. Please try again.";
                }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;

            } elseif (MenuItems::MEDICATION_DELIVERY_REQ == $ussdSession->previousFeedbackType) {
                if (is_numeric($lastSelection) && $lastSelection >=1 && $lastSelection <= 2) {
                    $userParams = $ussdSession->userParams . UssdSession::MEDICATION_DELIVERY_ID . "=" . $lastSelection . "*";
                    $ussdSession->userParams = $userParams;
                    if ("1" == $lastSelection) {//Request Delivery
                        $ussdSession = $menuItems->setCountyLocationRequest($ussdSession);
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    } elseif ("2" == $lastSelection) {//Confirm Delivery
                        $ussdSession = $menuItems->setRidersCodeRequest($ussdSession);
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    } else {
                        $ussdSession = $menuItems->setMedicationDelivery($ussdSession);
                        $reply = "CON INVALID INPUT. Only number 1-2 allowed.\n" . $ussdSession->currentFeedbackString . $menuSuffix;
                    }
                } else {
                    $reply = "END Connection error. Please try again.";
                }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;
                
            } elseif (MenuItems::DELIVERY_COUNTY_LOCATION_REQ == $ussdSession->previousFeedbackType) {
                $location = trim($params[count($params) - 1]);
                if ($location) {
                    $userParams = UssdSession::DELIVERY_COUNTY_LOCATION_ID . "=" . $location . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setDeliveryLocationRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setCountyLocationRequest($ussdSession);

                        $reply = "CON The name you entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                }

             } elseif (MenuItems::DELIVERY_LOCATION_REQ == $ussdSession->previousFeedbackType) {
                $location = trim($params[count($params) - 1]);
                if ($location) {
                    $userParams = UssdSession::DELIVERY_LOCATION_ID . "=" . $location . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setConfirmMeditationDeliveryRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setDeliveryLocationRequest($ussdSession);

                        $reply = "CON The name you entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::CONFIRM_MEDICATION_DELIVERY_REQ == $ussdSession->previousFeedbackType) {
                 $location = trim($params[count($params) - 1]);
                  $county = trim($params[count($params) - 2]);
                 
                $displayedClinicCodesArray = explode("#", UssdSession::getUserParam(UssdSession::CONFIRM_MEDICATION_DELIVERY_ID, $ussdSession->userParams));
                $clinicalTypeSize = count($displayedClinicCodesArray);
                if (is_numeric($lastSelection) && $lastSelection > 0 && $lastSelection <= $clinicalTypeSize) {
 //$reply = "Please confirm that you want your medication delivered to \n".$location.", ".$county." County.";
                    if (is_numeric($lastSelection) && $lastSelection == 1) {

                        $userParams = $ussdSession->userParams . UssdSession::CONFIRM_MEDICATION_DELIVERY_ID . "=" . $clinicalTypeSize . "*";
                        $ussdSession->userParams = $userParams;
                        $reply = "END " . self::saveAcceptRef($ussdSession);
                    }else{
                         $reply = "END You have declined Referral. Thank you"; 
                    }
                } else {
                    $ussdSession = $menuItems->setConfirmMeditationDeliveryRequest($ussdSession);
                    $reply = "CON INVALID INPUT. Select from 1-" . $clinicalTypeSize . ".\n" . $ussdSession->currentFeedbackString;
                }

             } elseif (MenuItems::RIDERS_CODE_REQ == $ussdSession->previousFeedbackType) {
                $pin = trim($params[count($params) - 1]);
                if (isValidPIN($pin)) {
                    $userParams = $ussdSession->userParams . UssdSession::RIDERS_CODE_ID . "=" . $pin . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::registerNewUse($ussdSession);
                } else {
                    $ussdSession = $menuItems->setRidersCodeRequest($ussdSession);

                        $reply = "CON The Secret code is INVALID.\n" . $ussdSession->currentFeedbackString;
                }
            } else {
                    $reply = "END Connection error. Please try again.";
            }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;           
    }

    function mflCodeSearch($ussdSession){
        $ussdMfl = new UssdFacility();
        $ussdMfl->msisdn = $ussdSession->msisdn;
        $ussdMfl->mflCode = UssdSession::getUserParam(UssdSession::MFL_CODE_ID, $ussdSession->userParams);
        if(mflCodeSend($ussdMfl)){
            return "You have send MFL Code successfully!";  
        } else {
            return "There was an error in your request. Please try again.";           
        }
    }

    function updateFacility($ussdSession){
        $ussdFacility = new UssdFacility();
        $ussdFacility->msisdn = $ussdSession->msisdn;
        $ussdFacility->clinicalType = UssdSession::getUserParam(UssdSession::CLINIC_TYPE_ID, $ussdSession->userParams);
        $ussdFacility->phoneNumber = UssdSession::getUserParam(UssdSession::PHONE_NUMBER_ID, $ussdSession->userParams);
        if(updateFacility($ussdFacility)){
            return "You have updated Facility successfully!";  
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

    function facilityNameSearch($ussdSession){
        $ussdFacility = new UssdFacility();
        $ussdFacility->msisdn = $ussdSession->msisdn;
        $ussdFacility->facilityName = UssdSession::getUserParam(UssdSession::FACILITY_NAME_ID, $ussdSession->userParams);
        if(facilityNameSearch($ussdFacility)){
            return "You have send facility Name successfully!";  
        } else {
            return "There was an error in your request. Please try again.";           
        }
    }
}