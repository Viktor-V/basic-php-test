<?php

// MySQL class
class sql{
	public $conn=false;
	public $res=false;
	private $conf;
	public $cQuery=false;
	public $uniqIDs = false; // true | false | gms
	public $ob=false;
	public $lastID = 0;
	public $profiling = false;

	function connect($i=0){
		global $a; // need linking to environment
		$c = $a->conf['sql'][$i]; // get config

        $cs = microtime(true);
		$this->ob = new mysqli($c['HOST'], $c['DB_LGN'],$c['DB_PSW'], $c['DB']);
		if ($this->ob->connect_errno) {
			$a->sys->err("Can't connect to database\n(" . $this->ob->connect_error.")\n");
		}
        if($this->profiling) {
            $ce = microtime(true);
            echo "connected [" . round($ce - $cs, 4) . " s]<br>\n";
        }

		$this->ob->query("SET NAMES UTF8");
		$this->ob->query("SET CHARACTER SET UTF8");
        $this->ob->query("SET session sql_mode=''");

        if($this->profiling) {
            $ce2 = microtime(true);
            echo "SET cfg [" . round($ce2 - $ce, 4) . " s]<br>\n";
        }
		
		$this->conn = true; // connection done
		
		return $this->conn;
	}
	function connection(){
		if($this->conn) return $this->conn; //if connection exists return it
		else return $this->connect(); // overwise create connection and return it
	}
	
	//----------------------------------------------
	// Executes query
	// IN $str - query string
	// OUT sql_result object with query executed
	public function exec($str){
		$c = $this->connection(); // prepare connection
		
		// caching VS using cache
		if(!$str) $str = $this->cQuery;
		else $this->cQuery = $str;

        $ce = microtime(true);
		//--
		$r = new sql_result($str, $this->ob); // create result object and set query
		$r->query(); // execute query

        if($this->profiling){
            $ce2 = microtime(true);
            echo "[".round($ce2 - $ce, 4)." s] EXEC: {$str} <br><br>\n\n";
        }


		return $r; // return result object
	}
	
	public function execAll($qs=array(), $replace=array()){
		foreach($qs as $k=>$q){
			foreach($replace as $k=>$v)	$q = str_replace("%".$k, $v, $q);
			$this->exec($q);
		}
	}
	//-----------------------------------------------
	// Returns formated array with data corresponding to query
	// IN $str - query string
	// OUT array of data rows		
	public function get($str=''){
		$r = $this->exec($str);
		return $r->getRows();
	}
	public function get1($str=''){
		$r = $this->exec($str);
		return $r->getRow();
	}
	
	public function g($table=false, $args = array(), $order=''){
		if(!$table) return false;
		$q = $this->prepareQ($table, $args, $order);
		return $this->get($q);
	}
	public function g1($table=false, $args = array(), $order=''){
		if(!$table) return false;
		$q = $this->prepareQ($table, $args, $order);
		return $this->get1($q);
	}
	public function updateList($fields, $where, $add=''){
		$whers = '';
		$q = '';
		if(is_array($fields) && $fields){
			foreach($fields as $key=>$val){
				if(($key!='ID') || !isSet($where[$key])){
					if($whers) $whers.=', ';
					$whers.=" `$key`='".addSlashes($val)."'";
				}
			}
		}
		if($whers)
			$q.=" $whers ";
		
		return $q;
	}
	public function whereList($where){
		$q = '';
		if(is_array($where) && $where){
			$whers = '';
			foreach($where as $key=>$val){
				if($whers) $whers.=' AND';
				$whers.=" `$key`='".addSlashes($val)."'";
			}
			if($whers)
				$q.=" WHERE $whers ";
		}
		elseif(is_int($where)){
			$q.=" WHERE ID='".intVal($where)."' ";
		}
		return $q;
	}
	public function update($table=false, $fields=array(), $where=array(),$useReplace=false){
		global $a;
		if(!$table || !$fields) return false;
		
		if($q = $this->updateList($fields, $where)){
			if($where === false){
				if($this->uniqIDs){
					$uniqID = $this->getUniqID();
					$q = " ID='{$uniqID}',".$q;
				}
				$q = " SET ".$q;
				$rr = $this->exec((($useReplace)?"REPLACE":"INSERT")." INTO `$table`".$q)->r;
				return $rr;
			}
			elseif($qW=$this->whereList($where)){
				$q = " SET ".$q;
				return $this->exec("UPDATE `$table`".$q.$qW)->r;
			}
		}
			
		return false;
	}
	public function insert($table=false, $fields=array()){
		return $this->update($table, $fields, false);
	}
	public function delete($table=false, $args = array()){
		global $a;
		if(!$table) return 0;
		if(is_array($args) && $args){
			$whers = '';
			foreach($args as $key=>$val){
				if($whers) $whers.=' AND';
				$whers.=" `$key`='".addSlashes($val)."'";
			}
			if($whers){
				$qD="DELETE FROM `$table` WHERE $whers";
				$q="INSERT INTO log_delete (`userID`, `date`, `q`) VALUES ('".$a->user->ID."','".date("Y-m-d H:i:s")."','".addSlashes($qD)."')";
				$this->exec($q);
				$this->exec($qD);
				return 1;
			}
			else return 0;
		}
		else return 0;
	}
	function prepareQ($table=false, $args = array(), $order=''){
		if(!$table) return false;
		$q = "SELECT * FROM `$table`";
		if(is_array($args) && $args){
			$whers = '';
			foreach($args as $key=>$val){
				if($whers) $whers.=' AND';
				$whers.=" `$key`='".addSlashes($val)."'";
			}
			if($whers)
				$q.=" WHERE $whers ";
		}
		elseif(is_int($args)){
			$q.=" WHERE ID='".intVal($args)."' ";
		}
		if($order) $q.=" ".$order;
		return $q;
	}
	
