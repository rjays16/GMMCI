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
$section = $_REQUEST['search_section'];
$service = $_REQUEST['search_service'];
$group = $_REQUEST['search_grp'];
$mode = $_REQUEST['mode'];
$view_mode = $_REQUEST['view-mode'];

$group_id = $_REQUEST["grp_id"];
$group_name = $_REQUEST["grp_name"];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'srv_name' => 'name',
	'srv_code' => 'service_code',
	'srv_stat_grp' => 'status_grp',
	'srv_stat_param' => 'status_param',
	'group_name' => 'group_name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'srv_name';

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

/*$sql = "SELECT SQL_CALC_FOUND_ROWS  l.code_num, l.service_code, l.name, l.price_cash, l.price_charge \n".
			",if((select count(p.service_code) from seg_lab_result_params as p where p.service_code=l.service_code)>0
			,'YES','NO') as `status_param`,\n".
			"if( (select count(gp.service_code) from seg_lab_result_groupparams as gp where gp.service_code=l.service_code)>0,
			'YES','NO') as `status_grp`\n".
			", (select gp.group_id from seg_lab_result_groupparams as gp where gp.service_code=l.service_code) as `grp_id`\n".
			"FROM seg_lab_services AS l LEFT JOIN seg_lab_service_groups AS g ON l.group_code=g.group_code \n".
			"WHERE l.in_lis='0' AND l.status NOT IN ('deleted','hidden','inactive','void')"; */
if($view_mode=="with_group")
{
	//$cond = " AND !ISNULL(gp.group_id) \n";
	$join = //"LEFT JOIN seg_lab_result_groupparams AS gp ON l.service_code=gp.service_code \n";
			" INNER JOIN seg_lab_result_groupname AS gn ON gp.group_id=gn.group_id AND gn.status <> 'deleted' \n";
	$column = ", gn.name AS `group_name`, gp.group_id ";
	$open_mode = "edit";
	$caption = "Edit Test Group";
}
else if($view_mode=="without_group")
{
	$cond = " AND ISNULL(gp.group_id) \n";
	$join = "";
	$column = "";
	$open_mode = "new";
	$caption = "New Test Group";
}

//$sql = "SELECT SQL_CALC_FOUND_ROWS  l.code_num, l.service_code, l.name, gp.group_id, \n".
$sql = "SELECT SQL_CALC_FOUND_ROWS  l.code_num, l.service_code, l.name, \n".
				"IF( EXISTS(SELECT 
					      p.service_code 
					    FROM
					      seg_lab_result_params p 
					    WHERE p.service_code = l.service_code 
					      AND (
					        p.status <> 'deleted' 
					        OR p.status IS NULL
					      )),\n".
						"'YES','NO') AS `status_param`, \n".
				"IF( EXISTS(SELECT gp.service_code FROM seg_lab_result_groupparams gp WHERE \n".
						"gp.service_code=l.service_code AND gp.status <> 'deleted'),\n".
						"'YES','NO') AS `status_grp` \n".
				$column.
				"FROM seg_lab_services AS l \n".
				"LEFT JOIN seg_lab_service_groups AS g ON l.group_code=g.group_code \n".
				"LEFT JOIN seg_lab_result_groupparams AS gp ON l.service_code=gp.service_code \n".
				$join.
				"WHERE l.in_lis='0' AND l.status NOT IN ('deleted','hidden','inactive','void')".$cond;
if($search_service)
{
	$sql.=" AND (l.service_code LIKE '%$search_service%' OR l.name LIKE '%$search_service%') \n";
}
if($search_section)
{
	$sql.=" AND g.group_code='$search_section' \n";
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


//echo $sql;
/*echo "<pre>";
print_r($_REQUEST);
echo "</pre>";  */

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	$appendBtn = "";
	$editBtn = "";
	$cancelBtn = "";
	$param_txt = "Assign Parameters";
	$grp_txt = "Assign Test Group";
	while ($row = $result->FetchRow()) {
		if($mode=="add_service")
		{
			$msg="Already belonged to another group.";
			if($row['status_grp']=='YES')
				$appendBtn = '&nbsp;&nbsp;<img src="../../../images/cashier_check.png" name="check" class="link" onclick="javascript:alert(\''.$msg.'\');return false;"/>';
			else
				$appendBtn = '&nbsp;&nbsp;<img src="../../../images/cashier_check.png" name="check" class="link" onclick="appendItem(\''.$row['service_code'].'\',\''.$row['name'].'\',\''.$group_id.'\',\''.$group_name.'\');return false;"/>';
			$paramsBtn = '';
			$grpBtn = '';
		}else
		{
			$paramsBtn = '<button class="segButton" name="edit"  onmouseover="tooltip(\''.$param_txt.'\')" onmouseout="nd();"  onclick="openAddParamTray(\''.$row['service_code'].'\', \''.$row['group_id'].'\',\''.$row["grp_name"].'\');return false;"><img src="../../../gui/img/common/default/brick.png"/>Params</button>';
			$grpBtn = '&nbsp;&nbsp;<button class="segButton" name="edit"  onmouseover="tooltip(\''.$grp_txt.'\')" onmouseout="nd();"  onclick="openGroupTray(\''.$open_mode.'\',\''.$caption.'\',\''.$row["group_id"].'\',\''.$row["grp_name"].'\');return false;"><img src="../../../gui/img/common/default/bricks.png"/>Group</button>';
			//$cancelBtn = '&nbsp;&nbsp;<img src="../../../images/cashier_delete_small.gif" name="delete" class="link" onclick="return false;"/>';
			$appendBtn = '';
			$cancelBtn = '';
		}
		$data[] = array(
			'srv_code'=> $row['service_code'],
			'srv_name'=>$row['name'],
			//'srv_charge'=>$row['price_cash'],
			//'srv_stat_grp'=>$row['status_grp'],
			'group_name'=>$row["group_name"],
			'srv_stat_param'=>$row['status_param'],
			'options'=> $appendBtn.
									$paramsBtn.
									$grpBtn
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