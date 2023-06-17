<?php
require_once('roots.php');
require_once($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/care_api_classes/class_personell.php');


global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
//$table_name = $_GET['table'];
if (!$sortname) $sortname = 'nr';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];

//echo 'Qtype ' .$_POST['qtype'];
//$_POST['qtype'] = 8; 
$where = "";
//if ($query || $qtype) $where = " AND (cp2.name_last LIKE '%$query%' OR cp2.name_first LIKE '%$query%')";
$personell = new Personell();
$total = 0;    
$result = $personell->get_personell($_POST['qtype'], 0, '', true);
$temp_row = $result->FetchRow();
$total = $temp_row['num'];
$result = $personell->get_personell($_POST['qtype'], 0, $limit);

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



if ($result) {
while($row = $result->FetchRow()) {

  $add_personell = addslashes('<a href="javascript:void(0)" onclick="add_personell(\''.$row['nr'].'\')" id="add_personell">hello</a>');  
  if ($rc) $json .= ",";
  $json .= "\n{";
  $json .= "id:'".$row['nr']."',";
  $json .= "cell:['".$row['name_last']."'";
  $json .= ",'".$add_personell."']";
  $json .= "}";
  $rc = true; 
}
}
else {
  if ($rc) $json .= ",";
  $json .= "\n{";
  $json .= "id:'0',";
  $json .= "cell:['No Entry'";
  $json .= ",' ']";
  $json .= "}";
  $rc = true; 
}


$json .= "]\n";
$json .= "}";
echo $json;
 
?>

