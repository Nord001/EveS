<?php

// Just print number with thousand separator
function numfmt($numstr=""){
return number_format($numstr, 0, ',', ' ');
}

require_once './libs/db.php';

require_once './libs/ale/factory.php';
$ale = AleFactory::getEVECentral();

require_once './libs/auth.php';
// check permissions
$user = new CUser;
//$acl_allowed = array(4,);

if (!$user->checkSession() or !in_array($user->group, $acl_allowed)){
    die("Not authorised!<br>\n<a href='index.php'>Return</a>");
}


$DB = new mydb;
$DB->connect();
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


//=== TODO: move this to function ==========
// try get prices for raw
$params = array('typeid'=>$raw_ids, 'regionlimit' => "10000002");
$xml = $ale->marketstat($params);

$raw_prices = array();
foreach($xml->marketstat[0]->{type} as $mat){
    $mat_id = (string) $mat['id'];
    $mat_minprice = (double) $mat->sell[0]->median;
    $raw_prices += array( $mat_id => $mat_minprice);
}

// try get prices for intermid
$params = array('typeid'=>$intermid_ids, 'regionlimit' => "10000002");
$xml = $ale->marketstat($params);

$intermid_prices = array();
foreach($xml->marketstat[0]->{type} as $mat){
    $mat_id = (string) $mat['id'];
    $mat_minprice = (double) $mat->sell[0]->median;
    $intermid_prices += array( $mat_id => $mat_minprice);
}

// try get prices for complex
$params = array('typeid'=>$complex_ids, 'regionlimit' => "10000002");
$xml = $ale->marketstat($params);

$complex_prices = array();
foreach($xml->marketstat[0]->{type} as $mat){
    $mat_id = (string) $mat['id'];
    $mat_medprice = (double) $mat->sell[0]->median;
    $complex_prices += array( $mat_id => $mat_medprice);
}
// ========================================

// Load 2 types of reactions
$raw2intermid = $DB->select_and_fetch("SELECT * FROM invTypeReactions AS ir LEFT JOIN invTypes ON ir.typeID = invTypes.typeID WHERE ir.input = \"0\" AND groupID = \"428\"", "typeID");
$intermid2complex = $DB->select_and_fetch("SELECT * FROM invTypeReactions AS ir LEFT JOIN invTypes ON ir.typeID = invTypes.typeID WHERE ir.input = \"0\" AND groupID = \"429\"", "typeID");
//$allreactions = $DB->select_and_fetch("SELECT * FROM invTypeReactions ", "reactionTypeID");
// load reaction quantity (f'd CCP)
$qphall = $DB->select_and_fetch("SELECT * FROM dgmTypeAttributes WHERE attributeID=\"726\"", "typeID");

//print("Complex materials:<br>");
foreach ($intermid2complex  as $ctid => $v){
    $name = $v['typeName'];
    $raid = $v['reactionTypeID'];
    $materials = $DB->select_and_fetch("SELECT * FROM invTypeReactions  as ir WHERE ir.reactionTypeID = \"$raid\" AND ir.input = \"1\"", "typeID");
    $mat_string="";
    $moon_complex[$ctid]['materials'] = $materials;
    foreach($materials as $intermid ){
	$inmid = $intermid['typeID'];
	$mat_string .= $moon_intermid[$inmid]['typeName']." ";
    }

//print("$ctid => $name, $mat_string\n<br>");
}

//print("Intermid materials:<br>");
foreach ($raw2intermid  as $inmid => $v){
    $name = $v['typeName'];
    $raid = $v['reactionTypeID'];
    $materials = $DB->select_and_fetch("SELECT * FROM invTypeReactions  as ir WHERE ir.reactionTypeID = \"$raid\" AND ir.input = \"1\"", "typeID");
    $mat_string="";
    $moon_intermid[$inmid]['materials'] = $materials;
    foreach($materials as $raw ){
	$rawid = $raw['typeID'];
	$mat_string .= $moon_raw[$rawid]['typeName']." ";
    }
//print("$inmid => $name, $mat_string\n<br>");
}

// well, we have all required stuff, let's build final table


$table ="<table border='1'  cellpadding='2px' cellspacing='1px' width='100%'>";
$row_mask = true; // bit for row colorizing

