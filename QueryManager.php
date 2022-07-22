<?php
include_once('Models.php');

function _select($sql, $params) {

    $username = 'root';
    $password = '1234';
    $database = 'art_ussd';
    $host = 'localhost';
  

    $res = array();
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetchAll();
    } catch (PDOException $error) {
       // var_dump($sql);
       // var_dump($params);
       // var_dump($error);        
       error_log("[ERROR : " . date("Y-m-d H:i:s") . "] _select error: " . $error . "\nSQL=" . $sql . "\nParams=" . print_r($params, true), 3, LOG_FILE);
    }
    return $res;
}
/**
 * Performs database insert, update and delete
 */
function _execute($sql, $params) {
    $username = 'root';
    $password = '1234';
    $database = 'art_ussd';
    $host = 'localhost';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return TRUE;
    } catch (PDOException $error) {
       // var_dump($error); 
       // var_dump($sql);
       // var_dump($params);
        error_log("[ERROR : " . date("Y-m-d H:i:s") . "] _execute error: " . $error . "\nSQL=" . $sql . "\nParams=" . print_r($params, true), 3, LOG_FILE);
        return FALSE;
    }
}
function createNewUssdSession($ussdSession) {
error_log("[ERROR : " . date("Y-m-d H:i:s") . "] query \nParams=" . print_r($ussdSession, true), 3, LOG_FILE);
    $sql = "INSERT INTO ussd_sessions (sessionId,msisdn,UssdCode,UssdString,UssdProcessString,currentFeedbackString,currentFeedbackType,startTime,userParams)"
            . " VALUES(:sessionId,:msisdn,:ussdCode,:ussdString,:ussdProcessString,:currentFeedbackString,:currentFeedbackType,:startTime,:userParams)";
    $params = array(
        ':sessionId' => $ussdSession->sessionId,
        ':msisdn' => $ussdSession->msisdn,
        ':ussdCode' => $ussdSession->ussdCode,
        ':ussdString' => $ussdSession->ussdString,
        ':ussdProcessString' => $ussdSession->ussdProcessString,
        ':currentFeedbackString' => $ussdSession->currentFeedbackString,
        ':currentFeedbackType' => $ussdSession->currentFeedbackType,
        ':startTime' => date('Y-m-d H:i:s'),
        ':userParams' => $ussdSession->userParams,
    );
    return _execute($sql, $params);
}
function getUssdSessionList($sessionId){
    $ussdSessionList = array();
    $sql = "SELECT sessionId,msisdn,UssdCode,UssdString,UssdStringPrefix,UssdProcessString,previousFeedbackType,currentFeedbackString,currentFeedbackType,startTime,userParams"
            . " FROM ussd_sessions"
            . " WHERE sessionId=:sessionId LIMIT 1";
    $params = array(
        ':sessionId' => $sessionId,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $ussdSession = new UssdSession();
        $ussdSession->sessionId = $record['sessionId'];
        $ussdSession->msisdn = $record['msisdn'];
        $ussdSession->ussdCode = $record['UssdCode'];
        $ussdSession->ussdString = $record['UssdString'];
        $ussdSession->ussdStringPrefix = $record['UssdStringPrefix'];
        $ussdSession->ussdProcessString = $record['UssdProcessString'];
        $ussdSession->previousFeedbackType = $record['previousFeedbackType'];
        $ussdSession->currentFeedbackString = $record['currentFeedbackString'];
        $ussdSession->currentFeedbackType = $record['currentFeedbackType'];
        $ussdSession->startTime = $record['startTime'];
        $ussdSession->userParams = $record['userParams'];
        $ussdSessionList[] = $ussdSession;
    }
    return $ussdSessionList;
}

function updateUssdSession($ussdSession) {
    $sql = "UPDATE ussd_sessions SET UssdString=:ussdString,UssdStringPrefix=:ussdStringPrefix, UssdProcessString=:ussdProcessString,"
            . "previousFeedbackType=:previousFeedbackType,currentFeedbackString=:currentFeedbackString,currentFeedbackType=:currentFeedbackType,userParams=:userParams"
            . " WHERE sessionId=:sessionId";
    $params = array(
        ':ussdString' => $ussdSession->ussdString,
        ':ussdStringPrefix' => $ussdSession->ussdStringPrefix,
        ':ussdProcessString' => $ussdSession->ussdProcessString,
        ':previousFeedbackType' => $ussdSession->previousFeedbackType,
        ':currentFeedbackString' => $ussdSession->currentFeedbackString,
        ':currentFeedbackType' => $ussdSession->currentFeedbackType,
        ':userParams' => $ussdSession->userParams,
        ':sessionId' => $ussdSession->sessionId,
    );
    return _execute($sql, $params);
}

function getClinicTypeCode() {
    $clinicTypeList = array();
    $sql = "SELECT type_id,type_desc"
            . " FROM tbl_clinictypes";
    $params = array(
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $clinicType = new ClinicType();
        $clinicType->type_id = $record['type_id'];
        $clinicType->type_desc = $record['type_desc'];
        $clinicTypeList[] = $clinicType;
    }
    return $clinicTypeList;
}

