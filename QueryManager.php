<?php
include_once('Models.php');
include_once('./classes/DotEnv.php');
use DevEnvReader\DotEnv;
(new DotEnv(__DIR__ . '/.env'))->load();
function _select($sql, $params) {

$username =$_ENV['DB_USERNAME'];
    $password =$_ENV['DB_PASSWORD'];
    $database =$_ENV['DB_DATABASE'];
    $host =$_ENV['DB_HOST'];
   // $port=$_ENV['DB_PORT'];
  

    $res = array();
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetchAll();
    } catch (PDOException $error) {        
       error_log("[ERROR : " . date("Y-m-d H:i:s") . "] _select error: " . $error . "\nSQL=" . $sql . "\nParams=" . print_r($params, true), 3, LOG_FILE);
    }
    return $res;
}
/**
 * Performs database insert, update and delete
 */
function _execute($sql, $params) {
    $username =$_ENV['DB_USERNAME'];
    $password =$_ENV['DB_PASSWORD'];
    $database =$_ENV['DB_DATABASE'];
    $host =$_ENV['DB_HOST'];
 

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

function getRegimentType() {
    $optionTypeList = array();
    $sql = "SELECT  regimen_id, regimen_desc FROM tbl_regimen";
           
    $params = array(
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $optionType = new RegimenType();
        $optionType->regimen_id = $record['regimen_id'];
        $optionType->regimen_desc = $record['regimen_desc'];
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







function facilityNameInsert($ussdUser) {
    $sql = "INSERT INTO tbl_facility (msisdn,facilityName)"
            . " VALUES(:msisdn,:facilityName)";
    $params = array(
        ':msisdn' => $ussdUser->msisdn,
        ':facilityName' => $ussdUser->facilityName,
    );
    return _execute($sql, $params);
}


function updateFacility($ussdUser,$phoneNumber, $clinicType) {
  $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
    
        ':msisdn' => $ussdUser->msisdn
    );
    $user_details= _select($sql1, $params2);

  //  echo $clinicType; exit();

    //


      $sql3 = "SELECT tbl_location_details.telephone, tbl_clinictypes.type_desc  FROM tbl_location "
      . " INNER JOIN tbl_location_details ON tbl_location.location_id=tbl_location_details.location_id "
. " INNER JOIN tbl_clinictypes ON tbl_clinictypes.type_id=tbl_location_details.location_type WHERE tbl_location.mfl_code=:mfl_code AND "
. " tbl_location_details.location_type=:location_type";
              $params3 = array(
    
        ':mfl_code' => $user_details[0]['mfl_code'],
        ':location_type' => $clinicType
    );
    $location_details= _select($sql3, $params3);

    //print_r( $location_details); exit();


      $sql = "UPDATE tbl_location_details AS b"
            ." INNER JOIN tbl_location AS g ON b.location_id = g.location_id"
            . " SET b.telephone = :telephone"
            . " WHERE  g.mfl_code=:mfl_code AND b.location_type =:location_type";

        // $sql = "UPDATE tbl_facility SET clinicalType=:clinicalType,phoneNumber=:phoneNumber
        // WHERE msisdn=:msisdn";
        $params = array(
            ':location_type' => $clinicType,
            ':telephone' => $phoneNumber,
            ':mfl_code' => $user_details[0]['mfl_code'],
        );
         _execute($sql, $params);

         return $location_details;

        //


}


function getUssdUserList($msisdn) {
    $ussdUserList = array();
    $sql = "SELECT  tbl_user.user_id, tbl_provider.msisdn, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
    $params = array(
        ':msisdn' => $msisdn,
    );
    $resultset = _select($sql, $params);

    //echo $msisdn; exit();
    foreach ($resultset as $record) {
        $ussdUser = new UssdUser();
        $ussdUser->user_id = $record['user_id'];
        $ussdUser->msisdn = $record['msisdn'];
        $ussdUser->mfl_code = $record['mfl_code'];
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





function saveAcceptRef($ussdUser, $ccc_no, $status) {

     $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
                ':msisdn' => $ussdUser->msisdn
              );

$user_details= _select($sql1, $params2);

//Check if Patient Has Been Referral or is a Silent Referral
$sqlcheck = "SELECT  COUNT(*) as 'exists' FROM tbl_refferal WHERE ccc_no=:ccc_no AND referral_type='Normal' AND r_status=0";
  $paramscheck = array(
    ':ccc_no' => $ccc_no
  );
$patient_exists= _select($sqlcheck, $paramscheck);
if($patient_exists[0]['exists']>0)
{
    //Referral Exist Hence has been initiated
    $sql3 = "SELECT tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM tbl_location "  
            ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
            ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
            ." WHERE tbl_location.mfl_code=(SELECT  initiator_mfl_code FROM tbl_refferal WHERE ccc_no=:ccc_no AND r_status=0 ORDER BY tbl_refferal.refferal_id DESC LIMIT 1) AND tbl_location_details.location_type=1";
            $params3 = array(
        ':ccc_no' => $ccc_no 
     );
    $reffering_location_details= _select($sql3, $params3);


    //Get Details Of Accepting Facility
    $sql9 = "SELECT tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM tbl_location "
          
    ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
    ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
    ." WHERE tbl_location.mfl_code=:mfl_code AND tbl_location_details.location_type=1";
      $params9 = array(

':mfl_code' => $user_details[0]['mfl_code']
);
    $accepting_details= _select($sql9, $params9);

    if($status==1) //Accepted Referral
    {
        $sql = "UPDATE tbl_refferal SET acceptor_id=:acceptor_id, acceptance_date=:acceptance_date, "
        . " r_status=1 WHERE ccc_no=:ccc_no AND referral_type='Normal' AND r_status=0 AND reffered_mfl_code=:mfl_code;";
    }else{ //Declined Referral
        $sql = "UPDATE tbl_refferal SET acceptor_id=:acceptor_id, acceptance_date=:acceptance_date, "
        . " r_status=5 WHERE ccc_no=:ccc_no AND referral_type='Normal' AND r_status=0 AND reffered_mfl_code=:mfl_code;";

    }


        $params = array(
        ':acceptor_id' =>  $user_details[0]['user_id'],
        ':acceptance_date' => date('Y-m-d H:i:s'),
        ':ccc_no' =>  $ccc_no,
        ':mfl_code' =>  $user_details[0]['mfl_code'],
        );
        _execute($sql, $params);




}else
{


    //Facility Inititor Details

    $sql3 = " SELECT   tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM "
    ." tbl_patient INNER JOIN tbl_person ON tbl_patient.person_id=tbl_person.person_id "
    ." INNER JOIN tbl_patient_facilities ON tbl_patient_facilities.patient_id = tbl_patient.patient_id "
     ." INNER JOIN  tbl_location ON tbl_patient_facilities.mfl_code=tbl_location.mfl_code "
     ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
 ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
 ." WHERE  tbl_patient.ccc_no = :ccc_no AND tbl_location_details.location_type=1  ORDER BY tbl_patient_facilities.id DESC LIMIT 1; ";
$params3 = array(

':ccc_no' => $ccc_no
);
$reffering_location_details= _select($sql3, $params3);


  //Get Details Of Accepting Facility
  $sql9 = "SELECT tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM tbl_location "
          
  ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
  ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
  ." WHERE tbl_location.mfl_code=:mfl_code AND tbl_location_details.location_type=1";
    $params9 = array(

':mfl_code' => $user_details[0]['mfl_code']
);
  $accepting_details= _select($sql9, $params9);



    // Save Details as Silent Transfer

    $sqlSilent = "INSERT INTO tbl_refferal ( ccc_no ,referral_type ,initiation_date ,initiator_id ,reffered_mfl_code "
    ." , initiator_mfl_code, acceptance_date, acceptor_id, r_status) "
    ." VALUES ( :ccc_no, :referral_type, :initiation_date, :initiator_id, :reffered_mfl_code,   :initiator_mfl_code, :acceptance_date, :acceptor_id, :r_status
)";



if($status==1) //Accepted Referral
    {

            $paramsSilent = array(
            ':ccc_no' => $ccc_no,
            ':referral_type' => 'Silent',
            ':initiation_date' => date("Y-m-d h:i:sa"),
            ':initiator_id' => $user_details[0]['user_id'],
            ':initiator_mfl_code' => '',
            ':reffered_mfl_code' => $user_details[0]['mfl_code'],
            ':acceptance_date'=>date("Y-m-d h:i:sa"),
            ':acceptor_id'=>$user_details[0]['user_id'],
            ':r_status'=>'1'
            // ':drug_days' => $number_days,
            );
    }else
    {

        $paramsSilent = array(
            ':ccc_no' => $ccc_no,
            ':referral_type' => 'Silent',
            ':initiation_date' => date("Y-m-d h:i:sa"),
            ':initiator_id' => $user_details[0]['user_id'],
            ':initiator_mfl_code' => '',
            ':reffered_mfl_code' => $user_details[0]['mfl_code'],
            ':acceptance_date'=>date("Y-m-d h:i:sa"),
            ':acceptor_id'=>$user_details[0]['user_id'],
            ':r_status'=>'5'
            // ':drug_days' => $number_days,
         );

    }


_execute($sqlSilent, $paramsSilent);



}
 
     
    if($status==1) //Accepted Referral
    {
        //Get PatientID
        $sql4 = "SELECT patient_id FROM tbl_patient WHERE ccc_no=:ccc_no";
          $params4 = array(
        ':ccc_no' => $ccc_no
        );

        $patient_id= _select($sql4, $params4);

        //Insert New Patient Location Details & Close existing Refferal

        $sql6 = "UPDATE tbl_patient_facilities SET to_date=:to_date WHERE patient_id=:patient_id AND to_date IS NULL;";
     $params6 = array(
     ':patient_id' =>  $patient_id[0]['patient_id'],
     ':to_date' => date('Y-m-d H:i:s'),
     );
     _execute($sql6, $params6);

        $sql5 = "INSERT INTO tbl_patient_facilities (patient_id, mfl_code, from_date, created_at) VALUE	(:patient_id, :mfl_code, :from_date, :created_at);";
     $params5 = array(
        ':patient_id' =>  $patient_id[0]['patient_id'],
     ':mfl_code' =>  $user_details[0]['mfl_code'],
     ':from_date' => date('Y-m-d H:i:s'),
     ':created_at' =>  date('Y-m-d H:i:s'),
     );
     _execute($sql5, $params5);

    }


        return array($accepting_details, $reffering_location_details);
       
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





function initiate_referals_details($ussdUser,$ccc_number, $number_days) {
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
                ':msisdn' => $ussdUser->msisdn
              );
    $user_details= _select($sql1, $params2);


      $sql3 = "SELECT tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM tbl_location "
             ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
            ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
            ." WHERE tbl_location.mfl_code=:mfl_code AND tbl_location_details.location_type=1";
              $params3 = array(
    
        ':mfl_code' => $user_details[0]['mfl_code'],
    );
    $provider_location_details= _select($sql3, $params3);

   

     

         return $provider_location_details;
}


function get_provider_location_details($ussdUser,$ccc_number, $number_days) {
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
                ':msisdn' => $ussdUser->msisdn
              );
    $user_details= _select($sql1, $params2);


      $sql3 = "SELECT tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM tbl_location "
             ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
            ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
            ." WHERE tbl_location.mfl_code=:mfl_code AND tbl_location_details.location_type=1";
              $params3 = array(
    
        ':mfl_code' => $user_details[0]['mfl_code'],
    );
    $provider_location_details= _select($sql3, $params3);

   // print_r($provider_location_details); exit();

     

         return $provider_location_details;
}

function checkCccNumber_exists($ccc_no, $ussdUser) {
    $ussdUserList = array();
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
                ':msisdn' => $ussdUser->msisdn
              );
    $user_details= _select($sql1, $params2);


    $sql = " SELECT  tbl_patient.*  FROM "
    ." tbl_patient INNER JOIN tbl_person ON tbl_patient.person_id=tbl_person.person_id "
    ." INNER JOIN tbl_patient_facilities ON tbl_patient_facilities.patient_id = tbl_patient.patient_id "
    ." INNER JOIN (	SELECT MAX(tbl_patient_facilities.id) as id, tbl_patient_facilities.patient_id  FROM tbl_patient_facilities GROUP BY  tbl_patient_facilities.patient_id) tbl2 ON tbl2.id =tbl_patient_facilities.id"
    ." WHERE  tbl_patient.ccc_no = :ccc_no AND tbl_patient.is_active=1 AND tbl_patient_facilities.mfl_code=:mfl_code  ORDER BY tbl_patient_facilities.id DESC LIMIT 1; ";
    $params = array(
        ':ccc_no' => $ccc_no,
        ':mfl_code' => $user_details[0]['mfl_code'],
    );
    $resultset = _select($sql, $params);
    
    foreach ($resultset as $record) {
        $ussdUser = new UssdFacility();
        $ussdUser->ccc_no = $record['ccc_no'];
        $ussdUserList[] = $ussdUser;
    }
    return $ussdUserList;
}

