<?php
require_once('roots.php');
require($root_path.'include/inc_environment_global.php');



function countRec($where) {
	global $db;
	$sql = "select COUNT(*) from seg_packages sp
						inner join seg_packages_clinics spc on (sp.package_id = spc.package_id) inner join care_department cd on (cd.nr = spc.clinic_id)
						$where group by sp.package_id  $sort $limit";
	$result = $db->Execute($sql);
	$row = $result->FetchRow();
	return (isset($row[0]) ? $row[0] : 0);
}


global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = 'sp.'.$_POST['sortname'];
$sortorder = $_POST['sortorder'];
//$table_name = $_GET['table'];
if (!$sortname) $sortname = 'create_time';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];


$where = "";
if ($query || $qtype) $where = " WHERE sp.package_name LIKE '%$query%'";
$total = countRec($where);

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json; charset=ISO-8859-1");
$json = "";
$json .= "{\n";

$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;
$query = "select sp.package_id, sp.package_name, sp.package_price, group_concat(name_formal) as department from seg_packages sp
inner join seg_packages_clinics spc on (sp.package_id = spc.package_id) inner join care_department cd on (cd.nr = spc.clinic_id)
$where group by sp.package_id $sort $limit";

	 $result = $db->Execute($query);
	 if($db->Affected_Rows())
	 {
		 while($row = $result->FetchRow()) {

		 //$mode = ($row['in_or_death'] == 'Yes') ? 'new' : 'edit';
		 //$post_operative = '<a href="'.$root_path.'modules/or/or_main/or_deaths.php?refno='.$row['refno'].'&mode='.$mode.'" id="select_or_death"></a>';
			 $edit = '<a href="javascript:void(0)" onclick="open_edit_package_popup('.$row['package_id'].')" id="edit_charge"></a>';
			$post_operative = '';
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['package_id']."',";
			$json .= "cell:['".htmlentities($row['package_name'])."'";
			$json .= ",'".number_format($row['package_price'], 2, '.', ',')."'";
			$json .= ",'".$row['department']."'";
			$json .= ",'".$edit."'";
			$json .= ",'".$post_operative."']";
			$json .= "}";
			$rc = true;
		 }
	 }
	 else
	 {
//			print_r($query);
//			print_r($db->ErrorMsg());

			$json .= "\n{";
			$json .= "id:' ',";
			$json .= "cell:[' '";
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