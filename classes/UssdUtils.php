<?php
session_start();

function generateMenu($menuArray) {
    $menu = "";
    for ($i = 1; $i <= count($menuArray); $i++) {
        $menu .= $i . ": " . $menuArray[($i - 1)];
        if ($i != count($menuArray)) {
            $menu .= "\n";
        }
    }
    return $menu;
}

function cleanUssdString($ussdString) {
    if (strpos($ussdString, "*98*") !== false) {
        $ussdString = str_replace("\\*98\\*", "*", $ussdString);
    }

    if (strpos($ussdString, "*0*") !== false) {
        $ussdString = str_replace("\\*0\\*", "*", $ussdString);
    }
    return $ussdString;
}

function isValidName($name) {
    if ($name == " ") {
        return false;
    } elseif (is_numeric($name)) {
        return false;
    } else {
        return true;
    }
}

function isAppointmentDate($apptDate) {
    if ($apptDate == " ") {
        return false;
    } else {
        return true;
    }
}

function isRequiredMinimumSize($string, $requiredSize) {
    if (strlen($string) >= $requiredSize) {
        return true;
    } else {
        return false;
    }
}

function isValidDateToYMD($dob){
     d ==$dob.getDate();
     m == $dob.getMonth() + 1;
     y == $dob.getFullYear();
     return '' + y + '-' + (m<=9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
}
function isValidIdNumber($idNumber) {
    $idNumber = str_replace(" ", "", $idNumber);
    if (strlen($idNumber) < 5) {
        return false;
    } else {
        return true;
    }
}
function isValidNumberOfdays($days) {
    $days = str_replace(" ", "", $days);
    if (strlen($days) > 3) {
        return false;
    } else {
        return true;
    }
}
function isValidIdCCCNumber($cccNumber) {
    $cccNumber = str_replace(" ", "", $cccNumber);
    if (strlen($cccNumber) < 10) {
        return false;
    } else {
        return true;
    }
}
function isValidPhone($phone) {
    if ($phone == " ") {
        return false;
    } elseif (!is_numeric($phone) && strlen($mflCode) < 10) {
        return false;
    } else {
        return true;
    }
}
function isValidPIN($pin) {
    if (is_numeric($pin) < 4) {
        return false;
    } else {
        return true;
    }
}

function isOptions($options) {
    if (in_array($options, array("Acept", "Decline"))) {
            return true;
        } else {
            return false;
        }
}

function isRandom($random){
 if (!isset($_SESSION[$random]))
    {
        $_SESSION[$random] = mt_rand(1000,9999);
    }

return $random;
    
}

function randomNumber($fourDigitRandomNumber){
    $fourDigitRandomNumber = rand(1000,9999);
    return $fourDigitRandomNumber;
    
}

function gen(){
    $fourDigitRandomNumber = rand(1000,9999);
    return $fourDigitRandomNumber;
}


// error_log("[ERROR : " . date("Y-m-d H:i:s") . "] query from safaricom \nParams=" . print_r($var, true), 3, LOG_FILE);
// error_log("[ERROR : " . date("Y-m-d H:i:s") . "] query from safaricom \nParams=" . print_r($var(), true), 3, LOG_FILE);



function isValidIdMFLCode($mflCode) {
    $mflCode = str_replace(" ", "", $mflCode);
    if (strlen($mflCode) < 4 && strlen($mflCode) > 5) {
        return false;
    } else {
        return true;
    }
}

