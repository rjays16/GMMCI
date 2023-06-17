<?php
/**
* ListGen.php
*
*
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/dashboard/DashletSession.php";
require_once $root_path."classes/json/json.php";

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'patient';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'e.encounter_date',
	'name' => 'patient',
	'confinement' => 't.type'
);
if (!$sortMap[$sortName]) $sort = 'patient ASC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

if ($_SESSION['sess_temp_userid']!=='admin') {
$query = "SELECT u.login_id,u.personell_nr,\n".
		"a.location_nr `dept`\n".
	"FROM care_users u\n".
		"INNER JOIN care_personell p ON p.nr=u.personell_nr\n".
		"INNER JOIN care_personell_assignment a ON a.personell_nr=p.nr\n".
		"LEFT JOIN care_department d ON d.nr=a.location_nr\n".
	"WHERE login_id=".$db->qstr($_SESSION['sess_temp_userid']);
$info = $db->GetRow($query);
}

if (!empty($_GET['key'])) {
	$where = array();
	if ($_SESSION['sess_temp_userid']=='admin'||$info) {
	$where = array(
		"NOT e.is_discharged",
		"e.status NOT IN ('deleted')"
	);

		$key = $_GET['key'];
		if(is_numeric($key)) {
			$where[]="p.pid=".$db->qstr(trim($key));
		} else {
			if (strpos($key, ',') !== false) {
				$split_key = explode(',', $key);
				foreach ($split_key as $i=>$v)
					$split_key[$i] = trim($v);

				if ($split_key[0] || $split_key[1]) {
					$where[] = "p.name_last LIKE " .
						$db->qstr($split_key[0].'%') .
						" AND p.name_first LIKE " .
						$db->qstr($split_key[1]."%");
			}
			} else {
				$where[] = "p.name_last LIKE " .
					$db->qstr(trim($key) . '%');
			}
		}

	if ($_GET['filter'] == 'department')
	{
		//$where[] = "consulting_dept_nr=".$db->qstr($info['dept']);
        $parentDept = $db->GetOne("SELECT parent_dept_nr FROM care_department WHERE nr=" . $db->qstr($info['dept']) );
        $or = array();
        $or[] = "current_dept_nr=". $db->qstr($info['dept']);
		$or[] = "consulting_dept_nr=". $db->qstr($info['dept']);
        $or[] = "d.parent_dept_nr=". $db->qstr($info['dept']);
	        $or[] = "r.referrer_dept=". $db->qstr($info['dept']);
        if (!empty($parentDept) && $parentDept!='0') {
            $or[] = "consulting_dept_nr=". $db->qstr($parentDept);
            $or[] = "d.parent_dept_nr=". $db->qstr($parentDept);
        }
        $where[] = implode(' OR ', $or);
	}

		if($_GET['filter'] == 'assigned')
	{
		$where[] = "current_att_dr_nr=".$db->qstr($info['personell_nr'])." OR consulting_dr_nr=".$db->qstr($info['personell_nr']);
	}

		$query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(e.encounter_nr) `encounter`, ".
				"e.pid,e.encounter_date,t.type,e.encounter_nr,fn_get_person_name(p.pid) `patient` ".
				"FROM care_encounter e ".
				"INNER JOIN care_type_encounter t ON t.type_nr=e.encounter_type ".
				"INNER JOIN care_person p ON p.pid=e.pid ".
				"LEFT JOIN seg_referral r ON r.encounter_nr = e.encounter_nr ".
	            "LEFT JOIN care_department d ON d.nr= e.consulting_dept_nr OR d.nr = r.referrer_dept ".
				"WHERE (". implode(") AND (",$where).") ".
				"ORDER BY $sort ".
		"LIMIT $offset, $maxRows";
}

	if (!empty($where)) {
$db->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->Execute($query);

		$data = array();
		if ($rs !== false) {
	$total = 0;
			//$total = $db->GetOne("SELECT FOUND_ROWS()");
	$rows = $rs->GetRows();
	foreach ($rows as $row)
	{
				if($enc!= $row['encounter']){
					$data[] = array(
			'pid' => $row['pid'],
						'date' => date("M d, Y", strtotime($row['encounter_date'])),
			'name' => strtoupper($row['patient']),
			'encounter' => strtoupper($row['encounter']),
			'confinement' => $row['type'],
			'active' => $session->get('ActivePatientFile')
		);
					$total += 1;
					$enc=$row['encounter'];
	}
}
		} else {
	$total = 0;
		}
	} else {
		$currentPage = 0;
		$total = 0;
		$data = array();
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
);
} else {
	$response = array(
		'currentPage' => 0,
		'total' => 0,
		'data'=> array()
	);
}

/**
* Convert data to JSON and print
*
*/

$json = new Services_JSON;
print $json->encode($response);