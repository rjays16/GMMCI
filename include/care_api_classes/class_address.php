<?php
/**
* @package care_api
*/

/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');

/**
*  Address methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance.
* @package care_api
*/
class Address extends Core {
	/**
	* Database table for the country of citizenship data.
	* @var string
	* burn added: March 14, 2007
	*/
	var $tb_country='seg_country';
	/**
	* Country's id
	* @var int
	* burn added: March 14, 2007
	*/
	var $country_id;
	/**
	* Database table for the region address data.
	* @var string
	* burn added: February 20, 2007
	*/
	var $tb_regions='seg_regions';
	/**
	* Region's id
	* @var int
	* burn added: February 20, 2007
	*/
	var $region_id;
	/**
	* Database table for the province address data.
	* @var string
	* burn added: February 21, 2007
	*/
	var $tb_provinces='seg_provinces';
	/**
	* Province's id
	* @var int
	* burn added: February 21, 2007
	*/
	var $province_id;
	/**
	* Database table for the municipality/city address data.
	* @var string
	* burn added: February 21, 2007
	*/
	var $tb_municity='seg_municity';
	/**
	* Municipality/City's id
	* @var int
	* burn added: February 21, 2007
	*/
	var $municity_id;
	/**
	* Database table for the barangay address data.
	* @var string
	* burn added: February 21, 2007
	*/
	var $tb_barangays='seg_barangays';
	/**
	* Barangay's id
	* @var int
	* burn added: February 21, 2007
	*/
	var $barangay_id;
	/**
	* Field name of the primary key ('country_code', 'region_nr', 'prov_nr', 'mun_nr', OR 'brgy_nr')
	* @var string
	* burn added: February 21, 2007
	*/
	var $fld_primary_key;
	/**
	* Field name of the primary 'name'  ('country_name', 'region_name', 'prov_name', 'mun_name', OR 'brgy_name')
	* @var string
	* burn added: February 21, 2007
	*/
	var $fld_primary_name;
    var $fld_primary_code;
	/**
	* Table name where the foreign key is located
	*		$tb_regions for 'region_nr' in $tb_provinces, 
	*		$tb_provinces for 'prov_nr' in $tb_municity, 
	*		$tb_municity for 'mun_nr' in $tb_barangays, 
	* @var string
	* burn added: February 23, 2007
	*/
	var $tb_foreign;
	/**
	* Field name of the foreign key 
	*		'region_nr' for $tb_provinces, 
	*		'prov_nr' for $tb_municity, 
	*		'mun_nr' for $tb_barangays
	* @var string
	* burn added: February 23, 2007
	*/
	var $fld_foreign_key;
	/**
	* Field name of the foreign 'name' 
	*		'region_name' for $tb_provinces, 
	*		'prov_name' for $tb_municity, 
	*		'mun_name' for $tb_barangays
	* @var string
	* burn added: February 23, 2007
	*/
	var $fld_foreign_name;
	/**
	* Buffer for row returned by adodb's FetchRow() method
	* @var array
	* burn added: August 22, 2006
	*/
	var $row;
	/**
	* Fieldnames of "seg_country" table. Primary key is "counrty_code".
	* @var array
	* burn added: March 14, 2007
	*/		
	var $fld_country=array(
									'country_code',
									'country_name',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
	/**
	* Fieldnames of "seg_regions" table. Primary key is "region_nr".
	* @var array
	*/		
	var $fld_region=array(
									'region_nr',
                                    'code',
									'region_name',
									'region_desc',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
	/**
	* Fieldnames of "seg_provinces" table. Primary key is "prov_nr".
	* @var array
	*/		
	var $fld_province=array(
									'prov_nr',
                                    'code',
									'prov_name',
									'region_nr',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
	/**
	* Fieldnames of "seg_municity" table. Primary key is "mun_nr".
	* @var array
	*/		
	var $fld_municity=array(	
									'mun_nr',
                                    'code',
									'mun_name',
									'zipcode',
									'prov_nr',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
	/**
	* Fieldnames of "seg_barangays" table. Primary key is "brgy_nr".
	* @var array
	*/		
	var $fld_barangay=array(
									'brgy_nr',
                                    'code',
									'brgy_name',
									'mun_nr',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');

	/**
	* Constructor
	* @param string, Address type: 'region' (default), 'province', 'municity', or 'barangay'
	*/
	function Address($type='region'){
	
		if ($type=='old'){
			$this->_oldProvinces();
		}elseif ($type=='barangay'){
			$this->_useBarangays();
		}elseif ($type=='municity'){
			$this->_useMuniCity();
		}elseif ($type=='province'){
			$this->_useProvinces();
		}elseif ($type=='region'){
			$this->_useRegions();
		}else{
			$this->_useCountry();
		}
	}
	/**
	* Sets the core table to the table name and field names of "seg_country" table 
	* @access private
	*/
	function _useCountry(){
		$this->coretable=$this->tb_country;
		$this->ref_array=$this->fld_country;
		$this->fld_primary_key='country_code';
		$this->fld_primary_name='country_name';
	}
	/**
	* Sets the core table to the table name and field names of "seg_regions" table 
	* @access private
	*/
	function _useRegions(){
		$this->coretable=$this->tb_regions;
		$this->ref_array=$this->fld_region;
		$this->fld_primary_key='region_nr';
		$this->fld_primary_name='region_name';
        $this->fld_primary_code='code';
	}
	/**
	* Sets the core table to the table name and field names of "seg_provinces" table 
	* @access private
	*/
	function _useProvinces(){
		$this->coretable=$this->tb_provinces;
		$this->ref_array=$this->fld_province;
		$this->fld_primary_key='prov_nr';
		$this->fld_primary_name='prov_name';
        $this->fld_primary_code='code';
		$this->tb_foreign=$this->tb_regions;
		$this->fld_foreign_key='region_nr';
		$this->fld_foreign_name='region_name';
	}
	/**
	* Sets the core table to the table name and field names of "seg_municity" table 
	* @access private
	*/
	function _useMuniCity(){
		$this->coretable=$this->tb_municity;
		$this->ref_array=$this->fld_municity;
		$this->fld_primary_key='mun_nr';
		$this->fld_primary_name='mun_name';
        $this->fld_primary_code='code';
		$this->tb_foreign=$this->tb_provinces;
		$this->fld_foreign_key='prov_nr';
		$this->fld_foreign_name='prov_name';
		$this->tb_foreign2=$this->tb_regions;
		$this->fld_foreign2_key='region_nr';
		$this->fld_foreign2_name='region_name';
	}
	/**
	* Sets the core table to the table name and field names of "seg_barangay" table 
	* @access private
	*/
	function _useBarangays(){
		$this->coretable=$this->tb_barangays;
		$this->ref_array=$this->fld_barangay;
		$this->fld_primary_key='brgy_nr';
		$this->fld_primary_name='brgy_name';
        $this->fld_primary_code='code';
		$this->tb_foreign=$this->tb_municity;
		$this->fld_foreign_key='mun_nr';
		$this->fld_foreign_name='mun_name';
		$this->tb_foreign2=$this->tb_provinces;
		$this->fld_foreign2_key='prov_nr';
		$this->fld_foreign2_name='prov_name';
		$this->tb_foreign3=$this->tb_regions;
		$this->fld_foreign3_key='region_nr';
		$this->fld_foreign3_name='region_name';
	}

	function _oldProvinces(){
#		$this->coretable='sall_province';
#		$this->fld_primary_key='prov_code';
#		$this->fld_primary_name='province';
#		$this->coretable='sall_municipal';
#		$this->fld_primary_key='muni_code';
#		$this->fld_primary_name='municipal';
#		$this->coretable='sall_bgy';
#		$this->fld_primary_key='bgy_code';
#		$this->fld_primary_name='bgyname';
		$this->coretable='code_occupation';
		$this->fld_primary_key='code_occu';
		$this->fld_primary_name='occupation';
#		$this->tb_foreign=$this->tb_regions;
#		$this->fld_foreign_key='region_nr';
#		$this->fld_foreign_name='region_name';
	}

	function setBurnPID($pid=''){

		$zero='0';
		$i=0;
		$newPID=$pid;
#		for ($i=0; $i < 12-strlen($pid); $i++){
#			$newPID=$zero.$newPID;
#		}
		
		while($i<(12-strlen($pid))){
			$newPID=$zero.$newPID;
			$i++;
		}

		return $newPID;
	}


	function getPerson($cond=''){
		global $db;
		
      $this->sql="SELECT * FROM m_patient AS m, m_patient2 AS m2 WHERE m.file_no=m2.file_no LIMIT 1";
#      $this->sql="SELECT * FROM m_patient AS m, m_patient2 AS m2 
#						WHERE m.file_no=m2.file_no AND m.file_no=".$cond;
#	echo "getPerson : this->sql = '".$this->sql."' <br> \n";
#	exit();
		if($this->result=$db->Execute($this->sql)) {
			if($this->rec_count=$this->result->RecordCount()) {
				return $this->result;	 
			} else { return FALSE; }
		} else { return FALSE; }	
	}

	function savePerson($data){
		global $db;
		/*
      $this->sql=" INSERT INTO care_person
							(pid,date_reg,name_last,name_first,name_middle,name_maiden,
							sex,date_birth,place_birth,street_name,brgy_nr,citizenship,
							occupation,civil_status,religion,mother_name,father_name,spouse_name,
							guardian_name,status,history,create_id,create_time) 
						VALUES 
							( ?, ?, ?, ?, ?, ?,
							  ?, ?, ?, ?, ?, ?,
						     ?, ?, ?, ?, ?, ?,
							  ?, ?, ?, ?, ? )";
		*/
		#edited by VAN 05-20-08
		$this->sql=" INSERT INTO care_person
							(pid,date_reg,name_last,name_first,name_middle,name_maiden,
							sex,date_birth,place_birth,contact_no,street_name,brgy_nr,citizenship,
							occupation,civil_status,religion,
							mother_fname,mother_maidenname,mother_mname,mother_lname,
							father_fname,father_mname,father_lname,
							spouse_name,
							guardian_name,status,history,create_id,create_time) 
						VALUES 
							( ?, ?, ?, ?, ?, ?,
							  ?, ?, ?, ?, ?, ?, ?,
							  ?, ?, ?,
						      ?, ?, ?, ?,
							  ?, ?, ?,
							  ?,
							  ?, ?, ?, ?, ? )";					  

#	echo "savePerson : this->sql = '".$this->sql."' <br> \n";
#	exit();
		if($buf=$db->Execute($this->sql,$data)){	
			return TRUE;
		}else{ 
			return FALSE; 
		}
	}

	function saveAddressInfoFromArrayOld($data){
		global $db;
		
#      $this->sql="INSERT INTO $this->coretable (prov_nr, prov_name, region_nr, code, create_id, create_time) 
#						VALUES (?, ?, ?, ?, 'Bernard Klinch S. Clarito II', NOW())";
#      $this->sql="INSERT INTO $this->coretable (mun_nr, mun_name, prov_nr, code, create_id, create_time) 
#						VALUES (?, ?, ?, ?, 'Bernard Klinch S. Clarito II', NOW())";
#      $this->sql="INSERT INTO $this->coretable (brgy_nr, brgy_name, mun_nr, code, create_id, create_time) 
#						VALUES (?, ?, ?, ?, 'Bernard Klinch S. Clarito II', NOW())";
      $this->sql="INSERT INTO seg_occupation (occupation_nr, occupation_name, create_id, create_date) 
						VALUES (?, ?, 'Bernard Klinch S. Clarito II', NOW())";

#	echo "saveAddressInfoFromArrayOld : this->sql = '".$this->sql."' <br> \n";
#	exit();
		if($buf=$db->Execute($this->sql,$data)){	
			return TRUE;
		}else{ 
			return FALSE; 
		}
	}


	/**
	* Returns the  value of the last inserted primary key of a row based on the column field name
	*
	* This function uses the  core table set by the child class
	* @return int Non-zero if value ok, else zero if not found
	*/
	function LastInsertPKAddress(){
		global $db;

		$this->sql="SELECT max($this->fld_primary_key) AS nr FROM $this->coretable";
# echo "LastInsertPKAddress : this->sql = '".$this->sql."' <br> \n";
		if ($this->row=$db->Execute($this->sql)) {
			if ($this->row->RecordCount()){
				$temp = $this->row->FetchRow();
				return $temp['nr'];
			} else {
				return 0;
			}
		}else{
			return 0;
		}
	}
	/**
	* Gets all regions, provinces, municipalities/cities, OR barangays. 
	* Returns ADODB record object or boolean.
	* The returned adodb object contains rows of array with index keys 
	* corresponding to $fld_region, $fld_province, $fld_municity, OR $fld_barangay.
	* @access public
	* @return mixed
	*/
#	function getAllActiveCityTown(){
	function getAllAddress($cond='', $order_by=''){
#	function getAllAddress(){
		global $db;

		$where='WHERE 1';
		if (trim($cond)!="")	$where=" $cond ";
		$this->sql="SELECT *  FROM $this->coretable $where\n";
		if ($order_by)
			$this->sql.="ORDER BY $order_by";
		else
			$this->sql.="ORDER BY $this->fld_primary_name ASC ";
#		$this->sql="SELECT *  FROM $this->coretable ORDER BY $this->fld_primary_name ASC ";
#echo "getAllAddress : this->sql = '".$this->sql."' <br> \n";
		if($this->result=$db->Execute($this->sql)) {
			if($this->rec_count=$this->result->RecordCount()) {
				return $this->result;	 
			} else { return FALSE; }
		} else { return FALSE; }	
#		return $this->getAllDataObject();	# from class Core
	}
	/**
	* Same as getAllAddress but uses the limit feature of adodb 
	*		to limit the number or rows actually returned.
	* Returns ADODB record object or boolean.
	* @param int  Length of data, or number of rows to be returned, default 30 rows
	* @param int Start index offset, default 0 = start index
	* @param string Sort item, default = 'name'
	* @param string Sort direction, default = 'ASC'
	* @param boolean, if foreign fields are required to be retrieved
	* @return mixed
	*/
#	function getLimitActiveCityTown($len=30,$so=0,$oitem='name',$odir='ASC'){
	function getLimitAddress($len=30,$so=0,$oitem='',$odir='ASC',$foreign_fld=FALSE){
		/**
		* @global ADODB-db-link
		*/
		global $db;
		if (empty($oitem))	
			$oitem = $this->fld_primary_name;		
		$this->sql="SELECT * FROM $this->coretable "; // original code
        //edited by jasper 02/12/13
        //$fields = "t1." . $this->fld_primary_key . ",t1." . $this->fld_primary_name .
                  ",t1." . $this->fld_primary_code . ",t2." . $this->fld_foreign_name;
        //$this->sql="SELECT " . $fields ." FROM $this->coretable ";
		if ($foreign_fld){
				# Includes info of the foreign fields
			$this->sql.=" AS t1, $this->tb_foreign AS t2 ";
			$this->sql.=" WHERE t1.$this->fld_foreign_key=t2.$this->fld_foreign_key 
								AND t1.$this->fld_primary_key NOT IN ('0','') ";
		}
		$this->sql.=" ORDER BY $oitem $odir ";
#echo "getLimitAddress : this->sql = '".$this->sql."' <br> \n";
        //echo ($this->sql);
		if ($this->res['glact']=$db->SelectLimit($this->sql,$len,$so)) {
			if ($this->rec_count=$this->res['glact']->RecordCount()) {
				return $this->res['glact'];
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Counts all regions, provinces, municipalities/cities, OR barangays. 
	* Returns the count, else return zero.
	* @return int 
	*/
#	function countAllActiveCityTown(){
	function countAllAddress(){
		/**
		* @global ADODB-db-link
		*/
		global $db;

		$this->sql=" SELECT $this->fld_primary_key FROM $this->coretable 
						WHERE $this->fld_primary_key NOT IN ('0','')";
		if ($this->res['caact']=$db->Execute($this->sql)) {
			return $this->res['caact']->RecordCount();
		}else{
			return 0;
		}
	}

	/**
	* Checks if "region", "province", "municipality/city", OR "barangay"
	*		exists in the database based on "region_nr" OR "region_name",
	*		"prov_nr" OR "prov_name", "mun_nr" OR "mun_name", OR
	*		"brgy_nr" OR "brgy_name"
	* NOTE: Set first the $this->coretable and $this->ref_array
	*					$this->_useRegions();
	*					$this->_useProvinces();
	*					$this->_useMuniCity();
	*					$this->_useBarangays();
	*
	* @access public
	* @param int, the region, province, municipality/city, OR barangay id
	* @param string, name of the region, province, municipality/city, OR barangay
	* @param boolean, TRUE if 'update' mode
	* @param int, the region, province, OR municipality/city id 
	*			when inserting province, municipality/city, OR barangay, respectively.
	* @return boolean
	* burn added: February 21, 2007
	*/

    //added by jasper 01/31/13
    function CodeExists($code="", $update=FALSE, $nr=0) {
       global $db;
       //echo ($update . "/" . $nr ."/" . $code);
       if ($update) {
           $cond = $this->fld_primary_key . "<>'" . $nr . "' and code='" . $code . "'";
           $this->sql="select code from $this->coretable where " . $cond;
       }
       else {
           $cond = "code = " . $code;
           $this->sql="select code from $this->coretable where " . $cond;
       }
       //echo ($this->sql);
       if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
       // return $this->sql;
    }  //end of CodeExists

    function getCodebyNr($nr=0){
       global $db;
       $cond = $this->fld_foreign_key . "='" . $nr . "'";
       $this->sql = "select code from " . $this->tb_foreign . " where " . $cond;
       if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
                return $this->result;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    //added by jasper 01/31/13

#	function CityTownExists($name='',$country='') {
	function addressExists($nr=0, $name='',$update=FALSE,$foreign_nr=0) {
		global $db;

/*
echo " hello burn enter addressExists: $this->fld_primary_key = '".$nr."';";
echo " $this->fld_primary_name = '".$name."';";
echo " $this->fld_foreign_key = '".$foreign_nr."' <br>\n";
*/
		if ($update){
				# update mode
			$cond = "$this->fld_primary_key NOT IN (".$nr.") ";	
		} else {
			$cond = "$this->fld_primary_key = ".$nr;
		}
		if ($nr==0){
			$cond = "$this->fld_primary_name = '".$name."' ";
		} else {
				# update mode
			$cond.= " AND $this->fld_primary_name = '".$name."' ";
		}
		if ($foreign_nr!=0){
				# 	use in checking the following:
				#		[a] same province names & belong to same region (foreign_nr)
				#		[b] same municipality/city names & belong to same province (foreign_nr)
				#		[c] same barangay names & belong to same municipality/city (foreign_nr)
			$cond.= " AND $this->fld_foreign_key = ".$foreign_nr;	
		}
		$this->sql="SELECT $this->fld_primary_name FROM $this->coretable WHERE $cond";
# echo "addressExists : this->sql = '".$this->sql."' <br> \n";
		if($this->result=$db->Execute($this->sql)) {
			if($this->result->RecordCount()) {
#				echo " hello burn enter addressExists TRUE <br>\n";
				return TRUE;
			} else { 
#				echo " hello burn enter addressExists FALSE 1 <br>\n"; 
				return FALSE; 
			}
		} else { 
#			echo " hello burn enter addressExists FALSE 2 <br>\n"; 
			return FALSE; 
		}
	}

	/**
	* Checks if "zipcode" already	exists in the database 
	* @access public
	* @param int, the zip code of municipality/city
	* @param int, the municipality/city id
	* @return boolean
	* burn added: February 27, 2007
	*/
	function zipcodeExists($nr=0, $zipcode=0) {
		global $db;

#echo " hello burn enter zipcodeExists: zipcode = '".$zipcode."';";
		$cond = " zipcode = ".$zipcode;
		if ($nr!=0){
				# update mode
			$cond.= " AND $this->fld_primary_key NOT IN (".$nr.") ";	
		} 
		$this->sql="SELECT * FROM $this->coretable WHERE $cond ";
# echo "zipcodeExists : this->sql = '".$this->sql."' <br> \n";
		if($this->result=$db->Execute($this->sql)) {
			if($this->result->RecordCount()) {
#				echo " hello burn enter zipcodeExists TRUE <br>\n";
				return TRUE;
			} else { 
#				echo " hello burn enter zipcodeExists FALSE 1 <br>\n"; 
				return FALSE; 
			}
		} else { 
#			echo " hello burn enter zipcodeExists FALSE 2 <br>\n"; 
			return FALSE; 
		}
	}


	/**
	* Gets all record information of a region, province, municipality/city, OR barangay 
	*		based on the record "nr" key. 
	* Returns an ADODB record object or boolean.
	* NOTE: Set first the $this->coretable and $this->ref_array
	*					$this->_useRegions();
	*					$this->_useProvinces();
	*					$this->_useMuniCity();
	*					$this->_useBarangays();
	* @param int Record nr (region, province, municipality/city, OR barangay nr) key
	* @param boolean, if foreign fields are required to be retrieved
	* @return object
	*/
#	function getCityTownInfo($nr='') {
	function getAddressInfo($nr='',$foreign_fld=FALSE) {
		global $db;
		#commented by VAN 02-11-08
		#if(empty($nr)) return FALSE;
		$this->sql="SELECT * FROM $this->coretable "; 
		if ($foreign_fld){
				# Includes info of the foreign fields
			$this->sql.=" AS t, $this->tb_foreign AS t1 ";
			if (isset($this->tb_foreign2))
				$this->sql.=", $this->tb_foreign2 AS t2 ";
			if (isset($this->tb_foreign3))
				$this->sql.=", $this->tb_foreign3 AS t3 ";				
			$this->sql.=" WHERE t.$this->fld_primary_key=$nr ";
			$this->sql.=" AND t.$this->fld_foreign_key=t1.$this->fld_foreign_key ";
			if (isset($this->tb_foreign2))
				$this->sql.=" AND t1.$this->fld_foreign2_key=t2.$this->fld_foreign2_key ";
			if (isset($this->tb_foreign3))
				$this->sql.=" AND t2.$this->fld_foreign3_key=t3.$this->fld_foreign3_key ";
		}else{
			$this->sql.=" WHERE $this->fld_primary_key=$nr ";
		}
#echo "getAddressInfo : this->sql = '".$this->sql."' <br> \n";
#exit();
        //echo $this->sql;
		if($this->result=$db->Execute($this->sql)) {
			if($this->result->RecordCount()) {
				return $this->result;
			} else { 
				return FALSE; 
			}
		} else { 
			return FALSE; 
		}
   }

	function getAddressInfo2($nr='',$foreign_fld=FALSE) {
		global $db;
		
		if(empty($nr)) return FALSE;
		$this->sql="SELECT * FROM $this->coretable "; 
		if ($foreign_fld){
				# Includes info of the foreign fields
			$this->sql.=" AS t1, $this->tb_foreign AS t2 ";
			$this->sql.=" WHERE t1.$this->fld_primary_key=$nr ";
			$this->sql.=" AND t1.$this->fld_foreign_key=t2.$this->fld_foreign_key";
		}else{
			$this->sql.=" WHERE $this->fld_primary_key=$nr ";
		}
#echo "getAddressInfo : this->sql = '".$this->sql."' <br> \n";
#exit();
		if($this->result=$db->Execute($this->sql)) {
			if($this->result->RecordCount()) {
				return $this->result;
			} else { 
				return FALSE; 
			}
		} else { 
			return FALSE; 
		}
   }
	/**
	* Insert new region, province, municipality/city, OR barangay info in the database table. 
	* The data is contained in associative array and passed by reference.
	* The array keys must correspond to the field names contained 
	*		in $fld_region, $fld_province, $fld_municity, OR $fld_barangay.
	* NOTE: Set first the $this->coretable and $this->ref_array
	*					$this->_useRegions();
	*					$this->_useProvinces();
	*					$this->_useMuniCity();
	*					$this->_useBarangays();
	* @param array Data to save. By reference.
	* @return boolean
	*/
#	function saveCityTownInfoFromArray(&$data){
	function saveAddressInfoFromArray(&$data){
		global $HTTP_SESSION_VARS, $dbtype;
		
		$this->data_array=$data;
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		unset($this->data_array['create_time']);
		unset($this->data_array['modify_time']);

			#	The array keys ($this->buffer_array) must correspond to the field names contained 
			#		in $fld_region, $fld_province, $fld_municity, OR $fld_barangay.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {
			if(isset($this->data_array[$v])&&($this->data_array[$v]!='')) {
				$this->buffer_array[$v]=$this->data_array[$v];
			}
		}
/*
	echo "saveAddressInfoFromArray : this->data_array = '";
	print_r ($this->data_array);
	echo "' <br> \n";
	echo "saveAddressInfoFromArray : this->buffer_array = '";
	print_r ($this->buffer_array);
	echo "' <br> \n";
*/
		$x='';
		$v='';
		$index='';
		$values='';
		while(list($x,$v)=each($this->buffer_array)) {
			# use backquoting for mysql and no-quoting for other dbs 
			if ($dbtype=='mysql') $index.="`$x`,";
				else $index.="$x,";
				
			if (stristr($v,'null')) $values.='NULL,';
				elseif (is_numeric($v))	$values.="$v,";
				else  $values.="'$v',";
		}
		reset($this->data_array);
		reset($this->buffer_array);
		$index=substr_replace($index,'',(strlen($index))-1);
		$values=substr_replace($values,'',(strlen($values))-1);
      $this->sql="INSERT INTO $this->coretable ($index, modify_time, create_time) VALUES ($values, NOW(), NOW())";
#	echo "saveAddressInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#	exit();
		return $this->Transact();		
	}

	function saveAddressInfoFromArray2(&$data){
		global $HTTP_SESSION_VARS, $dbtype;
		
		$this->data_array=$data;
	echo "saveAddressInfoFromArray2 : data = '";
	print_r ($data);
	echo "' <br> \n";
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_time']='NULL';
		$this->data_array['modify_time']='NULL';

			#	The array keys ($this->buffer_array) must correspond to the field names contained 
			#		in $fld_region, $fld_province, $fld_municity, OR $fld_barangay.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {

			if(isset($this->data_array[$v])&&($this->data_array[$v]!='')) {
				$this->buffer_array[$v]=$this->data_array[$v];
#				if($v=='create_time' && $this->data['create_time']!='') 
				if($v=='create_time' && $this->data_array['create_time']!='') 
					$this->buffer_array[$v] = date('Y-m-d H:i:s');
				if($v=='modify_time' && $this->data_array['modify_time']!='') 
					$this->buffer_array[$v] = date('Y-m-d H:i:s');
			}
		}
	#echo "saveAddressInfoFromArray2 : this->data_array = '";
	#print_r ($this->data_array);
	#echo "' <br> \n";
	#echo "saveAddressInfoFromArray2 : this->buffer_array = '";
	#print_r ($this->buffer_array);
	#echo "' <br> \n";
		$x='';
		$v='';
		$index='';
		$values='';
		while(list($x,$v)=each($this->buffer_array)) {
			# use backquoting for mysql and no-quoting for other dbs 
			if ($dbtype=='mysql') $index.="`$x`,";
				else $index.="$x,";
				
			if (stristr($v,'null')) $values.='NULL,';
				elseif (is_numeric($v))	$values.="$v,";
				else  $values.="'$v',";
		}
		reset($this->data_array);
		reset($this->buffer_array);
		$index=substr_replace($index,'',(strlen($index))-1);
		$values=substr_replace($values,'',(strlen($values))-1);
      $this->sql="INSERT INTO $this->coretable ($index) VALUES ($values)";
	echo "saveAddressInfoFromArray2 : this->sql = '".$this->sql."' <br> \n";
	exit();
		return $this->Transact();		
	}

	/**
	* Updates the region, province, municipality/city, OR barangay's data. 
	* The data is contained in associative array and passed by reference.
	* The array keys must correspond to the field names contained 
	*		in $fld_region, $fld_province, $fld_municity, OR $fld_barangay.
	* NOTE: Set first the $this->coretable and $this->ref_array
	*					$this->_useRegions();
	*					$this->_useProvinces();
	*					$this->_useMuniCity();
	*					$this->_useBarangays();
	* Only the keys of data to be updated must be present in the passed array.
	* @param int region, province, municipality/city, OR barangay's record nr (primary key)
	* @param array Data passed as reference
	* @return boolean
	*/
#	function updateCityTownInfoFromArray($nr,&$data){
	function updateAddressInfoFromArray($nr,&$data){
		global $HTTP_SESSION_VARS, $dbtype;

		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		if(isset($this->data_array['$this->fld_primary_key'])) 
			unset($this->data_array['$this->fld_primary_key']);
		unset($this->data_array['create_id']);
		unset($this->data_array['create_time']);
		unset($this->data_array['modify_time']);
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
#		return $this->updateDataFromInternalArray($nr);

			#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {
			if(isset($this->data_array[$v])&&($this->data_array[$v]!='')) {
				$this->buffer_array[$v]=$this->data_array[$v];
			}
		}
#	echo "updateAddressInfoFromArray : this->data_array = '";
#	print_r ($this->data_array);
#	echo "' <br> \n";
#	echo "updateAddressInfoFromArray : this->buffer_array = '";
#	print_r ($this->buffer_array);
#	echo "' <br> \n";
		$elems='';
		while(list($x,$v)=each($this->buffer_array)) {
			# use backquoting for mysql and no-quoting for other dbs. 
			if ($dbtype=='mysql') $elems.="`$x`=";
				else $elems.="$x=";
			
			if(stristr($v,$concatfx)||stristr($v,'null')) $elems.=" $v,";
				else $elems.="'$v',";
		}
		# Bug fix. Reset array.
		reset($this->data_array);
		reset($this->buffer_array);
		$elems=substr_replace($elems,'',(strlen($elems))-1);
      $this->sql="UPDATE $this->coretable SET $elems, modify_time=NOW() WHERE $this->fld_primary_key=$nr";
#	echo "updateAddressInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#	exit();
		return $this->Transact();
	}

	function updateAddressInfoFromArray2($nr,&$data){
		global $HTTP_SESSION_VARS, $dbtype;

		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		if(isset($this->data_array['$this->fld_primary_key'])) 
			unset($this->data_array['$this->fld_primary_key']);
		$this->data_array['create_id']='NULL';
		$this->data_array['create_time']='NULL';
		$this->data_array['modify_time']='NULL';
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
#		return $this->updateDataFromInternalArray($nr);

			#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {
			if(isset($this->data_array[$v])&&($this->data_array[$v]!='')) {
				$this->buffer_array[$v]=$this->data_array[$v];
#				if($v=='create_time' && $this->data['create_time']!='') 
				if($v=='create_time' && $this->data_array['create_time']!='') 
					$this->buffer_array[$v] = date('Y-m-d H:i:s');
				if($v=='modify_time' && $this->data_array['modify_time']!='') 
					$this->buffer_array[$v] = date('Y-m-d H:i:s');
			}
		}
#	echo "updateAddressInfoFromArray : this->data_array = '";
#	print_r ($this->data_array);
#	echo "' <br> \n";
#	echo "updateAddressInfoFromArray : this->buffer_array = '";
#	print_r ($this->buffer_array);
#	echo "' <br> \n";
		$elems='';
		while(list($x,$v)=each($this->buffer_array)) {

			# use backquoting for mysql and no-quoting for other dbs. 
			if ($dbtype=='mysql') $elems.="`$x`=";
				else $elems.="$x=";
			
			if(stristr($v,$concatfx)||stristr($v,'null')) $elems.=" $v,";
				else $elems.="'$v',";
		}
		# Bug fix. Reset array.
		reset($this->data_array);
		reset($this->buffer_array);
		$elems=substr_replace($elems,'',(strlen($elems))-1);
      $this->sql="UPDATE $this->coretable SET $elems WHERE $this->fld_primary_key=$nr";
#	echo "updateAddressInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#	exit();
		return $this->Transact();
	}

	/**
	* Searches for the region, province, municipality/city, OR barangay based on a string keyword.
	* Returns an ADODB record object of search results or boolean.
	* NOTE: Set first the $this->coretable and $this->ref_array
	*					$this->_useRegions();
	*					$this->_useProvinces();
	*					$this->_useMuniCity();
	*					$this->_useBarangays();
	* @param string Search keyword
	* @return mixed
	*/
#	function searchActiveCityTown($key){
	function searchAddress($key){
		global $db, $sql_LIKE;

		if(empty($key)) return FALSE;
		$select="SELECT * FROM $this->coretable ";
		$this->sql="$select WHERE $this->fld_primary_name $sql_LIKE '$key%' ";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return $this->result;
			}else{	
				$this->sql="$select WHERE $this->fld_primary_name $sql_LIKE '%$key' ";
				if($this->result=$db->Execute($this->sql)){
					if($this->result->RecordCount()){
						return $this->result;
					}else{
						$this->sql="$select WHERE $this->fld_primary_name $sql_LIKE '%$key%' ";
						if($this->result=$db->Execute($this->sql)){
							if($this->result->RecordCount()){
								return $this->result;
							}else{return FALSE;}
						}else{return FALSE;}
					}
				}else{return FALSE;}
			}
		} else { return FALSE; }
	}

	/**
	* Limited return search for the region, province, municipality/city, OR barangay. 
	* Returns an ADODB record object of search results or boolean.
	* NOTE: Set first the $this->coretable and $this->ref_array
	*					$this->_useRegions();
	*					$this->_useProvinces();
	*					$this->_useMuniCity();
	*					$this->_useBarangays();
	* @param string Search keyword
	* @param int Maximum number of rows returned, default=30
	* @param int Start index offset, defaut = 0 (start)
	* @param string  Sort order item, default= $fld_primary_name={'region_nr','prov_nr', 'mun_nr', 'brgy_nr'}
	* @param string  Sort direction, default = ASC
	* @param boolean, if foreign fields are required to be retrieved
	* @return mixed
	*/
#	function searchLimitActiveCityTown($key,$len=30,$so=0,$oitem='name',$odir='ASC'){
	function searchLimitAddress($key,$len=30,$so=0,$oitem='',$odir='ASC',$foreign_fld=FALSE){
		/**
		* @global ADODB-db-link
		*/
		global $db, $sql_LIKE;
        //$db->debug=true;
		if (empty($key)) return FALSE;
		if (empty($oitem)) $oitem='$this->fld_primary_name';
		$select = " SELECT * FROM $this->coretable ";
#		$where = " $this->fld_primary_name $sql_LIKE '$key%' ";
		$append = " ORDER BY $oitem $odir ";
		if ($foreign_fld){
				# Includes info of the foreign fields
			$select.=" AS t1, $this->tb_foreign AS t2 ";
			$where=" $this->fld_primary_name $sql_LIKE '$key%' ";
			$where.=" AND t1.$this->fld_foreign_key=t2.$this->fld_foreign_key ";
		}else{
			# The search is for region. It could be region's short name OR region's name
			$where=" $this->fld_primary_name $sql_LIKE '$key%' ";
			$where.=" OR region_desc $sql_LIKE '$key%' ";
		}
		$this->sql="$select WHERE $where $append";
		if($this->res['slact']=$db->SelectLimit($this->sql,$len,$so)){
			if($this->rec_count=$this->res['slact']->RecordCount()){
# echo "searchLimitAddress 1: this->sql = '".$this->sql."'<br>\n";
				return $this->res['slact'];
		    }else{
				if ($foreign_fld){
						# Includes info of the foreign fields
					$where=" $this->fld_primary_name $sql_LIKE '%$key' ";
					$where.=" AND t1.$this->fld_foreign_key=t2.$this->fld_foreign_key ";
				}else{
					# The search is for region. It could be region's short name OR region's name
					$where=" $this->fld_primary_name $sql_LIKE '%$key' ";
					$where.=" OR region_desc $sql_LIKE '%$key' ";
				}
				$this->sql="$select WHERE $where $append";
				if($this->res['slact']=$db->SelectLimit($this->sql,$len,$so)){
					if($this->rec_count=$this->res['slact']->RecordCount()){
# echo "searchLimitAddress 2: this->sql = '".$this->sql."'<br>\n";
						return $this->res['slact'];
					}else{
						if ($foreign_fld){
								# Includes info of the foreign fields
							$where=" $this->fld_primary_name $sql_LIKE '%$key%' ";
							$where.=" AND t1.$this->fld_foreign_key=t2.$this->fld_foreign_key ";
						}else{
							# The search is for region. It could be region's short name OR region's name
							$where=" $this->fld_primary_name $sql_LIKE '%$key%' ";
							$where.=" OR region_desc $sql_LIKE '%$key%' ";
						}
						$this->sql="$select WHERE $where $append";
						if($this->res['slact']=$db->SelectLimit($this->sql,$len,$so)){
							if($this->rec_count=$this->res['slact']->RecordCount()){
# echo "searchLimitAddress 3: this->sql = '".$this->sql."'<br>\n";
								return $this->res['slact'];
							}else{return FALSE;}
						}else{return FALSE;}
					}
				}else{return FALSE;}
			}
	   } else { return FALSE; }
	}

	/**
	* Searches for the region, province, municipality/city, OR barangay
	*		but returns only the total count of the resulting rows.
	* Returns the count value, else returns zero.
	* @param string Search keyword
	* @return int
	*/
#	function searchCountActiveCityTown($key){
	function searchCountAddress($key){
		/**
		* @global ADODB-db-link
		*/
		global $db, $sql_LIKE;

		if(empty($key)) return FALSE;
		$select="SELECT $this->fld_primary_key FROM $this->coretable ";
		$this->sql="$select WHERE $this->fld_primary_name $sql_LIKE '$key%' ";
#echo "searchCountAddress: this->sql = '".$this->sql."'<br>\n";
		if($this->res['scact']=$db->Execute($this->sql)){
#echo "searchCountAddress: this->res['scact']->RecordCount() = '".$this->res['scact']->RecordCount()."'<br>\n";
			if($this->rec_count=$this->res['scact']->RecordCount()){
				return $this->rec_count;
			}else{
				$this->sql="$select WHERE $this->fld_primary_name $sql_LIKE '%$key' ";
				if($this->res['scact']=$db->Execute($this->sql)){
					if($this->rec_count=$this->res['scact']->RecordCount()){
						return $this->rec_count;
					}else{
						$this->sql="$select WHERE $this->fld_primary_name $sql_LIKE '%$key%' ";						
						if($this->res['scact']=$db->Execute($this->sql)){
							if($this->rec_count=$this->res['scact']->RecordCount()){
								return $this->rec_count;
							}else{return 0;}
						}else{return 0;}
					}
				}else{return 0;}
			}
		}else{return 0;}
	}

/*
			FUNCTIONS BELOW ARE THE ORIGINAL CODES
*/

	/**
	* Database table for the citytown address data.
	* @var string
	*/
	var $tb_citytown='care_address_citytown';
	/**
	* City/town's id
	* @var string
	* burn added: August 22, 2006
	*/
	var $cityTown_id;
	/**
	* Fieldnames of care_address_citytown table. Primary key is "nr".
	* @var array
	*/		
	var $fld_citytown=array(
									'nr',
									'unece_modifier',
									'unece_locode',
									'name',
									'iso_country_id',
									'unece_locode_type',
									'unece_coordinates',
									'info_url',
									'use_frequency',
									'status',
									'history',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
									
	/**
	* Constructor
	* @param int Primary key of address record.
	*/
/*
	function Address($nr){   # burn comment: August 25, 2006
//	function Address($nr='',$name=''){   # burn added: August 25, 2006
	    $this->cityTown_id=$nr;   # burn added: August 23, 2006
//	    $this->cityTown_name=$name;   # burn added: August 25, 2006
		$this->coretable=$this->tb_citytown;
		$this->ref_array=$this->fld_citytown;
	}
*/
	/**
	* Sets the core table to the table name and field names of care_address_citytown 
	* @access private
	*/
	function _useCityTown(){
		$this->coretable=$this->tb_citytown;
		$this->ref_array=$this->fld_citytown;
	}
	/**
	* Gets all active city town addresses. Returns ADODB record object or boolean.
	* The returned adodb object contains rows of array with index keys 
	* corresponding to $fld_citytown.
	* @access public
	* @return mixed
	*/
	function getAllActiveCityTown(){
		/**
		* @global ADODB-db-link
		*/
	    global $db;
		$this->sql="SELECT * FROM $this->tb_citytown WHERE status NOT IN ('inactive','hidden','deleted','void')";
	    if ($this->res['gaact']=$db->Execute($this->sql)) {
		    if ($this->rec_count=$this->res['gaact']->RecordCount()) {
		        return $this->res['gaact'];
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Same as getAllActiveCityTown but uses the limit feature of adodb to limit the number or rows actually returned.
	* Returns ADODB record object or boolean.
	* @param int  Length of data, or number of rows to be returned, default 30 rows
	* @param int Start index offset, default 0 = start index
	* @param string Sort item, default = 'name'
	* @param string Sort direction, default = 'ASC'
	* @return mixed
	*/
	function getLimitActiveCityTown($len=30,$so=0,$oitem='name',$odir='ASC'){
		/**
		* @global ADODB-db-link
		*/
	    global $db;
		$this->sql="SELECT * FROM $this->tb_citytown WHERE status NOT IN ('inactive','hidden','deleted','void') ORDER BY $oitem $odir";
	    if ($this->res['glact']=$db->SelectLimit($this->sql,$len,$so)) {
		    if ($this->rec_count=$this->res['glact']->RecordCount()) {
		        return $this->res['glact'];
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Counts all active city town addresses. Rreturns the count, else return zero.
	* @return int 
	*/
	function countAllActiveCityTown(){
		/**
		* @global ADODB-db-link
		*/
	    global $db;
		$this->sql="SELECT nr FROM $this->tb_citytown WHERE status NOT IN ($this->dead_stat)";
	    if ($this->res['caact']=$db->Execute($this->sql)) {
		    return $this->res['caact']->RecordCount();
		}else{return 0;}
	}
	/**
	* Checks if city or town exists in the database based on name ONLY.
	*
	* @access public
	* @param string Name of the city or town
	* @return boolean
	* burn added: August 25, 2006
	*/
	function CityTownNameExists($cityTown_name='') {
		global $db;
	    //if(!$this->_internResolveFirmID($firm_id)) return FALSE;
#		echo " <br> hello burn enter CityTownNameExists = ".$cityTown_name;
//	    if($this->result=$db->Execute("SELECT name FROM $this->tb_citytown WHERE name $sql_LIKE '$cityTown_name'")) {
		if($this->result=$db->Execute("SELECT name FROM $this->tb_citytown WHERE name = '$cityTown_name'")) {
			if($this->result->RecordCount()) {
#				echo " <br> hello burn enter CityTownNameExists TRUE";
				return TRUE;
			} else { 
#				echo " <br> hello burn enter CityTownNameExists FALSE 1"; 
				return FALSE; 
			}
	   } else { 
#			echo " <br> hello burn enter CityTownNameExists FALSE 2"; 
			return FALSE; 
		}
   }
    /**
	* Checks if city or town exists based on name and ISO country code keys.
	* @param string Name of the city or town
	* @param string Country ISO code
	* @return boolean
	*/
	function CityTownExists($name='',$country='') {

		/**
		* @global ADODB-db-link
		*/
	    global $db, $sql_LIKE;
	    if(empty($name)) return FALSE;
		$this->sql="SELECT nr FROM $this->tb_citytown WHERE name $sql_LIKE '$name' AND iso_country_id $sql_LIKE '$country'";
	    if($buf=$db->Execute($this->sql)) {
	        if($buf->RecordCount()) {
			    return TRUE;
		    } else { return FALSE; }
	   } else { return FALSE; }
   }
	/**
	* Gets all record information of a city or town based on the record "nr" key. 
	* Returns an ADODB record object or boolean.
	* @param int Record nr (citytown nr) key
	* @return object
	*/
	function getCityTownInfo($nr='') {
		/**
		* @global ADODB-db-link
		*/
	    global $db;
	    //$db->debug=true;
	    if(empty($nr)) return FALSE;
		$this->sql="SELECT * FROM $this->tb_citytown WHERE nr=$nr";
	    if($this->res['gcti']=$db->Execute($this->sql)) {
	        if($this->res['gcti']->RecordCount()) {
			    return $this->res['gcti'];
		    } else { return FALSE; }
	   } else { return FALSE; }
   }
	/**
	* Insert new city/town info in the database table. The data is contained in associative array and passed by reference.
	* The array keys must correspond to the field names contained in $fld_citytown.
	* @param array Data to save. By reference.
	* @return boolean
	*/
	function saveCityTownInfoFromArray(&$data){
		global $HTTP_SESSION_VARS;
		$this->_useCityTown();
		$this->data_array=$data;
		$this->data_array['status']='normal';
		$this->data_array['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_time']='NULL';
		return $this->insertDataFromInternalArray();
	}
	/**
	* Updates the city/town's data. The data is contained in associative array and passed by reference.
	* The array keys must correspond to the field names contained in $fld_citytown. 
	* Only the keys of data to be updated must be present in the passed array.
	* @param int City/town's record nr (primary key)
	* @param array Data passed as reference
	* @return boolean
	*/
	function updateCityTownInfoFromArray($nr,&$data){
		global $HTTP_SESSION_VARS;
		$this->_useCityTown();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		if(isset($this->data_array['nr'])) unset($this->data_array['nr']);
		if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
		// clear the where condition
		$this->where='';
		$this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		return $this->updateDataFromInternalArray($nr);
	}
	/**
	* Searches for the active city or town based on a string keyword.
	* Returns an ADODB record object of search results or boolean.
	* @param string Search keyword
	* @return mixed
	*/
  	function searchActiveCityTown($key){
		global $db, $sql_LIKE;
		if(empty($key)) return FALSE;
		$select="SELECT *  FROM $this->tb_citytown ";
		$append=" AND status NOT IN ('inactive','deleted','closed','hidden','void')";
		$this->sql="$select WHERE ( name $sql_LIKE '$key%' OR unece_locode $sql_LIKE '$key%' ) $append";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return $this->result;
		    }else{	
				$this->sql="$select WHERE ( name $sql_LIKE '%$key' OR unece_locode $sql_LIKE '%$key' ) $append";
				if($this->result=$db->Execute($this->sql)){
					if($this->result->RecordCount()){
						return $this->result;
					}else{
						$this->sql="$select WHERE ( name $sql_LIKE '%$key%' OR unece_locode $sql_LIKE '%$key%' ) $append";
						if($this->result=$db->Execute($this->sql)){
							if($this->result->RecordCount()){
								return $this->result;
							}else{return FALSE;}
						}else{return FALSE;}
					}
				}else{return FALSE;}
			}
	   } else { return FALSE; }
   	}
	/**
	* Limited return search for the active city or town. 
	* Returns an ADODB record object of search results or boolean.
	* @param string Search keyword
	* @param int Maximum number of rows returned, default=30
	* @param int Start index offset, defaut = 0 (start)
	* @param string  Sort order item, default= name
	* @param string  Sort direction, default = ASC
	* @return mixed
	*/
  	function searchLimitActiveCityTown($key,$len=30,$so=0,$oitem='name',$odir='ASC'){
		/**
		* @global ADODB-db-link
		*/
		global $db, $sql_LIKE;
        //$db->debug=true;
		if(empty($key)) return FALSE;
		$select="SELECT *  FROM $this->tb_citytown ";
		$append=" AND status NOT IN ($this->dead_stat) ORDER BY $oitem $odir";
		$this->sql="$select WHERE ( name $sql_LIKE '$key%' OR unece_locode $sql_LIKE '$key%' ) $append";
echo "this->sql = '".$this->sql."'<br> \n";
		if($this->res['slact']=$db->SelectLimit($this->sql,$len,$so)){
			if($this->rec_count=$this->res['slact']->RecordCount()){
				return $this->res['slact'];
		    }else{
				$this->sql="$select WHERE ( name $sql_LIKE '%$key' OR unece_locode $len '%$key' ) $append";
				if($this->res['slact']=$db->SelectLimit($this->sql,$len,$so)){
					if($this->rec_count=$this->res['slact']->RecordCount()){
						return $this->res['slact'];
					}else{
						$this->sql="$select WHERE ( name $sql_LIKE '%$key%' OR unece_locode $sql_LIKE '%$key%' ) $append";
						if($this->res['slact']=$db->SelectLimit($this->sql,$len,$so)){
							if($this->rec_count=$this->res['slact']->RecordCount()){
								return $this->res['slact'];
							}else{return FALSE;}
						}else{return FALSE;}
					}
				}else{return FALSE;}
			}
	   } else { return FALSE; }
   	}
	/**
	* Searches for the active city or town but returns only the total count of the resulting rows.
	* Returns the count value, else returns zero.
	* @param string Search keyword
	* @return int
	*/
   	function searchCountActiveCityTown($key){
		/**
		* @global ADODB-db-link
		*/
		global $db, $sql_LIKE;
		if(empty($key)) return FALSE;
		$select="SELECT nr FROM $this->tb_citytown ";
		$append=" AND status NOT IN ($this->dead_stat)";
		$this->sql="$select WHERE ( name $sql_LIKE '$key%' OR unece_locode $sql_LIKE '$key%' ) $append";
		if($this->res['scact']=$db->Execute($this->sql)){
			if($this->rec_count=$this->res['scact']->RecordCount()){
				return $this->rec_count;
			}else{	
				$this->sql="$select WHERE ( name $sql_LIKE '%$key' OR unece_locode $sql_LIKE '%$key' ) $append";
				if($this->res['scact']=$db->Execute($this->sql)){
					if($this->rec_count=$this->res['scact']->RecordCount()){
						return $this->rec_count;
					}else{
						$this->sql="$select WHERE ( name $sql_LIKE '%$key%' OR unece_locode $sql_LIKE '%$key%' ) $append";
						if($this->res['scact']=$db->Execute($this->sql)){
							if($this->rec_count=$this->res['scact']->RecordCount()){
								return $this->rec_count;
							}else{return 0;}
						}else{return 0;}
					}
				}else{return 0;}
			}
		}else{return 0;}
	}

	/**
	* Resolves the city/town's id.
	* @access private
	* @param string city/town id
	* @return boolean
	* burn added: August 22, 2006
	*/
	function _internResolveCityTownID($cityTown_id='') {
	    if (empty($cityTown_id)) {
		    if(empty($this->cityTown_id)) {
			    return FALSE;
			} else { return TRUE; }
		} else {
		     $this->cityTown_id=$cityTown_id;
			return TRUE;
		}
	}

	/**
	* Gets the usage frequency of a city/town based on its primary key "nr".
	*
	* @access public
	* @param string city/town id
	* @return mixed integer or boolean
	* burn added: August 22, 2006
	*/

    function getUseFrequency($cityTown_id='') {
	
        global $db;
        
	    if(!$this->_internResolveCityTownID($cityTown_id)) return FALSE;
	    if($this->result=$db->Execute("SELECT use_frequency FROM $this->tb_citytown WHERE nr=$this->cityTown_id")) {
	        if($this->result->RecordCount()) {
		        $this->row=$this->result->FetchRow();
			    return $this->row['use_frequency'];
		    } else { return FALSE; }
	   } else { return FALSE; }
	   
    }

    /**
	* Increases usage frequency of a city/town.
	*
	* @access public
	* @param string city/town id
	* @param int Increase step
	* @return boolean
	* burn added: August 30, 2006
	*/
	function updateUseFrequency($cityTown_id='',$step=1) {

		echo " <br> hello burn enter City/Town updateUseFrequency cityTown_id= ".$cityTown_id;
		if(!$this->_internResolveCityTownID($cityTown_id)) return FALSE;

		$this->sql="UPDATE $this->tb_citytown SET use_frequency=(use_frequency + 1) WHERE nr=$this->cityTown_id";
		if($this->result=$this->Transact($this->sql)) {
           echo " <br> hello burn enter City/Town updateUseFrequency TRUE 2";
           return TRUE;
		} else { echo " <br> hello burn enter City/Town updateUseFrequency FALSE 2"; return FALSE; }
   }
	
	#added by VAN 05-24-08
	function getBarangay(){
	    global $db;
		 $this->sql="SELECT * FROM seg_barangays ORDER BY brgy_name ASC";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result;
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}
	
	function getMunicityByBrgy($brgy_nr){
	    global $db;
		 $this->sql="SELECT * FROM seg_barangays WHERE brgy_nr = '$brgy_nr'";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}
	
	function getProvinceByBrgy($mun_nr){
	    global $db;
		 $this->sql="SELECT * FROM seg_municity WHERE mun_nr = '$mun_nr'";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}
	
	function getProvinceInfo($prov_nr){
	    global $db;
		 $this->sql="SELECT * FROM seg_provinces WHERE prov_nr = '$prov_nr'";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}
    
    function getCountryInfo($country_code){
        global $db;
         $this->sql="SELECT * FROM seg_country WHERE country_code = '$country_code'";
                         
        if ($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                return $this->result->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
	
	function getCountry(){
        global $db;
         $this->sql="SELECT * FROM seg_country ORDER BY country_name ASC";
                         
        if ($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                return $this->result;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
	
	function getMunicipal(){
	    global $db;
		 $this->sql="SELECT * FROM seg_municity ORDER BY mun_name ASC";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result;
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}
	
	function getProvince(){
	    global $db;
		$this->sql="SELECT * FROM seg_provinces ORDER BY prov_name ASC";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result;
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}
	
	//added by maimai 12-17-2014
	function getLatestPk(){
		global $db;
		
		$this->sql = "SELECT MAX(".$this->fld_primary_key.")+1 FROM ".$this->coretable;

		return $db->GetOne($this->sql);
	}

	//end added by maimai
	function catchAllAddresses($keyword="", $mode="", $offset=0, $rowcount=10, $sort_order="") {
		global $db;
		
		$keyword = substr($db->qstr($keyword),1,-1);
		$calc_rows = "";
		$sql_arr = array();
		if ($mode=="B" || !$mode) {
			$sql = "SELECT";
			if (!$calc_rows) { 
				$calc_rows= TRUE;
				$sql.=" SQL_CALC_FOUND_ROWS";
			}
			$sql .= " b.brgy_nr AS `code`, b.brgy_name AS `name`, 
CONCAT(IFNULL(b.brgy_name,'<No name>'), ', ',
  IFNULL(m.mun_name,'<No name>'), ', ',
  IFNULL(p.prov_name,'<No name>'), ' ',
  IFNULL(m.zipcode,'<No zipcode>')) AS `full`,'B' AS `location`
FROM seg_barangays AS b LEFT JOIN seg_municity AS m ON m.mun_nr=b.mun_nr LEFT JOIN seg_provinces AS p ON p.prov_nr=m.prov_nr
WHERE b.brgy_name REGEXP '[[:<:]]$keyword'";
			$sql_arr[] = $sql;
		}
		
		if ($mode=="M" || $mode=="Z" || !$mode) {
			$sql = "SELECT";
			$mode2 = $mode ? $mode : "M";
			if (!$calc_rows) { 
				$calc_rows= TRUE;
				$sql.=" SQL_CALC_FOUND_ROWS";
			}
			$sql .= " m.mun_nr AS `code`, m.mun_name AS `name`, 
CONCAT(IFNULL(m.mun_name,'<No name>'), ', ',
  IFNULL(p.prov_name,'<No name>'), ' ',
  IFNULL(m.zipcode,'<No zipcode>')) AS `full`,'M' AS `location`
FROM seg_municity AS m LEFT JOIN seg_provinces AS p ON p.prov_nr=m.prov_nr
WHERE m.mun_name REGEXP '[[:<:]]$keyword' OR m.zipcode='$keyword'";
			$sql_arr[] = $sql;
		}
		
		if ($mode=="P" || !$mode) {
			$sql = "SELECT";
			if (!$calc_rows) { 
				$calc_rows= TRUE;
				$sql.=" SQL_CALC_FOUND_ROWS";
			}
			$sql .= " p.prov_nr AS `code`, p.prov_name AS `name`, 
CONCAT( IFNULL(p.prov_name,'<No name>'), ', ',
  IFNULL(r.region_name,'<No region>')
) AS `full`,'P' AS `location`
FROM seg_provinces AS p LEFT JOIN seg_regions AS r ON p.region_nr=r.region_nr
WHERE p.prov_name REGEXP '[[:<:]]$keyword'";
			$sql_arr[] = $sql;
		}
		
		$this->sql = "(" . 
			implode(") UNION (", $sql_arr).
		")\n";
		
		if ($sort_order) $this->sql .= "ORDER BY $sort_order\n";
		if ($rowcount) $this->sql .= "LIMIT $offset, $rowcount\n";
		
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
   #-----------------------------
} # end of class Address
?>
