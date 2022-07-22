<?php

include_once('UssdUtils.php');
include_once('./QueryManager.php');

class MenuItems {
	const MYACCOUNT_CATEGORY_REQ = "MYACCOUNT_CATEGORY_REQ";
	const MAINMENU_REQ = "MAINMENU_REQ";
	const TRANSIT_CLIENT_REQ = "TRANSIT_CLIENT_REQ";
	const CCC_NUMBER_REQ = "CCC_NUMBER_REQ";
	const NUMBER_OF_DAYS_FOR_DRUG_REQ = "NUMBER_OF_DAYS_FOR_DRUG_REQ";
	const PATIENT_DETAILS_REQ = "PATIENT_DETAILS_REQ";
	const SECRET_PIN_REQ = "SECRET_PIN_REQ";
	const ACCEPT_REFERRAL_REQ = "ACCEPT_REFERRAL_REQ";
	const DECLINE_REFERRAL_REQ = "DECLINE_REFERRAL_REQ";
	const FACILITY_DIRECTORY_REQ = "FACILITY_DIRECTORY_REQ";
	const REFERRAL_SERVICES_REQ = "REFERRAL_SERVICES_REQ";
	const UPDATE_FACILITY_DETAILS_REQ = "UPDATE_FACILITY_DETAILS_REQ";
	const CLINIC_TYPE_REQ = "CLINIC_TYPE_REQ";
	const PHONE_NUMBER_REQ = "PHONE_NUMBER_REQ";
	const MFL_CODE_REQ = "MFL_CODE_REQ";
    const INIT_MFL_CODE_REQ = "INIT_MFL_CODE_REQ";

	const FACILITY_NAME_REQ = "FACILITY_NAME_REQ";
	const INITIATE_REFERRAL_REQ = "INITIATE_REFERRAL_REQ";
	const APPOINTMENT_DATE_REQ = "APPOINTMENT_DATE_REQ";
	const CURRENT_REGIMEN_REQ = "CURRENT_REGIMEN_REQ";
	const AUTHORISATION_REQ = "AUTHORISATION_REQ";
    const TRANSIT_CLIENT_CCC_NUMBER_REQ = "TRANSIT_CLIENT_CCC_NUMBER_REQ";
    const PATIENT_DETAILS_CCC_NUMBER_REQ = "PATIENT_DETAILS_CCC_NUMBER_REQ";
    const ACCEPT_REF_CCC_NUMBER_REQ = "ACCEPT_REF_CCC_NUMBER_REQ";
    const INITIAL_REF_CCC_NUMBER_REQ = "INITIAL_REF_CCC_NUMBER_REQ";
    const MORE_OPTIONS_REQ = "MORE_OPTIONS_REQ";
    const NUMBER_OF_DAYS_REQ = "NUMBER_OF_DAYS_REQ";
    const FIRSTNAME_REQ = "FIRSTNAME_REQ";
    const LASTNAME_REQ = "LASTNAME_REQ";
    const ID_NUMBER_REQ = "ID_NUMBER_REQ";
    const GENERATE_PIN_REQ = "GENERATE_PIN_REQ";
    const PIN_REQ = "PIN_REQ";
    const MFL_CODE_REQUEST = "MFL_CODE_REQUEST";
    const PATIENT_DETAILS_SEARCH_REQUEST = "PATIENT_DETAILS_SEARCH_REQUEST";
    const TRANSIT_SEARCH_REQUEST = "TRANSIT_SEARCH_REQUEST";

	var $reply;
    var $userParams;


    public function setFirstNameRequest($ussdSession) {

        $ussdSession->currentFeedbackString = "Enter your First Name to register for this service:";
        $ussdSession->currentFeedbackType = self::FIRSTNAME_REQ;
        return $ussdSession;
    }