function checkCccNumber($ccc_no) {
    $ussdUserList = array();
    $sql = "SELECT * FROM tbl_patient WHERE ccc_no=:ccc_no and is_active=1";
    $params = array(
        ':ccc_no' => $ccc_no,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $ussdUser = new UssdFacility();
        $ussdUser->ccc_no = $record['ccc_no'];
        $ussdUserList[] = $ussdUser;
    }
    return $ussdUserList;
}


function checkMflCode($code) {
    $ussdUserList = array();
    $sql = "SELECT * FROM tbl_master_facility INNER JOIN tbl_location ON tbl_location.mfl_code=tbl_master_facility.`code`" 
           . " WHERE tbl_location.mfl_code = :code LIMIT 1 ";
    $params = array(
        ':code' => $code,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $ussdUser = new UssdFacility();
        $ussdUser->code = $record['code'];
        $ussdUserList[] = $ussdUser;
    }
    return $ussdUserList;
}
  
function initiate_referal($ussdUser,$ccc_number, $mflCode,$apptDate, $regiment) {
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
                ':msisdn' => $ussdUser->msisdn
              );
    $user_details= _select($sql1, $params2);

    $sql9 = "SELECT tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM tbl_location "
          
            ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
            ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
            ." WHERE tbl_location.mfl_code=:mfl_code AND tbl_location_details.location_type=1";
              $params9 = array(
    
        ':mfl_code' => $user_details[0]['mfl_code']
    );
    $initiator_location_details= _select($sql9, $params9);


      $sql3 = "SELECT tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM tbl_location "
          
            ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
            ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
            ." WHERE tbl_location.mfl_code=:mfl_code AND tbl_location_details.location_type=1";
              $params3 = array(
    
        ':mfl_code' => $mflCode
    );
    $reffered_location_details= _select($sql3, $params3);
  //  print_r($reffered_location_details); exit();

      $sql = "INSERT INTO tbl_refferal ( ccc_no ,referral_type ,initiation_date ,initiator_id ,reffered_mfl_code ,appointment_date"
             ." ,current_regimen , initiator_mfl_code) "
             ." VALUES ( :ccc_no, :referral_type, :initiation_date, :initiator_id, :reffered_mfl_code, :appointment_date,  :current_regimen, :initiator_mfl_code
)";

        //$date=date_create("2013-03-15");
        //echo date_format($date,"Y/m/d H:i:s");
        //Generate a valid date
        $array_date=str_split(str_replace('-','',str_replace('/','',$apptDate)), 2);
       
        $params = array(
            ':ccc_no' => $ccc_number,
            ':referral_type' => 'Normal',
            ':initiation_date' => date("Y-m-d h:i:sa"),
            ':initiator_id' => $user_details[0]['user_id'],
            ':initiator_mfl_code' => $user_details[0]['mfl_code'],
            ':reffered_mfl_code' => $mflCode,
            ':appointment_date' => date('Y-m-d',strtotime($array_date[0].'-'.$array_date[1].'-'.$array_date[2].$array_date[3])),
            ':current_regimen' => $regiment,
            // ':drug_days' => $number_days,
        );
         _execute($sql, $params);

         return array($initiator_location_details, $reffered_location_details);
}


