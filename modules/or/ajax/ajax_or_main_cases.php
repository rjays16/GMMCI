<?php
require_once('roots.php');
require($root_path.'include/inc_environment_global.php');   



   function countRec($where) {
  global $db;
  $sql = "SELECT COUNT(sos.refno)
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.nr=ceo.op_room)
LEFT JOIN or_main_death omd ON (omd.or_main_refno = som.or_main_refno)  $where $sort $limit";
  $result = $db->Execute($sql);
  $row = $result->FetchRow();
  return $row[0];    
}


global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
//$table_name = $_GET['table'];
if (!$sortname) $sortname = 'request_date';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];

if (isset($_POST['qtype']) && $_POST['qtype'] != '' && $_POST['qtype'] != 'all') {
  $qtype = 'cd.nr='.$_POST['qtype'] . ' AND';                      
}
else {
  $qtype = '';
}

if (isset($_POST['qtype2']) && $_POST['qtype2'] != '' && $_POST['qtype2'] != 'all_status') {
  if ($_POST['qtype2']=='dead') {
    $qtype2 = " AND omd.or_main_refno<>''";
  }
  elseif ($_POST['qtype2']=='post') {
    $qtype2 = " AND omd.or_main_refno IS NULL AND som.status='{$_POST['qtype2']}'";
  }
  else {
    $qtype2 = " AND som.status='{$_POST['qtype2']}'";
  }
}

else {
  $qtype2 = '';
}


$where = "";
if ($query || $qtype || $qtype2)  {
  $where = "WHERE $qtype (ordername LIKE '%$query%' OR sos.refno='$query' OR pid='$query') $qtype2";
  //echo $where;
}
else
  $where = "WHERE ceo.op_date = CURRENT_DATE() OR sos.request_date = CURRENT_DATE()";
  
$total = countRec($where);   
 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";

$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;
$query = "SELECT sos.refno, som.status, omd.or_main_refno as is_dead,
CONCAT_WS(' ',DATE_FORMAT(sos.request_date, '%m-%d-%Y'), TIME_FORMAT(sos.request_time, '%h:%i %p')) as request_date,
CONCAT_WS(' ',DATE_FORMAT(ceo.op_date, '%m-%d-%Y'), TIME_FORMAT(ceo.op_time, '%h:%i %p')) as operation_date,
sos.pid, sos.ordername, cr.info, cd.name_formal
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.nr=ceo.op_room)
LEFT JOIN or_main_death omd ON (omd.or_main_refno = som.or_main_refno) $where $sort $limit";

  
   $result = $db->Execute($query);
   $statuses = array('request'=>'Pending',
                     'cancelled'=>'Cancelled',
                     'approved'=>'Approved',
                     'scheduled'=>'Scheduled',
                     'pre_op'=>'Pre-operation',
                     'post'=>'Post-operation');
   while($row = $result->FetchRow()) {
    if ($row['is_dead'] != '') {
      $status = 'Dead';
    }
    
    else {
      $status = $statuses[$row['status']];
    }
    $preview_case = addslashes('<a href="javascript:void(0)" onclick="preview_or_case(\''.$row['refno'].'\')" id="preview_or_case"></a>');
    
    if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['refno']."',";
    $json .= "cell:['".$row['request_date']."'";
    $json .= ",'".$row['operation_date']."'";
    $json .= ",'".$row['pid']."'";
    $json .= ",'".ucwords(strtolower($row['ordername']))."'";
    $json .= ",'".$row['name_formal']."'";
    $json .= ",'".$status."'";
    $json .= ",'".$preview_case."']";
    $json .= "}";
    $rc = true; 
   }


$json .= "]\n";
$json .= "}";
echo $json;
 
?>

