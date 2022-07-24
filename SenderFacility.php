<?php
include_once('classes\DotEnv.php');
include_once('classes\SMS.php');
use DevEnvReader\DotEnv;

(new DotEnv(__DIR__ . '/.env'))->load();


include_once('Models.php');

$send=new _sender();

//Process Facility Queries
$username =$_ENV['DB_USERNAME'];
$password =$_ENV['DB_PASSWORD'];
$database =$_ENV['DB_DATABASE'];
$host =$_ENV['DB_HOST'];
$port=$_ENV['DB_PORT'];
try {
$pdo = new PDO("mysql:host=$host;port=".$port.";dbname=$database", $username, $password);
$pdo->beginTransaction();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Fetch Pending Facility Queries
foreach($pdo->query('SELECT * FROM tbl_facility_queries WHERE q_status="0";') as $row)
{
    //Check Type of Search
     if($row['facility_name']<>'')
    {
        $stmtQ = $pdo->prepare("CALL sp_searchfacility_name(:p_facility_name)");
		$stmtQ->bindValue(':p_facility_name', $row['facility_name']);
		$stmtQ->execute();
        $res = $stmtQ->fetchAll();
        //Loop Through & send notification 
        foreach($res as $i)
        {
            //echo $i['Resultset']; exit();
           $resurn_msg=$send->sendSMS($_ENV['SENDER_URL'],$i['Resultset'],$row['initiator_msdn'], $_ENV['SHORTCODE'],$_ENV['API-TOKEN'] );
           print_r($resurn_msg);

        }

        //Update Status Of Query 

    }else if($row[facility_no]<>'')
    {
        $stmtQ = $pdo->prepare("CALL sp_searchfacility_mfl(:p_facility_mfl)");
		$stmtQ->bindValue(':p_facility_mfl', $row[facility_no]);
		$stmtQ->execute();
        $res = $stmtQ->fetchAll();
        //Loop Through & send notification 

        //Update Status Of Query 

    }


    
}

} catch (PDOException $error) {
    // var_dump($sql);
    // var_dump($params);
    // var_dump($error);        
    error_log("[ERROR : " . date("Y-m-d H:i:s") . "] senderFacility error: " . $error, 3, LOG_FILE);
 }

?>