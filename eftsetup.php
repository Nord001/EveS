<?php

class EFTSetup
{
  public $name = '';
  public $modules;

  private $ale;
  private $DB;

/*--------------------------------------------------------------------------*/

  function __construct($DB, $ale)
  {
    $this->modules = array();
    $this->DB = $DB;
    $this->ale = $ale;
  }

/*--------------------------------------------------------------------------*/

  function parse($setupText)
  {
    $emptySlots = array('[empty high slot]', '[empty med slot]', '[empty low slot]', '[empty rig slot]');
    $fitLines = array_map('trim', explode("\n", $setupText));
    foreach ($fitLines as $fitLine)
    {
      if (strlen($fitLine) > 0  && !in_array($fitLine, $emptySlots))
      {
        if (substr($fitLine, 0, 1) == '[' && $this->name == '')
          $this->name = $fitLine;
        else
          $this->addModule($fitLine);
      }
    }
    $this->loadModuleId();
    $this->loadModulePrice();
    ksort($this->modules);
  }

/*--------------------------------------------------------------------------*/

  private function addModule($str)
  {
    $newModule = new EFTModule;
    $newModule->parseModule($str);

    if (array_key_exists($newModule->name, $this->modules))
      $this->modules[$newModule->name]->quantity += $newModule->quantity;
    else
      $this->modules[$newModule->name] = $newModule;          
  }

/*--------------------------------------------------------------------------*/

  private function loadModuleId()
  {
    $names = array_map('mysql_real_escape_string', array_keys($this->modules));
    $param =  "'".implode("', '", $names)."'";

    $sql = "SELECT typeID, typeName FROM invTypes WHERE typeName IN ($param)";
    $itemList = $this->DB->select_and_fetch($sql, 'typeID');
    foreach ($itemList as $itemId => $itemData)
    {
      $this->modules[$itemData['typeName']]->id = $itemId;
    }
  }

/*--------------------------------------------------------------------------*/

  private function loadModulePrice()
  {
    foreach ($this->modules as $m)
    {
      if ($m->isKnown()) 
        $m->price = $this->getPriceById($m->id);
    }
  }

/*--------------------------------------------------------------------------*/

  private function getPriceById($id)
  {
    $params = array('typeid' => $id, 'regionlimit' => '10000002');

    $xml = $this->ale->marketstat($params);
    $itm_minprice = 0;
    foreach($xml->marketstat[0]->{type} as $itm)
    {
      $itm_minprice = (double) $itm->sell[0]->min;
      break;
    }

    return $itm_minprice;
  }

  public function knownModulesCount()
  {
    $cnt = 0;
    foreach ($this->modules as $m)
    {
      if ($m->isKnown()) $cnt++;
    }
    return $cnt;
  }

  public function unknownModulesCount()
  {
    $cnt = 0;
    foreach ($this->modules as $m)
    {
      if ($m->isUnknown()) $cnt++;
    }
    return $cnt;
  }
}

/*==========================================================================*/

class EFTModule
{
  public $id = 0;
  public $name = '';
  public $ammo = '';
  public $quantity = 1;
  public $price = 0.0;

/*--------------------------------------------------------------------------*/
  public function isKnown()
  {
    return $this->id != 0;
  }

  public function isUnknown()
  {
    return $this->id == 0;
  }

  public function parseModule($str)
  {
    if (preg_match('/^(?<name>.+)\,(?<ammo>.+)$/', $str, $matches) == 1)
    {
      $this->name = $matches['name'];
      $this->ammo = $matches['ammo'];
    }
    elseif (preg_match('/^(?<name>.+)\s+x(?<quantity>\d+)$/', $str, $matches) == 1)
    {
      $this->name = $matches['name'];
      $this->quantity = $matches['quantity'];
    }
    else
      $this->name = $str;
  }
}

?>
