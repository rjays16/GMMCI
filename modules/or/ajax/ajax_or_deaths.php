<?php
require_once('roots.php');
require($root_path.'include/inc_environment_global.php');



	 function countRec($where) {
	global $db;
	$sql = "SELECT COUNT(sos.refno)
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
/*INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room) */
WHERE som.status IN ('pre_op', 'post', 'request')
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
sos.pid, sos.ordername, cr.info, cd.name_formal, IFNULL(ord.or_main_refno, 'Yes') as in_or_death
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room)
LEFT JOIN or_main_death ord ON (ord.or_main_refno=som.or_main_refno)
WHERE som.status IN ('pre_op', 'post') $where $sort $limit";   */

$query = "SELECT distinct sos.refno, som.or_main_refno, som.status,
CONCAT_WS(' ',DATE_FORMAT(sos.request_date, '%m-%d-%Y'), TIME_FORMAT(sos.request_time, '%h:%i %p')) as request_date,
sos.pid, sos.ordername
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
WHERE som.status IN ('request', 'pre_op', 'post') $where $sort $limit";

	 $result = $db->Execute($query);
	 while($row = $result->FetchRow()) {

	 $mode = ($row['in_or_death'] == 'Yes') ? 'new' : 'edit';
	 $post_operative = '<a href="'.$root_path.'modules/or/or_main/or_deaths.php?refno='.$row['refno'].'&mode='.$mode.'" id="select_or_death"></a>';

		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['refno']."',";
		$json .= "cell:['".$row['refno']."'";
		$json .= ",'".$row['request_date']."'";
		$json .= ",'".$row['pid']."'";
		$json .= ",'".ucwords(strtolower($row['ordername']))."'";
		$json .= ",'".$row['name_formal']."'";
		$json .= ",'".$row['info']."'";
		$json .= ",'".$post_operative."']";
		$json .= "}";
		$rc = true;
	 }


$json .= "]\n";
$json .= "}";
echo $json;

?>

