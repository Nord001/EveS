<?php if(!defined('SMARTY_DIR')) exit('no direct access allowed'); ?>
<?php $_smarty_tpl->decodeProperties('a:1:{s:15:"file_dependency";a:1:{s:10:"F135052920";a:2:{i:0;s:10:"header.tpl";i:1;i:1270078322;}}}'); ?>
<?php /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-04-01 03:32:05
         compiled from "header.tpl" */ ?>
<?php if ($_smarty_tpl->getVariable('user')->value->authorized){?>
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
<?php }?>
