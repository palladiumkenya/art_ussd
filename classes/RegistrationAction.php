<?php
include_once('./Models.php');
include_once('./QueryManager.php');
include_once('MenuItems.php');
include_once('UssdUtils.php');

class RegistrationAction {

    public function process($ussdSession) {
        $menuItems = new MenuItems();

        if ($ussdSession->ussdProcessString == "") {
            $ussdSession = $menuItems->setFirstNameRequest($ussdSession);
            $reply = "CON Welcome to ART USSD. " . $ussdSession->currentFeedbackString;
        } else {
            $params = explode("*", $ussdSession->ussdProcessString);
            if (MenuItems::FIRSTNAME_REQ == $ussdSession->previousFeedbackType) {
                $firstName = trim($params[count($params) - 1]);
                if (isValidName($firstName)) {
                    $userParams = UssdSession::FIRSTNAME . "=" . $firstName . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setLastNameRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setFirstNameRequest($ussdSession);

                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::LASTNAME_REQ == $ussdSession->previousFeedbackType) {
                $lastName = trim($params[count($params) - 1]);
                if (isValidName($lastName)) {
                    $userParams = $ussdSession->userParams . UssdSession::LASTNAME . "=" . $lastName . "*";
                    $ussdSession->userParams = $userParams;
                    $ussdSession = $menuItems->setIdNumberRequest($ussdSession);
                    $reply = "CON " . $ussdSession->currentFeedbackString;
                } else {
                    $ussdSession = $menuItems->setLastNameRequest($ussdSession);

                        $reply = "CON The name you entered contains NUMBERS or INVALID characters.\n" . $ussdSession->currentFeedbackString;
                }
            } elseif (MenuItems::ID_NUMBER_REQ == $ussdSession->previousFeedbackType) {
                $idNumber = trim($params[count($params) - 1]);
                if (isValidIdNumber($idNumber)) {
                    $userParams = $ussdSession->userParams . UssdSession::IDNUMBER . "=" . $idNumber . "*";
                    $ussdSession->userParams = $userParams;
                    $reply = "END " . self::registerNewUser($ussdSession);
                } else {
                    $ussdSession = $menuItems->setIdNumberRequest($ussdSession);
                        $reply = "CON You entered an INVALID ID number.\n" . $ussdSession->currentFeedbackString;
                }
            }
        }
        $ussdSession->currentFeedbackString = $reply;
        return $ussdSession;
    }
    
    function registerNewUser($ussdSession){
        $ussdUser = new UssdUser();
        $ussdUser->msisdn = $ussdSession->msisdn;
        $ussdUser->firstName = UssdSession::getUserParam(UssdSession::FIRSTNAME, $ussdSession->userParams);
        $ussdUser->lastName = UssdSession::getUserParam(UssdSession::LASTNAME, $ussdSession->userParams);
        $ussdUser->idNumber = UssdSession::getUserParam(UssdSession::IDNUMBER, $ussdSession->userParams);
        
        if(createUssdUser($ussdUser)){
                return "You have been registered successfully!";
            
        } else {
                return "There was an error in your registration. Please try again.";
            
        }
    }

}
