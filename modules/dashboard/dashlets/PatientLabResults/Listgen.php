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
	$sortName = 'date';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	//'date' => 'r.serv_dt DESC, r.serv_tm',
	'date' => 'date_received',
);
//if (!$sortMap[$sortName]) $sort = 'serv_dt, serv_tm DESC';
if (!$sortMap[$sortName]) $sort = 'date_received DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');

$query = "SELECT pid, encounter_type, encounter_date, admission_dt, discharge_date, is_discharged 
            FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

#$pid = $db->GetOne($query);
$enc_row = $db->GetRow($query);
$pid = $enc_row['pid'];

if (($enc_row['encounter_type'] == '1') || ($enc_row['encounter_type'] == '2')){
    $encounter_date = date("Y-m-d",strtotime($enc_row['encounter_date']));
    $discharged_date = date("Y-m-d",strtotime($enc_row['encounter_date']));
}else{
    $encounter_date = date("Y-m-d",strtotime($enc_row['admission_dt']));
    if (!$enc_row['is_discharged'])
       $enc_row['discharge_date'] = date("Y-m-d"); 
    $discharged_date = date("Y-m-d",strtotime($enc_row['discharge_date']));
}

$data = Array();
if($pid) {
  /*edited by mai 08-13-2014*/
	      $query = "SELECT 
                    SQL_CALC_FOUND_ROWS IF(
                      ISNULL(gp.group_id),
                      gp.group_id,
                      gp.group_id
                    ) AS group_id,
                    r.refno,
                    r.pid,
                    s.service_code,
                    GROUP_CONCAT(s.name) AS services,
                    d.date_served AS request_date,
                    d.date_served AS date_received 
                  FROM
                    seg_lab_serv r 
                    INNER JOIN seg_lab_servdetails d 
                      ON d.refno = r.refno 
                    INNER JOIN seg_lab_services s 
                      ON s.service_code = d.service_code 
                    LEFT JOIN seg_lab_result_groupparams AS gp 
                      ON gp.service_code = d.service_code 
                  WHERE r.status NOT IN (
                      'deleted',
                      'hidden',
                      'inactive',
                      'void'
                    ) 
                    AND r.STATUS NOT IN (
                      'deleted',
                      'hidden',
                      'inactive',
                      'void'
                    ) 
                    AND d.STATUS NOT IN (
                      'deleted',
                      'hidden',
                      'inactive',
                      'void'
                    ) 
                    AND d.status = 'done' 
                    AND d.is_served = 1 
                    AND r.`encounter_nr` = $db->qstr($encounter_nr) 
                    GROUP BY gp.group_id, r.refno 
                  ORDER BY UNIX_TIMESTAMP(date_received) DESC 
                    LIMIT $offset, $maxRows";       

		/*echo "<pre>";
		print_r($query);
		echo "</pre>";*/
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->Execute($query);

	$data = Array();
	if ($rs !== false)
	{
		$total = 0;
		$total = $db->GetOne("SELECT FOUND_ROWS()");
		$rows = $rs->GetRows();
		foreach ($rows as $row)
		{
            
            //added by VAN 02-06-2013
            if ($row['nth_take']==1){
               $services = $row['services'].'<font color="BLUE"> (First Take)</font>'; 
            }elseif ($row['nth_take'] > 1){
               $service_code = $db->qstr($row['service_code']); 
               $sql_l = "SELECT name FROM seg_lab_services WHERE service_code=$service_code"; 
               $services = $db->GetOne($sql_l);
               
               switch($row['nth_take']){
                    case '1' :  
                                $nth_take = 'First'; 
                                break;
                    case '2' :  
                                $nth_take = 'Second'; 
                                break;
                    case '3' :  
                                $nth_take = 'Third'; 
                                break;
                    case '4' :  
                                $nth_take = 'Fourth'; 
                                break;
                    case '5' :  
                                $nth_take = 'Fift'; 
                                break;
                    case '6' :  
                                $nth_take = 'Sixth'; 
                                break;
                    case '7' :  
                                $nth_take = 'Seventh'; 
                                break;
                    case '8' :  
                                $nth_take = 'Eighth'; 
                                break;
                    case '9' :  
                                $nth_take = 'Ninth'; 
                                break;
                    case '10' : 
                                $nth_take = 'Tenth'; 
                                break;
                }

               $services = $services.'<font color="BLUE"> ('.$nth_take.' Take)</font>'; 
            }else{
               $services = $row['services'];
            }
            //----------------------
            
            $withresult = 0;
            if ($row['filename'])
                $withresult = 1;
                            
            $data[] = Array(
                'date' => nl2br(date("M-d-Y\nh:ia", strtotime($row["request_date"]))),
				        'service' => $services ,
				        'refno' => $row["refno"],
                'pid' => $row["pid"],
               /*'lis_order_no' => $row["lis_order_no"],
                'filename' => $row["filename"],
                'withresult' => $withresult,*/
                'service_code' => $row['service_code'],
                'group_id' => $row['group_id']
			);
		}
	}
}

if (!$data)
{
	$total = 0;
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
);

/**
* Convert data to JSON and print
*
*/

$json = new Services_JSON;
print $json->encode($response);