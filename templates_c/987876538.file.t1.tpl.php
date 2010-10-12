<?php if(!defined('SMARTY_DIR')) exit('no direct access allowed'); ?>
<?php $_smarty_tpl->decodeProperties('a:1:{s:15:"file_dependency";a:3:{s:10:"F987876538";a:2:{i:0;s:6:"t1.tpl";i:1;i:1264944202;}s:10:"F135052920";a:2:{i:0;s:10:"header.tpl";i:1;i:1270078322;}s:10:"F239105369";a:2:{i:0;s:10:"footer.tpl";i:1;i:1264346308;}}}'); ?>
<?php /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-04-06 09:14:38
         compiled from "t1.tpl" */ ?>
<html>
<head>
<title>T1 items manufacturing calculation</title>
<script language='javascript'>
<?php echo $_smarty_tpl->getVariable('mscript')->value;?>


function removeSpaces(string) { return string.split(' ').join(''); }

function addSeparator_( sValue ){
var sRegExp = new RegExp('(-?[0-9]+)([0-9]<?php echo 3;?>
)');
while(sRegExp.test(sValue)) {
    sValue = sValue.replace(sRegExp, '$1 $2');
}
return sValue;
}

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


function getIdx(s){
    var p = new Array;
    p = s.split('.');
    return parseInt(p[1]);
}

function CalculateTotal(me){
//var baseWaste = ??;    // todo: take as parameter
var tbl = document.getElementById('t1tbl');
var row_num = tbl.rows.length;
for (var i=0; i<row_num; i++){
    var num_cells = tbl.rows[i].cells.length;
    var qarr = new Array; var rarr = new Array; var varr = new Array;
    var tcell = null; var pcell = null; var mcell = null;
    var total = 0.0;
    var baseWaste  = 0.0;

    for (var j=0; j<num_cells; j++){
    var cid = tbl.rows[i].cells[j].id;
    var idx = -1;
    switch (cid.charAt(0)){
	case 'q':{
	    idx = getIdx(cid);
	    qarr[idx] = tbl.rows[i].cells[j];
	    break;
	}
	case 'r':{
            idx = getIdx(cid);
            rarr[idx] = tbl.rows[i].cells[j];
	    break;
	}
	case 'v':{
            idx = getIdx(cid);
            varr[idx] = tbl.rows[i].cells[j];
	    break;
	}
	case 't':{
	    baseWaste = parseFloat(cid.substring(2,cid.length));	    
	    tcell = tbl.rows[i].cells[j];
	    break;
	}
	case 'p':{
	    pcell = tbl.rows[i].cells[j];
	    break;
	}
	case 'm':{
	    mcell = tbl.rows[i].cells[j];
	    break;
	}

    }// switch
    }// for j

    for (var c=0; c<=6; c++){
    if (qarr[c]){
	var q = parseFloat(removeSpaces(qarr[c].innerHTML));
        if (me >= 0){
            var r = Math.round(q * (baseWaste/100)*(1/(parseFloat(me)+1)));
        }else{
            var r = Math.round(q* (baseWaste/100)*(1-parseFloat(me)));
	}
	r += q;
        rarr[c].innerHTML =  addSeparator(r.toString());
	var mval=r*mparr[c];
//	varr[c].innerHTML = addSeparator(Math.round(mval).toString());
	total += mval;
    }
    if (tcell){
	tcell.innerHTML = addSeparator(Math.round(total).toString());
    }
    if (pcell){
	var prof =  parseFloat(removeSpaces(mcell.innerHTML))- total;
	pcell.innerHTML = addSeparator(Math.round(prof).toString());
    }	
    } // for c
}// for i
} // end function

function AdjustME(fid){
    var f = document.getElementById(fid);
    var m = f.value? f.value : 0;
    CalculateTotal(m);
}
</script>

<style type="text/css"><!--
th{
    font-size: 8pt;
    font-family: sans-serif;
    border-collapse:collapse;
    background-color:#F9A9AB;
}
.row1 {
    background-color: #CCFFCC;
    font-size: 8pt;
    font-family:Verdana, Arial, Helv, Helvetica, sans-serif;
    border-collapse:collapse;
    white-space:nowrap;
}
.row2{
    background-color: #A9D0F9;
    font-size: 8pt;
    font-family:Verdana, Arial, Helv, Helvetica, sans-serif;
    border-collapse:collapse;
    white-space:nowrap;
}
//--></style>

</head>
<body>

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
<?php } /*  End of included template "header.tpl" */   $_smarty_tpl = array_pop($_tpl_stack); unset($_template); ?>
Select market group:<br>
<form action='t1.php' method='post'>
<select name='grselect'><?php echo $_smarty_tpl->getVariable('selector')->value;?>
</select><input type='submit' value='Get'>
</form>
<?php echo $_smarty_tpl->getVariable('table')->value;?>

<?php $_template = new Smarty_Template ('footer.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id,  $_smarty_tpl->compile_id);$_template->caching = 0; $_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template; /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-01-24 18:18:30
         compiled from "footer.tpl" */ ?>
<hr>
(c) 2010 <a href='http://eve-ps.ru'>Post Scriptum</a> corporation <br>
<i>Generated: <?php echo $_smarty_tpl->smarty->plugin_handler->executeModifier('date_format',array(time(),"%d/%m/%Y %H:%M"),true);?>
</i>
<?php /*  End of included template "footer.tpl" */   $_smarty_tpl = array_pop($_tpl_stack); unset($_template); ?>

<script language='javascript'>
    AdjustME('adjr');
</script>

</body>
</html>
