<html>
<head>
<title>Fitting cost calculator</title>

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
