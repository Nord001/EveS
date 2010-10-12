<?php if(!defined('SMARTY_DIR')) exit('no direct access allowed'); ?>
<?php $_smarty_tpl->decodeProperties('a:1:{s:15:"file_dependency";a:3:{s:11:"F1252656130";a:2:{i:0;s:11:"options.tpl";i:1;i:1285510847;}s:10:"F135052920";a:2:{i:0;s:10:"header.tpl";i:1;i:1270078322;}s:10:"F239105369";a:2:{i:0;s:10:"footer.tpl";i:1;i:1264346308;}}}'); ?>
<?php /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-09-26 18:20:53
         compiled from "options.tpl" */ ?>
<html>
<body>
<?php echo $_smarty_tpl->getVariable('cdate')->value;?>

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



<form action='options.php?a=save' method='post' id='optform'>
<h1>Mineral price setup</h1>
<OL>
<LI><input type="radio" name="mset" value="jita" <?php if ($_smarty_tpl->getVariable('opts')->value['minOpt']=="jita"){?>checked<?php }?>>Use automatic Jita prices</LI>
<LI><input type="radio" name="mset" value="user" <?php if ($_smarty_tpl->getVariable('opts')->value['minOpt']=="user"){?>checked<?php }?>>Use user defined values:
<table id='mtbl'>
<tr>
    <th >Tritanium</th>
    <th >Pyerite</th>
    <th >Mexallon</th>
    <th >Isogen</th>
    <th >Nocxium</th>
    <th >Zydrine</th>
    <th >Megacyte</th>
</tr>
<tr>
    <td><input type='text' name='m_13' value='0' size='5'></td>
    <td><input type='text' name='m_14' value='0' size='5'></td>
    <td><input type='text' name='m_15' value='0' size='5'></td>
    <td><input type='text' name='m_16' value='0' size='5'></td>
    <td><input type='text' name='m_17' value='0' size='5'></td>
    <td><input type='text' name='m_18' value='0' size='5'></td>
    <td><input type='text' name='m_19' value='0' size='5'></td>
    </tr>
</table>
</LI>
<LI><input type="radio" name="mset" value="zero" <?php if ($_smarty_tpl->getVariable('opts')->value['minOpt']=="zero"){?>checked<?php }?>>Set all mineral prices to zero</LI>
</OL>
<H1>Invention pure cost setup</H1>
<OL>
<LI><input type="radio" name="rset" value="perf" <?php if ($_smarty_tpl->getVariable('opts')->value['skillOpt']=="perf"){?>checked<?php }?>>Use perfect skills in calculation</LI>
<LI><input type="radio" name="rset" value="user" <?php if ($_smarty_tpl->getVariable('opts')->value['skillOpt']=="user"){?>checked<?php }?>>Use user defined values:
<table id='mtbl'>
<tr>
    <th >Skill</th>
    <th >Value</th>
</tr>
<tr>
    <td>Ammar Encryption Methods</td>
    <td><input type='text' name='r_1' value='0' size='1'></td>
</tr>
<tr>
    <td>Ammarian Starship Engineering</td>
    <td><input type='text' name='r_2' value='0' size='1'></td>
</tr>


</table>
</LI>
</OL>


<input type='submit' value='Save'>
</form>

<?php $_template = new Smarty_Template ('footer.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id,  $_smarty_tpl->compile_id);$_template->caching = 0; $_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template; /* Smarty version Smarty3-SVN$Rev: 3286 $, created on 2010-01-24 18:18:30
         compiled from "footer.tpl" */ ?>
<hr>
(c) 2010 <a href='http://eve-ps.ru'>Post Scriptum</a> corporation <br>
<i>Generated: <?php echo $_smarty_tpl->smarty->plugin_handler->executeModifier('date_format',array(time(),"%d/%m/%Y %H:%M"),true);?>
</i>
<?php /*  End of included template "footer.tpl" */   $_smarty_tpl = array_pop($_tpl_stack); unset($_template); ?>

<script language='javascript'>
    var mo = document.getElementById('optform');
    <?php echo $_smarty_tpl->getVariable('jmopts')->value;?>

    
</script>


</body>
</html>
