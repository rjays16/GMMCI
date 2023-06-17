<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "../test_manager/roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;
$section = $_REQUEST['search_section'];
$service = $_REQUEST['search_service'];
$group = $_REQUEST['search_grp'];
$mode = $_REQUEST['mode'];
$view_mode = $_REQUEST['view-mode'];

$group_id = $_REQUEST["grp_id"];
$group_name = $_REQUEST["grp_name"];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'srv_form' => 'order_nr',
	'srv_name' => 'name',
	'srv_code' => 'service_code',
	'srv_stat_grp' => 'status_grp',
	'srv_stat_param' => 'status_param',
	'group_name' => 'group_name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'srv_form';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);

$data = array();
$phFilters = array();
if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}

if($view_mode=="Group_Form")
{
	$cond = " AND ISNULL(gp.group_id) \n";
	$join = "";
	$column = "";
	$open_mode = "new";
	$caption = "New Test Group";
}


$sql = "SELECT SQL_CALC_FOUND_ROWS rp.*, p.`sex`, rf.`name` AS form_name FROM seg_lab_result_params AS rp
			LEFT JOIN seg_lab_result_param_assignment AS rpa
			ON rp.`param_id` = rpa.`param_id`
			INNER JOIN seg_lab_result_forms AS rf
			ON rpa.`service_code` = rf.`form_id`
			LEFT JOIN care_person AS p
			ON p.`pid` = '$pid'
		WHERE rpa.`service_code` = '$search_service'";

if($sort_sql)
{
	$sql.=" ORDER BY {$sort_sql} ";
}
if($maxRows)
{
	$sql.=" LIMIT $offset, $maxRows";
}

$result = $db->Execute($sql);

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	$param_txt = "Assign Parameters";
	$edit_txt = "Edit Form";
	$delete_txt = "Delete Form";
	while ($row = $result->FetchRow()) {
		if($row['is_female'] && $row['is_male'])
			$gender = "Both";
		else if($row['is_female'] && !$row['is_male'])
			$gender = "Female";
		else
			$gender = "Male";

		if($row['sex'] == 'm')
			$sex = 'Male';
		else if($row['sex'] == 'f')
			$sex = 'Female';
		else
			$sex = '';

		if($row['is_numeric'])
			$type = "Numeric";
		else if($row['is_boolean'])
			$type = "Checkbox";
		else if($row['is_longtext'])
			$type = "Long Text";
		else
			$type = "Text";

		if($row['SI_lo_normal'] && $row['SI_hi_normal'])
			$si = $row['SI_lo_normal'] ." - ". $row['SI_hi_normal']." ".$row['SI_unit'];
		else
			$si = '';

		if($row['CU_lo_normal'] && $row['CU_hi_normal'])
			$cu = $row['CU_lo_normal'] ." - ". $row['CU_hi_normal']." ".$row['CU_unit'];
		else
			$cu = "";

		$checkbox = '<input type="checkbox" class="editbtnpol" id="selectfrom_'.$row['param_id'].'" \
					name="selectForm_'.$row['param_id'].'" 
					onclick="selectFromGroup(\''.$row['param_id'].'\', \''.$search_service.'\', \''.$row['name'].'\', 
							\''.$gender.'\', \''.$type.'\', \''.$si.'\', \''.$cu.'\', \''.$row['form_name'].'\',
							\''.$row['SI_unit'].'\', \''.$row['CU_unit'].'\', \''.$row['SI_lo_normal'].'\', \''.$row['SI_hi_normal'].'\'
							, \''.$row['CU_lo_normal'].'\', \''.$row['CU_hi_normal'].'\', \''.$sex.'\')">';

		$data[] = array(
			'param_id'=> $checkbox,
			'param_name'=>$row['name'],
			'param_gender'=>$gender,
			'param_type'=> $type,
			'SI_range'=> $si,
			'CU_range'=> $cu
		);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);