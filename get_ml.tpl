<html>
<title>T2 item matherial list</title>
<head>
<link rel="stylesheet" type="text/css" href="common.css" />
<script language='javascript'>
var oldN = 1;
{include file='./libs/tools.js'}

function AdjustN(){
var newN = document.getElementById('nroot').value;
if (newN <=0){
    document.getElementById('nroot').value = 1;
    newN = 1;
};
var tbl = document.getElementById('mtbl');
var row_num = tbl.rows.length;
for (var i=0; i<row_num; i++){
    var num_cells = tbl.rows[i].cells.length;
    for (var j=0; j<num_cells; j++){
	cell = tbl.rows[i].cells[j];
	if (cell.id == "n"){
	    var currVal = parseCFloat(removeSpaces(cell.innerHTML));
	    cell.innerHTML = addSeparator((currVal/oldN)*newN);
	}
    }
}
oldN = newN;
}
</script>
</head>
<body>
{$cdate}
{include file='header.tpl'}
Matherial list for {$root_name} {if $root_portion>1}({$root_portion} item per pack){/if} <br>
Number of items: <input id='nroot' type='text' name='RootN' value='1' size='5'>
<BUTTON		NAME='adjr_' onClick="AdjustN()">Adjust</BUTTON>
{$mtbl}
<br>
<i>For simple copying table to Excel use "Paste special..." -> Unicode</i>
{include file='footer.tpl'}

<script language='javascript'>
AdjustN();
</script>

</body>
</html>
