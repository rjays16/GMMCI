<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json; charset=ISO-8859-1");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$searchkey = $_REQUEST['search'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date_received' => 'date_received',
);

$sortName = $_REQUEST['sort'];

if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'date_received';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$data = array();
if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}

# convert * and ? to % and &
$searchkey=strtr($searchkey,'*?','%_');
$searchkey=trim($searchkey);
#$suchwort=$searchkey;
$searchkey = str_replace("^","'",$searchkey);
$suchwort=addslashes($searchkey);

if(is_numeric($suchwort)) {
    $pid = $suchwort;    
} else {
    # Try to detect if searchkey is composite of first name + last name
    if(stristr($searchkey,',')){
            $lastnamefirst=TRUE;
    }else{
            $lastnamefirst=FALSE;
    }

    $cbuffer=explode(',',$searchkey);

    # Remove empty variables
    for($x=0;$x<sizeof($cbuffer);$x++){
            $cbuffer[$x]=trim($cbuffer[$x]);
            if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
    }

    # Arrange the values, ln= lastname, fn=first name, rd = request date
    if($lastnamefirst){
            $fn=$comp[1];
            $ln=$comp[0];
            $rd=$comp[2];
    }else{
            $fn=$comp[0];
            $ln=$comp[1];
            $rd=$comp[2];
    }
    if($ln && $fn){
    $sql2_cond =" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
    
    $sql2 = "SELECT p.* FROM care_person p ".$sql2_cond;
    $person_info = $db->getRow($sql2);
    $pid = $person_info['pid']; 
}
}

if($pid){
    #$cond = " WHERE filename LIKE '$pid%' ";
    $cond = " WHERE p.pid = ".$db->qstr($pid);
}else{
    $cond = " WHERE DATE(s.serv_dt)=DATE(NOW())";
}

/*$query = "SELECT SQL_CALC_FOUND_ROWS 
            s.encounter_nr, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type,
            SUBSTR(h.filename,1,INSTR(h.filename, '_')-1) AS `pid`,
            fn_get_person_name(SUBSTR(h.filename,1,INSTR(h.filename, '_')-1)) AS patient,
            IF(fn_calculate_age(IF(s.serv_dt,s.serv_dt,DATE(h.date_received)),p.date_birth),fn_get_age(IF(s.serv_dt,s.serv_dt,DATE(h.date_received)),p.date_birth),age) AS age,
            UPPER(p.sex) AS sex,
            o.refno, date_received AS request_date, 
            SUBSTR(h.filename,INSTR(h.filename, '_')+1,LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4) `lis_order_no`,
            IF(fn_get_labtest_request_all(o.refno)<>'',
               fn_get_labtest_request_all(o.refno),
               CONCAT('MANUALLY ENCODED with Order No. ',
                       SUBSTR(h.filename,INSTR(h.filename, '_')+1,
                           LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4))) AS services, 
            o.refno, sr.nth_take, sr.service_code, h.*
            FROM seg_hl7_pdffile_received h
            LEFT JOIN seg_lab_hclab_orderno o ON o.lis_order_no=(SUBSTR(h.filename,INSTR(h.filename, '_')+1,LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4))
            LEFT JOIN seg_lab_serv_serial sr ON sr.refno=o.refno AND sr.lis_order_no=o.lis_order_no 
            INNER JOIN care_person p ON p.pid=SUBSTR(h.filename,1,INSTR(h.filename, '_')-1) 
            LEFT JOIN seg_lab_serv s ON s.refno=o.refno
            LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr ".
            $cond;*/

$query = "SELECT DISTINCT SQL_CALC_FOUND_ROWS 
            s.encounter_nr, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, 
            e.encounter_type, p.pid, 
            fn_get_person_name(p.pid) AS patient, 
            IF(fn_calculate_age(s.serv_dt,p.date_birth),fn_get_age(s.serv_dt,p.date_birth),age) AS age, 
            sd.`service_code`, serv.`name` AS services, IFNULL(gp.`group_id`,'') AS group_id, rd.`service_date`,
            UPPER(p.sex) AS sex, s.refno 
            FROM seg_lab_serv AS s
            INNER JOIN care_person p 
                ON s.`pid` = p.`pid`
            LEFT JOIN seg_lab_servdetails AS sd 
                ON s.`refno` = sd.`refno` 
            LEFT JOIN seg_lab_resultdata AS rd 
                ON rd.`refno` = sd.`refno`
            LEFT JOIN seg_lab_services AS serv
                ON serv.`service_code` = sd.`service_code`
            LEFT JOIN seg_lab_result_groupparams AS gp 
                ON gp.service_code = sd.service_code
            LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr ".
            $cond."AND sd.`status` = 'done' ";            
#echo $query;

if($sort_sql) {
	$query.=" ORDER BY s.`serv_dt` DESC";
}
if($maxRows) {
	$query.=" LIMIT $offset, $maxRows";
}

$db->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->Execute($query);

