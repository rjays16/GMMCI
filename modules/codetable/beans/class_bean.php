<?php
require "./roots.php";
class Bean {
	var $db;
	var $objectName;
	var $coreTable;
	var $auditTable;
	var $deleteFlag;
	var $fields;
	var $sql;
	var $dictionary;
	var $resultSet;
	var $foundRows;
	var $result;

	var $nullValue;
	var $emptyValue;

	const METADATA_DIR = 'modules/codetable/metadata/';

	function Bean($objectName = false) {
		global $db, $dictionary;

		$this->nullValue = 'NULL';
		$this->emptyValue = null;

		// classes extending the Bean class usually specify the object name before instantiation
		// specify object name if you want to use the generic Bean class
		if ($objectName) {
			$this->objectName = $objectName;
		}

		// ensure that all related metadata definitions are loaded
		$this->keyValues =Array();
		$this->fields = Array();
		$this->loadMeta() or die('Metadata definitions not loaded for object \''.$objectName.'\'...');

		// load active connection to bean property
		$this->db = $db;
	}

	/**
	 * Loads all metadata definitons for the current object
	 *
	 * @return boolean outcome of the load attempt
	 */
	function loadMeta() {
		global $root_path;
		if (!$this->objectName) {
			return false;
		}

		// load metadata definition from the definitions file, if found
		include_once $root_path.Bean::METADATA_DIR.$this->objectName.'/'.$this->objectName.'_metadata.php';

		// create reference to metadata definition within the class
		$this->dictionary =& $Dictionary[$this->objectName];

		// setup key values array
		foreach ($this->dictionary['primaryKeys'] as $field) {
			$this->keyValues[$field] = $this->emptyValue;
		}

		// setup fields array
		foreach ($this->dictionary['fields'] as $field=>$value) {
			$this->fields[$field] = $this->emptyValue;
		}

		$this->coreTable = $this->dictionary['coreTable'];
		$this->auditTable = $this->dictionary['auditTable'];
		$this->deleteFlag = $this->dictionary['deleteFlag'];
		if ($this->auditTable) {
			$this->audit = true;
		}
		return true;
	}

	/*
	*
	*/
	function getFields() {
		return array_keys($this->fields);
	}

	function setKeyValues($pkArray) {
		if (!is_array($pkArray)) $pkArray = array($pkArray);
		$i = 0;
		foreach ($this->keyValues as $key=>$v) {
			if (isset($pkArray[$key])) {
				$this->fields[$key] = $pkArray[$key];
				$this->keyValues[$key] = $pkArray[$key];
			}
			elseif (isset($pkArray[$i])) {
				$this->fields[$key] = $pkArray[$i];
				$this->keyValues[$key] = $pkArray[$i];
			}
			else {
				$this->KeyValues[$key] = $this->emptyValue;
			}
			$i++;
		}
	}

	/**
	* Pass data values to the bean from an array
	*
	* @param mixed $data the bean data in Array format
	* @return nothing
	*/
	function load( $data ) {
		if (!$data) return false;
		foreach ( $this->fields as $field=>$value ) {
			if (isset($data[$field])) {
				$this->fields[$field] = $data[$field];
			}
			else {
				$this->field[$field] = $this->emptyValue;
			}
		}

		foreach ( $this->keyValues as $field=>$value ) {
			if (isset($data[$field]) && $data[$field]!==$this->emptyValue) {
				$this->keyValues[$field] = $data[$field];
			}
			else {
				$this->keyValues[$field] = $this->emptyValue;
			}
		}
	}

	/**
	* Fetches a row in the database and stores it in the bean; ideally, the key Values
	* specifying the row should be set first through the setKeyValues function. Otherwise,
	* the function will just fetch the first available row by random-access
	*
	* @param bool $no_store do not store the fetched row in the bean, instead just return
	*   the fetched row
	* @return mixed the fetched row
	*/
	function fetch( $no_store=false ) {
		$this->sql = "SELECT `".implode('`,`',$this->getFields())."`\n".
			"FROM ".$this->dictionary['coreTable']."\n";

		$where = array();
		foreach ($this->keyValues as $i=>$v) {
			if ($v !== $this->emptyValue) {
				$where[] .= "`$i`=".$this->db->qstr($v);
			}
		}
		if ($where) {
			$this->sql .= 'WHERE '. implode("\nAND ", $where) . "\n";
		}

		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$row = $this->db->GetRow($this->sql);

		// store fetched fields in bean field array if $no_store flag is set
		if (!$no_store) {
			$this->load($row);
		}
		return $row;
	}

