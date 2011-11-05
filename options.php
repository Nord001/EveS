<?php
function store_opt($DB, $uid, $mopt, $sopt, $muser, $suser, $misc ){
    
    $s_muser = serialize($muser); // mineral prices
    $s_suser = serialize($suser); // user skills
    $s_misc  = serialize($misc);  // user other pref's

    $known_opts = $DB->select_and_fetch("SELECT * FROM  userOpts WHERE uid=$uid", "uid");
    $kid = $known_opts[$uid]["id"];
    if (count($known_opts)>0){
	// update record
	$DB->update("UPDATE userOpts SET minOpt='$mopt', skillOpt='$sopt', miscOpt='$s_misc', 
                                         minDefined='$s_muser', skillDefined='$s_suser' 
                                    WHERE id='$kid'");	
	return true;
    }else{
	// create new record
	$DB->insert("INSERT INTO userOpts  (uid, id, minOpt, skillOpt, minDefined, skillDefined, miscOpt) 
				    VALUES ('$uid','','$mopt', '$sopt', '$s_muser', '$s_suser', '$s_misc') ");
	return true;
    }
return false;
}

require_once './libs/opts.php';
/*
function load_options($DB, $uid){
    $opts = $DB->select_and_fetch("SELECT * FROM  userOpts WHERE uid=$uid", "uid");
if (count($opts) >0){
    $user_opts = $opts[$uid];
    // repack other options
    $umisc = unserialize($user_opts['miscOpt']);
    $user_opts['bpoT1me']  =  $umisc['bpoT1me'];
    $user_opts['bpoT2me']  =  $umisc['bpoT2me'];
    $user_opts['bpoT22me'] =  $umisc['bpoT22me'];

}else{
// Load default 
    $user_opts = array("minOpt" => "jita", "skillOpt" => "perf", "bpoT1me" => "10", "bpoT2me" => "-4", "bpoT22me" => "50");
}
return $user_opts;
}
*/

function parse_input($data, &$mopt, &$sopt, &$muser, &$suser, &$misc){

if (in_array($data["mset"], array("jita", "user", "zero") )){
    $mopt = $data["mset"];
} else {
    $mopt = "jita";
}

if (in_array($data["rset"], array("perf","user"))){
    $sopt = $data["rset"];
} else {
    $sopt = "perf";
}

$muser = array();

for ($i=13; $i<20; $i++){
    $input = $data["m_$i"];
    $muser["$i"]  = is_numeric($input)? $input: 0;
}

$suser = array();
for ($i=1; $i<3; $i++){
    $input = $data["r_$i"];
    $suser["$i"]  = is_numeric($input)? $input: 0;
}
$misc = array();
$input = $data["bpc_m1"];
$misc['bpoT1me'] = is_numeric($input)? $input: 0;
$input = $data["bpc_m2"];
$misc['bpoT2me'] = is_numeric($input)? $input: 0;
$input = $data["bpc_m22"];
$misc['bpoT22me'] = is_numeric($input)? $input: 0;

//var_dump($misc);
return;
}

require_once('./libs/auth.php');
$user = new CUser;

require_once './libs/db.php';
$DB = new mydb;
$DB->connect();


if (!$user->checkSession() or !in_array($user->group, $acl_allowed)){
    die("Not authorised!<br>\n<a href='index.php'>Return</a>");
}

include('./libs/sm/Smarty.class.php');
$smarty = new Smarty;

parse_str($_SERVER['QUERY_STRING'], $url);
//var_dump($_REQUEST);
// open jscript arrays
$jmopts = "";
//$smopts = "sopts = array(";
if (isset($url['a']) & ($url['a'] == 'save')){ //store config
    parse_input($_POST, $mopt, $sopt, $muser, $suser, $misc);
    $status = "fail";
    if (store_opt($DB, $user->getUID(), $mopt, $sopt, $muser, $suser, $misc)){
	$status="success";
    }else{
	$status="fail";
    }
    $url = "options.php";

// create redirect page 
    @header("Content-type: text/html; charset=windows-1251\r\n");
    @header("Refresh: 0;url=".$url);
    $smarty->assign("url", $url);
    $smarty->assign("status", $status);
    $smarty->display('redirect.tpl');

} else { // load config
    $opts = load_options($DB, $user->getUID());

    $mopts = unserialize($opts["minDefined"]);
    $sopts = unserialize($opts["skillDefined"]);

    for ($i=13; $i<20; $i++){
	$jmopts .= "mo.m_$i.value = ".$mopts[$i].";\n";
    }

$smarty->assign("jmopts", $jmopts);
$smarty->assign("user", $user);
$smarty->assign("opts", $opts);
//var_dump($opts);
$smarty->display('options.tpl');


}




?>