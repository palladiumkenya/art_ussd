<?php
//date_default_timezone_set('Africa/Nairobi');
//define("LOG_FILE", "error.log");

//$_GET = $_REQUEST;
//$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : '';
//$msisdn = isset($_GET['msisdn']) ? $_GET['msisdn'] : '';
//$serviceCode = isset($_GET['serviceCode']) ? $_GET['serviceCode'] : '';
//$ussdString = isset($_GET['text']) ? $_GET['text'] : '';


 date_default_timezone_set('Africa/Nairobi');
 define("LOG_FILE", "error.log");
// error_log("[ERROR : " . date("Y-m-d H:i:s") . "] query from safaricom \nParams=" . print_r($_REQUEST, true), 3, LOG_FILE);
 $sessionId = isset($_REQUEST['sessionId']) ? $_REQUEST['sessionId'] : '';
 $msisdn = isset($_REQUEST['phoneNumber']) ? $_REQUEST['phoneNumber'] : '';
 $serviceCode = isset($_REQUEST['serviceCode']) ? $_REQUEST['serviceCode'] : '';
 $ussdString = isset($_REQUEST['text']) ? $_REQUEST['text'] : '';


include_once("Models.php");
include_once ("./classes/MenuItems.php");
include_once ("./classes/RootMenuAction.php");
include_once ("./classes/MyAccountAction.php");
include_once ("./classes/FacilityDirectoryAction.php");
include_once ("./classes/ReferralServicesAction.php");
include_once ("./classes/UssdUtils.php");
include_once ("./classes/GenerateSecretPin.php");

if ($ussdString == "") {
    $ussdSession = new UssdSession();
    $ussdSession->sessionId = $sessionId;
    $ussdSession->msisdn = $msisdn;
    $ussdSession->ussdCode = $serviceCode;
    $ussdSession->ussdString = $ussdString;
    $ussdSession->ussdProcessString = $ussdString;

    $rootMenu = new RootMenuAction();
    $ussdSession = $rootMenu->process($ussdSession);
    createNewUssdSession($ussdSession);
} else {
    $ussdString = cleanUssdString($ussdString);
    $ussdSessionList = getUssdSessionList($sessionId);
    if (count($ussdSessionList) > 0) {
        $ussdSession = $ussdSessionList[0];
        $ussdSession->ussdString = $ussdString;
        $ussdSession->ussdProcessString = $ussdString;
        $ussdSession->previousFeedbackType = $ussdSession->currentFeedbackType;

        if (MenuItems::AUTHORISATION_REQ == $ussdSession->previousFeedbackType) {
              $ussdSession = new UssdSession();
              $reply = "END You are not registered for ART USSD Services.";
              $ussdSession->currentFeedbackString = $reply;
        // if (MenuItems::FIRSTNAME_REQ == $ussdSession->previousFeedbackType ||
        //         MenuItems::LASTNAME_REQ == $ussdSession->previousFeedbackType ||
        //         MenuItems::ID_NUMBER_REQ == $ussdSession->previousFeedbackType) {
        //     $registration = new RegistrationAction();
        //     $ussdSession = $registration->process($ussdSession);
    
        } else {
            $menuItems = new MenuItems();
//            $menuSuffix = "\n00 Home";
            $params = explode("*", $ussdSession->ussdProcessString);
            $lastSelection = trim($params[count($params) - 1]);
            if ("" == $ussdSession->ussdProcessString || "00" === $lastSelection ||
                    MenuItems::MYACCOUNT_CATEGORY_REQ == $ussdSession->previousFeedbackType) {
                $ussdSession = $menuItems->setMainMenu($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString;
                $ussdSession->currentFeedbackString = $reply;
            } elseif (MenuItems::MAINMENU_REQ == $ussdSession->previousFeedbackType) {
                if ("1" == $lastSelection) {//facility directory
                    $facilityDirectory = new FacilityDirectoryAction();
                    $ussdSession = $facilityDirectory->process($ussdSession);
                } elseif ("2" == $lastSelection) {//referral services
                    $referralService = new ReferralServicesAction();
                    $ussdSession = $referralService->process($ussdSession);
                 } elseif ("3" == $lastSelection) {//Gen pin
                    $referralService = new GenerateSecretPin();
                    $ussdSession = $referralService->process($ussdSession);                   
                } else {
                    $ussdSession = $menuItems->setMainMenu($ussdSession);
                    $reply = "CON INVALID INPUT. Only number 1-3 allowed.\n" . $ussdSession->currentFeedbackString;
                    $ussdSession->currentFeedbackString = $reply;
                }
            } elseif (MenuItems::FACILITY_DIRECTORY_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::FACILITY_NAME_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::CLINIC_TYPE_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::PHONE_NUMBER_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::MFL_CODE_REQUEST == $ussdSession->previousFeedbackType ||
                MenuItems::MORE_OPTIONS_REQ_DIR == $ussdSession->previousFeedbackType ||

                MenuItems::MFL_CODE_REQ == $ussdSession->previousFeedbackType ) {
                $facilityDirectory = new FacilityDirectoryAction();
                $ussdSession = $facilityDirectory->process($ussdSession);

            } elseif (MenuItems::REFERRAL_SERVICES_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::INITIAL_REF_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::INIT_MFL_CODE_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::APPOINTMENT_DATE_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::CURRENT_REGIMEN_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::ACCEPT_REF_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::MORE_OPTIONS_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::PATIENT_DETAILS_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::SECRET_PIN_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::TRANSIT_CLIENT_CCC_NUMBER_REQ == $ussdSession->previousFeedbackType ||
                MenuItems::NUMBER_OF_DAYS_REQ == $ussdSession->previousFeedbackType ) {
                $facilityDirectory = new ReferralServicesAction();
                $ussdSession = $facilityDirectory->process($ussdSession);
            } else {
                $referralService = new GenerateSecretPin();
                $ussdSession = $referralService->process($ussdSession);
            }
//            $ussdSession->currentFeedbackString = $reply;
        }
    } else {
        $ussdSession = new UssdSession();
        $reply = "END Connection error. Please try again.";
        $ussdSession->currentFeedbackString = $reply;
    }
    updateUssdSession($ussdSession);
}

echo $ussdSession->currentFeedbackString;

