<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/inc_date_format_functions.php');

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json; charset=ISO-8859-1");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$rep_category = $_REQUEST['rep_category'];
$search = $_REQUEST['search'];
$hrn = $_REQUEST['hrn'];
$sdate = date("Y-m-d H:i:s",strtotime($_REQUEST['specificdate']));
$sdateb1 = date("Y-m-d H:i:s",strtotime($_REQUEST['between1']));
$sdateb2 = date("Y-m-d H:i:s",strtotime($_REQUEST['between2']));
$selrecord="";
$seldate="";
$name = explode(",",$_REQUEST['name']);

$selrecord = "none";

if($_REQUEST['name']||$_REQUEST['case_no']||$_REQUEST['hrn']){
    switch ($_REQUEST['selrecord']) {
                        case 'name':
                            $selrecord = "";
                            $pid=null;
                            $sql = "SELECT * FROM care_person WHERE name_first LIKE '%".$name[1]."%' AND name_last LIKE '%".$name[0]."%'";
                            $result = $db->Execute($sql);
                            while ($row = $result->FetchRow()) {                                
                                $pid=$row['pid'];
                                $selrecord_c[]='pk_value='.$pid;
                            }                                                    
                            $selrecord=implode(" OR ",$selrecord_c); 
                        break;
                        case 'case_no':
                            $selrecord = "";  
                            $selrecord = 'pk_value='.$_REQUEST['case_no'];
                        break;
                        break;
                        case 'hrn':
                            $selrecord = "";
                            $selrecord = 'pk_value='.$_REQUEST['hrn'];
                        break;
                        default:
                        $selrecord = "";
                    }
}


/*
if($_REQUEST['chkdate']){
switch ($_REQUEST['seldate']) {
                    case 'today':
                        $seldate = 'DATE(date_changed)=DATE(NOW())';
                    break;
                    case 'thisweek':
                        $seldate = 'YEAR(date_changed)=YEAR(NOW()) AND WEEK(date_changed)=WEEK(NOW())';
                    break;
                    break;
                    case 'thismonth':
                        $seldate = 'YEAR(date_changed)=YEAR(NOW()) AND MONTH(date_changed)=MONTH(NOW())';
                    break;
                    case 'specificdate':
                        $seldate = "DATE(date_changed)='$sdate'";
                    break;
                    case 'between':
                         $seldate = "DATE(date_changed) BETWEEN '".$sdateb1."' AND '".$sdateb2."'";
                    break;
                    default:
                    $seldate = "";
                }
}
*/

//date("F j, Y",strtotime($_REQUEST["specificdate"]));  date("Y-m-d",strtotime($_REQUEST["between1"]));  



if(($selrecord)&&($seldate)){
    $where = $selrecord." AND ".$seldate;
}
else {
    $where = $selrecord."".$seldate;
}


$sortDir = 'DESC';
$sortMap = array(
    'rep_group' => 'rep_group',
    'rep_name' => 'rep_name',
    
//     'rep_script' => trim($row['field_c']),
//            'rep_group' => trim($row['old_value']),
//            'rep_name' => trim($row['new_value']),
    
);

$sortName = $_REQUEST['sort'];


if (!$sortName || !array_key_exists($sortName, $sortMap))
    $sortName = 'rep_group';

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



$sql = "SELECT SQL_CALC_FOUND_ROWS r.* FROM seg_audit_trail r 
        WHERE ".$selrecord." ORDER BY date_changed DESC";

/*
if($search) {
    $sql.=" AND rep_name LIKE '%".$search."%'";
}

if($rep_category) {
    $sql.=" AND rep_category = '".$rep_category."'";
}

if($sort_sql) {
    #$sql.=" ORDER BY {$sort_sql} ";
    $sql.=" ORDER BY rep_group, rep_name";
}
*/

if($maxRows) {
    $sql.=" LIMIT $offset, $maxRows";
}
$result = $db->Execute($sql);
#echo "ss = ".$sql;

if ($result !== FALSE) {
    $total = $db->GetOne("SELECT FOUND_ROWS()");
 while ($row = $result->FetchRow()) {
     
    $field = $row["field_c"];
    $row["field_c"] = "";
    $fields = explode(",",$field);   
    for($i=0;$fields[$i]!=null;$i++){
       $row["field_c"].= "".$fields[$i]."<br />";  
    }
    
    $oldValue = $row["old_value"];
    $row["old_value"] = "";
    $oldValues = explode(",",$oldValue);   
    for($i=0;$oldValues[$i]!=null;$i++){
       $row["old_value"].= "".$oldValues[$i]."<br />";  
    }
    
    $newValue = $row["new_value"];
    $row["new_value"] = "";
    $newValues = explode(",",$newValue);   
    for($i=0;$newValues[$i]!=null;$i++){
       $row["new_value"].= "".$newValues[$i]."<br />";  
    }
                   
                   
    
        $data[] = array(
            'user_id' => trim($row['user_id']),
            'field' => trim($row['field_c']),
            'oldvalue' => trim($row['old_value']),
            'newvalue' => trim($row['new_value']),
            'datechange' => trim($row['date_changed']),
            'recordpointer' => trim($row['pk_value'])
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