<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');   



   
function countRec($where, $where2) {
       global $db;
    $sql = "SELECT (SELECT count(*) as total FROM seg_other_services AS s
LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id $where)
+
(SELECT count(*) as total FROM seg_otherhosp_services AS s 
LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id
 WHERE (s.status NOT IN ('deleted','hidden','inactive','void')) $where2) as total";
   
   
    $result = $db->Execute($sql);
    $row = $result->FetchRow();
    return $row[0];    
}



global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
$table_name = $_GET['table'];
if (!$sortname) $sortname = 'name';
if (!$sortorder) $sortorder = 'desc';

if ($sortname == 'name') {
  $sortname = 'name';
}
$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];
//$qtype = $_POST['qtype'];
$where = "";
$where2 = "";
if ($query) $where = "WHERE name LIKE '$query%'";
if ($query) $where2 = " AND name LIKE '$query%'";
$total = countRec($where, $where2);   


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

 $query = "(SELECT 1 as source, s.name,s.name_short,s.price,s.service_code AS code,s.description,t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,s.lockflag
FROM seg_other_services AS s
LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id $where)
union
(SELECT 2 as source, s.name,'',s.price,s.service_code AS code,'',t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,''
FROM seg_otherhosp_services AS s
LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id
 WHERE (s.status NOT IN ('deleted','hidden','inactive','void')) $where2)  $sort $limit";
    

   $result = $db->Execute($query);
  
   while($row = $result->FetchRow()) {
     $js = "javascript:xajax_add_misc(\'$table_name\', \'{$row['code']}\', \'{$row['source']}\', \'{$row['account_type']}\')";
     $select = '<input type="button" value="Select" onclick="'.$js.'"/>';
     $description = ($row['description'] != '') ? $row['description'] : 'No description';
     if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['code']."',";
    $json .= "cell:['".$row['code']."'";
    $json .= ",'".addslashes($row['name'])."'";
    $json .= ",'".trim($description)."'";
    $json .= ",'".number_format($row['price'], 2, '.', ',')."'";
    $json .= ",'".$select."']";
    $json .= "}";
    $rc = true;                    

   }


$json .= "]\n";
$json .= "}";
echo $json;
 
?>



