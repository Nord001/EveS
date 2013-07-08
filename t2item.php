<?php

$def_t2_me = -4; 
$def_t22_me = 0;


// show matherial for selected item
function StepID(){
parse_str($_SERVER['QUERY_STRING'], $url);
    if ($url['step'] == "2"){
	return 2;
    }
return 1;
}

// Just print number with thousand separator
function numfmt($numstr="", $rzd = 0){
return number_format($numstr, $rzd, ',', ' ');
}

function adjustME($q, $me, $w){
$debug .= "<br>adjustME, q=$q, me=$me, w=$w\n<br>";
    if ($me >= 0){
    	$r = $q + round($q * ($w/100)*(1/($me+1)));
    }else{
	$r = $q + round($q* ($w/100)*(1-($me)));
        }
return $r;
}

// =============================================================================================================
function drawTable($iid, $DB, $ale, $itemnum=1){

$root_item = $DB->select_and_fetch("SELECT * FROM `invTypes` AS it 
INNER JOIN `invBlueprintTypes` AS ibt 
ON it.typeID=productTypeID 
WHERE typeID=$iid", "typeID");

$root_name  = $root_item[$iid]["typeName"];
$root_bpid  = $root_item[$iid]["blueprintTypeID"];
$root_waste = $root_item[$iid]["wasteFactor"];
$root_gid   = $root_item[$iid]["groupID"];
$root_portion = $root_item[$iid]["portionSize"];

$mextra = $DB->select_and_fetch("SELECT t.typeID, t.groupID, t.typeName, r.quantity, r.damagePerJob, recycle
FROM ramTypeRequirements AS r
 INNER JOIN invTypes AS t
  ON r.requiredTypeID = t.typeID
WHERE r.typeID = $root_bpid 
 AND r.activityID = 1
 AND r.damagePerJob > 0", "typeID");

// find prototype id
$prid = 0;
foreach ($mextra as $mid =>$v){
    if ($v["groupID"] == $root_gid){
	$prid = $mid; //prototype id
	break;
    }
}
$mproto = $DB->select_and_fetch("SELECT t.typeID, t.typeName, m.quantity
FROM invTypeMaterials AS m
 INNER JOIN invTypes AS t
  ON m.materialTypeID = t.typeID
WHERE m.typeID = $prid", "typeID");

$mraw = $DB->select_and_fetch("SELECT t.typeID, t.typeName, m.quantity
FROM invTypeMaterials AS m
 INNER JOIN invTypes AS t
  ON m.materialTypeID = t.typeID
WHERE m.typeID = $iid", "typeID");

// Build full list of component id's - will used in requests
$midlist = array();
$midlist = array_keys($mextra);
foreach( $mraw as $i => $v){
    array_push($midlist, $v['typeID']);
};

// Build QUERY IN string
$inq = "IN(";
foreach ($midlist as $mid){
    $inq .= "$mid,";
}
$inq .="-1)";


// check if we have BPO on material, we can make it
$mbpo = $DB->select_and_fetch("SELECT productTypeID FROM invBlueprintTypes WHERE productTypeID $inq", "productTypeID");
$mmkd = array_keys($mbpo); // these we can build

$root_me    = $_REQUEST["iid_me"];
if (!is_Numeric($iid_me) & !empty($iid_me)) die("Missing param<br>\n");

// build next step list -> these we want build
parse_str($_SERVER['QUERY_STRING'], $url);
$build_items = array();

foreach($mmkd as $bit){
    if ($url[$bit."_$iid"] == 'm'){ 
	$bit_num = 1;
	if (in_array($bit, array_keys($mraw))){
	// it is raw mat, apply me
	$bit_num = adjustME($mraw[$bit]['quantity'], $root_me, $root_waste);
    }else{
	$bit_num = $mextra[$bit]['quantity']*$mextra[$bit]['damagePerJob'];
    }
    $build_items[$bit] = $bit_num;
}	
}

// add root item id for eve-central request to materials list
array_push($midlist, $iid);

$params = array('typeid'=>$midlist, 'regionlimit' => "10000002");
//var_dump($params);

$xml = $ale->marketstat($params);
$mprices = array();
foreach($xml->marketstat[0]->{type} as $mat){
    $mat_id = (string) $mat['id'];
    $mat_minprice = (double) $mat->sell[0]->min;
    $mprices += array( $mat_id => $mat_minprice);
}

$table = "
<table id='mt_$iid'>
<tr><td colspan='7' id='n' style='display:none'>$itemnum</td></tr>
<tr><th></th><th width=300px>Material</th><th>Q.Ideal</th><th>Q.Real</th><th>Buy</th><th>Make</th><th>Price</th><th>Sum</th></tr>\n";
// Extra
$grayed = (StepID() == 1) ? "" : "disabled";
foreach ($mextra  as $mid => $v){
    $mename = $v["typeName"];
    $megroup = $v["groupID"];
    $merequired = $v["quantity"]* $v["damagePerJob"];	
    if (in_array($mid, $mmkd)){
	$rbname = $mid."_".$iid;
	if (in_array($mid, array_keys($build_items))){
	    $opts = "<td id='sl'><input type='radio' name='$rbname' value='b' $grayed></td>
		     <td><input type='radio' name='$rbname' value='m' checked $grayed></td>";	
	}else{
	    $opts = "<td id='sl'><input type='radio' name='$rbname' value='b' checked $grayed></td>
		    <td><input type='radio' name='$rbname' value='m' $grayed></td>";	

	}
    }else{
	$opts = "<td></td><td></td>";
    }
    $price = numfmt($mprices[$mid], 2);
    $sum = numfmt($mprices[$mid]*$merequired);
    $table.="<tr><td id='mid' style='visibility:hidden'>$mid</td><td>$mename</td><td id='q'>$merequired</td><td id='rr'>$merequired</td>$opts<td align='right' id='p'>$price</td><td align='right' id='s'>$sum</td></tr>\n";
}
// RAW
foreach ($mraw  as $mid => $v){
    $mrname = $v["typeName"];
    $mrquantity = ($v["quantity"] - $mproto[$mid]["quantity"])*$itemnum;
    
    if ($mrquantity > 0){
    if (in_array($mid, $mmkd)){
	$rbname = $mid."_".$iid;
	if (in_array($mid,array_keys($build_items))){
	    $opts = "<td id='sl'><input type='radio' name='$rbname' value='b' $grayed></td>
		     <td><input type='radio' name='$rbname' value='m' checked $grayed></td>";	
	}else{
	    $opts = "<td id='sl'><input type='radio' name='$rbname' value='b' checked $grayed></td>
		    <td><input type='radio' name='$rbname' value='m' $grayed></td>";	
	}
	}else{
	    $opts = "<td></td><td></td>";
        }

	$price = numfmt($mprices[$mid], 2);
	$sum = numfmt($mprices[$mid]*$mrquantity);
	$table.="<tr><td id='mid' style='visibility:hidden'>$mid</td><td>$mrname</td><td id='q'>$mrquantity</td><td id='r'>-</td>$opts<td align='right' id='p'>$price</td><td align='right' id='s'>$sum</td></tr>\n";
    } // mrquantity >0
}
// add total row
$table .= "<tr><td colspan='5'></td><td ><b>Total</b>:</td><td align='right' id='tt'>-</td></tr>";
$mkt_value = numfmt($mprices[$iid]*$itemnum*$root_portion, 0);
$table .= "<tr><td colspan='5'></td><td ><b>Market</b>:</td><td align='right' id='mk'>$mkt_value</td></tr>";

$table .= "</table>";
$retval=array();
$retval['table']   = $table;
$retval['iname']   = $root_name;
$retval['incount'] = $itemnum; // FIX IT
$retval['iwaste']  = $root_waste; // move to table
$retval['build_items']  = $build_items; // prepared list for next step
$retval['root_portion'] = $root_portion;
return $retval;
}
// end of function

// ==================================================================================================
require_once './libs/db.php';
$DB = new mydb;
$DB->connect();

include('./libs/sm/Smarty.class.php');
$smarty = new Smarty;

require_once('./libs/auth.php');
$user = new CUser;
if (!$user->checkSession() or !in_array($user->group, $acl_allowed)){
    die("Not authorised!<br>\n<a href='index.php'>Return</a>");
}

require_once './libs/ale/factory.php';
$ale = AleFactory::getEVECentral();

require_once './libs/opts.php';
$opts = load_options($DB, $user->getUID());
$def_t2_me = $opts['bpoT2me'];
$def_t22_me = $opts['bpoT22me'];


$iid = $_REQUEST["iid"];
if (!is_Numeric($iid)) die("Missing param<br>\n");
$iid_me = $_REQUEST["iid_me"];
if (!is_Numeric($iid_me) & !empty($iid_me)) die("Missing param<br>\n");

// First step
$step = drawTable($iid, $DB, $ale, 1);
$iname = $step['iname'];
$build_items = $step['build_items'];
$portion = $step['root_portion'];
$start_form ="";
$end_form   = "";
$adjuster="";

if (StepID() == 1){

    if ($portion > 1 ){$addtext = "x$portion";} else {$addtext = "";};
    $start_form = "<b>Materials for $iname $addtext</b><br>\n
    BPO ME:&nbsp;<input id='t2mein' type='text' name='RootME' value='$def_t2_me' size='5'>
    <BUTTON	NAME='adjr_' onClick=\"AdjustME_t2('mt_$iid', 't2mein', 't2meout')\">Adjust</BUTTON>
    <form>
    <input type='hidden' name='step' value='2'>
    <input type='hidden' name='iid' value='$iid'>
    <input type='hidden' id='t2meout' name='iid_me' value='$def_t2_me'>";
    $end_form = "<br><input type='submit' value='Step 2'></form>";
} else{
    $start_form ="<b>Materials for $iname</b><br>\n
    BPO ME: $iid_me
    <input id='t2mein' type='hidden' name='RootME' value='$iid_me' size='5'>
    ";
}

// auto-adjust on load
$adjuster .= "AdjustME_t2('mt_$iid', 't2mein', 't2meout');\n";
// no root adjust/submit button on step 2 and 3
$page = $start_form.$step['table'].$end_form;

// Second step
$mt_jtables = "var mat_tables=new Array(\"mt_$iid\","; // JScript global arr, store material tables id for summary
foreach($build_items as $bit => $bit_num){
    
    $step2 = drawTable($bit, $DB, $ale, $bit_num);
    $iname = $step2['iname'];
    $incount = $step2['incount'];
    $tbl_name = "mt_".$bit;
    $tbl_hdr = "<b>Materials for $iname x $incount</b><br>\n
    $name BPO ME:&nbsp;<input id='me$bit' type='text' name='RootME' value='$def_t22_me' size='5'>
    <BUTTON NAME='adjr_' onClick=\"AdjustME('$tbl_name', 'me$bit')\">Adjust</BUTTON>" ;
    $adjuster .= "AdjustME('mt_$bit', 'me$bit');";    
    
    $page.= $tbl_hdr.$step2['table'];
    $mt_jtables .= "\"$tbl_name\",";
}
$mt_jtables .= "0);\n"; // close Jscript array
$mt_jnum_tables = 1+count($build_items);
$jglobalvars="var portion=$portion;\nvar num_tables=$mt_jnum_tables;\n$mt_jtables ";

//$smarty->assign("root_name", $step['iname']);
$smarty->assign("root_id", $iid);

$smarty->assign("user", $user);
$smarty->assign("page", $page);
$smarty->assign("adjuster", $adjuster);
$smarty->assign("root_waste",$step['iwaste']); //bug!
$smarty->assign("jglobalvars",$jglobalvars); 
$smarty->assign("root_portion", $portion);
$smarty->assign("root_name", $step['iname']);
$smarty->display('t2item.tpl');
