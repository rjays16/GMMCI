<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
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
$params = $_REQUEST['service_code'];
$has_group = $_REQUEST['group_id'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'param_name' => 'name',
	'order_nr' => 'order_nr',
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'param_name';

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

/*if($has_group)
{
	$sql = "SELECT SQL_CALC_FOUND_ROWS gn.group_id,gn.name AS `grpname`,p.param_id, p.name, p.is_numeric, p.is_boolean, p.is_longtext, ".
			"p.order_nr, p.SI_unit, p.SI_lo_normal, p.SI_hi_normal, p.CU_unit, p.CU_lo_normal, p.CU_hi_normal, p.is_male, p.is_female ".
			"FROM seg_lab_result_params AS p ".
			"LEFT JOIN seg_lab_result_groupparams AS gp ON p.service_code=gp.service_code ".
			"LEFT JOIN seg_lab_result_groupname AS gn ON gp.group_id=gn.group_id ".
			"WHERE (ISNULL(p.status) OR p.status <> 'deleted') AND (gp.status <> 'deleted')";
}else
{*/
	$sql = "SELECT SQL_CALC_FOUND_ROWS p.group_id,p.param_id, p.name, p.is_numeric, p.is_boolean, p.is_longtext, \n".
			"pa.order_nr, p.SI_unit, p.SI_lo_normal, p.SI_hi_normal, p.CU_unit, p.CU_lo_normal, p.CU_hi_normal, p.norm_type \n".
			"FROM seg_lab_result_params AS p \n".
			"LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
			"WHERE (ISNULL(p.status) OR p.status <> 'deleted')";
//}

if($params)
{
	$sql.=" AND pa.service_code=".$db->qstr($params);
}
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

	$data_type="Text";
	while ($row = $result->FetchRow()) {
		if($row["is_numeric"])
			$data_type="Numeric";
			else if($row["is_boolean"])
				$data_type="Boolean";
				else if($row["is_longtext"])
					$data_type="Long Text";
					else
						$data_type="Text";

		/*if($row["is_male"]==$row["is_female"])
			$gender="Both";
			else if($row["is_male"]<$row["is_female"])
				$gender="Female";
				else if($row["is_male"]>$row["is_female"])
					$gender="Male";*/

		$gender = ucfirst($row['norm_type']);

		$si_range="";
		$cu_range="";
		if($row["SI_lo_normal"] || $row["SI_hi_normal"] || $row["SI_unit"])
			$si_range=$row['SI_lo_normal'].'-'.$row['SI_hi_normal'].' '.$row['SI_unit'];
		if($row["CU_lo_normal"] || $row["CU_hi_normal"] || $row["CU_unit"])
			$cu_range=$row['CU_lo_normal'].'-'.$row['CU_hi_normal'].' '.$row['CU_unit'];

		$mode = 'edit';
		$caption = 'Update Test Parameter';
		$edit_txt = "Edit Parameter";
		$del_txt = "Delete Parameter";
		$data[]=array(
			'param_group_id'=>$row["group_id"],
			'param_name'=>$row["name"],
			'data_type'=>$data_type,
			'order_nr'=>$row["order_nr"],
			'gender'=>$gender,
			'si_range'=>$si_range,
			'cu_range'=>$cu_range,
			'options'=> '<img src="../../../images/cashier_edit.gif" name="edit" class="segSimulatedLink" onmouseover="tooltip(\''.$edit_txt.'\')" onmouseout="nd();" onclick="openParamsTray(\''.$mode.'\',\''.$caption.'\',\''.$params.'\',\''.$row['param_id'].'\',\''.$row['group_id'].'\',\''.$row['grpname'].'\');return false;"/>'.
			//'options'=> '<img src="../../../images/cashier_edit.gif" name="edit" class="link" onclick="return false;"/>'.
									'&nbsp;&nbsp;<img src="../../../images/cashier_delete_small.gif" name="delete" class="segSimulatedLink" onmouseover="tooltip(\''.$del_txt.'\')" onmouseout="nd();" onclick="deleteParam(\''.$row['param_id'].'\');return false;"/>'
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