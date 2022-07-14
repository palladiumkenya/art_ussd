<?php
include_once('./QueryManager.php');
include_once ("./classes/MenuItems.php");
include_once('RegistrationAction.php');

class RootMenuAction {
    public function process($ussdSession){
        $ussdUserList = getUssdUserList($ussdSession->msisdn);
        if(count($ussdUserList)>0){
            $menuItems = new MenuItems();
            $userParams = UssdSession::USER_ID . "=" . $ussdUserList[0]->id . "*" ;
            $ussdSession->userParams = $userParams;
            $ussdSession = $menuItems->setMainMenu($ussdSession);
            $reply = "CON " . $ussdSession->currentFeedbackString;
            $ussdSession->currentFeedbackString = $reply;
        } else {
            $authorisation = new RegistrationAction();
            $ussdSession = $authorisation->process($ussdSession);
        }
        return $ussdSession;
    }
}