function getOptionType() {
    $optionTypeList = array();
    $sql = "SELECT type_id,type_desc"
            . " FROM tbl_options";
    $params = array(
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $optionType = new OptionType();
        $optionType->type_id = $record['type_id'];
        $optionType->type_desc = $record['type_desc'];
        $optionTypeList[] = $optionType;
    }
    return $optionTypeList;
}



function mflCodeSend($ussdUser) {
    $sql = "INSERT INTO tbl_facility (msisdn,mflCode)"
            . " VALUES(:msisdn,:mflCode)";
    $params = array(
        ':msisdn' => $ussdUser->msisdn,
        ':mflCode' => $ussdUser->mflCode,
    );
    return _execute($sql, $params);
}
function facilityNameSearch($ussdUser) {
    $sql = "INSERT INTO tbl_facility (msisdn,facilityName)"
            . " VALUES(:msisdn,:facilityName)";
    $params = array(
        ':msisdn' => $ussdUser->msisdn,
        ':facilityName' => $ussdUser->facilityName,
    );
    return _execute($sql, $params);
}

function updateFacility($ussdUser) {
        $sql = "UPDATE tbl_facility SET clinicalType=:clinicalType,phoneNumber=:phoneNumber
        WHERE msisdn=:msisdn";
        $params = array(
            ':clinicalType' => $ussdUser->clinicalType,
            ':phoneNumber' => $ussdUser->phoneNumber,
            ':msisdn' => $ussdUser->msisdn,
        );
        return _execute($sql, $params);
}


function getUssdUserList($msisdn) {
    $ussdUserList = array();
    $sql = "SELECT id,msisdn,firstName,lastName,idNumber,dateCreated"
            . " FROM ussd_users"
            . " WHERE msisdn=:msisdn LIMIT 1";
    $params = array(
        ':msisdn' => $msisdn,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $ussdUser = new UssdUser();
        $ussdUser->id = $record['id'];
        $ussdUser->msisdn = $record['msisdn'];
        $ussdUser->firstName = $record['firstName'];
        $ussdUser->lastName = $record['lastName'];
        $ussdUser->idNumber = $record['idNumber'];
        $ussdUser->dateCreated = $record['dateCreated'];
        $ussdUserList[] = $ussdUser;
    }
    return $ussdUserList;
}



function createUssdUser($ussdUser) {
    $sql = "INSERT INTO ussd_users (msisdn,firstName,lastName)"
            . " VALUES(:msisdn,:firstName,:lastName)";
    $params = array(
        ':msisdn' => $ussdUser->msisdn,
        ':firstName' => $ussdUser->firstName,
        ':lastName' => $ussdUser->lastName,
    );
    return _execute($sql, $params);
}


function saveAcceptRef($ussdUser) {
    $sql = "INSERT INTO tbl_facility (cccNumber,optionType)"
            . " VALUES(:cccNumber,:optionType)";
    $params = array(
        ':cccNumber' => $ussdUser->cccNumber,
        ':optionType' => $ussdUser->optionType,

    );
    return _execute($sql, $params);
}



function secretPin($ussdUser) {
    $sql = "INSERT INTO tbl_facility (cccNumber,pin,msisdn)"
            . " VALUES(:cccNumber,:pin,:msisdn)";
    $params = array(
        ':msisdn' => $ussdUser->msisdn,
        ':cccNumber' => $ussdUser->cccNumber,
        ':pin' => $ussdUser->pin,

    );
    return _execute($sql, $params);
}

function transit($ussdUser) {
    $sql = "INSERT INTO tbl_facility (cccNumber,numberOfDrugs,msisdn)"
            . " VALUES(:cccNumber,:numberOfDrugs,:msisdn)";
    $params = array(
        ':cccNumber' => $ussdUser->cccNumber,
        ':numberOfDrugs' => $ussdUser->numberOfDrugs,
        ':msisdn' => $ussdUser->msisdn,

    );
    return _execute($sql, $params);
}


function initialReference($ussdUser) {
    $sql = "INSERT INTO tbl_facility (cccNumber,mflCode,daysOfAppointment,currentRegime,msisdn)"
            . " VALUES(:cccNumber,:mflCode,:daysOfAppointment,:currentRegime,:msisdn)";
    $params = array(
        ':cccNumber' => $ussdUser->cccNumber,
        ':mflCode' => $ussdUser->mflCode,
        ':daysOfAppointment' => $ussdUser->daysOfAppointment,
        ':currentRegime' => $ussdUser->currentRegime,
        ':msisdn' => $ussdUser->msisdn,
    );
    return _execute($sql, $params);
}

function generatePin($ussdUser) {
    $pin = rand(1000,9999);
    $sql = "INSERT INTO tbl_facility (msisdn,pin)"
            . " VALUES(:msisdn,:pin)";
    $params = array(
        'msisdn' => $ussdUser->msisdn,
        ':pin' => $pin,

    );
    return _execute($sql, $params);
}

