<?php
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');
class FacilityDirectoryAction {
    public function process($ussdSession) {
        $menuItems = new MenuItems();
        $menuSuffix = "\n00 Home";
        $params = explode("*", $ussdSession->ussdProcessString);
        $lastSelection = trim($params[count($params) - 1]);
            if(MenuItems::MAINMENU_REQ == $ussdSession->previousFeedbackType){
                $ussdSession = $menuItems->setFacilityDirectoryRequest($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
            } elseif (MenuItems::FACILITY_DIRECTORY_REQ == $ussdSession->previousFeedbackType) {
                if (is_numeric($lastSelection) && $lastSelection >=1 && $lastSelection <= 3) {
                    $userParams = $ussdSession->userParams . UssdSession::FACILITY_DIRECTORY_ID . "=" . $lastSelection . "*";
                    $ussdSession->userParams = $userParams;
                    if("1"==$lastSelection){//Search by name
                        $ussdSession = $menuItems->setFacilityNameRequest($ussdSession);
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    }elseif("2"== $lastSelection){//Search by MFL code
                        $ussdSession = $menuItems->setMFLCodeRequest($ussdSession);    
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
                    }elseif("3"== $lastSelection){//Update Facility
                        $ussdSession = $menuItems->setAddClinicTypeRequest($ussdSession);    
                        $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;                     
                    }else{
                        $ussdSession = $menuItems->setFacilityDirectoryRequest($ussdSession);
                        $reply = "CON INVALID INPUT. Only number 1-3 allowed.\n" . $ussdSession->currentFeedbackString . $menuSuffix;              
                    }
                } else {
                    $reply = "END Connection error. Please try again.";
                }
                $ussdSession->currentFeedbackString = $reply;
                return $ussdSession;
              
         
            } elseif (MenuItems::FACILITY_NAME_REQ == $ussdSession->previousFeedbackType) {
                $facilityName = trim($params[count($params) - 1]);
                 if (isValidName($facilityName)) {
                    $userParams = $ussdSession->userParams . UssdSession::FACILITY_NAME_ID . "=" . $facilityName . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setSearchFacilityNameRequest($ussdSession,$facilityName);
                    $reply = "CON" . $ussdSession->currentFeedbackString. $menuSuffix;;
                    
                } else {
                    $ussdSession = $menuItems->setFacilityNameRequest($ussdSession);
                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }


              } elseif (MenuItems::CLINIC_TYPE_REQ == $ussdSession->previousFeedbackType) {
                $displayedClinicCodesArray = explode("#", UssdSession::getUserParam(UssdSession::CLINIC_TYPE_ID, $ussdSession->userParams));
                $clinicalTypeSize = count($displayedClinicCodesArray);
                if (is_numeric($lastSelection) && $lastSelection > 0 && $lastSelection <= $clinicalTypeSize) {
                    $userParams = $ussdSession->userParams . UssdSession::CLINIC_TYPE_ID . "=" . $clinicalTypeSize . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setPhoneNumberRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setAddClinicTypeRequest($ussdSession);
                    $reply = "CON INVALID INPUT. Select from 1-" . $clinicalTypeSize . ".\n" . $ussdSession->currentFeedbackString;
                }

            } elseif (MenuItems::PHONE_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $phone = trim($params[count($params) - 1]);
                 if (isValidPhone($phone)) {
                    $userParams = $ussdSession->userParams . UssdSession::PHONE_NUMBER_ID . "=" . $phone . "*";
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::updateFacility($ussdSession);
                } else {
                    $ussdSession = $menuItems->setPhoneNumberRequest($ussdSession);
                        $reply = "CON The Phone Number you have entered is INVALID .\n" . $ussdSession->currentFeedbackString;
                } 

            } elseif (MenuItems::MFL_CODE_REQ == $ussdSession->previousFeedbackType) {
                $mflCode = trim($params[count($params) - 1]);
                error_log("[ERROR : " . date("Y-m-d H:i:s") . "] query from safaricom \nParams=" . print_r($mflCode, true), 3, LOG_FILE);
                 if (isValidIdMFLCode($mflCode)) {
                    $userParams = $ussdSession->userParams . UssdSession::MFL_CODE_ID . "=" . $mflCode . "*";
                    $ussdSession->userParams = $userParams;
                   
                    $ussdSession = $menuItems->setSearchMFLCodeRequest($ussdSession,$mflCode);
                    $reply = "CON" . $ussdSession->currentFeedbackString. $menuSuffix;
                } else {
                    $ussdSession = $menuItems->setMFLCodeRequest($ussdSession);
                        $reply = "CON The code you entered is INVALID characters.\n" . $ussdSession->currentFeedbackString;
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