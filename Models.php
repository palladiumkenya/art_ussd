<?php
class UssdSession {
    var $sessionId;
    var $msisdn;
    var $ussdCode;
    var $ussdString;
    var $ussdStringPrefix;
    var $ussdProcessString;
    var $previousFeedbackType;
    var $currentFeedbackString;
    var $currentFeedbackType;
    var $startTime;
    var $userParams;
    var $test;

    const MENU_ID = "MENU_ID";
    const USER_ID = "USER_ID";
    const CCCNUMBER = "CCCNUMBER";
    const NOT_FOUND = "NOT_FOUND";
    const MYACCOUNT_LIST_IDS = "MYACCOUNT_LIST_IDS";




    const MYACCOUNT_CATEGORY_ID = "MYACCOUNT_CATEGORY_ID";
    const MAINMENU_ID = "MAINMENU_ID";
    const TRANSIT_CLIENT_ID = "TRANSIT_CLIENT_ID";
    const CCC_NUMBER_ID = "CCC_NUMBER_ID";
    const NUMBER_OF_DAYS_FOR_DRUG_ID = "NUMBER_OF_DAYS_FOR_DRUG_ID";
    const PATIENT_DETAILS_ID = "PATIENT_DETAILS_ID";
    const SECRET_PIN_ID = "SECRET_PIN_ID";
    const ACCEPT_REFERRAL_ID = "ACCEPT_REFERRAL_ID";
    const DECLINE_REFERRAL_ID = "DECLINE_REFERRAL_ID";
    const FACILITY_DIRECTORY_ID = "FACILITY_DIRECTORY_ID";
    const REFERRAL_SERVICES_ID = "REFERRAL_SERVICES_ID";
    const UPDATE_FACILITY_DETAILS_ID = "UPDATE_FACILITY_DETAILS_ID";
    const CLINIC_TYPE_ID = "CLINIC_TYPE_ID";
    const PHONE_NUMBER_ID = "PHONE_NUMBER_ID";
    const MFL_CODE_ID = "MFL_CODE_ID";
    const INIT_MFL_CODE_ID = "INIT_MFL_CODE_ID";
    const GENERATE_PIN_ID = "GENERATE_PIN_ID";
    const FACILITY_NAME_ID = "FACILITY_NAME_ID";
    const INITIATE_REFERRAL_ID = "INITIATE_REFERRAL_ID";
    const APPOINTMENT_DATE_ID = "APPOINTMENT_DATE_ID";
    const CURRENT_REGIMEN_ID = "CURRENT_REGIMEN_ID";
    const AUTHORISATION_ID = "AUTHORISATION_ID";
    const TRANSIT_CLIENT_CCC_NUMBER_ID = "TRANSIT_CLIENT_CCC_NUMBER_ID";
    const PATIENT_DETAILS_CCC_NUMBER_ID = "PATIENT_DETAILS_CCC_NUMBER_ID";
    const ACCEPT_REF_CCC_NUMBER_ID = "ACCEPT_REF_CCC_NUMBER_ID";
    const INITIAL_REF_CCC_NUMBER_ID = "INITIAL_REF_CCC_NUMBER_ID";
    const MORE_OPTIONS_ID = "MORE_OPTIONS_ID";
    const NUMBER_OF_DAYS_ID = "NUMBER_OF_DAYS_ID";
    const FIRSTNAME = "FIRSTNAME";
    const LASTNAME ="LASTNAME";
    const IDNUMBER = "IDNUMBER";
    const PIN_ID = "PIN_ID";
    const MFL_CODE_REQUEST_ID = "MFL_CODE_REQUEST_ID";
    const PROVIDER_ACCESS_ID = "PROVIDER_ACCESS_ID";
    const UNIQUE_PATIENT_ID_NUMBER_ID = "UNIQUE_PATIENT_ID_NUMBER_ID";
    const DIRECTORY_SERVICE_ID = "DIRECTORY_SERVICE_ID";
    const MEDICATION_DELIVERY_ID = "MEDICATION_DELIVERY_ID";
    const DELIVERY_LOCATION_ID = "DELIVERY_LOCATION_ID";
    const DELIVERY_COUNTY_LOCATION_ID = "DELIVERY_COUNTY_LOCATION_ID";
    const CONFIRM_MEDICATION_DELIVERY_ID = "CONFIRM_MEDICATION_DELIVERY_ID"; 
    const RIDERS_CODE_ID = "RIDERS_CODE_ID"; 

    


    

    

 
    
    public static function getUserParam($paramName, $userParams) {
        $params = explode("*", $userParams);
        //get latest input
        for ($i = count($params) - 1; $i > -1; $i--) {
            $keyValue = explode("=", $params[$i]);
            if ($paramName == $keyValue[0]) {
                return $keyValue[1];
            }
        }
        return self::NOT_FOUND;
    }
}
    class ClinicType {
        var $type_id;
        var $type_desc;
    }
class OptionType {
        var $type_id;
        var $type_desc;
}

class RegimenType {
    var $regimen_id;
    var $regimen_desc;
}


class UssdUser {
    var $user_id;
    var $msisdn;
    var $mfl_code;
    var $firstName;
    var $lastName;
    var $idNumber;
    var $dateCreated;

}

class UssdFacility {
    var $id;
    var $msisdn;
    var $name;
    var $phoneNumber;
    var $code;
    var $ContactDetails;
    var $daysOfAppointment;
    var $currentRegime;
    var $pin;
    var $upn;
    var $numberOfDrugs;
    var $optionType;
    var $cccNumber;

}

class FacilityQuery {
    var $initiator_id;
    var $facility_mfl;
    var $facility_name;
    var $phone_no;
    var $pin;

}
