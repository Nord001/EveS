<?php

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

?>