	/**
	 * private function to retrieve the total number of rows (disregarding the LIMIT clause) of the
	 * last query that has the SQL_CALC_FOUND_ROWS clause
	 *
	 * @return int the total row count of the query without the LIMIT clause
	 */
	function _found_rows() {
		return $this->db->GetOne("SELECT FOUND_ROWS()");
	}

	/**
	 * Returns all fields based on the ListView columns definiton object $columnsDefs
	 *
	 * the function parses the passed Columns definition object ($columnsDefs) and
	 * returns an ordered array of all the defined fields
	 *
	 * @param Array Columns definition for the List View, usually referenced from the *_listview.php file
	 * @return Array array of all ListView fields
	 */
	function getListViewFields($columnsDefs) {
		$fields = array();
		foreach ($columnsDefs as $v) {
			if ($v['field']) {
				$fields[] = $v['field'];
			}
		}
		return $fields;
	}

	/**
	 * Returns a paged view of the data retrieved from the database as
	 * specified by the $columnDefs column definition
	 *
	 * @param Array Columns definition for the List View, usually referenced from the *_listview.php file
	 * @param int offset the index of the record that will be first retrieved
	 * @param int limit the maximum number of rows to fetch
	 * @param string the ORDER BY clause of the SQL query
	 * @param bool if set, the query will contain the directive SQL_CALC_FOUND_ROWS
	 * @return Array fetched rows stored in array format
	 */
	function getListViewRows( $columnsDefs, $custom_filters=null, $offset=0, $limit=-1, $sort='', $calc_found_rows=false ) {
		$fields = $this->getListViewFields($columnsDefs);

		$this->sql = "SELECT ";
		if ($calc_found_rows) {
			$this->sql .= "SQL_CALC_FOUND_ROWS";
		}

		if ($this->deleteFlag) {
			$fields[] = "`{$this->deleteFlag}` `_deleted`";
		}

		$this->sql .= "\n".
			implode(',', $fields) ."\n".
			"FROM ".$this->coreTable."\n";

		$filters = array();
		if ($this->deleteFlag) {
			$filters[$this->deleteFlag] = "`{$this->deleteFlag}`=0";
		}

		if (is_array($custom_filters)) {
			foreach ($custom_filters as $field=>$filter) {
				if ($filter) {
					$filters[$field] = $filter;
				}
				elseif ($filter===null) {
					unset($filters[$field]);
				}
			}
		}
		if ($filters) {
			$where = array();
			foreach ($filters as $field=>$filter) {
				$where[] = $filter;
			}
			$this->sql .= 'WHERE '.implode("\nAND ", $where). "\n";
		}
		if ($sort) {
			$this->sql.="ORDER BY $sort\n";
		}
		if ($limit !== -1) {
			$this->sql.="LIMIT {$offset},{$limit}\n";
		}
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		#echo $this->sql;
		if (($resultSet = $this->db->Execute($this->sql)) !== false) {
			$this->result = $resultSet->GetRows();
			if ($calc_found_rows) {
				$this->foundRows = $this->_found_rows();
			}
			else {
				$this->foundRows = $resultSet->RecordCount();
			}
			return $this->result;
		}
		else {
			// error?
			return false;
		}
	}

	/**
	* put your comment there...
	*
	* @param string $audit_id
	* @return mixed
	*/
	function getAuditDetails( $audit_id ) {
		if (!$this->audit) return false;
		$this->sql = "SELECT field_name, before_value, after_value, audit_timestamp, action, login_id\n".
			'FROM '.$this->auditTable."\n".
			'WHERE audit_id='.$this->db->qstr($audit_id);
		return $this->db->GetAssoc($this->sql);
	}

