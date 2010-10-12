<?php

require_once './libs/db.php';

include('./libs/sm/Smarty.class.php');
$smarty = new Smarty;

require_once('./libs/auth.php');
$user = new CUser;
if (!$user->checkSession() or !in_array($user->group, $acl_allowed)){
    die("Not authorised!<br>\n<a href='index.php'>Return</a>");
}

$DB = new mydb;
$DB->connect();
// Load all t2 items and make list

$ingid = $_REQUEST["grselect"];

if (!is_Numeric($ingid) & !empty($ingid)){ die("Missing param<br>\n"); }


$sql = "SELECT ig.groupID, ig.groupName, count(it.typeName), ib.techLevel
FROM invGroups As `ig`
INNER JOIN invTypes AS `it` ON ig.groupID=it.groupID
INNER JOIN invBlueprintTypes AS `ib` ON it.typeID=ib.productTypeID
WHERE (
ig.categoryID IN (6,7) AND
it.published='1' AND
ib.techLevel='2' AND
(ig.groupID<'773' OR ig.groupID>'786')
) group by groupID Order By groupName";
$groups = $DB->select_and_fetch($sql, "groupID");

$selector = "";
foreach ($groups as $gid => $gr){
    $groupName = $gr['groupName'];
    if ($gid == $ingid){
        $selector.="<option value='$gid' SELECTED>$groupName</option>";
    }else{
        $selector.="<option value='$gid'>$groupName</option>";
    }
}

if (!empty($ingid)){

$t2all = $DB->select_and_fetch("SELECT it.typeID, it.marketGroupID, it.typeName
FROM `invBlueprintTypes` AS `ibt`
LEFT JOIN invTypes AS it ON ibt.productTypeID = it.typeId
WHERE (
ibt.techLevel = '2'
AND it.published = '1'
AND it.marketGroupID IS NOT NULL
AND it.groupID = $ingid
AND ((it.marketGroupID < '1206') OR (it.marketGroupID > '1239'))
)
ORDER BY it.marketGroupID", "typeID");

$group_marker = "-1";
$itemlist ="";
foreach ($t2all  as $t2id => $v){
$gid = $v["marketGroupID"];
$itemlist .= "<a href='./t2item.php?iid=$t2id'>".$v["typeName"]."</a><br>";
}
} //empty

$smarty->assign("user", $user);
$smarty->assign("selector", $selector);
$smarty->assign("itemlist", $itemlist);
$smarty->display('t2list.tpl');