function transit($ussdUser,$ccc_number, $number_days) {
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
                ':msisdn' => $ussdUser->msisdn
              );
    $user_details= _select($sql1, $params2);


  

            $sql3 = " SELECT   tbl_location_details.telephone, tbl_master_facility.`name` as facility_name FROM "
                   ." tbl_patient INNER JOIN tbl_person ON tbl_patient.person_id=tbl_person.person_id "
	               ." INNER JOIN tbl_patient_facilities ON tbl_patient_facilities.patient_id = tbl_patient.patient_id "
	                ." INNER JOIN  tbl_location ON tbl_patient_facilities.mfl_code=tbl_location.mfl_code "
	                ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code=tbl_master_facility.`code` "
	            ." INNER JOIN tbl_location_details ON tbl_location_details.location_id=tbl_location.location_id "
	            ." WHERE  tbl_patient.ccc_no = :ccc_no AND tbl_location_details.location_type=1  ORDER BY tbl_patient_facilities.id DESC LIMIT 1; ";
              $params3 = array(
    
        ':ccc_no' => $ccc_number
    );
    $patient_location_details= _select($sql3, $params3);

      $sql = "INSERT INTO tbl_refferal ( ccc_no ,referral_type ,initiation_date ,initiator_id ,initiator_mfl_code, drug_days, r_status
) VALUES ( :ccc_no, :referral_type, :initiation_date, :initiator_id, :initiator_mfl_code,  :drug_days, :r_status
)";
        $params = array(
            ':ccc_no' => $ccc_number,
            ':referral_type' => 'Transit',
            ':initiation_date' => date("Y-m-d h:i:sa"),
             ':initiator_id' => $user_details[0]['user_id'],
              ':initiator_mfl_code' => $user_details[0]['mfl_code'],
               ':drug_days' => $number_days,
               ':r_status' => '1'
        );
         _execute($sql, $params);

         return $patient_location_details;
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
    //print_r($ussdUser); exit();
    $sql = "UPDATE tbl_provider SET pin=:pin, pin_generation_date=:pin_generation_date "
       . " WHERE msisdn=:msisdn";
        $params = array(
        ':msisdn' => $ussdUser,
        ':pin' => $pin,
        ':pin_generation_date' => date('Y-m-d H:m:s'),
        );
     //  return _select($sql, $params);
        if(_execute($sql, $params))
            {
                return $pin;
            };
}


