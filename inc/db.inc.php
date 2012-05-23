<?php
	if (!defined('INCLUDE_SECURE')) die();
		
	class Database {
		public $pdo;
		public $_host, $_db, $begin;
		function __construct($host, $db, $user, $password)
		{
			$this->_host = $host; $this->_db = $db;
			try{
				$this->pdo = new PDO('mysql:host='.$host.';dbname='.$db, $user, $password);
			}catch(PDOException $e){
				trigger_error(json_encode(array('406','test')));
				die();
			}
			$this->begin = false;
		}
		public function prepare($query){
			return $this->pdo->prepare( (string)$query );
		}
		protected function execute($query, $params, &$sql) {
			$sql = $this->pdo->prepare( (string)$query );
			
			try{
				for ($i=1;$i<sizeof($params);++$i){
					if ($params[$i] === null){
						$params[$i] = '';
					}
					$sql->bindValue($i, $params[$i]);
				}
			}catch(Exception $e) {
				trigger_error('407'); # Error preparing SQL statement
			}
			
			try {
				if ($sql->execute()){
					return true;
				}else{
					#trigger_error( serialize( array('407', 'Error executing SQL statement: ' . json_encode($params) . '  Error: ' . json_encode($sql->errorInfo()) . '<br /><pre>' . var_export(debug_backtrace(), true) . '</pre>' ) ) );
					return false;
				}
			}catch(Exception $e) {
				trigger_error( serialize('407', 'Error executing SQL statement: ' . json_encode($params )) );
			}
			return false;
		}
		public function insert_multi($table_name, $data){
			for ($i=0;$i<sizeof($data);++$i){
				$type = '';
				$val = '';
				foreach($data[$i] as $a=>$b){
					if (!is_int($a)){
						if ($type != '') $type .= ',';
						$type .= '`'.$a.'`';

						if ($val != '') $val .= ',';
						$val .= '"'.$b.'"';
					}
				}
				$query = 'INSERT INTO `'.$table_name.'`('.$type.') VALUES('.$val.');';
				if ($this->insert($query) === false){
					return false;
				}
			}
			return true;
		}
		public function insert($sqlid){
			if (defined($sqlid)){
				$sqlid = constant( $sqlid );
			}
			$sql = null;
			$fga = func_get_args();
			$ret = $this->execute($sqlid, $fga, $sql);
			if ($ret === true){
				return $this->pdo->lastInsertId();
			}else{
				return false;
			}
		}
		public function update($sqlid){
			if (defined($sqlid)){
				$sqlid = constant( $sqlid );
			}
			$sql = null;
			$fga = func_get_args();
			return $this->execute($sqlid, $fga, $sql);
		}
		public function query($sqlid){
			if (defined($sqlid)){
				$sqlid = constant( $sqlid );
			}
			$sql = null;
			$fga = func_get_args();
			$ret = $this->execute($sqlid, $fga, $sql);
			if ($ret){
				return $sql->fetchColumn();
			}else{
				return false;
			}
		}
		public function query_table($sqlid){
			if (defined($sqlid)){
				$sqlid = constant( $sqlid );
			}
			$sql = null;
			$fga = func_get_args();
			$ret = $this->execute($sqlid, $fga, $sql);
			if ($ret){
				return $sql->fetchAll();
			}else{
				return false;
			}		
		}
		public function query_fetch_num($sqlid){
			if (defined($sqlid)){
				$sqlid = constant( $sqlid );
			}
			$sql = null;
			$fga = func_get_args();
			$ret = $this->execute($sqlid, $fga, $sql);
			if ($ret){
				return $sql->fetchAll(PDO::FETCH_NUM);
			}else{
				return false;
			}		
		}
		public function query_list($sqlid){
			if (defined($sqlid)){
				$sqlid = constant( $sqlid );
			}
			$sql = null;
			$fga = func_get_args();
			$ret = $this->execute($sqlid, $fga, $sql);

			if ($ret){
				return $sql->fetch();
			}else{
				return false;
			}		
		}
		public function rollBack(){
			return $this->pdo->rollBack();
		}
		public function beginTransaction(){
			if ($this->begin) return;
			$this->begin = true;
			return $this->pdo->beginTransaction();
		}
		public function commit(){
			if ($this->begin == false) return;
			$this->begin = false;
			return $this->pdo->commit();
		}
		public function query_multi($query){
			$this->pdo->beginTransaction();
			$query = explode(';', $query);
			try {
				for ($i=0;$i<sizeof($query);++$i){
					if (trim($query[$i]) == '') continue;

					if ($this->pdo->exec( (string)$query[$i] ) === false){
						throw new Exception('');
					}
				}
				$this->pdo->commit();
				return true;
			}catch(Exception $e) {
				$this->pdo->rollBack();
				return false;
			}
		}
	}
?>