    public function setLastNameRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter your Last Name:";
        $ussdSession->currentFeedbackType = self::LASTNAME_REQ;
        return $ussdSession;
    }

    public function setIdNumberRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter your ID/Passport number:";
        $ussdSession->currentFeedbackType = self::ID_NUMBER_REQ;
        return $ussdSession;
    }

     public function setMainMenu($ussdSession) {
        $userId = UssdSession::getUserParam(UssdSession::USER_ID, $ussdSession->userParams);

        $userParams = UssdSession::USER_ID . "=" . $userId . "*";
        $ussdSession->userParams = $userParams;

        $menuArray = array("Facility Directory", "Referral Services", "Generate Secret pin");
        $ussdSession->currentFeedbackString = "Select one:\n" . generateMenu($menuArray);
//        }
        $ussdSession->currentFeedbackType = self::MAINMENU_REQ;
        return $ussdSession;
    }
    public function setFacilityDirectoryRequest($ussdSession) {
        $menuArray = array("Search By Name", "Search BY MFL Code", "Update Facility");
        $ussdSession->currentFeedbackString = "Select one:\n" . generateMenu($menuArray);
        $ussdSession->currentFeedbackType = self::FACILITY_DIRECTORY_REQ;
        return $ussdSession;
    }

      public function setPinRequest($ussdSession) {
        $pin = rand(1000,9999);
        $ussdSession->currentFeedbackString = $pin;
        $ussdSession->currentFeedbackType = self::PIN_REQ;
        return $ussdSession;
    }

    public function setGenPassword($ussdSession) {
        $clinicTypeList = getOptionType();
        $reply = "Select Options to Generate pin:";
        if (count($clinicTypeList) > 0) {
            $displayedClinicTypeList = "";
            for ($i = 1; $i <= count($clinicTypeList); $i++) {
                $reply .= "\n" . $i . ":" . $clinicTypeList[$i - 1]->type_desc;
                if ($i != count($clinicTypeList)) {
                    $displayedClinicTypeList .= $clinicTypeList[$i - 1]->type_id . "#";
                } else {
                    $displayedClinicTypeList .= $clinicTypeList[$i - 1]->type_id;
                }
            }
            $userParams = $ussdSession->userParams . UssdSession::GENERATE_PIN_ID . "=" . $displayedClinicTypeList . "*";
            $ussdSession->userParams = $userParams;
        } else {
            $reply = "\nOption not found.";
        }
        $ussdSession->currentFeedbackString = $reply;
        $ussdSession->currentFeedbackType = self::GENERATE_PIN_REQ;
        return $ussdSession;
    }  
    public function setFacilityNameRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Facility Name:";
        $ussdSession->currentFeedbackType = self::FACILITY_NAME_REQ;
        return $ussdSession;
    }
    public function setMFLCodeRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter MFL Code:";
        $ussdSession->currentFeedbackType = self::MFL_CODE_REQ;
        return $ussdSession;
    }

    public function setAddClinicTypeRequest($ussdSession) {
        $clinicTypeList = getClinicTypeCode();
        $reply = "Select Clinic Type:";
        if (count($clinicTypeList) > 0) {
            $displayedClinicTypeList = "";
            for ($i = 1; $i <= count($clinicTypeList); $i++) {
                $reply .= "\n" . $i . ":" . $clinicTypeList[$i - 1]->type_desc;
                if ($i != count($clinicTypeList)) {
                    $displayedClinicTypeList .= $clinicTypeList[$i - 1]->type_id . "#";
                } else {
                    $displayedClinicTypeList .= $clinicTypeList[$i - 1]->type_id;
                }
            }
            $userParams = $ussdSession->userParams . UssdSession::CLINIC_TYPE_ID . "=" . $displayedClinicTypeList . "*";
            $ussdSession->userParams = $userParams;
        } else {
            $reply = "\nNo Clinic Type was found.";
        }
        $ussdSession->currentFeedbackString = $reply;
        $ussdSession->currentFeedbackType = self::CLINIC_TYPE_REQ;
        return $ussdSession;
    } 
    public function setPhoneNumberRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Phone Number:";
        $ussdSession->currentFeedbackType = self::PHONE_NUMBER_REQ;
        return $ussdSession;
    }
    public function setReferralServicesRequest($ussdSession) {
        $menuArray = array("Initiate Referral", "Accept Referral","Get Patient Details","Transit Client");
        $ussdSession->currentFeedbackString = "Select one:\n" . generateMenu($menuArray);
        $ussdSession->currentFeedbackType = self::REFERRAL_SERVICES_REQ;
        return $ussdSession;
    }
    public function setTransitClientRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Patient CCC Number:";
        $ussdSession->currentFeedbackType = self::TRANSIT_CLIENT_CCC_NUMBER_REQ;
        return $ussdSession; 
    }
    public function setPatientDetailsRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Patient CCC Number:";
        $ussdSession->currentFeedbackType = self::PATIENT_DETAILS_CCC_NUMBER_REQ;
        return $ussdSession;
    }
     public function setAcceptReferralRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Patient CCC Number:";
        $ussdSession->currentFeedbackType = self::ACCEPT_REF_CCC_NUMBER_REQ;
        return $ussdSession;
    }
     public function setInitialReferralRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Patient CCC Number:";
        $ussdSession->currentFeedbackType = self::INITIAL_REF_CCC_NUMBER_REQ;
        return $ussdSession;
    }

    public function setPatientCCCNumberRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Patient CCC Number:";
        $ussdSession->currentFeedbackType = self::CCC_NUMBER_REQ;
        return $ussdSession;
    }
    public function setMoreOptionsRequest($ussdSession) {
        $clinicTypeList = getOptionType();
        $reply = "Select Options:";
        if (count($clinicTypeList) > 0) {
            $displayedClinicTypeList = "";
            for ($i = 1; $i <= count($clinicTypeList); $i++) {
                $reply .= "\n" . $i . ":" . $clinicTypeList[$i - 1]->type_desc;
                if ($i != count($clinicTypeList)) {
                    $displayedClinicTypeList .= $clinicTypeList[$i - 1]->type_id . "#";
                } else {
                    $displayedClinicTypeList .= $clinicTypeList[$i - 1]->type_id;
                }
            }
            $userParams = $ussdSession->userParams . UssdSession::MORE_OPTIONS_ID . "=" . $displayedClinicTypeList . "*";
            $ussdSession->userParams = $userParams;
        } else {
            $reply = "\nOption not found.";
        }
        $ussdSession->currentFeedbackString = $reply;
        $ussdSession->currentFeedbackType = self::MORE_OPTIONS_REQ;
        return $ussdSession;
    } 

    public function setFacilityMFLCodeRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Facility MFL Code:";
        $ussdSession->currentFeedbackType = self::MFL_CODE_REQ;
        return $ussdSession;
    }

    public function setFacilityInitMFLCodeRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Facility MFL Code:";
        $ussdSession->currentFeedbackType = self::INIT_MFL_CODE_REQ;
        return $ussdSession;
    }
    public function setAppoinmentDateRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Date of Appointment (DDMMYYYY):";
        $ussdSession->currentFeedbackType = self::APPOINTMENT_DATE_REQ;
        return $ussdSession;
    }
    public function setCurrentRegimentRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Current Regimen:";
        $ussdSession->currentFeedbackType = self::CURRENT_REGIMEN_REQ;
        return $ussdSession;
    }
    // public function setSecretPinRequest($ussdSession) {
    //     $ussdSession->currentFeedbackString = "Enter Pin:";
    //     $ussdSession->currentFeedbackType = self::SECRET_PIN_REQ;
    //     return $ussdSession;
    // }
    public function setSecretPinRequest($ussdSession) {
        $secretPin = getPin($ussdSession->msisdn); 
        $pin = $secretPin[0]->pin; 
        error_log("[ERROR : " . date("Y-m-d H:i:s") . "] query from \nParams=" . print_r($secretPin, true), 3, LOG_FILE);
        $ussdSession->currentFeedbackString = "Enter this Secret pin: ".$pin;
        $ussdSession->currentFeedbackType = self::SECRET_PIN_REQ;
        return $ussdSession;
        
    }
    public function setNumberOfDaysRequest($ussdSession) {
        $ussdSession->currentFeedbackString = "Enter Number of Days:";
        $ussdSession->currentFeedbackType = self::NUMBER_OF_DAYS_REQ;
        return $ussdSession;
    }

    public function setSearchFacilityNameRequest($ussdSession,$facilityName) {
        $mflCodeRequestsList = searchFacilityName($facilityName);
        $reply = "Search by Facility Name:";
        if (count($mflCodeRequestsList) > 0) {
            for ($i = 0; $i < count($mflCodeRequestsList); $i++) {
                $reply .= "\n" . "Dear Provider, Here is the facility \n we have found in our directory: "
                .$mflCodeRequestsList[$i]->facilityName. "\n ( MFL "
                . $mflCodeRequestsList[$i]->mflCode." )\n, "  . $mflCodeRequestsList[$i]->type_desc." CCC Number: \n". $mflCodeRequestsList[$i]->cccNumber." .MOH";
            }
        } else {
            $reply = "Facility not found.";
        }
        $ussdSession->currentFeedbackString = $reply;
        $ussdSession->currentFeedbackType = self::MFL_CODE_REQUEST;
        return $ussdSession;
    }
    public function setSearchPatientDetailsRequest($ussdSession,$cccNumber) {
        $mflCodeRequestsList = searchPatientDetails($cccNumber);
      
        $reply = "Search Patient Details: ";
        if (count($mflCodeRequestsList) > 0 ) {
            for ($i = 0; $i < count($mflCodeRequestsList); $i++) {
                $reply .= "\n" . "Dear Provider, client with UPN \n"
                .$mflCodeRequestsList[$i]->upn. "\n Treatment details; Regimen: \n"
                . $mflCodeRequestsList[$i]->currentRegime." and CCC number is\n "  . $mflCodeRequestsList[$i]->cccNumber." \n Clinician number: ".$mflCodeRequestsList[$i]->msisdn;
            }
        } else {
            $reply = "The CCC Number is Invalid.";
        }
        $ussdSession->currentFeedbackString = $reply;
        $ussdSession->currentFeedbackType = self::PATIENT_DETAILS_SEARCH_REQUEST;
        return $ussdSession;
    }


    public function setSearchTransistRequest($ussdSession,$cccNumber) {
        $mflCodeRequestsList = searchPatientDetails($cccNumber);
      
        $reply = "Transist Client: ";
        if (count($mflCodeRequestsList) > 0 ) {
            for ($i = 0; $i < count($mflCodeRequestsList); $i++) {
                $reply .= "\n" . "Dear Provider, client with UPN \n"
                .$mflCodeRequestsList[$i]->upn. "\n Treatment details; Regimen: \n"
                . $mflCodeRequestsList[$i]->currentRegime." and CCC number is\n "  . $mflCodeRequestsList[$i]->cccNumber;
            }
        } else {
            $reply = "The CCC Number is Invalid.";
        }
        $ussdSession->currentFeedbackString = $reply;
        $ussdSession->currentFeedbackType = self::TRANSIT_SEARCH_REQUEST;
        return $ussdSession;
    }


    public function setSearchMFLCodeRequest($ussdSession,$mflCode) {
        $mflCodeRequestsList = searchMfl($mflCode);

        $reply = "Search by MFL code:";
        if (count($mflCodeRequestsList) > 0) {
            for ($i = 0; $i < count($mflCodeRequestsList); $i++) {
                $reply .= "\n" . date_format(new DateTime($mflCodeRequestsList[$i]->created_date), "d-m-y ") . " MFL Code. " . $mflCodeRequestsList[$i]->mflCode. " Phone. " . $mflCodeRequestsList[$i]->msisdn;
            }
        } else {
            $reply = "No MFL code was found.";
        }
        $ussdSession->currentFeedbackString = $reply;
        $ussdSession->currentFeedbackType = self::MFL_CODE_REQUEST;
        return $ussdSession;
    }

    // public function setPatientDetailsRequest($ussdSession,$facilityName) {
    //     $mflCodeRequestsList = searchFacilityName($facilityName);
    //     $reply = "Search by Facility Name:";
    //     if (count($mflCodeRequestsList) > 0) {
    //         for ($i = 0; $i < count($mflCodeRequestsList); $i++) {
    //             $reply .= "\n" . "Dear Provider, here'\n
    //             is the facility we \n
    //             have found in our directory\n "  . $mflCodeRequestsList[$i]->facilityName. "( " . $mflCodeRequestsList[$i]->mflCode .")";
    //         }
    //     } else {
    //         $reply = "Facility with that Name was not found.";
    //     }
    //     $ussdSession->currentFeedbackString = $reply;
    //     $ussdSession->currentFeedbackType = self::MFL_CODE_REQUEST;
    //     return $ussdSession;
    // }
}