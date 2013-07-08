<html>
<head>
<title>T2 items manufacturing calculation/items list</title>

<link rel="stylesheet" type="text/css" href="common.css" />

</head>
<body>

{include file='header.tpl'}
Select market group:<br>
<form action='t2list.php' method='post'>
<select name='grselect'>{$selector}</select>
<input type='submit' value='Get'>
</form>
{$itemlist}
{include file='footer.tpl'}

</body>
</html>