	/**
	 * Returns a paged view of the audit history. Ideally, setKeyValues function should
	 * be called first to retrieve audit history for a specific entry. Otherwise, a
	 * full audit view of the code table object will be returned
	 *
	 * @param int offset the index of the record that will be first retrieved
	 * @param int limit the maximum number of rows to fetch
	 * @param string the ORDER BY clause of the SQL query
	 * @param bool if set, the query will contain the directive SQL_CALC_FOUND_ROWS
	 * @return mixed returns fetched rows stored in array format if successful, returns FALSE otherwise
	 */
	function getAuditTrail( $offset=0, $limit=-1, $sort='', $calc_found_rows=false ) {
		if (!$this->audit) return false;

		$this->sql = "SELECT ";
		if ($calc_found_rows) {
			$this->sql .= "SQL_CALC_FOUND_ROWS";
		}

		$key_fields = array_keys($this->keyValues);
		$fields = array_merge($key_fields , array('audit_id', 'login_id', 'audit_timestamp', 'action', 'field_name', 'before_value', 'after_value'));

		$this->sql .= "\n".
			implode(',', $fields) ."\n".
			"FROM ".$this->auditTable."\n";

		$where = array();
		foreach ($this->keyValues as $i=>$v) {
			if ($v !== $this->emptyValue) {
				$where[] .= "`$i`=".$this->db->qstr($v);
			}
		}
		if ($where) {
			$this->sql .= 'WHERE '. implode("\nAND ", $where) . "\n";
		}

		$this->sql .= "GROUP BY audit_id\n";

		if ($sort) {
			$this->sql.="ORDER BY $sort\n";
		}
		if ($limit !== -1) {
			$this->sql.="LIMIT {$offset},{$limit}\n";
		}

		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		if (($resultSet = $this->db->Execute($this->sql)) !== false) {
			$this->result = $resultSet->GetRows();
			if ($calc_found_rows) {
				$this->foundRows = $this->_found_rows();
			}
			else {
				$this->foundRows = $resultSet->RecordCount();
			}
			return $this->result;
		}
		else {
			// error?
			return false;
		}
	}

	/**
	* private method for checking if a row exists with primary values defined in
	* the $keys argument
	* @param mixed $keys the keys of the row to be checked
	* @return bool return TRUE if the row exists
	*/
	function _row_exists($keyValues) {
		// we will be using the logic in setKeyValues for this function so we will save
		// the internal keyValues first and restore them later...
		$saveKeyValues = $this->keyValues;

		$this->setKeyValues($keyValues);
		$query = 'SELECT EXISTS(SELECT * FROM '.$this->coreTable.' ';
		$where = array();
		foreach ($this->keyValues as $i=>$v) {
			if ($v !== $this->emptyValue) {
				$where[] .= "`$i`=".$this->db->qstr($v);
			}
		}
		if ($where) {
			$query .= 'WHERE '. implode("\nAND ", $where) . ")\n";
		}

		$result = $this->db->GetOne($query);

		// ...and restore Key Values array
		$this->keyValues = $saveKeyValues;
		return (int)$result==1;
	}


