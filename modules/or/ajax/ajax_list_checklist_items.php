<?php
require_once('roots.php');
require($root_path.'include/inc_environment_global.php');   



function countRec($where) {
	global $db;
	//count all instances of the search keyword
	$sql = "SELECT COUNT(*) FROM seg_or_checklist $where";  
	$result = $db->Execute($sql);
	$row = $result->FetchRow();
	return $row[0];    
}


global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = 'seg_or_checklist.'.$_POST['sortname'];
$sortorder = $_POST['sortorder'];
//$table_name = $_GET['table'];
if (!$sortname) $sortname = 'seg_or_checklist.date_created';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];


$where = "WHERE is_deleted=0";
if ($query || $qtype) $where = " WHERE seg_or_checklist.is_deleted=0 AND seg_or_checklist.checklist_question LIKE '%$query%'";
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
//retrieve all unique checklist items
$query = "SELECT DISTINCT seg_or_checklist.checklist_id, seg_or_checklist.checklist_question, 
					seg_or_checklist.has_detail, seg_or_checklist.label_data, seg_or_checklist_main.is_mandatory  
					FROM seg_or_checklist_main 
					INNER JOIN seg_or_checklist 
					ON seg_or_checklist_main.checklist_id=seg_or_checklist.checklist_id
					$where $sort $limit";   

	 $result = $db->Execute($query); 
	 if($db->Affected_Rows())
	 {
		 while($row = $result->FetchRow()) {
			
			$edit = '<a href="javascript:void(0)" onclick="open_edit_package_popup('.$row['checklist_id'].')" id="edit_charge"></a>';
			$delete = '<a href="javascript:void(0)" onclick="open_delete_package_popup('.$row['checklist_id'].')" id="delete_charge"></a>';   //
			//$edit = '<a href="javascript:void(0)" id="edit_charge"></a>';
//			$delete = '<a href="javascript:void(0)" id="delete_charge"></a>';   //
			if($row['has_detail']==0||$row['has_detail']=='0'){$detail='NONE';}
			else{$detail=$row['label_data'];}     
			if($row['is_mandatory']==0||$row['is_mandatory']=='0'){$mandatory_item='No';}
			else{$mandatory_item='Yes';}
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['checklist_id']."',";
			$json .= "cell:['".htmlentities($row['checklist_question'])."'";
			$json .= ",'".$detail."'";
			$json .= ",'".$mandatory_item."'";
			$json .= ",'".$edit."'";
			$json .= ",'".$delete."']";
			$json .= "}";
			$rc = true; 
		 }
	 }
	 else
	 {
		 print_r($query);
		 print_r($db->ErrorMsg());
	 }


$json .= "]\n";
$json .= "}";
echo $json;
 
?>