function getDateCreated($msisdn) {
    $ratesList = array();
     $sql = "SELECT created_date, msisdn,pin"
            . " FROM tbl_facility"
            . " WHERE msisdn=:msisdn"
            . "  order by pin DESC"
            . "  LIMIT 1";
    $params = array(
        ':msisdn' => $msisdn,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $rate = new UssdFacility();
        $rate->msisdn = $record['msisdn'];
         $rate->pin = $record['pin'];
        $rate->created_date = $record['created_date'];
        $ratesList[] = $rate;
    }
    return $ratesList;
}

function searchMfl($mflCode) {
    $mflList = array();
    $sql = "SELECT mflCode,created_date, msisdn"
            . " FROM tbl_facility"
            . " WHERE mflCode LIKE '%$mflCode'"
            . "  order by id DESC"
            . " LIMIT 1";
    $params = array(
        ':mflCode' => $mflCode,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $mflCategory = new UssdFacility();

        $mflCategory->mflCode = $record['mflCode'];
         $mflCategory->msisdn = $record['msisdn'];
         $mflCategory->created_date = $record['created_date'];
        $mflList[] = $mflCategory;
    }
    return $mflList;
}

function getPin($msisdn) {
    $ratesList = array();
     $sql = "SELECT pin"
            . " FROM tbl_facility"
            . " WHERE msisdn=:msisdn"
            . " AND pin is not null"
            . "  order by id DESC"
            . "  LIMIT 1";
    $params = array(
        ':msisdn' => $msisdn,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $rate = new UssdFacility();
        $rate->pin = $record['pin'];
        $ratesList[] = $rate;
    }
    return $ratesList;
}
function searchFacilityName($facilityName) {
    $mflList = array();
     $sql = "SELECT rv.facilityName,rv.created_date,rv.mflCode, rv.cccNumber,rv.pin,r.type_desc, rv.msisdn,rv.upn,rv.currentRegime"
            . " FROM tbl_facility rv"
            . " LEFT JOIN tbl_clinictypes r ON rv.clinicalType=r.type_id"
            . " WHERE facilityName LIKE '%$facilityName'"
            . "  order by id DESC"
            . " LIMIT 1";
    $params = array(
        ':facilityName' => $facilityName,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $mflCategory = new UssdFacility();

        $mflCategory->cccNumber = $record['cccNumber'];
        $mflCategory->pin = $record['pin'];
        $mflCategory->facilityName = $record['facilityName'];
        $mflCategory->type_desc = $record['type_desc'];
        $mflCategory->mflCode = $record['mflCode'];
        $mflCategory->currentRegime = $record['currentRegime'];    
        $mflCategory->upn = $record['upn'];
        $mflCategory->msisdn = $record['msisdn'];
        $mflCategory->created_date = $record['created_date'];
        $mflList[] = $mflCategory;
    }
    return $mflList;
}


function searchPatientDetails($cccNumber) {
    $mflList = array();
     $sql = "SELECT rv.facilityName,rv.created_date,rv.mflCode, rv.cccNumber,rv.pin,r.type_desc, rv.msisdn,rv.upn,rv.currentRegime"
            . " FROM tbl_facility rv"
            . " LEFT JOIN tbl_clinictypes r ON rv.clinicalType=r.type_id"
            . " WHERE cccNumber LIKE '%$cccNumber'"
            . "  order by id DESC"
            . " LIMIT 1";
    $params = array(
        ':cccNumber' => $cccNumber,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $mflCategory = new UssdFacility();

        $mflCategory->cccNumber = $record['cccNumber'];
        $mflCategory->pin = $record['pin'];
        $mflCategory->facilityName = $record['facilityName'];
        $mflCategory->type_desc = $record['type_desc'];
        $mflCategory->mflCode = $record['mflCode'];
        $mflCategory->currentRegime = $record['currentRegime'];    
        $mflCategory->upn = $record['upn'];
        $mflCategory->msisdn = $record['msisdn'];
        $mflCategory->created_date = $record['created_date'];
        $mflList[] = $mflCategory;
    }
    return $mflList;
}


// function searchPatientDetails($cccNumber) {
//     $mflList = array();
//     $sql = "SELECT rv.facilityName,rv.created_date, rv.cccNumber,rv.pin,r.type_desc"
//             . " FROM tbl_facility rv"
//             . "LEFT JOIN tbl_clinictypes r ON rv.clinicalType=r.type_id"
//             . " WHERE cccNumber LIKE '%$cccNumber'"
//             . " AND pin is not null"
//             . "  order by id DESC"
//             . " LIMIT 1";
//     $params = array(
//          ':cccNumber' => $cccNumber,
//         //':pin' => $pin,
       
//     );
//     $resultset = _select($sql, $params);
//     foreach ($resultset as $record) {
//         $patientSearchCategory = new UssdFacility();
//         $patientSearchCategory->pin = $record['pin'];
//         $patientSearchCategory->cccNumber = $record['cccNumber'];
//         $patientSearchCategory->type_desc = $record['type_desc'];
//         $patientSearchCategory->created_date = $record['created_date'];
//         $searchList[] = $patientSearchCategory;
//     }
//     return $searchList;
// }