function getDateCreated($msisdn) {
    $ratesList = array();
     $sql = "SELECT msisdn, pin, pin_generation_date FROM tbl_provider WHERE  msisdn=:msisdn";
    $params = array(
        ':msisdn' => $msisdn,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $rate = new UssdFacility();
        $rate->msisdn = $record['msisdn'];
         $rate->pin = $record['pin'];
        $rate->created_date = $record['pin_generation_date'];
        $ratesList[] = $rate;
    }
    return $ratesList;
}


function searchMfl($facilityName) {
    $mflList = array();
    $sql = "SELECT tbl_master_facility.`name`,tbl_master_facility.`code` , GROUP_CONCAT(' ',tbl_clinictypes.type_desc,': ', "
    ."tbl_location_details.telephone) as ContactDetails"
     . " FROM "
    . " tbl_location"
    ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code = tbl_master_facility.`code`"
    ." INNER JOIN tbl_location_details ON tbl_location_details.location_id = tbl_location.location_id"
    ." INNER JOIN tbl_clinictypes ON tbl_clinictypes.type_id=tbl_location_details.location_type"
     ." WHERE tbl_master_facility.`code` = '$facilityName'"
    ." GROUP BY    tbl_master_facility.`name`,tbl_master_facility.`code`"
    ." LIMIT 1;";
    $params = array(
        ':facilityName' => $facilityName,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $mflCategory = new UssdFacility();

        $mflCategory->code = $record['code'];
        $mflCategory->name = $record['name'];
        $mflCategory->ContactDetails = $record['ContactDetails'];
  
        $mflList[] = $mflCategory;
    }
    return $mflList;
}



