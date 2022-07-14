<?php
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');

class MyAccountAction {
    public function process($ussdSession) {
        $menuItems = new MenuItems();
        $menuSuffix = "\n00 Home";
        $params = explode("*", $ussdSession->ussdProcessString);
        $lastSelection = trim($params[count($params) - 1]);

         if(MenuItems::MAINMENU_REQ == $ussdSession->previousFeedbackType){
                $ussdSession = $menuItems->setMyAccountCategories($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
        } elseif (MenuItems::MYACCOUNT_CATEGORY_REQ == $ussdSession->previousFeedbackType) {
            if ("1" == $lastSelection) {//Facility Directory
                $ussdSession = $menuItems->setFaciltyDirectory($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
            } elseif ("2" == $lastSelection) {//Referal Services
                $ussdSession = $menuItems->setReferralDirectory($ussdSession);
                $reply = "CON " . $ussdSession->currentFeedbackString . $menuSuffix;
            } else {
                $ussdSession = $menuItems->setMyAccountCategories($ussdSession);
                $reply = "CON INVALID INPUT. Only number 1-2 allowed.\n" . $ussdSession->currentFeedbackString;
            }
       } elseif (condition) {
           # code...
       }else {
            $reply = "END Connection error. Please try again.";
       }
        $ussdSession->currentFeedbackString = $reply;
        return $ussdSession;
         
}}