<?php if(!defined('SMARTY_DIR')) exit('no direct access allowed'); ?>
<?php $_smarty_tpl->decodeProperties('a:1:{s:15:"file_dependency";a:3:{s:11:"F1965454471";a:2:{i:0;s:10:"t2item.tpl";i:1;i:1279589310;}s:10:"F135052920";a:2:{i:0;s:10:"header.tpl";i:1;i:1270078322;}s:10:"F239105369";a:2:{i:0;s:10:"footer.tpl";i:1;i:1264346308;}}}'); ?>
<?php /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-07-20 05:29:04
         compiled from "t2item.tpl" */ ?>
<html><head>
<title>T2 item details</title>
<script language='javascript'>
// global vars section
<?php echo $_smarty_tpl->getVariable('jglobalvars')->value;?>

// 
function removeSpaces(string) { return string.split(' ').join(''); }
function addSeparator(SS) {
    var X = "";
    var S = String(SS);

    var sign = S.charAt(0);
    if ((sign == '-') | (sign == '+')){
        tmpS = S.substring(1,S.length);
        S  = tmpS;
    }else{
        sign="";
    }
    var L = S.length;
    for (var i=S.length-1; i>0;i--){
        var rz = L-i;
        if (rz>0 & (rz % 3) == 0){
            X = " "+S.charAt(i)+X;
        }else{
            X = S.charAt(i)+X;
        }
    }
    return sign+S.charAt(0)+X;
}

function CalculateTotal(tblid, me){
var baseWaste = <?php echo $_smarty_tpl->getVariable('root_waste')->value;?>
; 	// todo: take as parameter
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
    for (var j=0; j<num_cells; j++){
	switch (tbl.rows[i].cells[j].id){
	case 'p': var p_cell = tbl.rows[i].cells[j];break;
	case 'q': var q_cell = tbl.rows[i].cells[j];break;
	case 'r': r_cell = tbl.rows[i].cells[j];break;
	case 's': s_cell = tbl.rows[i].cells[j];break;
	case 'tt':tt_cell = tbl.rows[i].cells[j];break;
	case 'sl':sl_cell = tbl.rows[i].cells[j];break;
	case 'n': in_cell = tbl.rows[i].cells[j];break;
	}
    } // by cell
    var n = in_cell ? parseFloat(in_cell.innerHTML): 1;
    var r = q_cell ? parseFloat(q_cell.innerHTML): -1;
    var p = p_cell ? parseFloat(removeSpaces(p_cell.innerHTML)): -1;
    var q = q_cell ? parseFloat(q_cell.innerHTML): -1;
    if (r_cell){ // calc real q. and sum
	if (me >= 0){
	    r = r + n*Math.round((q/n) * (parseFloat(baseWaste)/100)*(1/(parseFloat(me)+1)));
	}else{
	    r = r + n*Math.round((q/n) * (parseFloat(baseWaste)/100)*(1-(parseFloat(me))));
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
	page_total += parseFloat(tbl_total);
    }
    var page_mkt   = removeSpaces(getCell(mat_tables[0], "mk").innerHTML);
    var ccell = getCell("pgsum", "pgt");
    ccell.innerHTML = addSeparator(page_total);
    ccell = getCell("pgsum", "pgm");
    ccell.innerHTML = addSeparator(page_mkt);
    ccell = getCell("pgsum", "pgp");
    ccell.innerHTML = addSeparator(page_mkt-page_total);
}

</script>
</head><body>
<?php $_template = new Smarty_Template ('header.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id,  $_smarty_tpl->compile_id);$_template->caching = 0; $_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template; /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-04-01 03:32:05
         compiled from "header.tpl" */  if ($_smarty_tpl->getVariable('user')->value->authorized){?>
<a href='index.php'>Tools home</a>&nbsp;::&nbsp;<a href='index.php?a=lo'>Logout</a>&nbsp;::&nbsp;<a href='options.php'>Options</a><br>
Hello <?php echo $_smarty_tpl->getVariable('user')->value->name;?>
! 
<hr>
<?php }else{ ?>
This is area with restricted access, please login:
<form method="post" action='./index.php?a=li'>
Login: <input type='text' name='name'>
Password: <input type='password' name='pass'>
<input type='submit' value='Ok'>
</form>
<hr>
<?php } /*  End of included template "header.tpl" */   $_smarty_tpl = array_pop($_tpl_stack); unset($_template);  echo $_smarty_tpl->getVariable('page')->value;?>

<p><b>Page summary:</b></br>
<table id='pgsum'>
<tr><td>Total summary: </td><td id='pgt' align='right'>-</td></tr>
<tr><td>Total market: </td><td id='pgm' align='right'>-</td></tr>
<tr><td>Calculated profit: </td><td id='pgp' align='right'>-</td></tr>
</table>

<?php $_template = new Smarty_Template ('footer.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id,  $_smarty_tpl->compile_id);$_template->caching = 0; $_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template; /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-01-24 18:18:30
         compiled from "footer.tpl" */ ?>
<hr>
(c) 2010 <a href='http://eve-ps.ru'>Post Scriptum</a> corporation <br>
<i>Generated: <?php echo $_smarty_tpl->smarty->plugin_handler->executeModifier('date_format',array(time(),"%d/%m/%Y %H:%M"),true);?>
</i>
<?php /*  End of included template "footer.tpl" */   $_smarty_tpl = array_pop($_tpl_stack); unset($_template); ?>
<script language='javascript'>
<?php echo $_smarty_tpl->getVariable('adjuster')->value;?>

</script>
</body></html>
