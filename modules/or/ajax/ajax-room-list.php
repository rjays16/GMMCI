<?php
require_once('roots.php');
require($root_path.'include/inc_environment_global.php');


#edited by Cherry June 1, 2010
	 function countRec($where) {
	global $db;
	$sql = "SELECT COUNT(som.or_main_refno)
FROM seg_or_main AS som
	INNER JOIN seg_or_room AS sor
		ON sor.room_nr = som.room_nr
	INNER JOIN care_encounter AS ce
		ON ce.encounter_nr = som.encounter_nr
WHERE som.status IN('post','pre_op')
		AND som.room_nr IS NOT NULL
ORDER BY som.or_main_refno
 ";
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
/*$query = "SELECT sos.refno, som.or_main_refno, som.status, som.or_type,
CONCAT_WS(' ',DATE_FORMAT(sos.request_date, '%m-%d-%Y'), TIME_FORMAT(sos.request_time, '%h:%i %p')) as request_date,
sos.pid, sos.ordername, cr.info, cd.name_formal
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.nr=ceo.op_room) WHERE som.status IN ('pre_op', 'post') $where $sort $limit";   */

#Added by Cherry June 1, 2010
$query = "SELECT SQL_CALC_FOUND_ROWS
	som.or_main_refno  AS ref_no,
	som.ceo_refno      AS case_no,
	sor.room_name,
	som.date_operation,
	fn_get_person_lastname_first(ce.pid) AS patient_name,
	'occupied'         AS room_status
FROM seg_or_main AS som
	INNER JOIN seg_or_room AS sor
		ON sor.room_nr = som.room_nr
	INNER JOIN care_encounter AS ce
		ON ce.encounter_nr = som.encounter_nr
WHERE som.status IN('post','pre_op')
		AND som.room_nr IS NOT NULL
ORDER BY som.or_main_refno
$limit";

	 $result = $db->Execute($query);
	 while($row = $result->FetchRow()) {
	 		$refno = $row['ref_no'];
			$dte_operation = $row['date_operation'];
			$mode = ($row['status'] == 'pre_op') ? 'new' : 'edit';
			$details_btn = '<a id="room_details" onclick="openDetailsTray('.$refno.')" href="javascript:void(0)"></a>';
			#$details_btn = '<button class="segButton" style="cursor: pointer;" onclick="openDetailsTray('.'\''.$refno.'\', '.'\''.$dte_operation.'\');return false;" title="Update Room Status"><img src="../../../gui/img/common/default/page_go.png"/>Update</button>';

			$post_operative = '<a href="'.$root_path.'modules/or/or_asu/or_asu_post.php?refno='.$row['refno'].'&mode='.$mode.'&pid='.$row['pid'].'&encounter_nr='.$row['encounter_nr'].'" id="post_operative"></a>';
			$or_type = $post_operative;

			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['ref_no']."',";
			$json .= "cell:['".$row['ref_no']."'";
			$json .= ",'".$row['case_no']."'";
			$json .= ",'".$row['date_operation']."'";
			$json .= ",'".$row['patient_name']."'";
			$json .= ",'".ucwords(strtolower($row['room_status']))."'";
			$json .= ",'".$details_btn."']";
			$json .= "}";
			$rc = true;
	 }

	 if (!$rc) {
			$json .= "\n{";
			$json .= "id:' ',";
			$json .= "cell:[' '";
			$json .= ",' '";
			$json .= ",' '";
			$json .= ",' '";
			$json .= ",' '";
			$json .= ",'']";
			$json .= "}";
	 }

$json .= "]\n";
$json .= "}";
echo $json;

?>

