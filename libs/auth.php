<?php
// == config here ==================================================
$auth_db = "localforum";
$session_table = "insessions";
$user_table = "inmembers";
$acl_allowed = array(4,11,13);


// == end of config ================================================


require_once './libs/db.php';

class CUser{

public $authorized, $name, $group;
private $UDB, $uid;

//  default constructor
function CUser(){
    global $auth_db;
    $this->authorized = false;
    $this->UDB = new mydb;
    $this->UDB->settings['sql_database'] =  $auth_db;
    $this->UDB->connect();
}

function setCookie($k, $v){
    // one year limit ;)
    $expires = time() + 60*60*24*365;
    @setcookie($k, $v, $expires, "/", "");
    return;
}

function getCookie($k){
    if ( isset($_COOKIE[$k]) ) {
            return urldecode($_COOKIE[$k]);
    }else{
        return NULL;
    }
}

function getUID(){
    return $this->uid;
}

function getAuth(){
    return $this->authorized;
}

function startSession(){
    global $session_table;
    // remove old stuff
    $this->killSession();
    $this->cleanupSession();
    // extend session if exist
    // prepare var's for insert query
    $new_sid   = $this->session_id  = md5( uniqid(microtime()) );
    $new_name  = $this->name;
    $new_uid   = $this->uid;
    $new_ip    = $_SERVER['REMOTE_ADDR'];
    $new_useragent = substr($_SERVER['HTTP_USER_AGENT'],0,50);
    $new_time =  time();
    $new_location = "Market Tool";
    $new_group = $this->group;

    $query_chk = "SELECT * FROM $session_table WHERE member_id=$new_uid";
    $this->UDB->select_and_fetch($query_chk, "id");
    if (count($rec) == 1){
	reset($rec); $fk = key($rec);
	// found session for this user, use it
	$new_sid = $rec[$fk]['id'];
    }else{

    $query_new = "INSERT INTO $session_table (id, member_name, member_id, ip_address, browser, running_time, location, login_type, member_group) ".
    	     "VALUES ('$new_sid', '$new_name', '$new_uid', '$new_ip', '$new_useragent', '$new_time', '$new_location', '', $new_group)";

    $this->UDB->insert($query_new);
    }
    
    $this->setCookie("session", $new_sid);
}

// return true (session valid) or false
function checkSession(){
    global $session_table;
    $csid = $this->getCookie("session");
    $found_sid = preg_replace("/([^a-zA-Z0-9])/", "", $csid);
    if (empty($found_sid) | is_null($found_sid))  return false;
    $sql = "SELECT *FROM $session_table WHERE id='$found_sid'";
    $rec = $this->UDB->select_and_fetch($sql, "id");
    if (count($rec) == 1){
	reset($rec); $fk = key($rec);
	$this->uid   = $rec[$fk]['member_id'];
	$this->name  = $rec[$fk]['member_name'];
	$this->group = $rec[$fk]['member_group'];
	$this->authorized = true;
	return true;
    }
    return false;
}

function killSession(){
    global $session_table;
    $uid = $this->uid;
    $this->UDB->delete("DELETE FROM $session_table WHERE member_id='$uid'");
    $this->setCookie("session", -1);
}

function cleanupSession(){

}


function login($n, $p){
    global $user_table;

    $inp_name = addslashes(filter_var($n, FILTER_SANITIZE_EMAIL));
    $inp_pass = addslashes(filter_var($p, FILTER_SANITIZE_EMAIL));
    $inp_pass_md5 = md5($inp_pass);
// we have pair' let's check our db

    $rec = $this->UDB->select_and_fetch("SELECT * from $user_table WHERE name='$inp_name' and password='$inp_pass_md5'","id");    
    if ( count($rec) ==1){
	reset($rec); $fk = key($rec);
    // User is recognized, welcome
	$this->uid   = $rec[$fk]['id'];
	$this->name  = $rec[$fk]['name'];
	$this->group = $rec[$fk]['mgroup'];
	$this->startSession();
	$this->authorized = true;

    } else {
	$this->authorized = false;
    }

}

function logout(){
    $this->killSession();
    $this->authorized = false;
}

} // class


?>