	public function getAsVars($str='', $lim=false){
		global $a;
		return $a->sys->arrayToVars($this->get($str), $lim, 'tds', 'value');
	}
	
	public function count($str=false){
		if(!$str) return false;

		$cnt = 0;
		$newStr = "";
		
		if(stripos($str, "HAVING") || stripos($str, "UNION")){
			$r = $this->exec($str);
			$cnt = $r->numRows();			
		}
		else{
			$ps = explode("FROM ", $str);

			if(count($ps)==2){ // just two parts
				$newStr = "SELECT 1 FROM ".$ps[1];
				$ps = explode("ORDER BY ", $newStr);
				if(count($ps)==2){ // just two parts
					$newStr = $ps[0];
				}
				$r = $this->exec($newStr);
				$cnt = $r->numRows();
			}
		}
		return $cnt;
	}
	public function getLastID(){
		if($this->uniqIDs) return $this->lastID;
		else return $this->ob->insert_id;
	}
	
	public function duplicate($table='',$id=0,$replace=array(),$linked=array()){
		if(!$table || !$id) return 0;
		
		if($item = $this->g1($table,$id)){
			$qw = "INSERT INTO `$table` SET";
			$q = "";
			foreach($item as $k=>$v){
				if(!in_array($k,array("id","ID"))){
					if(isSet($replace[$k])) $v = $replace[$k];
					if($q) $q.=',';
					$q.=" `$k`='".addSlashes($v)."'";
				}
			}
			if($q){
				if($res = $this->exec($qw.$q)){
					$newID = $this->getLastID();
					foreach($linked as $lk=>$ltable){
						if($link = $this->g($ltable,array($table."ID"=>$id))){
							foreach($link as $k2=>$v2){
								$qw = "INSERT INTO `$ltable` SET";
								$q = "";
								foreach($v2 as $k3=>$v3){
									if($k3==$table."ID"){
										if($q) $q.=',';
										$q.=" `$k3`='".addSlashes($newID)."'";
									}
									elseif(!in_array($k3,array("id","ID"))){
										if(isSet($replace[$k3])) $v3 = $replace[$k3];
										if($q) $q.=', ';
										$q.=" `$k3`='".addSlashes($v3)."'";
									}
								}
								if($q) $this->exec($qw.$q);
							}
						}
					}
					return $newID;
				}
			}
		}
	}
	
	function getUniqID(){
        global $a;
		if($this->uniqIDs=="gms"){
			if(!@$a->gmsASQL){
				$a->gmsASQL = new sql();
				$a->gmsASQL->connect('gmsASQL');
			}
			$q1	= "SELECT uniqID FROM sites WHERE ID = '3'";
			$q2	= "UPDATE sites SET uniqID = uniqID + 1 WHERE ID = '3'";
			
			$c = $a->gmsASQL->get1($q1);
			$a->gmsASQL->exec($q2);
			$this->lastID = $c['uniqID']+1;
			return $c['uniqID']+1;
		}
	}
	
	function escape($str=0){
		return $this->ob->real_escape_string($str);
	}
}

class sql_result{
	public $r = false;
	public $q = false;
	public $ob = false;
	
	function __construct($str, $ob) {
		$this->q = $str;
		$this->ob = $ob;
	}
	public function query(){
        global $a;
        try{
            $this->r = $this->ob->query($this->q);
        }
        catch(Exception $e){
            die($e->getMessage() . ' [' . $this->q .']');
        }

		if(!$this->r && $a->diag) echo "<BR>ERR IN: ".$this->q."<BR>";
	}
	public function getRow(){
		if(!$this->r) return array();
		return $this->r->fetch_assoc();
	}
	public function getRows(){
		$d = array();
		while($row = $this->getRow()) $d[] =  $row;
		if($this->r) $this->r->free();
		return $d;
	}
	public function numRows() {
		global $a;
		if(!$this->r){
			if($a->diag)
				echo $a->sql->cQuery;
			return 0;
		}
		return $this->r->num_rows;
	}
}
