<?php
require_once('roots.php');
require($root_path.'include/inc_environment_global.php');



	 function countRec($where) {
	global $db;
	/*$sql = "SELECT COUNT(sos.refno)
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.nr=ceo.op_room)
WHERE som.status IN ('scheduled', 'pre_op')
 $where $sort $limit"; */
$sql = "SELECT COUNT(sos.refno)
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = som.dept_nr)
WHERE som.status IN ('approved', 'pre_op')
AND is_main = '1'
 $where $sort $limit";
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
	$qtype = 'AND cd.nr='.$_POST['qtype'];
}
else {
	$qtype = '';
}

$where = "";
if ($query || $qtype) $where = "$qtype AND (ordername LIKE '%$query%' OR sos.refno='$query' OR pid='$query')";
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
/*$query = "SELECT sos.refno, som.or_main_refno, som.status,
CONCAT_WS(' ',DATE_FORMAT(sos.request_date, '%m-%d-%Y'), TIME_FORMAT(sos.request_time, '%h:%i %p')) as request_date,
sos.pid, sos.ordername, cr.info, cd.name_formal
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.nr=ceo.op_room) WHERE som.status IN ('scheduled', 'pre_op', 'request_DR') $where $sort $limit"; */

$query = "SELECT distinct sos.refno, som.or_main_refno, som.status, som.encounter_nr,
CONCAT_WS(' ',DATE_FORMAT(sos.request_date, '%m-%d-%Y'), TIME_FORMAT(sos.request_time, '%h:%i %p')) as request_date,
sos.pid, sos.ordername, cd.name_formal
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = som.dept_nr)
WHERE som.status IN ('approved', 'pre_op')
AND is_main = '1'
$where $sort $limit";

	#	$objResponse->addAlert($query);
		//echo $query;
	 $result = $db->Execute($query);
	 /*while($row = $result->FetchRow()) {
		 if ($row['status'] == 'pre_op') {
			 $status = 'Pre-operative';
		 }
		 elseif ($row['status'] == 'scheduled') {
			 $status = 'Scheduled';
		 }
		 elseif ($row['status'] == 'request_DR') {
			 $status = 'DR';
		 }
		 else {
			 $status = 'Illegal';

		 }

		 $schedule = '<a href="'.$root_path.'modules/or/or_main/or_main_pre_operative.php?refno='.$row['refno'].'" id="pre_operative"></a>';




		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['refno']."',";
		$json .= "cell:['".$row['refno']."'";
		$json .= ",'".$row['request_date']."'";
		$json .= ",'".$row['pid']."'";
		$json .= ",'".ucwords(strtolower($row['ordername']))."'";
		$json .= ",'".$row['name_formal']."'";
		$json .= ",'".$row['info']."'";
		$json .= ",'".$status."'";
		$json .= ",'".$schedule."']";
		$json .= "}";
		$rc = true;
	 }  */
	 while($row = $result->FetchRow()) {
		 if ($row['status'] == 'approved') {
			 $status = 'Approved';
		 }
		 elseif($row['status'] == 'pre_op'){
				$status = 'Pre-operative';
		 }
		 else {
			 $status = 'Illegal';
		 }

		 $schedule = '<a href="'.$root_path.'modules/or/or_main/or_main_pre_operative.php?refno='.$row['refno'].'&pid='.$row['pid'].'&encounter_nr='.$row['encounter_nr'].'" id="pre_operative"></a>';

		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['refno']."',";
		$json .= "cell:['".$row['refno']."'";
		$json .= ",'".$row['encounter_nr']."'";
		$json .= ",'".$row['request_date']."'";
		$json .= ",'".$row['pid']."'";
		$json .= ",'".ucwords(strtolower($row['ordername']))."'";
		$json .= ",'".$row['name_formal']."'";
		$json .= ",'".$status."'";
		$json .= ",'".$schedule."']";
		$json .= "}";
		$rc = true;
	 }

		/*
		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['refno']."',";
		$json .= "cell:['".$row['refno']."'";
		$json .= ",'".$row['encounter_nr']."'";
		$json .= ",'".$row['request_date']."'";
		$json .= ",'".$row['pid']."'";
		$json .= ",'".ucwords(strtolower($row['ordername']))."'";
		$json .= ",'".$row['name_formal']."'";
		$json .= ",'".$status."'";
		$json .= ",'".$schedule."']";
		$json .= "}";
		$rc = true;
	 }   */

$json .= "]\n";
$json .= "}";
echo $json;

?>