	/**
	* Implements a generic insert/update logic for the bean; ensure that the key Values
	* for the bean are set through the setKeyValues function first before calling this;
	* the function first queries the database for an existing row specified by the key Values
	* and determines if an insert or update operation is necessary
	*
	* @param bool audit if TRUE, an audit entry for the operation will be created
	* @return boolean returns TRUE the save operation is successful, FALSE otherwise
	*/
	function save( $force_insert=false ) {

		$audit_array = array();
		if ($force_insert) {
			$isUpdate = false;
		}
		else {
			$isUpdate = $this->_row_exists($this->keyValues);
		}

		// get current user credentials and timestamp
		$current_user = $_SESSION['sess_temp_userid'];
		$current_ts = date('YmdHis');
		if ($this->audit) {
			$audit_id = $this->_create_guid();
		}

		if ($isUpdate) {
			$old = $this->fetch(true);
			$this->sql = 'UPDATE '.$this->coreTable." SET\n";
			$updateFields = array();
			foreach ( $this->fields as $field=>$value ) {
				if ($value !== $this->emptyValue) {
//					if ($value != $old[$field]) {
//						if ($this->audit) {
//							$audit_array[] =
//								Array(
//									$field,
//									$old[$field],
//									$value
//								);
//						}
					if ($value === $this->nullValue)
						$updateFields[] = "`$field`=NULL";
					else
						$updateFields[] = "`$field`=".$this->db->qstr($value);
//					}
				}
			}

			// include current user credentials as modifier
			$updateFields[] = '`modify_id`='.$this->db->qstr($current_user);
			$updateFields[] = "`modify_time`='".$current_ts."'";

			$this->sql .= implode(",\n", $updateFields)."\n";

			$whereFields = array();
			foreach ( $this->keyValues as $field=>$value ) {
				if ($value !== $this->emptyValue) {
					if ($value === $this->nullValue)
						$whereFields[] = "`$field`=NULL";
					else
						$whereFields[] = "`$field`=".$this->db->qstr($value);
				}
			}
			$this->sql .= 'WHERE '.implode("\nAND ", $whereFields);

			$result = $this->db->Execute($this->sql);
		}
		else {
			$this->sql = 'INSERT INTO '.$this->coreTable."(\n";

			$audit_array[] = array('','','');
			$insertFields = array();
			$insertValues = array();
			$insertKeyValues = array();
			foreach ( $this->fields as $field=>$value ) {
				if ($value !== $this->emptyValue) {
					$insertFields[] = "`$field`";
					if ($value === $this->nullValue)
						$insertValues[] = 'NULL';
					else
						$insertValues[] = $this->db->qstr($value);
				}

				// save key Values
				if ( isset($this->keyValues[$field]) ) {
					$insertkeyValues[$field] = $value;
				}
			}

			$insertFields[] = '`create_id`';
			$insertValues[] = $this->db->qstr($current_user);
			$insertFields[] = '`create_time`';
			$insertValues[] = "'".$current_ts."'";

			$insertFields[] = '`modify_id`';
			$insertValues[] = $this->db->qstr($current_user);
			$insertFields[] = '`modify_time`';
			$insertValues[] = "'".$current_ts."'";

			$this->sql .= implode(',', $insertFields).")\n";
			$this->sql .= 'VALUES('. implode(',', $insertValues).")\n";

			$result = $this->db->Execute($this->sql);
		}

		if ($result === false) {
			$this->result = false;
		}
		else {
			if (!$isUpdate) {
				$this->setKeyValues($insertKeyValues);
				// reload the bean from DB
				$this->fetch();
			}
			else {
				// reload the bean from DB
				$this->fetch();
				foreach ( $this->fields as $field=>$value ) {
					if ($value != $old[$field] && $this->audit) {
						$audit_array[] = Array( $field, $old[$field], $value );
					}
					else {
						// same
					}
				}
			}

			// AUDIT TIME!!!
			if ($this->audit) {
				$key_fields = array_keys($this->keyValues);
				$key_values = array_values($this->keyValues);
				foreach ($key_values as $key=>$value) {
					$key_values[$key] = $this->db->qstr($value);
				}

				$audit_data = Array(
					'audit_id'=>$this->db->qstr($audit_id),
					'login_id'=>$this->db->qstr($current_user),
					'audit_timestamp'=>"'{$current_ts}'",
					'action'=>$isUpdate?"'update'":"'create'",
					'field_name'=>'?',
					'before_value'=>'?',
					'after_value'=>'?'
				);

				$audit_fields = array_merge($key_fields, array_keys($audit_data));
				$audit_values = array_merge($key_values, array_values($audit_data));

				$query = "INSERT INTO $this->auditTable(`".implode('`,`',$audit_fields)."`)\n".
					"VALUES(".implode(',', $audit_values).")";

				$this->db->Execute($query, $audit_array);
			}
			$this->result = true;
		}

		return $this->result;
	}

	/**
	* Generic delete logic
	*
	* @param mixed $additional_comments additional comments to include in the audit entry
	* @return bool returns TRUE if the delete operation is successful
	*/
	function delete( $additional_comments='' ) {
		// do not allow deleting if no keys are supplied
		if (!$this->keyValues) return false;

		if (!$this->deleteFlag) {
			// [hysically delete the entry if no delete flag field is defined]
			$this->result = false;
		}
		else {
			// Logical entry deletion
			$query = 'UPDATE '.$this->coreTable." SET `{$this->deleteFlag}`=1\n";
			$whereFields = array();
			foreach ( $this->keyValues as $field=>$value ) {
				if ($value !== $this->emptyValue) {
					if ($value === $this->nullValue)
						$whereFields[] = "`$field`=NULL";
					else
						$whereFields[] = "`$field`=".$this->db->qstr($value);
				}
			}
			$query .= 'WHERE '.implode("\nAND ", $whereFields);
			$this->result = $this->db->Execute($query);
		}

		if ($this->result !== false) {
			// AUDIT TIME!!!
			if ($this->audit) {
				$current_user = $_SESSION['sess_temp_userid'];
				$current_ts = date('YmdHis');
				$audit_id = $this->_create_guid();

				$key_fields = array_keys($this->keyValues);
				$key_values = array_values($this->keyValues);
				foreach ($key_values as $key=>$value) {
					$key_values[$key] = $this->db->qstr($value);
				}

				$audit_data = Array(
					'audit_id'=>$this->db->qstr($audit_id),
					'login_id'=>$this->db->qstr($current_user),
					'audit_timestamp'=>"'{$current_ts}'",
					'action'=>"'delete'",
					'field_name'=>"''",
					'before_value'=>$this->db->qstr($additional_comments),
					'after_value'=>"''"
				);

				$audit_fields = array_merge($key_fields, array_keys($audit_data));
				$audit_values = array_merge($key_values, array_values($audit_data));

				$query = "INSERT INTO $this->auditTable(`".implode('`,`',$audit_fields)."`)\n".
					"VALUES(".implode(',', $audit_values).")";

				$this->db->Execute($query);
			}
		}
		return $this->result;
	}