function searchFacilityName($facilityName) {
    $mflList = array();
    $sql = "SELECT tbl_master_facility.`name`,tbl_master_facility.`code` , GROUP_CONCAT(' ',tbl_clinictypes.type_desc,': ', "
    ."tbl_location_details.telephone) as ContactDetails"
     . " FROM "
    . " tbl_location"
    ." INNER JOIN tbl_master_facility ON tbl_location.mfl_code = tbl_master_facility.`code`"
    ." INNER JOIN tbl_location_details ON tbl_location_details.location_id = tbl_location.location_id"
    ." INNER JOIN tbl_clinictypes ON tbl_clinictypes.type_id=tbl_location_details.location_type"
     ." WHERE tbl_master_facility.`name` LIKE '%$facilityName%'"
    ." GROUP BY    tbl_master_facility.`name`,tbl_master_facility.`code`"
    ." LIMIT 3;";
    $params = array(
        ':name' => $facilityName,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $mflCategory = new UssdFacility();

        $mflCategory->code = $record['code'];
        $mflCategory->name = $record['name'];
        $mflCategory->ContactDetails = $record['ContactDetails'];
  
        $mflList[] = $mflCategory;
    }
    return $mflList;
}





function searchPatientDetails($cccNumber) {
    $mflList = array();
     $sql="SELECT "
            ." tbl_person.firstname, tbl_person.lastname, tbl_patient.date_of_birth, tbl_patient.ccc_no,  tbl_patient.art_start_date, tbl_patient_observations.viral_load, tbl_patient_observations.regimen, "
            ." tbl_patient_observations.tca, tbl_patient_facilities.mfl_code FROM "
            ." tbl_patient INNER JOIN tbl_person ON tbl_patient.person_id=tbl_person.person_id  "      
            ." INNER JOIN tbl_patient_facilities ON tbl_patient_facilities.patient_id = tbl_patient.patient_id "
            ." INNER JOIN tbl_patient_observations ON tbl_patient_observations.patient_id = tbl_patient.patient_id "
            ." WHERE tbl_patient.is_active=1 AND tbl_patient.ccc_no=:cccNumber ORDER BY tbl_patient_facilities.id DESC, tbl_patient_observations.id DESC "
            ." LIMIT 1";
    $params = array(
        ':cccNumber' => $cccNumber,
    );
    $resultset = _select($sql, $params);
    foreach ($resultset as $record) {
        $mflCategory = new UssdFacility();
        $mflCategory->firstname = $record['firstname'];
        $mflCategory->lastname = $record['lastname'];
        $mflCategory->date_of_birth = $record['date_of_birth'];
        $mflCategory->ccc_no = $record['ccc_no'];
        $mflCategory->art_start_date = $record['art_start_date'];
        $mflCategory->viral_load = $record['viral_load'];    
        $mflCategory->regimen = $record['regimen'];
        $mflCategory->tca = $record['tca'];
        $mflCategory->mfl_code = $record['mfl_code'];
        $mflList[] = $mflCategory;
    }
    return $mflList;
}


