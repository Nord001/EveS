<html>
<head>
<title>T2 items manufacturing calculation/items list</title>

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
body {
    background-color: #FFFFFF;
}
//--></style>

</head>
<body>

{include file='header.tpl'}
Select market group:<br>
<form action='t2list.php' method='post'>
<select name='grselect'>{$selector}</select><input type='submit' value='Get'>
</form>
{$itemlist}
{include file='footer.tpl'}

</body>
</html>
