<?php

require_once('./libs/auth.php');
$user = new CUser;
if (!$user->checkSession() or !in_array($user->group, $acl_allowed)){
    die("Not authorised!<br>\n<a href='index.php'>Return</a>");
}

include('./libs/sm/Smarty.class.php');
$smarty = new Smarty;

// ===========================================================================================
require_once './libs/db.php';
$DB = new mydb;
$DB->connect();

// ===========================================================================
$data_in = $_REQUEST['getml_data'];
$name_in = $_REQUEST['root_name'];
$num_in = $_REQUEST['root_num'];
$mlist = json_decode(stripslashes($data_in), true);
$sql_in = "";
//var_dump($mlist);
if (!is_array($mlist)) die("Empty matherial list!\n");
foreach ($mlist as $mid => $num){
    if (!is_Numeric($mid)) die("Missing param!\n");
	$sql_in .= "$mid, ";
}

$rq = $DB->select_and_fetch("SELECT * FROM invTypes WHERE typeID IN( $sql_in -1)", "typeID");

$mtbl = "<table id='mtbl'> <tr><th></th><th>Name</th><th>Number</th></tr>";
foreach ($mlist as $mid => $num){
    $mname = $rq[$mid]['typeName'];
    $mtbl .= "<tr><td style='visibility:hidden'>$mid</td><td><a href='javascript:CCPEVE.showMarketDetails($mid)'>$mname</a></td><td id='n' align='right'>$num</td></tr>";
}
$mtbl .= "</table>";

//var_dump($rq);
// ===========================================================================

$smarty->assign("user", $user);
$smarty->assign("mtbl", $mtbl);
$smarty->assign("root_name", $name_in);
$smarty->assign("root_portion", $num_in);
$smarty->display('get_ml.tpl');
?>