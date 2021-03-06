<html><head>
<title>T2 item details</title>
<script language='javascript'>


// global vars section
{$jglobalvars}
// js libs include
{include file='./libs/tools.js'}

function CalculateTotal(tblid, me){
var baseWaste = {$root_waste}; 	// todo: take as parameter
var tbl = document.getElementById(tblid);
var row_num = tbl.rows.length;
var tt_cell = null;
var total = 0.0;
var in_cell = null;
for (var i=0; i<row_num; i++){
    var num_cells = tbl.rows[i].cells.length;
    var r_cell = null;
    var s_cell = null;
    var sl_cell = null;
    var rr_cell = null;
    for (var j=0; j<num_cells; j++){
	switch (tbl.rows[i].cells[j].id){
	case 'p' : var p_cell = tbl.rows[i].cells[j];break;
	case 'q' : var q_cell = tbl.rows[i].cells[j];break;
	case 'r' : r_cell = tbl.rows[i].cells[j];break;
	case 's' : s_cell = tbl.rows[i].cells[j];break;
	case 'tt': tt_cell = tbl.rows[i].cells[j];break;
	case 'sl': sl_cell = tbl.rows[i].cells[j];break;
	case 'n' : in_cell = tbl.rows[i].cells[j];break;
	case 'rr': rr_cell = tbl.rows[i].cells[j];break;
	}
    } // by cell
    var n = in_cell ? parseCFloat(in_cell.innerHTML): 1;
    var r = q_cell ? parseCFloat(q_cell.innerHTML): -1;
    var rr = rr_cell ? parseCFloat(rr_cell.innerHTML): -1;
    var p =  p_cell ? parseCFloat(removeSpaces(p_cell.innerHTML)): -1;
    var p2 = p_cell ? removeSpaces(p_cell.innerHTML): -1;
    var q = q_cell ? parseCFloat(q_cell.innerHTML): -1;
    if (rr_cell){
        rr_cell.innerHTML = rr * n;
        q_cell.innerHTML = rr * n;
    }
    if (r_cell){ // calc real q. and sum
	if (me >= 0){
	    r = r + n*Math.round((q/n) * (parseCFloat(baseWaste)/100)*(1/(parseCFloat(me)+1)));
	}else{
	    r = r + n*Math.round((q/n) * (parseCFloat(baseWaste)/100)*(1-(parseCFloat(me))));
	}
	r_cell.innerHTML =  r;
    }
    if (s_cell){
	var sum = r * p;
	if (sl_cell){
	    if (!sl_cell.childNodes[0].checked) sum =0;
	}
	s_cell.innerHTML = addSeparator(Math.round(sum).toString());
	total = total + sum;
    }
}//by row
if (tt_cell){
    tt_cell.innerHTML = addSeparator(Math.round(total).toString());
}

}

function AdjustME(tblid, fid, itemnum){
    var f = document.getElementById(fid);
    var m = f.value? f.value : -4;
    CalculateTotal(tblid, m);
    pageSummary();
    
}

function AdjustME_t2(tblid, fid, foid){
    var f = document.getElementById(fid);
    var m = f.value? f.value : -4;
    CalculateTotal(tblid, m);
    var fo = document.getElementById(foid);     
    if (fo) fo.value = f.value;
    pageSummary();
}

function getCell(tblid, cellid){
    var tbl = document.getElementById(tblid);
    var row_num = tbl.rows.length;
    for (var i=0; i<row_num; i++){
	var num_cells = tbl.rows[i].cells.length;
	for (var j=0; j<num_cells; j++)
	    if (tbl.rows[i].cells[j].id == cellid)
		return tbl.rows[i].cells[j];
    }
    return null; //nothing found
}

function pageSummary(){
    var page_total = 0.0;
    for (var i=0; i<num_tables; i++){
	var tbl_total = removeSpaces(getCell(mat_tables[i], "tt").innerHTML);
	page_total += parseCFloat(tbl_total);
    }
    var page_mkt   = removeSpaces(getCell(mat_tables[0], "mk").innerHTML);
    var ccell = getCell("pgsum", "pgt");
    ccell.innerHTML = addSeparator(page_total);
    ccell = getCell("pgsum", "pgm");
    ccell.innerHTML = addSeparator(page_mkt);
    ccell = getCell("pgsum", "pgp");
    ccell.innerHTML = addSeparator(page_mkt-page_total);
}
function GetML(){
    var f = document.getElementById('getml_data');
    var out_array = new Object();
    
    for (var t=0; t<num_tables; t++){
	var tbl = document.getElementById(mat_tables[t]);
	var row_num = tbl.rows.length;
	var mid, mn;
    for (var i=0; i<row_num; i++){
	var num_cells = tbl.rows[i].cells.length;
	mn = -1; mid = -1;
	mb = true;
	for (var j=0; j<num_cells; j++){
	    cell = tbl.rows[i].cells[j];
	    if (cell.id == "mid"){ 
		mid = cell.innerHTML; }
	    if (cell.id == "r"){
		mn = cell.innerHTML; }
	    if (cell.id == "rr"){
		mn = cell.innerHTML; }
	    if (cell.id == "sl"){
		mb = cell.childNodes[0].checked;}
	}
	if (mid>0 && mn>0 && mb){
	    if (out_array[mid]>0){
		out_array[mid] += parseCFloat(mn);
	    }else{
		out_array[mid] = parseCFloat(mn);
	    }
	}
    }

    }

f.value = JSON.stringify(out_array);
}
</script>

<link rel="stylesheet" type="text/css" href="common.css" />

</head><body>
{include file='header.tpl'}
{$page}
<p><b>Page summary:</b></br>
<table id='pgsum'>
{if $root_portion>1}
<tr><td>Pack size:</td><td align='right'> {$root_portion}</td></tr>
{/if}
<tr><td>Total summary: </td><td id='pgt' align='right'>-</td></tr>
<tr><td>Total market: </td><td id='pgm' align='right'>-</td></tr>
<tr><td>Calculated profit: </td><td id='pgp' align='right'>-</td></tr>
</table>

<form action='get_ml.php' method='post'>
<input type='submit' onClick="GetML()" value='Get matherials list'>
<input type='hidden' name='getml_data' id='getml_data' value='NULL'>
<input type='hidden' name='root_name' id='rn' value='{$root_name}'>
<input type='hidden' name='root_num' id='rn' value='{$root_portion}'>
</form>

{include file='footer.tpl'}
<script language='javascript'>
{$adjuster}
</script>
</body></html>
