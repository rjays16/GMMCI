<?php
require_once('roots.php');
require($root_path.'include/inc_environment_global.php');   

function countRec($where) {
  global $db;
  $sql = "SELECT COUNT(sos.refno) FROM seg_ops_serv sos
          INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
          INNER JOIN care_encounter ce ON (sos.pid = ce.pid and sos.encounter_nr=ce.encounter_nr)
	  			INNER JOIN care_person cp ON (sos.pid = cp.pid)
          INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
          INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
          INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room) WHERE som.status='post' AND or_type='DR' $where $sort $limit";
  
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
if (!$sortname) $sortname = 'op_date';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];



$where = "";
if ($query || $qtype)  {
 $where = " AND (ordername LIKE '%$query%' OR sos.refno='$query' OR sos.pid='$query')";
}
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
$query = "SELECT sos.refno, CONCAT_WS(' ',DATE_FORMAT(ceo.op_date, '%m-%d-%Y'), TIME_FORMAT(ceo.op_time, '%h:%i %p')) as op_date,
          sos.pid, sos.ordername, cr.info as op_room, ce.consulting_dr as physician, cp.blood_group as blood_type
          FROM seg_ops_serv sos
          INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
          INNER JOIN care_encounter ce ON (sos.pid = ce.pid and sos.encounter_nr=ce.encounter_nr)
	  			INNER JOIN care_person cp ON (sos.pid = cp.pid)
          INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
          INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
          INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room) WHERE som.status='post' AND or_type='DR' $where $sort $limit";


   $result = $db->Execute($query);
   while($row = $result->FetchRow()) {
    
    $select = addslashes('<input type="button" class="select_dr_patient" value="Select" onclick="xajax_select_dr_patient(\''.$row['refno'].'\')" />');                         
   
    if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['refno']."',";
    $json .= "cell:['".$row['refno']."'";
    $json .= ",'".$row['pid']."'"; 
    $json .= ",'".ucwords(strtolower($row['ordername']))."'"; 
    $json .= ",'".$row['op_date']."'";
    $json .= ",'".$row['op_room']."'";
    $json .= ",'".$select."']";
    $json .= "}";
    $rc = true; 
   }


$json .= "]\n";
$json .= "}";
echo $json;
?>

