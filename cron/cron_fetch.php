<?php


// header
require_once "./../libs/db.php";
require_once './../libs/ale/factory.php';




// ids - input, array of requested typeID's
// minp, maxp - out, prices min and median
function ask_central($ids, $minp, $medp, $reg){
$ale = AleFactory::getEVECentral();

$params = array('typeid'=>$ids, 'regionlimit' => $reg);
$xml = $ale->marketstat($params);
// todo: error handler

$minp = array();
$medp = array();
$count = 0;
foreach($xml->marketstat[0]->{type} as $mat){
    $count++;
    $mat_id = (string) $mat['id'];
    $medprice = (double) $mat->sell[0]->median;
    $minprice = (double) $mat->sell[0]->min;
    $minp += array( $mat_id => $minprice);
    $medp += array( $mat_id => $medprice);
}

if ($count != count($ids)) return -1;
return 0;
}

function start_log_op($DB, $op_id="0"){
    $sql="INSERT INTO eves_cron (op_time, op_id, op_status)
		         VALUES (NOW(), '$op_id', '0')";
return $DB->insert($sql);
}

function end_log_op($DB, $op_id, $op_status){
    $sql="UPDATE eves_cron SET op_status=$op_status WHERE op_id=$op_id";
return $DB->update($sql);
}

function store_prices($DB, $op_id, &$minp, &$medp, $reg){
foreach ($minp as $id => $min){
    $med = $medp[$id];
    $sql = "INSERT INTO eves_moon_central  (typeID, op_id, mkt_min, mkt_med, regionID) 
				    VALUES ('$id', '$op_id', '$min', '$med', '$reg')";
$DB->insert($sql);
}
return;
}

// ======== main() ==========================================================
$jita_reg = "10000002";


$DB = new mydb;
$DB->connect();

$op_status = 0;
// check log table if prices have been stored already

$ops_today = $DB->select_and_fetch("SELECT * FROM eves_cron WHERE DATE(op_time) = DATE(NOW())");
if (count($ops_today)>0) die("Already fetched<br>");


$op_id = start_log_op($DB,1); // 1 for storing moon mats

// Load all matherials
$moon_raw = $DB->select_and_fetch("SELECT * FROM invTypes WHERE groupID='427'", "typeID");
$moon_intermid = $DB->select_and_fetch("SELECT * FROM invTypes WHERE groupID='428'", "typeID");
$moon_complex = $DB->select_and_fetch("SELECT * FROM invTypes WHERE groupID='429'", "typeID");

// Prepare index arrays for queries
$raw_ids = array();
foreach($moon_raw as $raw){
    array_push($raw_ids, $raw['typeID']);
}

$intermid_ids = array();
foreach($moon_intermid as $intermid){
    array_push($intermid_ids, $intermid['typeID']);
}

$complex_ids = array();
foreach($moon_complex as $complex){
    array_push($complex_ids, $complex['typeID']);
}

/// Ask and store prices

if (ask_central($raw_ids,&$raw_min, &$raw_med, $jita_reg)==0){
    store_prices($DB,$op_id, $raw_min, $raw_med, $jita_reg);
} else{
     $op_status = -1;
}

if (ask_central($intermid_ids,&$intermid_min, &$intermid_med, $jita_reg)==0){
    store_prices($DB,$op_id, $intermid_min, $intermid_med, $jita_reg);
} else{
     $op_status = -1;
}

if (ask_central($complex_ids,&$complex_min, &$complex_med, $jita_reg)==0){
    store_prices($DB,$op_id, $complex_min, $complex_med, $jita_reg);
} else{
     $op_status = -1;
}

end_log_op($DB,$op_id, $op_status);

$craw  = count ($raw_min);
$cintermid = count($intermid_min);
$ccomplex = count($complex_min);
$status = $op_status == 0? "sucesefully": "with error";
print("Stored $craw raw, $cintermid intermideate and $ccomplex compex prices $status");
?>