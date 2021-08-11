	<?php
/**
 * Created on Mar 5, 2020
 * Updata on Mar 6, 2020
 * @author Alexandre Bezerra Barbosa
 * @email alxbbarbosa@yahoo.com.br
 * @author Gabriel Assuero
 * @email gabrielassuerors@gmail.com
 * Version 1.1.0
 */

/**

	ActiveRecord return json 
	Configure $tableRows

*/

abstract class ActiveRecord {

	private $content;
	protected $tableRows;
	protected $idField;
	protected $table;
	protected $logTimestamp;

	public function __construct(){
		if(empty($this->tableRows)){

			$this->tableRows = 'NULL';
		}
		if(!is_bool($this->logTimestamp)){

			$this->logTimestamp = TRUE;
		}
		if($this->table == NULL){

			$this->table = strtolower(get_class($this));
		}
		if($this->idField == NULL){

			$this->idField  = 'id';
		}
	}

	public function setRows($key){

		$this->tableRows = $key;
	}

	public function getRows(){
		return $this->tableRows;
	}

	public function __set($key, $value){

		$this->content[$key] = $value;	
	}

	public function __get($key){

		return $this->content[$key];
	}

	public function __isset($key){

		return isset($this->content[$key]);
	}

	public function __unset($key){

		if(isset($key)){
			unset($this->content[$key]);
			return TRUE;
		}
		return FALSE;

	}

	public function __clone(){

		if(isset($this->content[$this->idField])){
			unset($this->content[$this->idField]);
		}

	}

	// Array to content array id is not computed

	public function getContent(){

		return $this->content;
	}

	public function setContent(array $array){

		$this->content = $array;
	}

	// Transform array to json 

	public function getJson(){

		return json_encode($this->content);
	}

	public function setJson(string $str){

		$this->content = json_decode($str);
	}

	//Suport function

	private function format($value){

		switch ($value) {
			case is_string($value) && !empty($value):
				return "'{$value}'";
				break;

			case is_bool($value):
				return $value ? 'TRUE' : 'FALSE';
				break;

			case $value !=='':
				return $value;
				break;
			
			default:
				return 'NULL';
				break;
		}

	}

	private function cvtContent(){

		$newContent = array();
		foreach ($this->content as $key => $value) {
			if (is_scalar($value)) {
				$newContent[$key] = $this->format($value);
			}
		}
		return $newContent;

	}

	//---------------------------------- SAVE -------------------------------------------- 

	public function save(){

		$newContent = $this->cvtContent();
 
	    if (isset($this->content[$this->idField])) {

	        $sets = array();
	        foreach ($newContent as $key => $value) {

	            if ($key === $this->idField)
	                continue;
	            $sets[] = "{$key} = {$value}";
	        }

	        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->id} = {$this->content[$this->idField]};";

	    } else {

	    	if ($this->logTimestamp === TRUE) {

	            $newContent['created_at'] = "'" . date('Y-m-d H:i:s') . "'";
	            $newContent['updated_at'] = "'" . date('Y-m-d H:i:s') . "'";
        	}

	        $sql = "INSERT INTO {$this->table} (" . implode(', ', array_keys($newContent)) . ') VALUES (' . implode(',', array_values($newContent)) . ');';
	    }

	    if ($connection = Connection::getInstance('./configdb.ini')) {

		    return $connection->exec($sql);
		} else {

		    throw new Exception('ERROR: CNT_NOT_FOUND');
		}

	}

	//---------------------------------- FIND --------------------------------------------

	public static function find($key){

		$class = get_called_class();
		$idField = (new $class())->idField;
		$table = (new $class())->table;

		$sql = 'SELECT * FROM'. (is_null($table) ? strtolower($class) : $table);
		$sql .= ' WHERE '. (is_null($idField) ? 'id' : $idField);
		$sql .= " = {$key}";

		if($connection = Connection::getInstance('./configdb.ini')){

			$result = $connection->query($sql);
			if($result){

				$newObject = $result->fetchObject(get_called_class());
			}
			return $newObject;

		} else {

			throw new Exception('ERROR: CNT_NOT_FOUND');
		}

	}

	//---------------------------------- DELETE -------------------------------------------


	public function delete(){

		if(isset($this->content[$this->idField])){

			$sql = "DELETE FROM {$this->table} WHERE {$this->idField} = {$this->content[$this->idField]}";

			if($connection = Connection::getInstance('./configdb.ini')){

				return $connection->query($sql);
			} else {

				throw new Exception('ERROR: CNT_NOT_FOUND');
			}
		}
	}

	//---------------------------------- LIST ALL -----------------------------------------

	public static function all(string $filter = '', int $limit = 0, int $offset = 0){

		$class = get_called_class();
		$table = (new $class())->table;

		$sql = 'SELECT * FROM'. (is_null($table) ? strtolower($class) : $table);
		$sql .= ($filter !== '') ? "WHERE {$filter}" : "";
		$sql .= ($limit >0) ? "LIMIT {$limit}" : "";
		$sql .= ($offset >0) ? "OFFSET {$offset}" : "";
		$sql .= ';'; 
		
		if($connection = Connection::getInstance('./configdb.ini')){

			$result = $connection->query($sql);
			return $result->fetchAll(PDO::FETCH_CLASS, get_called_class());
		} else {

			throw new Exception('ERROR: CNT_NOT_FOUND');
		}

	}

	//---------------------------------- FIND FIRST ---------------------------------------


	public static function findFisrt(string $filter = ''){

	    return self::all($filter, 1);
	}




}