foreach ($moon_complex as $complex){
$table .=	"<tr><th>Raw material</th><th>Sell</th><th>QpH</th><th>Mkt.$</th>".
		"<th>Interm. material</th><th>Sell</th><th>QpH</th><th>Mkt.Value</th><th>P.Cost</th><th>Prof.H</th>".
		"<th>Complex Material</th><th>Sell</th><th>QpH</th><th>Mkt.Value</th><th>P.C.Interm.</th><th>Prof.H.I.</th><th>P.C.Raw.</th><th>Prof.H.Raw.</th></tr>\n";

    $cid = $complex['typeID'];
    
    $row_mask = !$row_mask; // invert color

    $intermid_coloumn="";
    $complex_printed = false;
    $raw_rows = 0;
    $row_string = "";
    $complex_cost_raw = 0;  // buy only raw
    $complex_cost_intermid = 0; // buy only intermid
    $complex_amount = $qphall[$cid]['valueInt']*$intermid2complex[$cid]['quantity'];
    $complex_price  = $complex_prices[$cid];
    $complex_value  = $complex_price*$complex_amount;  // mkt. value
    
    $intermid_value_sum = 0;
    foreach($complex['materials'] as $intermid){
	$inmid = $intermid['typeID'];


	    $raw_coloumn = "";    
	    $intermid_printed = false;
	    $intermid_cost = 0; // sebes
	    $intermid_amount = $qphall[$inmid]['valueInt']*$raw2intermid[$inmid]['quantity'];
	    $intermid_price  = $intermid_prices[$inmid];  
	    $intermid_value  = $intermid_amount*$intermid_price;  //market value of 1h of materials
	    $complex_cost_intermid +=  $intermid_value;
	    foreach($moon_intermid[$inmid]['materials'] as $raw){
		$raw_rows ++;
		$rawid=$raw['typeID'];
		// 
		// shortcuts
		$raw_amount = $qphall[$rawid]['valueInt'];
		$raw_price = $raw_prices[$rawid];
		$raw_value = $raw_amount*$raw_price;
		$complex_cost_raw +=$raw_value;
		$intermid_cost += $raw_value;
		
		$raw_coloumn ="<td>&nbsp;".$moon_raw[$rawid]['typeName']."</td><td align='right'>".numfmt($raw_price)."</td><td>".$raw_amount."</td><td align='right'>".numfmt($raw_value)."</td>";
		// fucked verstka
		if (!$intermid_printed){
		    $intermid_coloumn = "\n\t<td rowspan='2'>&nbsp;".$moon_intermid[$inmid]['typeName']."</td><td rowspan='2' align='right'>".numfmt($intermid_price)."</td>".
					"<td rowspan='2'>".$intermid_amount."</td><td rowspan='2' align='right'>".numfmt($intermid_value)."</td>".
					"<td rowspan='2' align='right'>%intermid_cost</td><td rowspan='2' align='right'>%intermid_profit</td>";
		    $intermid_printed = true;
		}else{
		    $intermid_coloumn = "";
		}
		if (!$complex_printed){

		    $complex_coloumn = "\n\t\t<td rowspan='%rowspan' align='center'>&nbsp;".$complex['typeName']."</td><td rowspan='%rowspan' align='right'>".numfmt($complex_price)."</td>".
					"<td rowspan='%rowspan'>".$complex_amount."</td><td rowspan='%rowspan' align='right'>".numfmt($complex_value)."</td>".
					"<td rowspan='%rowspan' align='right'>%complex_cost_intermid</td><td rowspan='%rowspan' align='right'>%complex_profit_intermid</td>".
					"<td rowspan='%rowspan' align='right'>%complex_cost_raw</td><td rowspan='%rowspan' align='right'>%complex_profit_raw</td>";
		    $complex_printed = true;
		}else{
		    $complex_coloumn ="";
		}
		$row_style = $row_mask? "row1":"row2";
		$row_string .= "<tr class='$row_style'>".$raw_coloumn.$intermid_coloumn.$complex_coloumn."</tr>\n";

	    } // end of raw loop
	    
	    $row_string = preg_replace("/\%intermid_cost/", numfmt($intermid_cost), $row_string);
	    $row_string = preg_replace("/\%intermid_profit/", numfmt($intermid_value - $intermid_cost), $row_string);
	    
    }	// end of intermid loop

	    
	    $row_string = preg_replace("/\%complex_cost_intermid/", numfmt($complex_cost_intermid), $row_string);
	    $row_string = preg_replace("/\%complex_profit_intermid/", numfmt($complex_value - $complex_cost_intermid), $row_string);
            $row_string = preg_replace("/\%complex_cost_raw/", numfmt($complex_cost_raw), $row_string);
            $row_string = preg_replace("/\%complex_profit_raw/", numfmt($complex_value - $complex_cost_raw + $complex_cost_intermid/2), $row_string);


    $row_string = preg_replace("/\%rowspan/", $raw_rows, $row_string);
    $table .= $row_string;	    
}
$table.="</table>";
$html_header = <<<EOF

<html>
<head>
<title>Moon material spreadsheet (median prices)</title>
<style type="text/css"><!--
th{
    font-size: 8pt;
    font-family: sans-serif;
    border-collapse: collapse;
    background-color:#F9A9AB;
}
.row1 {
    background-color: #CCFFCC;
    font-size: 8pt;
    font-family:Verdana, Arial, Helv, Helvetica, sans-serif;
    border-collapse: collapse;
    white-space:nowrap;
}
.row2{
    background-color: #A9D0F9;
    font-size: 8pt;
    font-family:Verdana, Arial, Helv, Helvetica, sans-serif;
    border-collapse: collapse;
    white-space:nowrap;
}
//--></style>

</head>
<body>
EOF;

//print("Material table:<br>");


$html_footer = <<<EOF

</body>
</html>
EOF;

print($html_header.$table.$html_footer);

?>