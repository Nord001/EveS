<html>
<head>
<title>Equipment cost</title>
<script language='javascript'>
{$mscript}

function removeSpaces(string) { return string.split(' ').join(''); }

function addSeparator_( sValue ){
var sRegExp = new RegExp('(-?[0-9]+)([0-9]{3})');
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

<link rel="stylesheet" type="text/css" href="common.css" />

</head>
<body>

{include file='header.tpl'}

Fitting setup:<br>
<form name="myform" action="fitcost.php" method="post">
<textarea name="fittext" rows="10" cols="60">{$fittext}</textarea><br>
<input type="submit" value="Calculate">
<input type="button" value="Clear" onclick="myform.fittext.value='';">
</form>
{$tableCost}
<p>
{$tableUnknown}

{include file='footer.tpl'}

</body>
</html>
