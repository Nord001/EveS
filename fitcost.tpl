<html>
<head>
<title>Fitting cost calculator</title>

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
