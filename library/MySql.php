<?php
/**
* MollyCMS
* File: Database.class.php
**/
class Mysql extends \PDO {

	private $prefix = false;

	public function __construct($_dbName=DB_NAME) {
		parent::__construct(
				DB_TYPE.
				':host='.DB_HOST.
				';dbname='.$_dbName,
				DB_USER,
				DB_PASSWORD);
	}

	public function exe($statement, $data = null, $lastID = false) {
		$stmt = $this->prepare($statement);

		if ($data != null) {
			foreach ($data as $key => $value) {
				if (is_numeric($value)) {
					$stmt -> bindValue(":$key", $value, \PDO::PARAM_INT);
				} else {
					$stmt -> bindValue(":$key", $value);
				}
			}
		}

		$stmt->execute();

		if ($lastID) {
			return $this->lastInsertId();
		}
	}

	/**
	 * @param String $table - Name of table to insert into
	 * @param String $data - Parameters to bind
	 * @return Int - Last inserted row id
	 */
	public function insert($table, $data) {
		//Sort data alphabetically
		ksort($data);

		$fieldNames = implode('`,`', array_keys($data));

		$fieldValues = ':' . implode(', :', array_keys($data));

		if ($this->prefix) $table = $this->prefix.$table;

		$stmt = $this -> prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");

		foreach ($data as $key => $value) {

			if (is_numeric($value)) {
				$stmt -> bindValue(":$key", $value, \PDO::PARAM_INT);
			} else {
				$stmt -> bindValue(":$key", $value);
			}

		}

	    $stmt->execute();

	    $lastID = $this->lastInsertId();

	    if ($lastID != 0) {
	    	return $lastID;
	    } else {
	    	return "-1";
	    }
	}

	/**
	 * @param String $table - Name of table to insert into
	 * @param String $where - Where SQL string
	 */
	public function delete($sql, $data = array()) {
		//Sort data alphabetically
		ksort($data);

		$stmt = $this->prepare($sql);

		if($stmt){
			foreach ($data as $key => $value) {
				if (is_numeric($value)) {
					$stmt -> bindValue(":$key", $value, \PDO::PARAM_INT);
				} else {
					$stmt -> bindValue(":$key", $value);
				}
			}
			//error_log($sql);
			try{
				$stmt->execute();
			} catch (Exception $error){
		        error_log("Failed: " . $error->getMessage());
		    }

			if ($stmt->rowCount() > 0) {
				return "true";
			}
		}
		return "false";

	}

	public function __exec__($sql, $data = array()) {
		
		$this->exec($sql);
		return "true";

	}

	/**
	 * @param String $sql - A SQL string
	 * @param Array $data - Parameters to bind
	 * @param String $fetchType - A PDO Fetch Mode
	 * @return Array - An array with the data and rowCount
	 */
	public function select($sql, $data = array(), $fetchType = \PDO::FETCH_ASSOC) {
		//Sort data alphabetically
		ksort($data);

		$stmt = $this -> prepare($sql);

		foreach ($data as $key => $value) {

			if (is_numeric($value)) {
				$stmt -> bindValue(":$key", $value, \PDO::PARAM_INT);
			} else {
				$stmt -> bindValue(":$key", $value);
			}

		}

		$stmt->execute();

		if ($stmt->rowCount() == 0) {
			return false;
		} else {
			return $stmt->fetchAll($fetchType);
		}
	}

	/**
	*	Set the prefix that will be used on all database calls
	* 	@param String $prefix
	*/
	public function setTablePrefix($prefix) {
		$this->prefix = $prefix;
		return $prefix;
	}

}
