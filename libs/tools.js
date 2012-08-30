
function removeSpaces(string) { return string.split(' ').join(''); }

function addSeparator(SS) {
    var X = "";
    var S = String(SS);

    var sign = S.charAt(0);
    if ((sign == '-') | (sign == '+')){
        tmpS = S.substring(1,S.length);
        S  = tmpS;
    }else{ sign=""; }
    var L = S.length;
    var ppos = -1;
    var tl = "";
    for (i=0; i<L;i++){
    var c = S.charAt(i);
    if ((c == '.') || (c == ',')){
      ppos = i;
      tl = S.substring(ppos, L);
      S = S.substring(0, ppos);
      L = S.length;
    }
    }
    for (var i=S.length-1; i>0;i--){
        var rz = L-i;
        if (rz>0 & (rz % 3) == 0){
            X = " "+S.charAt(i)+X;
        }else{
            X = S.charAt(i)+X;
        }
    }
    return sign+S.charAt(0)+X+tl;
}

// parse 1,34 to 1.34 float
function parseCFloat(ins){
    if (ins){
    var s = String(ins);
    return parseFloat(s.replace(",", "."));
    }
    return null;
}