$data = Array();
if ($rs !== false){
    $total = 0;
    $total = $db->GetOne("SELECT FOUND_ROWS()");
    $rows = $rs->GetRows();
    foreach ($rows as $row){
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
            
            $withresult = 1;
                
        if ($row['encounter_type']==1){
            $enctype = "ERPx";
            $location = "EMERGENCY ROOM";
        }elseif (($row['encounter_type']==2)||($row['encounter_type']==5)){
            if ($row['encounter_type']==2)
                $enctype = "OPDx";
            else
                $enctype = "PHSx";

            $dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
            $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
        }elseif (($row['encounter_type']==3)||($row['encounter_type']==4)||($row['encounter_type']==6)){
            if ($row['encounter_type']==3)
                    $enctype = "INPx (ER)";
            elseif ($row['encounter_type']==4)
                    $enctype = "INPx (OPD)";
            elseif ($row['encounter_type']==6)
                    $enctype = "INPx (PHS)";

            $ward = $ward_obj->getWardInfo($row['current_ward_nr']);
            $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room_nr'];
        }else{
            $enctype = "WPx";
            $location = 'WALK-IN';
        }   

            $filename = $row["pid"].'_'.$row["lis_order_no"].'.pdf';
            
        $sql_date = "SELECT h.date_update, f.date_received as request_date
                        FROM seg_hl7_hclab_msg_receipt h
                        INNER JOIN seg_hl7_file_received f ON f.filename=h.filename
                        WHERE h.pid = ".$db->qstr($row["pid"])." LIMIT 1";
        $row_dt = $db->GetRow($sql_date); 
        
		$data[] = array(
            'date' => nl2br(date("M-d-Y", strtotime($row["service_date"]))),
            'service' => $services ,
            'refno' => ($row["refno"]) ? $row["refno"] : 'Manual',
            'pid' => $row["pid"],
            'group_id' => $row["group_id"],
            'code' => $row["service_code"],
            'patient' => $row["patient"],
            'age' => $row["age"],
            'sex' => $row["sex"],
            'patient_type' => ($row["refno"]) ? $enctype : '',
            'location' => ($row["refno"]) ? $location : '',
            'withresult' => $withresult
		);
	}
}
$query1 = "SELECT DISTINCT SQL_CALC_FOUND_ROWS 
            s.encounter_nr, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, 
            e.encounter_type, p.pid, 
            fn_get_person_name(CONCAT('W',p.pid)) AS patient,  
            sd.`service_code`, serv.`name` AS services, IFNULL(gp.`group_id`,'') AS group_id, rd.`service_date`,
            UPPER(p.sex) AS sex, s.refno 
            FROM seg_lab_serv AS s
            INNER JOIN seg_walkin p 
                ON s.`pid` = p.`pid`
            LEFT JOIN seg_lab_servdetails AS sd 
                ON s.`refno` = sd.`refno` 
            LEFT JOIN seg_lab_resultdata AS rd 
                ON rd.`refno` = sd.`refno`
            LEFT JOIN seg_lab_services AS serv
                ON serv.`service_code` = sd.`service_code`
            LEFT JOIN seg_lab_result_groupparams AS gp 
                ON gp.service_code = sd.service_code
            LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr ".
            $cond."AND sd.`status` = 'done' ";            
#echo $query;

if($sort_sql) {
    $query1.=" ORDER BY s.`serv_dt` DESC";
}
if($maxRows) {
    $query1.=" LIMIT $offset, $maxRows";
}

$db->SetFetchMode(ADODB_FETCH_ASSOC);
$rs1 = $db->Execute($query1);
if ($rs1 !== false){
    $total = 0;
    $total = $db->GetOne("SELECT FOUND_ROWS()");
    $rows = $rs1->GetRows();
    foreach ($rows as $row){
            $services = $row['services'];
            
                $withresult = 1;
                
        if ($row['encounter_type']==1){
            $enctype = "ERPx";
            $location = "EMERGENCY ROOM";
        }elseif (($row['encounter_type']==2)||($row['encounter_type']==5)){
            if ($row['encounter_type']==2)
                $enctype = "OPDx";
            else
                $enctype = "PHSx";

            $dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
            $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
        }elseif (($row['encounter_type']==3)||($row['encounter_type']==4)||($row['encounter_type']==6)){
            if ($row['encounter_type']==3)
                    $enctype = "INPx (ER)";
            elseif ($row['encounter_type']==4)
                    $enctype = "INPx (OPD)";
            elseif ($row['encounter_type']==6)
                    $enctype = "INPx (PHS)";

            $ward = $ward_obj->getWardInfo($row['current_ward_nr']);
            $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room_nr'];
        }else{
            $enctype = "WPx";
            $location = 'WALK-IN';
        }   

        $filename = $row["pid"].'_'.$row["lis_order_no"].'.pdf';  

        $sql_date = "SELECT h.date_update, f.date_received as request_date
                        FROM seg_hl7_hclab_msg_receipt h
                        INNER JOIN seg_hl7_file_received f ON f.filename=h.filename
                        WHERE h.pid = ".$db->qstr($row["pid"])." LIMIT 1";
        $row_dt = $db->GetRow($sql_date); 
        
		$data[] = array(
            'date' => nl2br(date("M-d-Y", strtotime($row["service_date"]))),
            'service' => $services ,
            'refno' => ($row["refno"]) ? $row["refno"] : 'Manual',
            'pid' => $row["pid"],
            'group_id' => $row["group_id"],
            'code' => $row["service_code"],
            'patient' => $row["patient"],
            'age' => '?',
            'sex' => '?',
            'patient_type' => ($row["refno"]) ? $enctype : '',
            'location' => ($row["refno"]) ? $location : '',
            'withresult' => $withresult
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