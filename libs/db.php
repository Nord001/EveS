<?php
class mydb {
public $settings;
private $state, $result;

function __construct(){
// database settings 
$this->settings = array("sql_database"   => "eve_import"    ,
            		"sql_user"       => "root"     		,
                    	"sql_pass"       => ""         	,
                    	"sql_host"       => "localhost"	,
                    	"sql_port"       => ""         	,
                     );

// database state
$this->state = array( 	"connected"	=> false,
			"cid"		=> 0,
		    );    
}

function connect() {

    if ($this->state['connected']) return; // already connected
    $this->state['cid'] = mysql_connect(  $this->settings['sql_host'] ,
	                                  $this->settings['sql_user'] ,
    	                                  $this->settings['sql_pass'], true);

    if ( !mysql_select_db($this->settings['sql_database'], $this->state['cid']) ) {
        echo ("ERROR: Cannot find database '".$this->settings['sql_database']."'\n"); die();
    }
    $this->state['connected'] = true;
} // connect()
 
function disconnect(){
    if ($this->state['connected']){
	mysql_close($this->state['cid']);
    }
    $this->state['connected'] = false;
    
}

 /// will return fetched array
function select_and_fetch($sql="", $key=""){
    if (!$this->state['connected']){
	echo("Database is not connected, failed query:$sql\n"); die();
    }

    $query_id = mysql_query($sql, $this->state['cid']);

    if (! $query_id ){
	$err = mysql_error($this->state['cid']);
	echo("mySQL query error: $sql<br>\nSQL said: $err\n");var_dump($this->settings); die();
    }

    $this->result = array();
    $count = 0;
    while ($row = mysql_fetch_array($query_id, MYSQL_ASSOC)) {
	$this->result = $this->result+array($row[$key]=>$row);
	$count++;
    } 
 return $this->result;
}

function insert($sql=""){
    if (!$this->state['connected']){
        echo("Database is not connected, failed query:$sql\n"); die();
    }

    $query_id =mysql_query($sql, $this->state['cid']);
   if (! $query_id ){
        echo("mySQL query error: $sql"); die();
    }

    return mysql_insert_id();
}

function update($sql=""){
    if (!$this->state['connected']){
        echo("Database is not connected, failed query:$sql\n"); die();
    }
    $query_id =mysql_query($sql, $this->state['cid']);
   if (! $query_id ){
        echo("mySQL query error: $sql"); die();
    }

}

function delete($sql=""){
    if (!$this->state['connected']){
        echo("Database is not connected, failed query:$sql\n"); die();
    }
    $query_id =mysql_query($sql, $this->state['cid']);
   if (! $query_id ){
        echo("mySQL query error: $sql"); die();
    }

}

} // end class

?>