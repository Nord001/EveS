<?php if(!defined('SMARTY_DIR')) exit('no direct access allowed'); ?>
<?php $_smarty_tpl->decodeProperties('a:1:{s:15:"file_dependency";a:3:{s:11:"F2085579878";a:2:{i:0;s:10:"t2list.tpl";i:1;i:1265174539;}s:10:"F135052920";a:2:{i:0;s:10:"header.tpl";i:1;i:1270078322;}s:10:"F239105369";a:2:{i:0;s:10:"footer.tpl";i:1;i:1264346308;}}}'); ?>
<?php /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-04-06 09:14:49
         compiled from "t2list.tpl" */ ?>
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
<form action='t2list.php' method='post'>
<select name='grselect'><?php echo $_smarty_tpl->getVariable('selector')->value;?>
</select><input type='submit' value='Get'>
</form>
<?php echo $_smarty_tpl->getVariable('itemlist')->value;?>

<?php $_template = new Smarty_Template ('footer.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id,  $_smarty_tpl->compile_id);$_template->caching = 0; $_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template; /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-01-24 18:18:30
         compiled from "footer.tpl" */ ?>
<hr>
(c) 2010 <a href='http://eve-ps.ru'>Post Scriptum</a> corporation <br>
<i>Generated: <?php echo $_smarty_tpl->smarty->plugin_handler->executeModifier('date_format',array(time(),"%d/%m/%Y %H:%M"),true);?>
</i>
<?php /*  End of included template "footer.tpl" */   $_smarty_tpl = array_pop($_tpl_stack); unset($_template); ?>

</body>
</html>
