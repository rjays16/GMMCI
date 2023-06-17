<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/care_api_classes/prescription/class_prescription_writer.php');

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$template_name = $_REQUEST['name'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'drug_name'=>'item_name',
	'template_name'=>'name'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'template_name';

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

$pres_obj = new SegPrescription();
$result = $pres_obj->getTemplates($template_name, $offset, $maxRows, $sort_sql);
$total = $pres_obj->FoundRows();
	/*echo "<pre>";
		print_r($pres_obj->sql);
		echo "</pre>";  */
$has_license = $pres_obj->isLicensedPersonell();
$data = array();
if ($result !== FALSE) {

	while ($row = $result->FetchRow()) {

		switch($row['period_interval'])
		{
			case 'D': $interval="Days"; break;
			case 'W': $interval="Weeks"; break;
			case 'M': $interval="Months"; break;
            default: $interval = '';
		}
        
        if (!empty($interval)) {
            $drugPeriod = $row['period_count']." ".$interval;
        }

        if($row['generic']){
            $dr_name = $row['generic'];
        }else{
            $dr_name = $row['item_name'];
        }
        
		$data[] = array(
			'template_name' => '<span style="font-size: 11px; font-weight: bold">' . strtoupper($row['name']) . '</span>',
			'template_owner'=>$row['owner'],
			'drug_name'=>"<span style='font-size: 11px; color:".($row['is_restricted']==1?'#ff0000':'#000000')."'>".$dr_name."</span>",
			'drug_qty'=>number_format($row['quantity'],0),
			'drug_dosage'=>$row['dosage'],
			'drug_period'=>@$drugPeriod,
			'options'=>'<button class="segButton" onclick="addTemplate(\''.$row['item_code'].'\',\''.trim($row['item_name']).'\',
				\''.number_format($row['quantity'],0).'\',\''.$row['dosage'].'\',\''.$row['period_count'].'\',\''.$row['period_interval'].'\',
				\''.$row['generic'].'\',\''.$row['availability'].'\',\''.$row['is_restricted'].'\',\''.$has_license.'\');
				return false;"><img src="../../gui/img/common/default/pill_add.png"/>Add</button>' .
                '<button class="segButton" onclick="if (confirm(\'Do you wish to delete this template?\')) xajax.call(\'deleteTemplate\', { parameters: [\'' . $row['id'] . '\'] }); return false;"><img src="../../gui/img/common/default/delete.png"/>X</button>'
		);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

/**
* Convert data to JSON and print
*
*/

$json = new Services_JSON;
print $json->encode($response);