function facilityQuerriesLogName($ussdUser,$facilityName) {
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
    
        ':msisdn' => $ussdUser->msisdn
    );
    $user_details= _select($sql1, $params2);
    $sql = "INSERT INTO  tbl_facility_queries (initiator_id ,initiator_mflcode ,query_param ,phone_no) "
          . " VALUES(:initiator_id,:facility_mfl,:facility_name,:phone_no)";
    $params = array(
        ':initiator_id' => $user_details[0]['user_id'],
        ':facility_mfl' => $user_details[0]['mfl_code'],
        ':facility_name' => $facilityName,
        ':phone_no' => $ussdUser->msisdn,
    );
    return _execute($sql, $params);
}

function mflQuerriesLogName($ussdUser,$facilityName) {
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
    
        ':msisdn' => $ussdUser->msisdn
    );
    $user_details= _select($sql1, $params2);
    $sql = "INSERT INTO  tbl_facility_queries (initiator_id ,initiator_mflcode ,query_param ,phone_no) "
          . " VALUES(:initiator_id,:facility_mfl,:facility_name,:phone_no)";
    $params = array(
        ':initiator_id' => $user_details[0]['user_id'],
        ':facility_mfl' => $user_details[0]['mfl_code'],
        ':facility_name' => $facilityName,
        ':phone_no' => $ussdUser->msisdn,
    );
    return _execute($sql, $params);
}

