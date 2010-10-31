<?php

function numfmt($numstr="", $rzd = 0)
{
  return number_format($numstr, $rzd, ',', ' ');
}

require_once './libs/ale/factory.php';
$ale = AleFactory::getEVECentral();

require_once './libs/db.php';
require_once './eftsetup.php';

include('./libs/sm/Smarty.class.php');
$smarty = new Smarty;

require_once('./libs/auth.php');
$user = new CUser;
if (!$user->checkSession() or !in_array($user->group, $acl_allowed))
{
  die("Not authorised!<br>\n<a href='index.php'>Return</a>");
}

$DB = new mydb;
$DB->connect();

if (get_magic_quotes_gpc())
  $fittext = trim(stripslashes($_POST['fittext']));
else
  $fittext = trim($_POST['fittext']);

$s = new EFTSetup($DB, $ale);
$s->parse($fittext);

if ($s->knownModulesCount() > 0)
{
  $tableCost = "<table cellpadding='2'><tr><th>Module name<th>Quantity<th>Min price (Jita)<th>Cost";
  $row_mark = 'row1';
  $totalCost = 0;
  foreach ($s->modules as $m)
  {
    if (!$m->isKnown()) continue;

    $cost = $m->quantity * $m->price;
    $totalCost += $cost;
    $tableCost .= "<tr class='$row_mark'>".
              "<td>".htmlspecialchars($m->name, ENT_QUOTES).
              "<td align='right'>".$m->quantity.
              "<td align='right'>".numfmt($m->price).
              "<td align='right'>".numfmt($cost);
    $row_mark = $row_mark == "row1" ? "row2" : "row1";
  }
  $tableCost .= "<tr class='$row_mark'><th colspan='3' align='right'>Total<td align='right'>".numfmt($totalCost)."</table>";
}
else
  $tableCost ='';

if ($s->unknownModulesCount() > 0)
{
  $tableUnknown = "Unrecognized modules:<br>".
                  "<table cellpadding='2'><tr><th>Module name<th>Quantity";
  $row_mark = 'row1';
  $totalCost = 0;
  foreach ($s->modules as $m)
  {
    if (!$m->isUnknown()) continue;

    $tableUnknown .= "<tr class='$row_mark'>".
                     "<td>".htmlspecialchars($m->name, ENT_QUOTES).
                     "<td align='right'>".$m->quantity;
    $row_mark = $row_mark == "row1" ? "row2" : "row1";
  }
  $tableUnknown .= "</table>";
}
else
  $tableUnknown ='';

$smarty->assign('user', $user);
$smarty->assign('fittext', $fittext);
$smarty->assign('tableCost', $tableCost);
$smarty->assign('tableUnknown', $tableUnknown);
$smarty->display('fitcost.tpl');
