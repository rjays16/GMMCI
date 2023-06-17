<?php
require("./roots.php");
require_once($root_path.'include/care_api_classes/class_error.php');
require_once($root_path.'classes/log4php/Logger.php');

/**
* Care2x API package
* @package care_api
*/

/**
*  Core methods. Will be extended by other classes.
*  Note this class should be instantiated only after a "$db" adodb  connector object
* has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Core {
	/**
	* @var string Table name used for core routines. Default table name.
	*/
	var $coretable;
	/**
	* @var object Instance of the Logger object
	* @access public
	* @author Alvin Quiñones
	*/
	var $logger;
	/**
	* @var string Name of the logger instance, defaults to root logger
	* @access private
	* @author Alvin Quinones (06-14-10)
	*/
	var $logger_name;
	/**
	* @var string Path to the configuration file (if applicable). Extending classes that use custom loggers should set this on initialization
	* @access private
	* @author Alvin Quinones (06-14-10)
	*/
	var $logger_config_file='include/logger.default.properties';
	/**
	* @var array Specifies the columns to be used as primary keys. Used in generic save routine
	* @author Alvin Quinones (06-09-10)
	*/
	var $key_columns;
	/**
	* @var string Holder for SQL query. Can be extracted with the "getLastQuery()" method.
	*/
	var $sql='';
	/**
	* @var array  Contains fieldnames of the table named in the $coretable. For internal update/insert operations.
	*/
	var $ref_array=array();
	/**
	* @var array   For internal update/insert operations
	*/
	var $data_array=array();
	/**
	* @var array   For internal update/insert operations
	*/
	var $buffer_array=array();
	/**
	* @var ADODB record object  For sql query results.
	*/
	var $result;
	/**
	* @var string  For update sql queries condition
	*/
	var $where;
	/**
	* @var int  For counting resulting rows. Can be extracted w/ the "LastRecordCount()" method.
	*/
	var $rec_count;
	/**
	* @var mixed
	*/
	var $buffer;
	/**
	* @var array  Used for containing results returned as pointer.
	*/
	var $res=array();
	/**#@+
	* @var boolean
	* @access private
	*/
	var $do_intern;
	var $ok;
	/**#@-*/
	var $is_preloaded=FALSE;
	/**
	*  Internal error message  usually used in debugging.
	* @var string
	* @access private
	*/
	var $error_reporter;
	var $error_msg='';
	var $db_error_msg='';
	/**
	* Status items used in sql queries "IN (???)"
	* @var string
	* @access private
	*/
	var $dead_stat="'deleted','hidden','inactive','void'";
	/**
	* Status items used in sql queries "IN (???)"
	* @var string
	* @access private
	*/
	var $normal_stat="'','normal'";

	var $insert_id = NULL;

	 # var $objDB;         // Added by LST to support transactions ... 12.11.2008


	/**
	* Initialize internal Logger object
	*
	* @param string $logger Name of logger instance. Defaults to the root Logger
	* @param string $config_file Curently not implemented. Path to the custom logger configuration file (relative to the root directory). Defaults to the default configuration file located at: include/logger.default.properties
	* @todo Use custom config file if sepecified
	* @author Alvin Quinones (06-14-10)
	*/
	function setupLogger($logger='') {
		global $root_path;
		@Logger::configure($root_path.$this->logger_config_file);
		if ($logger) {
			$this->logger_name = $logger;
			@$this->logger = Logger::getLogger($logger);
		}
		else {
			$this->logger_name = '';
			@$this->logger = Logger::getRootLogger();
		}
	}


	/**
	* Sets the coretable variable to the name of the database table.
	*
	* This points the core object to that database table and all core routines will use this table
	* until the core table is reset or replaced with another table name
	* @param string Table name
	* @param boolean Default value: FALSE. When set, the function retrieves metadata information for
	* the table and stores it as the reference array
	* @return void
	*/
	function setTable($table, $fetch_metadata=false) {
		global $db;
		$this->coretable=$table;
		if ($fetch_metadata) {
			$this->setRefArray($db->MetaColumnNames($this->coretable));
			$this->setkeyColumns($db->MetaPrimaryKeys($this->coretable));
		}
	}

	/**
	* Specifies the key columns of the core table
	*
	* @param mixed $array
	*/
	function setKeyColumns($array)	{
		if (!$array) return false;
		if (!is_array($array)) {
			$array = array($this->array);
		}
		$this->key_columns = $array;
	}

	/**
	* Retrieves the core table's key columns
	*
	* @return mixed
	*/
	function getKeyColumns()	{
		return $this->key_columns;
	}

	/**
	* Points the reference variable $ref_array to the field names' array.
	*
	* This field names array corresponds to  the database table set by the setTable() method
	* @param array By reference, the associative array containing the field names.
	* @return boolean
	*/
	function setRefArray(&$array) {
		if (!is_array($array)) {
			return FALSE;
		} else {
			$this->ref_array=$array;
			return TRUE;
		}
	}

	/**
	* Returns the contents of the ref_array property
	*
	* @return Array
	*/
	function getRefArray() {
		return $this->ref_array;
	}


	/**
	* Points the core data array to the external array that holds the data to be stored.
	* @param array  By reference, the associative array holding the data.
	*/
	function setDataArray(&$array){
		 $this->data_array=$array;

	}
	/**
	* Checks if a certain database record exists based onthe supplied query condition.
	*
	* Should be used privately.
	* @param string The query "where" condition without the WHERE word.
	* @return boolean
	*/
	function _RecordExists($cond=''){
		global $db;
		if(empty($cond)) return FALSE;
		if($this->result=$db->Execute("SELECT create_time FROM $this->coretable WHERE $cond")){
			if($this->result->RecordCount()){
				return TRUE;
			}else{return FALSE;}
		}else{return FALSE;}
	}

	/**
	* Sets the internal sql query variable to the sql query.
	* @param string Query statement.
	*/
	function setSQL(&$sql){
		$this->sql=$sql;
	}
	/**
	* Transaction routine, ADODB transaction. It internally uses the ADODB transaction routine.
	*
	* <code>
	* $sql="INSERT INTO care_users (item) VALUES ('value')";
	* $core->Transact($sql);
	* </code>
	* If the query parameter is empty, the method will use the sql query stored internally.
	* This internal sql query statement must be set with the setSQL() method or direct setting of variable before Transact() is called.
	*
	* <code>
	* $sql="INSERT INTO care_users (item) VALUES ('value')";
	* $core->setSQL($sql);
	* $core->Transact();
	* </code>
	*
	* or internally in class extensions
	*
	* <code>
	* $this->sql="INSERT INTO care_users (item) VALUES ('value')";
	* $this->Transact();
	* </code>
	*
	* @param string sql  SQL query statement.
	* @return TRUE/FALSE
	* @global ADODB db link
	* @access public
	*/
	function Transact($sql='') {
		global $db;
		if(!empty($sql)) {
			$this->setQuery($sql);
		}
		$db->BeginTrans();
		$this->ok=$db->Execute($this->getQuery());
		$this->setErrorMsg($db->ErrorMsg());
		$this->db_error_msg = $this->getErrorMsg();
		$this->insert_id = $db->Insert_ID();
		if($this->ok) {
			$db->CommitTrans();
			return TRUE;
		} else {
			if (!$this->getErrorMsg()) {
				$this->setErrorMsg($this->LastErrorMsg());
			}
			if ($this->logger) {
				@$this->logger->error("Query:".$this->getLastQuery()."\nError:".$this->getErrorMsg());
			}
			$db->RollbackTrans();
			return FALSE;
		}
	}

	/***
	*  Added by LST - 5-08-2008 to get actual error message raised in triggers!
	***/
	function LastErrorMsg() {
		global $db;
		$strSQL = "SELECT ErrorGID, Message FROM errorlog WHERE ABS(SECOND(created) - SECOND(NOW())) <= 1 ORDER BY ErrorGID DESC LIMIT 1";
		if (($row = $db->GetRow($strSQL)) !== false) {
			$db->Execute("DELETE FROM errorlog WHERE ErrorGID = ".$row["ErrorGID"]);
			return $row['Message'];
		}
		else {
			return "";
		}
	}


	/**
	* Filters the data array intended for saving, removing the key-value pairs that do not correspond to the table's field names.
	* @access private
	* @return int Size of the resulting data array.
	*/
	function _prepSaveArray(){
		$x='';
		$v='';

#		echo "_prepSaveArray : this->ref_array= ";
#		print_r($this->ref_array);
#		echo "<br> \n";
		// --- Inserted by LST - 2008-06-12
		$this->buffer_array = array();

		while(list($x,$v)=each($this->ref_array)) {

			if($v=='parent_dept_nr' && $this->data['parent_dept_nr']!='')
				{
					$this->buffer_array[$v] = 0;
				}

			#if(isset($this->data_array[$v])&&($this->data_array[$v]!='')) {
			#edited by VAN 10-12-09
			if(isset($this->data_array[$v])) {

				$this->buffer_array[$v]=$this->data_array[$v];
				if($v=='create_time' && $this->data['create_time']!='') $this->buffer_array[$v] = date('YmdHis');
			 }
		}
		# Reset the source array index to start

		reset($this->ref_array);
		return sizeof($this->buffer_array);
	}
	/**
	* Inserts data from the internal array previously filled with data by the <var>setDataArray()</var> method.
	*
	* This method also uses the field names from the internal array $ref_array previously set by "use????" methods that point
	* the core object to the proper table and fields names.
	* @access public
	* @return boolean
	*/
	function insertDataFromInternalArray() {

		//$this->data_array=NULL;
		$this->_prepSaveArray();

		# Check if  "create_time" key has a value, if no, create a new value
		//if(!isset($this->buffer_array['create_time'])||empty($this->buffer_array['create_time'])) $this->buffer_array['create_time']=date('YmdHis');
		#echo "insertDataFromInternalArray =";
		#echo "<br>";

		return $this->insertDataFromArray($this->buffer_array);
	}
	/**
	* Returns all records with the needed items from the table.
	*
	* The table name must be set in the coretable first by <var>setTable()</var> method.
	* @param string  items By reference. Items to be returned from each record fetched from the table. The items should be separted with commas.
	* @return mixed ADODB record object or boolean
	*
	* Example:
	*
	* <code>
	* $items="pid, name_last, name_first, birth_date, sex";
	* $core->setTable('care_person');
	* $persons = $core->getAllItemsObject($items);
	* while($row=$persons->FetchRow()){
	* ...
	* }
	* </code>
	*
	*/
	function getAllItemsObject(&$items) {
		global $db;
		$this->sql="SELECT $items  FROM $this->coretable";
#echo "<br>".$this->sql;
					if($this->res['gaio']=$db->Execute($this->sql)) {
			if($this->rec_count=$this->res['gaio']->RecordCount()) {
				 return $this->res['gaio'];
			} else { return FALSE; }
		} else { return FALSE; }
	}
	/**
	* Returns all records with all items from the table.
	*
	* The table name must be set in the coretable first by setTable() method.
	* @return mixed ADODB record object or boolean
	*
	* Example:
	*
	* <code>
	* $core->setTable('care_person');
	* $persons = $core->getAllDataObject();
	* while($row=$persons->FetchRow()){
	* ...
	* }
	* </code>
	*/
	function getAllDataObject() {
			global $db;
			$this->sql="SELECT *  FROM $this->coretable";
				//echo $this->sql;
				if($this->res['gado']=$db->Execute($this->sql)) {
						if($this->rec_count=$this->res['gado']->RecordCount()) {
				 return $this->res['gado'];
			} else { return FALSE; }
		} else { return FALSE; }
	}
	/**
	* Similar to getAllItemsObject() method but returns the records in an associative array.
	*
	* Returns all records with the needed items from the table. The table name must be set in the coretable first by <var>setTable()</var> method.
	*
	* Example:
	* <code>
	* $items="pid, name_last, name_first, birth_date, sex";
	* $core->setTable('care_person');
	* $persons = $core->getAllItemsArray($items);
	* while(list($x,$v)=each($persons)){
	* ...
	* }
	* </code>
	*
	* @param  string items By reference. Items to be returned from each record fetched from the table. The items should be separted with commas.
	* @return array associative
	* @access private
	*/
	function getAllItemsArray(&$items) {
			global $db;
			$this->sql="SELECT $items  FROM $this->coretable";
				//echo $this->sql;
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 //while($this->ref_array=$this->result->FetchRow());
				 //return $this->ref_array;
				 return $this->result->GetArray();
			} else { return FALSE; }
		} else { return FALSE; }
	}
	/**
	* Returns all records with the all items from the table.
	*
	* The table name must be set in the coretable first by setTable() method.
	* @return mixed ADODB record object or boolean
	* @global ADODB db link
	*
	* Example:
	* <code>
	* $core->setTable('care_person');
	* $persons = $core->getAllDataArray();
	* while(list($x,$v)=each($persons)){
	* ...
	* }
	* </code>
	*/
	function getAllDataArray() {
			global $db;
			$this->sql="SELECT *  FROM $this->coretable";
				//echo $this->sql;
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 while($this->ref_array=$this->result->FetchRow());
				 return $this->ref_array;
			} else { return FALSE; }
		} else { return FALSE; }
	}
	/**
	* Inserts data from an array  (passed by reference) into a table.
	*
	* This method  uses the table and field names from  internal variables previously set by "use????" methods that point
	* the object to the proper table and fields names. Private or public (preferably private being called by other methods).
	* @access private
	* @param array By reference. The array containing the data. Note: the array keys must correspond to the table field names.
	* @return boolean
	*/
	 function insertDataFromArray(&$array) {
		global $db, $errorReporter;
		global $dbtype;
		$x='';
		$v='';
		$index='';
		$values='';
		#echo "array = ";
		#print_r($array);
		#echo "<br> \n";
		if(!is_array($array)){ return FALSE;}
		while(list($x,$v)=each($array)) {

			# use backquoting for mysql and no-quoting for other dbs
			if (substr($dbtype, 0, 5) == 'mysql') $index.="`$x`,";
				else $index.="$x,";
			if (!strcasecmp($v,'null')) {
				$values.='NULL,';
			}
			else {
				$values.=$db->qstr($v).',';
			}
		}
		reset($array);
		$index=substr_replace($index,'',(strlen($index))-1);
		$values=substr_replace($values,'',(strlen($values))-1);

		$this->sql="INSERT INTO $this->coretable ($index) VALUES ($values)";
		#echo "insertDataFromArray : this->sql ='".$this->sql."' <br> \n";
		#exit();
		reset($array);

		$this->ok=$db->Execute($this->sql);
		$this->setErrorMsg($db->ErrorMsg());
		$this->insert_id = $db->Insert_ID();

		if($this->ok) {
			return TRUE;
		} else {
			if (!$this->getErrorMsg()) {
				$this->setErrorMsg($this->LastErrorMsg());
			}
			if ($this->logger) {
				@$this->logger->error("Query:".$this->getLastQuery()."\nError:".$this->getErrorMsg());
			}
			return FALSE;
		}
	}
	/**
	* Updates a record with the data from an array  (passed by reference) based on the primary key.
	*
	* This method also uses the field names from an internal array previously set by "use????" methods that point
	* the object to the proper table and fields names.
	* private or public (preferably private being call           ed by other methods)
	* @param array Data. By reference. Note: the array keys must correspond to the table field names
	* @param int Key used in the update queries' "where" condition
	* @param boolean Flags if the param $item_nr should be strictly numeric or not. Defaults to TRUE = strictly numeric.
	* @return boolean
	*/
	function updateDataFromArray(&$array,$item_nr='',$isnum=TRUE) {
		global $dbtype, $errorReporter;
		global $db;
		$x='';
		$v='';
		$elems='';
		#print_r($array);
		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
		else $concatfx='concat';

		#print_r($array);
		if(empty($array)) return FALSE;
		# if(empty($item_nr)||($isnum&&!is_numeric($item_nr))) return FALSE;
		while(list($x,$v)=each($array)) {
			# use backquoting for mysql and no-quoting for other dbs.
			if (substr($dbtype, 0, 5) == 'mysql')
				$elems.="`$x`=";
			else $elems.="$x=";

			if (stristr($v,$concatfx)!==false || !strcasecmp($v,'null')) {
				$elems.="$v,";
			}
			else {
				$elems.=$db->qstr($v).',';
			}
		}
		# Bug fix. Reset array.
		reset($array);

		$elems=substr_replace($elems,'',(strlen($elems))-1);
		if(empty($this->where)) $this->where="nr=$item_nr";
		$this->sql="UPDATE $this->coretable SET $elems WHERE $this->where";
		# Bug fix. Reset the condition variable to prevent affecting subsequent update calls.
		$this->where='';
		//echo '<br>'.$this->sql.'<br>';

		$this->ok=$db->Execute($this->sql);
		$this->db_error_msg = $db->ErrorMsg();
		$this->insert_id = $db->Insert_ID();

		if($this->ok) {
			return TRUE;
		} else {
			if (!$this->getErrorMsg()) {
				$this->setErrorMsg($this->LastErrorMsg());
			}
			if ($this->logger) {
				@$this->logger->error("Query:".$this->getLastQuery()."\nError:".$this->getErrorMsg());
			}
			return FALSE;
		}
	}


	/**
	* Updates a table using data from an internal array previously filled with data by the <var>setDataArray()</var> method.
	*
	* Update the record based on the primary key.
	* This method also uses the field names from an internal array previously set by "use????" methods that point
	* the object to the proper table and fields names.
	* @access public
	* @param int Key used in the update queries' "where" condition
	* @param boolean Flags if the param $item_nr should be strictly numeric or not. Defaults to TRUE = strictly numeric.
	* @return boolean
	*/
	function updateDataFromInternalArray($item_nr='',$isnum=TRUE) {
		$this->_prepSaveArray();
		return $this->updateDataFromArray($this->buffer_array,$item_nr,$isnum);
	}


	/**
	* Returns the the last sql query string
	* @return string
	*/
	function getLastQuery(){
		return $this->sql;
	}


	/**
	* Returns the the last sql query string. Synonym for getLastQuery
	* @return string
	*/
	function getQuery(){
		return $this->sql;
	}


	/**
	* Sets the internal query variable.
	* @param string Query to be saved
	*/
	function setQuery($query){
		$this->sql = $query;
		return $this->sql;
	}


	/**
	* Feturns the value of result
	* @return mixed
	*/
	function setResult($result){
		$this->result = $result;
		return $result;
	}

	/**
	* Feturns the value of result
	* @return mixed
	*/
	function getResult(){
		return $this->result;
	}


	/**
	* Feturns the value of error_msg, the internal error message.
	* @return string
	*/
	function getErrorMsg(){
		return $this->error_msg;
	}


	/**
	* Sets the internal error message to a given string.
	* @return string
	*/
	function setErrorMsg($msg){
		$this->error_msg = $msg;
		return $this->error_msg;
	}

	/**
	* Sets the "where"  condition in an update query used with the updateDataFromInternalArray() method.
	*
	* The where condition defaults to "nr='$nr'".
	* @access private
	* @param string cond The constraint for the sql query.
	* @return void
	*/
	function setWhereCondition($cond){
		$this->where=$cond;
	}
	/**
	* Returns the value of is_preloaded that is set by methods that preload large number of data.
	* @return boolean
	*/
	function isPreLoaded(){
		return $this->is_preloaded;
	}
	/**
	* Returns the value of rec_count
	* @return int
	*/
	function LastRecordCount(){
		return $this->rec_count;
	}



	/**
	* Saves temporary data to a cache in database.
	* @access public
	* @param string Cached data identification
	* @param mixed By referece.  Data to be saved.
	* @param boolean Signals the type of the data contained in the param $data.  FALSE=nonbinary data, TRUE=binary
	* @return boolean
	*/
	function saveDBCache($id,&$data,$bin=FALSE){
		if($bin) $elem='cbinary';
			else $elem='ctext';
		$this->sql="INSERT INTO care_cache (id,$elem,tstamp) VALUES ('$id','$data','".date('YmdHis')."')";
		return $this->Transact();
	}


	/**
	* Gets temporary data from the database cache.
	* @access public
	* @param string Cached data identification
	* @param mixed By reference.  Variable for the data to be fetched.
	* @param boolean   Signals the type of data contained in the $data.  FALSE=nonbinary data, TRUE=binary.
	* @return mixed string, binary or boolean
	*/
	function getDBCache($id,&$data,$bin=FALSE){
		global $db;
		$buf;
		$row;
		if($bin) $elem='cbinary';
			else $elem='ctext';
		$this->sql="SELECT $elem FROM care_cache WHERE id = '$id'";
				if($buf=$db->Execute($this->sql)) {
						if($buf->RecordCount()) {
				 $row=$buf->FetchRow();
				 $data=$row[$elem];
				 return TRUE;
			} else { return FALSE; }
		} else { return FALSE; }
	}
	/**
	* Deletes data from the database cache based on the id key.
	* @access public
	* @param char ID of data for deletion.
	* @return boolean
	*/
	function deleteDBCache($id){
		global $sql_LIKE;
		if(empty($id)) return FALSE;
		$this->sql="DELETE FROM care_cache WHERE id = '$id'";
		return $this->Transact();
	}
	/**
	* Returns the  core field names of the core table in an array.
	* @access public
	* @return array
	*/
	function coreFieldNames(){
		return $this->ref_array;
	}

	/**
	* Returns a list of filename within a path in array.
	* @access public
	* @param string Path of the filenames relative to the root path.
	* @param string Discriminator string.
	* @param  string The sort direction (ASC or DESC) defaults to ASC (ascending)
	* @return mixed  array or boolean
	*/
	function FilesListArray($path='',$filter='',$sort='ASC'){
		$localpath=$path.'/.';
		//echo "<br>$localpath<br>";
		$this->res['fla']=array();
		if(file_exists($localpath)){
			$handle=opendir($path);
			$count=0;
			while (FALSE!==($file = readdir($handle))) {
					if ($file != "." && $file != ".."){
					if(!empty($filter)){
						if(stristr($file,$filter)){
							$this->res['fla'][$count]=$file;
							$count++;
						}
					}else{
						$this->res['fla'][$count]=$file;
						$count++;
					}
					}
			}
			closedir($handle);
			if($count){
				$this->rec_count=$count;
				if($sort=='ASC'){
					@sort($this->res['fla']);
				}elseif($sort=='DESC'){
					@rsort($this->res['fla']);
				}
					return $this->res['fla'];
			}
		}else{
			return FALSE;
		}
	}
	/**
	* Returns the  value of the primary key of a row based on the column OID key
	*
	* Special for postgre and other dbms that returns OID after an insert query
	* @param str Table name
	* @param str Field name of the primary key
	* @param int OID value
	* @return int Non-zero if value ok, else zero if not found
	*/
	function postgre_Insert_ID($table,$pk,$oid=0){
		global $db;
		if(empty($oid)){
			 return 0;
		}else{
			$this->sql="SELECT $pk FROM $table WHERE oid=$oid";
			if($result=$db->Execute($this->sql)) {
				if($result->RecordCount()) {
					$buf=$result->FetchRow();
					 return $buf[$pk];
				} else { return 0; }
			} else { return 0; }
		}
	}
	/**
	* Returns the  value of the last inserted primary key of a row based on the column field name
	*
	* This function uses the  core table set by the child class
	* @param str Field name of the primary key
	* @param int OID value
	* @return int Non-zero if value ok, else zero if not found
	*/
	function LastInsertPK($pk='',$oid=0){
		global $dbtype;
		if(empty($pk)||empty($oid)){
			return $oid;
		}else{
			switch($dbtype){
				case 'mysql':
								case 'mysqlt': return $oid;
					break;
				case 'postgres': return $this->postgre_Insert_ID($this->coretable,$pk,$oid);
					break;
				case 'postgres7': return $this->postgre_Insert_ID($this->coretable,$pk,$oid);
					break;
				default: return $oid;
			}
		}
	}

	/**
	* Returns  a field concat string for sql query.
	*
	* This function resolves the problems of concatenating a field value with a string in different db types
	* @param str Field name
	* @param str String to concate
	* @return string
	*/
	function ConcatFieldString($fieldname,$str=''){
		global $dbtype;

		switch($dbtype){
            #added by VAS 07-04-2012
			case 'mysql' : return "CONCAT(IF($fieldname IS NULL, '', $fieldname),'$str')";
			case 'mysqlt': return "CONCAT($fieldname,'$str')";
				break;
			case 'postgres': return "$fieldname || '$str'";
				break;
			case 'postgres7':return "$fieldname || '$str'";
				break;
			default: return "$fieldname || '$str'";
		}
	}

	/**
	* Returns  a "history" field concat string for sql query.
	*
	* This function resolves the problems of concatenating the "history" field value with a string in different db types
	* @param str String
	* @return string
	*/
	function ConcatHistory($str=''){
		return $this->ConcatFieldString('history',$str);
	}

	/**
	* Returns  a "notes" field concat string for sql query.
	*
	* This function resolves the problems of concatenating the "note"  field value with a string in different db types
	* @param str String
	* @return string
	*/
	function ConcatNotes($str=''){
		return $this->ConcatFieldString('notes',$str);
	}

	/**
	* Returns  a field's string for sql query. Portions of the string is replaced by a string.
	*
	* This function resolves the problems of replacing a field value with a string in different db types
	* @param str Field name
	* @param str String to be replaced
	* @param str Replacement string
	* @return string
	*/
	function ReplaceFieldString($fieldname,$str1='',$str2=''){
		global $dbtype;
		switch($dbtype){
			case 'mysql':
			case 'mysqlt': return "REPLACE($fieldname,'$str1','$str2')";
				break;
				default: return "REPLACE($fieldname,'$str1','$str2')";
		}
	}
	/**
	* This will trim the string i.e. no whitespaces in the beginning and end of a string
	*                          AND only a single whitespace appears in between tokens/words
	*
	* @access public
	* @param string
	* @return trimmed string
	* burn added: August 25, 2006
	*/
	function stringTrim($str='') {
		/* Change a sequence of white spaces to a single white space */
		$new_str = preg_replace("/\s+/", " ", trim($str));
		return $new_str;
	}

	/**
	*  Helper function the MySQL FOUND_ROWS() function, only works properly when SQL_CALC_FOUND_ROWS
	* option is included in the most recent SQL statement executed.
	*
	* @return mixed returns FALSE on error, or the value of mysql's FOUND_ROWS function
	* @author Alvin Jed Quinones (2008-02-15)
	*/
	function FoundRows() {
		global $db;
		if (($this->result=$db->GetOne("SELECT FOUND_ROWS()")) !== false){
			return $this->result;
		} else {
			return FALSE;
		}
	}

	/**
	* Generates a history entry string
	*
	* Returns a formatted string which will be appended to the history field of the core table. Allows for
	* standardizing the format for the history entries
	*
	* @param mixed $transaction_type Type of transaction that will be indicated on the history entry.
	* Ideally one of the CRUD values should be used
	* @return string
	*/
	function makeHistory( $transaction_type='CREATE' ) {
		return sprintf("%s: %s [%s]\n", $transaction_type, date('Y-m-d h:i:sa'), $_SESSION['sess_temp_userid']);
	}


	/**
	* Generic multipls rows fetch logic
	*
	* Fetches records from the core table using key values to filter the target row/s
	*
	* @param Array $where
	* @return mixed
	*/
	function fetchAll($where='', $orderBy='', $offset=-1, $rows=-1, $calcFoundRows=true) {
		global $db;
		$sql = "SELECT `".implode("`,`",$this->coreFieldNames())."`\n".
			"FROM ".$this->coretable."\n";
		if ($where)
			$sql.= "WHERE {$where}\n";

		if ($orderBy) {
			$sql.= "ORDER BY {$orderBy}\n";
		}

		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$this->setSQL($sql);
		$this->result = $db->SelectLimit($this->getLastQuery(), $rows, $offset);
		if ($this->result === false) {
			if ($this->logger) {
				@$this->logger->error("SQL error... Query:".$this->getLastQuery()."\nError:".$db->ErrorMsg());
			}
		}
		return $this->result->GetRows();
	}



	/**
	* Generic row fetch logic
	*
	* Fetches a specific row from the core table using key values to specify the target row
	*
	* @param Array $keyValues
	* @return mixed
	*/
	function fetch($keyValues) {
		global $db;
		$whereCondition = '';
		$where = array();
		foreach ($this->getKeyColumns() as $field) {
			if (isset($keyValues[$field])) {
				$where[] = "`$field`=".$db->qstr($keyValues[$field]);
			}
		}
		if ($where) {
			$whereCondition = implode(" AND ", $where);
		}
		else {
			if ($this->logger) {
				@$this->logger->info("Cannot build WHERE condition... keyColumns=".print_r($this->getKeyColumns(), true).", keyColumns=".print_r($keyValues, true));
			}
			return false;
		}

		$sql = "SELECT `".implode("`,`",$this->coreFieldNames())."`\n".
			"FROM ".$this->coretable."\n";

		if ($whereCondition)
			$sql.= "WHERE {$whereCondition}";

		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$this->setSQL($sql);
		$this->result = $db->GetRow($this->getLastQuery());
		if ($this->result === false) {
			if ($this->logger) {
				@$this->logger->error("SQL error... Query:".$this->getLastQuery()."\nError:".$db->ErrorMsg());
			}
		}
		return $this->result;
	}


	/**
	* Delete a row from the core table data
	*
	* Provides generic deletion logic for classes that extend the Core class. Ideally, setKeyColumns
	* should be called first before invoking this method.
	*
	* @param Array $keyValues A key-value array map of the values for the key columns used to identify the row to be deleted
	* @param boolean $logical_delete If set to TRUE, the method looks for the is_deleted flag and sets it to 1 instead of physically deleting the table row.
	* @return boolean Returns TRUE/FALSE depending on the success of the operation/s
	*/
	function delete($keyValues, $logical_delete=false) {
		global $db;
		$whereCondition = '';
		$where = array();
		foreach ($this->getKeyColumns() as $field) {
			if (isset($keyValues[$field])) {
				$where[] = "`$field`=".$db->qstr($keyValues[$field]);
			}
		}
		if ($where) {
			$whereCondition = implode(" AND ", $where);
		}
		else {
			if ($this->logger) {
				@$this->logger->info("Cannot build WHERE condition... keyColumns=".print_r($this->getKeyColumns(), true).", keyColumns=".print_r($keyValues, true));
			}
			return false;
		}

		if ($logical_delete) {
			/**
			* @todo: check for the existence of the is_deleted field and decide whether to force a physical delete instead
			*/
			$history = $this->makeHistory('DELETE');
			$sql = "UPDATE ".$this->coretable." SET is_deleted=1, history=CONCAT(history,".$db->qstr($history).")\n";
		}
		else {
			$sql = "DELETE FROM ".$this->coretable."\n";
		}
		$sql.= "WHERE {$whereCondition}\n"."LIMIT 1";

		$this->setSQL($sql);
		$this->result = $db->Execute($this->getLastQuery());
		if ($this->result === false) {
			if ($this->logger) {
				@$this->logger->error("Delete operation failed... Query:".$this->getLastQuery()."\nError:".$db->ErrorMsg());
			}
		}
		return $this->result;
	}


	/**
	* Generic save routine for Insert/Update logic
	*
	* Provides basic save routine for classes that extend the Core class. Classes
	* that handle tables with more complex save logic should override this routine.
	*
	* @param mixed $id
	* @param mixed $data
	* @return boolean Returns FALSE on error, TRUE if successful
	*/
	function save($data, $force_insert=FALSE) {
		global $db;
		$update = false;

		if (!$force_insert) {
			$whereCondition = '';
			$where = array();
			foreach ($this->getKeyColumns() as $field) {
				if (isset($data[$field])) {
					$where[] = "`$field`=".$db->qstr($data[$field]);
				}
			}
			if ($where) {
				$whereCondition = implode(" AND ", $where);
				$this->sql = "SELECT EXISTS(SELECT 1 FROM $this->coretable WHERE {$whereCondition})";
				$entry = $db->GetOne($this->sql);
				$update = $entry !== '0';
			}
		}

		$data['modify_id']=$_SESSION['sess_temp_userid'];
		$data['modify_time']=date('YmdHis');

		if (!$update) {
			$data['create_id']=$_SESSION['sess_temp_userid'];
			$data['create_time']=date('YmdHis');
			$data['history']=$this->makeHistory('CREATE');
			$this->setDataArray($data);
			$saveok = $this->insertDataFromInternalArray();
		}
		else {
			$data['history']=$this->ConcatHistory($this->makeHistory('UPDATE'));
			$this->setDataArray($data);
			$this->setWhereCondition($whereCondition);
			$saveok = $this->updateDataFromInternalArray();
		}

		return $saveok;
	}

	/**
	* put your comment there...
	*
	*/
	function startTrans() {
		global $db;
		$db->StartTrans();
	}

	/**
	* put your comment there...
	*
	*/
	function failTrans() {
		global $db;
		$db->FailTrans();
	}

	/**
	* put your comment there...
	*
	*/
	function completeTrans() {
		global $db;
		$db->CompleteTrans();
	}

	/**
	* put your comment there...
	*
	* @param mixed $transaction_mode
	*/
	function setTransMode($transaction_mode) {
		global $db;
		$db->SetTransactionMode($transaction_mode);
	}

}