function updateContactLog($ussdUser,$phoneNumber, $clinicType) {
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
            . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
            . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
            . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
              $params2 = array(
    
        ':msisdn' => $ussdUser->msisdn
    );
    $user_details= _select($sql1, $params2);
    $sql = "INSERT INTO  tbl_facility_queries (initiator_id ,initiator_mflcode ,updated_phone ,clinic_type, phone_no) "
          . " VALUES(:initiator_id,:facility_mfl,:updated_phone ,:clinic_type,:phone_no)";
    $params = array(
        ':initiator_id' => $user_details[0]['user_id'],
         ':facility_mfl' => $user_details[0]['mfl_code'],
        ':updated_phone' =>$phoneNumber,
        ':clinic_type'=>$clinicType,
        ':phone_no' => $ussdUser->msisdn,
    );
    return _execute($sql, $params);
}

function log_sms_sent($ussdUser, $msg_type,$msg,$msg_status) {
    
    $sql1 = "SELECT  tbl_user.user_id, tbl_provider.mfl_code FROM tbl_user "
    . " INNER JOIN tbl_person ON tbl_user.person_id=tbl_person.person_id "
    . " INNER JOIN tbl_provider ON tbl_provider.person_id=tbl_person.person_id "
    . " WHERE tbl_provider.msisdn=:msisdn LIMIT 1";
      $params2 = array(

':msisdn' => $ussdUser->msisdn
);
$user_details= _select($sql1, $params2);
    //print_r($msg_status); exit();
    
    $msg_status = json_decode($msg_status, true);
    $sql = "INSERT INTO tbl_outgoing (mfl_code, message, destination, message_type, cost, msg_status, message_date, sent_date, at_msg_id ) "
          . " VALUES(:mfl_code, :message, :destination, :message_type, :cost, :msg_status, :message_date, :sent_date, :at_msg_id)";
    $params = array(
        ':mfl_code' => $user_details[0]['mfl_code'],
         ':message' => $msg,
        ':destination' =>$msg_status["data"]["SMSMessageData"]["Recipients"][0]["number"],
        ':message_type'=>$msg_type,
        ':cost' => $msg_status["data"]["SMSMessageData"]["Recipients"][0]["cost"],
        ':msg_status' =>$msg_status["data"]["SMSMessageData"]["Recipients"][0]["status"],
         ':message_date' => date('Y-m-d H:i:s'),
        ':sent_date' =>date('Y-m-d H:i:s'),
        ':at_msg_id'=>$msg_status["data"]["SMSMessageData"]["Recipients"][0]["messageId"]
    );

    
    return _execute($sql, $params);
}