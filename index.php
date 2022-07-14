<?php
date_default_timezone_set('Africa/Nairobi');
define("LOG_FILE", "error.log");

$_GET = $_REQUEST;
$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : '';
$msisdn = isset($_GET['phoneNumber']) ? $_GET['MSISDN'] : '';
$serviceCode = isset($_GET['serviceCode']) ? $_GET['serviceCode'] : '';
$ussdString = isset($_GET['text']) ? $_GET['text'] : '';


include_once("Models.php");
include_once ("./classes/MenuItems.php");
include_once ("./classes/RootMenuAction.php");
include_once ("./classes/MyAccountAction.php");
include_once ("./classes/FacilityDirectoryAction.php");
include_once ("./classes/ReferralServicesAction.php");
include_once ("./classes/UssdUtils.php");

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

        // if (MenuItems::AUTHORISATION_REQ == $ussdSession->previousFeedbackType) {
        //     $authorisation = new AuthorisationAction();
        //     $ussdSession = $authorisation->process($ussdSession);
        if (MenuItems::FIRSTNAME_REQ == $ussdSession->previousFeedbackType ||
            MenuItems::LASTNAME_REQ == $ussdSession->previousFeedbackType ||
            MenuItems::ID_NUMBER_REQ == $ussdSession->previousFeedbackType) {
            $registration = new RegistrationAction();
            $ussdSession = $registration->process($ussdSession);
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
                } else {
                    $ussdSession = $menuItems->setMainMenu($ussdSession);
                    $reply = "CON INVALID INPUT. Only number 1-2 allowed.\n" . $ussdSession->currentFeedbackString;
                    $ussdSession->currentFeedbackString = $reply;
                }
            } elseif (MenuItems::FACILITY_DIRECTORY_REQ == $ussdSession->previousFeedbackTypee) {
                $facilityDirectory = new FacilityDirectoryAction();
                $ussdSession = $facilityDirectory->process($ussdSession);
            } else {
                $referralService = new ReferralServicesAction();
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


