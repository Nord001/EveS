<?php
function numfmt($numstr="", $rzd = 0){
return number_format($numstr, $rzd, ',', ' ');
}

require_once './libs/ale/factory.php';
$ale = AleFactory::getEVECentral();

require_once './libs/db.php';

include('./libs/sm/Smarty.class.php');
$smarty = new Smarty;

require_once('./libs/auth.php');
$user = new CUser;
if (!$user->checkSession() or !in_array($user->group, $acl_allowed)){
    die("Not authorised!<br>\n<a href='index.php'>Return</a>");
}

// Load all t2 items and make list

//$groups = $DB->select_and_fetch("SELECT * FROM `invGroups`");
// Drop NULL marketGroupID as faction items
// Drop rigs 1206..1239 ID's

$DB = new mydb;
$DB->connect();
$ingid = $_REQUEST["grselect"];

if (!is_Numeric($ingid) & !empty($ingid)){ die("Missing param<br>\n"); }
$sql = "SELECT ig.groupID, ig.groupName, count(it.typeName), ib.techLevel
FROM invGroups As `ig`
INNER JOIN invTypes AS `it` ON ig.groupID=it.groupID
INNER JOIN invBlueprintTypes AS `ib` ON it.typeID=ib.productTypeID
WHERE (
ig.categoryID IN (6,7) AND 
it.published='1' AND 
ib.techLevel='1' AND
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
$t1all = $DB->select_and_fetch("SELECT it.typeID, it.marketGroupID, it.typeName, it.groupID, ibt.wasteFactor
FROM `invBlueprintTypes` AS `ibt`
LEFT JOIN invTypes AS it ON ibt.productTypeID = it.typeId
WHERE (
ibt.techLevel = '1'
AND it.published = '1'
AND it.marketGroupID IS NOT NULL
AND it.groupID = '$ingid'
) ORDER BY it.typeName", "typeID");

// Load prices for a) minerals, b) t1 from choosen group

$itemlist = array_keys($t1all);
// Build QUERY IN string
$inq = "IN(";
foreach ($itemlist as $t){
    $inq .= "$t,";
}
$inq .="-1)";

$SourceArray = array("34", "35", "36", "37", "38", "39", "40");  // Перечень материалов, которые используются далее
$SourceName = array(
  34 => "Tritanium",
  35 => "Pyerite",
  36 => "Mexallon",
  37 => "Isogen",
  38 => "Nocxium",
  39 => "Zydrine",
  40 => "Megacyte",
);

$itemflist = array_merge($itemlist, $SourceArray);

$params = array('typeid'=>$itemflist, 'regionlimit' => "10000002");
//var_dump($params);

$xml = $ale->marketstat($params);
$iprices = array();
foreach($xml->marketstat[0]->{type} as $itm){
    $itm_id = (string) $itm['id'];
    $itm_minprice = (double) $itm->sell[0]->min;
    $iprices += array( $itm_id => $itm_minprice);
}

$mscript = "var mparr = new Array;";
foreach($SourceArray as $key => $value)
  $mscript .= "mparr[$key] = " . $iprices[$value] . ";";

// Load materials for requested t1 
$sql = "SELECT typeID, ";
foreach($SourceArray as $key => $value)
{
  $sql .= "SUM( IF( materialTypeID = '$value', quantity, 0 ) ) AS `" . $SourceName[$value] . "`";
  if($key + 1 != sizeof($SourceArray))  // Отдельный костыль ставим на последнюю запятую
    $sql .= ', ';
}
$sql .= "
FROM invTypeMaterials
WHERE typeID $inq
GROUP BY typeID";
$mraw = $DB->select_and_fetch($sql, "typeID");

$table  = "Set BPO ME:&nbsp;<input id='adjr' type='text' name='RootME' value='0' size='5'>
<BUTTON NAME='adjr_' onClick=\"AdjustME('adjr')\">Adjust</BUTTON>";
$table .= "<table id='t1tbl'\n";
$table .= "<tr> <th rowspan='2'>Item name</th>";
foreach($SourceArray as $value)
  $table .= "<th colspan='2'>" . $SourceName[$value] . " (".$iprices[$value].")</th>";
$table .= "
	    <th rowspan='2'>P.Cost</th>
	    <th rowspan='2'>Mkt. min</th>
	    <th rowspan='2'>Profit</th>
	    </tr>";
$table .= "<tr>";
foreach($SourceArray as $value)
  $table .= "<th>Perf.Q</th><th>Real.Q</th>";
$table .= "</tr>";
$row_mark = "row1";
foreach ($t1all  as $t1id => $v){
$item_raw = $mraw[$t1id];
$table .= "<tr class='$row_mark'><td style='white-space: nowrap'>".$v['typeName']."</td>";
foreach($SourceArray as $key => $value)
  $table .= "<td align='right' id='q.$key'>".numfmt($item_raw[$SourceName[$value]])."&nbsp;</td> <td align='right' id='r.$key'>0</td>";
$table .= 
    "<td align='right' id='t.".$t1all[$t1id]['wasteFactor']."'>0</td>".
    "<td align='right' id='m'>".numfmt($iprices[$t1id])."</td>".
    "<td align='right' id='p'>0</td>".
    "</tr>\n";
    $row_mark = $row_mark == "row1"? "row2":"row1";
}
$table .="</table>";
}// empty ingid
$smarty->assign("mscript", $mscript);
$smarty->assign("table", $table);
$smarty->assign("user", $user);
$smarty->assign("selector", $selector);
$smarty->display('t1.tpl');