	/**
	* Generic restore logic
	*
	* @param mixed $additional_comments additional comments to include in the audit entry
	* @return bool returns TRUE if the delete operation is successful
	*/
	function restore( $additional_comments='' ) {
		// do not allow deleting if no keys are supplied
		if (!$this->keyValues) return false;

		if (!$this->deleteFlag) {
			// [hysically delete the entry if no delete flag field is defined]
			$this->result = false;
		}
		else {
			// Logical entry deletion
			$query = 'UPDATE '.$this->coreTable." SET `{$this->deleteFlag}`=0\n";
			$whereFields = array();
			foreach ( $this->keyValues as $field=>$value ) {
				if ($value !== $this->emptyValue) {
					if ($value === $this->nullValue)
						$whereFields[] = "`$field`=NULL";
					else
						$whereFields[] = "`$field`=".$this->db->qstr($value);
				}
			}
			$query .= 'WHERE '.implode("\nAND ", $whereFields);
			$this->result = $this->db->Execute($query);
		}

		if ($this->result !== false) {
			// AUDIT TIME!!!
			if ($this->audit) {
				$current_user = $_SESSION['sess_temp_userid'];
				$current_ts = date('YmdHis');
				$audit_id = $this->_create_guid();

				$key_fields = array_keys($this->keyValues);
				$key_values = array_values($this->keyValues);
				foreach ($key_values as $key=>$value) {
					$key_values[$key] = $this->db->qstr($value);
				}

				$audit_data = Array(
					'audit_id'=>$this->db->qstr($audit_id),
					'login_id'=>$this->db->qstr($current_user),
					'audit_timestamp'=>"'{$current_ts}'",
					'action'=>"'restore'",
					'field_name'=>"''",
					'before_value'=>$this->db->qstr($additional_comments),
					'after_value'=>"''"
				);

				$audit_fields = array_merge($key_fields, array_keys($audit_data));
				$audit_values = array_merge($key_values, array_values($audit_data));

				$query = "INSERT INTO $this->auditTable(`".implode('`,`',$audit_fields)."`)\n".
					"VALUES(".implode(',', $audit_values).")";

				$this->db->Execute($query);
			}
		}
		return $this->result;
	}

	/**
	* loads a custom logic hook that will be called when an event of type specified by $event
	* is processed on the bean
	* valid values for $event are as follows:
	*   - beforesave
	* 	- aftersave
	* 	- beforefetch
	* 	- afterfetch
	* 	- beforecreate
	* 	- aftercreate
	*
	* @param mixed $characters
	*/
	function _call( $event, $hookParams) {

	}


	/**
	* A temporary method of generating GUIDs.
	* @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
	*
	*/
	function _create_guid()
	{
		$microTime = microtime();
		list($a_dec, $a_sec) = explode(" ", $microTime);

		$dec_hex = dechex($a_dec* 1000000);
		$sec_hex = dechex($a_sec);

		$this->_ensure_length($dec_hex, 5);
		$this->_ensure_length($sec_hex, 6);

		$guid = "";
		$guid .= $dec_hex;
		$guid .= $this->_create_guid_section(3);
		$guid .= '-';
		$guid .= $this->_create_guid_section(4);
		$guid .= '-';
		$guid .= $this->_create_guid_section(4);
		$guid .= '-';
		$guid .= $this->_create_guid_section(4);
		$guid .= '-';
		$guid .= $sec_hex;
		$guid .= $this->_create_guid_section(6);

		return $guid;

	}

	function _create_guid_section($characters)
	{
		$return = "";
		for($i=0; $i<$characters; $i++)
		{
			$return .= dechex(mt_rand(0,15));
		}
		return $return;
	}

	function _ensure_length(&$string, $length)
	{
		$strlen = strlen($string);
		if($strlen < $length)
		{
			$string = str_pad($string,$length,"0");
		}
		else if($strlen > $length)
		{
			$string = substr($string, 0, $length);
		}
	}

}