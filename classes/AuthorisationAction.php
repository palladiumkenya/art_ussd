<?php

include_once('./Models.php');
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');

class AuthorisationAction {
	  $menuItems = new MenuItems();
      $params = explode("*", $ussdSession->ussdProcessString);
      $lastSelection = trim($params[count($params) - 1]);

           if (MenuItems::AUTHORISATION_REQ == $ussdSession->previousFeedbackType || $ussdSession->ussdProcessString == "") {
            $reply = "CON Your are not Registered to this USSD Platform. " . $ussdSession->currentFeedbackString;
              return $ussdSession;
        } 
        // else {
        //     $params = explode("*", $ussdSession->ussdProcessString);
        //     ussdSession->currentFeedbackString = $reply;
        //     return $ussdSession;
        // }

            

}