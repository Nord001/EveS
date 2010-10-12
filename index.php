<?php
require_once('./libs/auth.php');
$user = new CUser;


include('./libs/sm/Smarty.class.php');
$smarty = new Smarty;

parse_str($_SERVER['QUERY_STRING'], $url);
//var_dump($_REQUEST);

if (isset($url['a']) & ($url['a'] == 'li')){ //do login stuff
    $user->login($_REQUEST['name'], $_REQUEST['pass']);
}else if (isset($url['a']) & ($url['a'] == 'lo')){ //do login stuff
    $user->logout($_REQUEST['name'], $_REQUEST['pass']);
}else {
    $user->checkSession();
}



$smarty->assign("user", $user);
$smarty->display('index.tpl');


?>