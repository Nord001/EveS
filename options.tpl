<html>
<head>

<link rel="stylesheet" type="text/css" href="common.css" />

</head>
<body>
{$cdate}
{include file='header.tpl'}



<form action='options.php?a=save' method='post' id='optform'>
<h1>Mineral price setup</h1>
<OL>
<LI><input type="radio" name="mset" value="jita" {if $opts.minOpt eq "jita"}checked{/if}>Use automatic Jita prices</LI>
<LI><input type="radio" name="mset" value="user" {if $opts.minOpt eq "user"}checked{/if}>Use user defined values:
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
<LI><input type="radio" name="mset" value="zero" {if $opts.minOpt eq "zero"}checked{/if}>Set all mineral prices to zero</LI>
</OL>
<H1>Invention pure cost setup</H1>
<OL>
<LI><input type="radio" name="rset" value="perf" {if $opts.skillOpt eq "perf"}checked{/if}>Use perfect skills in calculation</LI>
<LI><input type="radio" name="rset" value="user" {if $opts.skillOpt eq "user"}checked{/if}>Use user defined values:
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

{include file='footer.tpl'}

<script language='javascript'>
    var mo = document.getElementById('optform');
    {$jmopts}
    
</script>


</body>
</html>
