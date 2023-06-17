<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_repgen.php');
$repgen_obj=new RepGen;

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json; charset=ISO-8859-1");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$rep_category = $_REQUEST['rep_category'];
$search = $_REQUEST['search'];
$dept_nr = $_REQUEST['dept_nr'];

#echo "cat, search, dept_nr = ".$rep_category." == ".$search." == ".$dept_nr;

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'rep_group' => 'rep_group',
    'rep_name' => 'rep_name',
);

$sortName = $_REQUEST['sort'];
#echo "<br><br>sortn = ".$sortName."<br><br>";

if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'rep_group';

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

$sql = "SELECT SQL_CALC_FOUND_ROWS r.* FROM seg_rep_templates_registry r 
		 WHERE is_active=1 AND rep_dept_nr=".$db->qstr($dept_nr);

if($search) {
    $sql.=" AND rep_name LIKE '%".$search."%'";
}

if($rep_category) {
    $sql.=" AND rep_category = ".$db->qstr($rep_category);
}

if($sort_sql) {
	#$sql.=" ORDER BY {$sort_sql} ";
    $sql.=" ORDER BY rep_group, rep_name";
}
if($maxRows) {
	$sql.=" LIMIT $offset, $maxRows";
}
$result = $db->Execute($sql);
#echo "ss = ".$sql;

if ($result !== FALSE) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
 	while ($row = $result->FetchRow()) {
 		$sTemp = '';
 		$param_ids=&$repgen_obj->getReportParamById($row['report_id']);
	 	if(is_object($param_ids)){
	 		while($row_param_ids=$param_ids->FetchRow()) {
	 			$parameter=&$repgen_obj->getReportParameter2($row_param_ids['param_id']);
		 		if(is_object($parameter)){
				    while($row_param=$parameter->FetchRow()) {
				    	
				        $sTemp = $sTemp.'<b>'.$row_param['parameter'].'</b>';
				        switch ($row_param['param_type']){
				           case 'option' :  
				                            $option_arr = explode(",", $row_param['choices']);
				                            $options="<option value=''>-Select ".$row_param['parameter']."-</option>";
				                            if (count($option_arr)){
				                                while (list($key,$val) = each($option_arr))  {
				                                    $val = substr(trim($val),0,strlen(trim($val))-1);
				                                    $val = substr(trim($val),1);
				                                    $val_arr = explode("-", $val);
				                                    $options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
				                                }
				                            }
				                            
				                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">
				                                     '.$options.'</select></span>';
				                            break;
				           case 'time' :    
				                            $jav =  '<script type="text/javascript">
				                                        jQuery(function($){
				                                            $J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from").mask("99:99");
				                                        });
				                                        jQuery(function($){
				                                            $J("#'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to").mask("99:99");
				                                        });
				                                    </script>';
				                            $param = $jav.'<span id="'.$row_param['param_id'].'">
				                                                <input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_from" type="text" value="">
				                                                <select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_from">
				                                                    <option value = "AM">AM</option>
				                                                    <option value = "PM">PM</option>
				                                                </select>
				                                                To
				                                                <input class="segInput" maxlength="5" size="2" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_to" type="text" value="">
				                                                <select class="segInput" name = "'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'_meridian_to">
				                                                    <option value = "AM">AM</option>
				                                                    <option value = "PM">PM</option>
				                                                </select>
				                                           </span>';
				                            break; 
				           case 'date' :    
				                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" maxlength="10" size="8" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" value=""></span>';
				                            break; 
				           
				           case 'boolean' : 
				                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value="1"></span>';
				                            break;
				           case 'radio' :   
				                            $param = '<span id="'.$row_param['param_id'].'"><input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="radio" value="1"></span>';;
				                            break;                                                                     
				           case 'sql' :     
				                            $option_sql=$db->Execute($row_param['choices']);
				                            $options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
				                            if (is_object($option_sql)){
				                                while ($row_option=$option_sql->FetchRow()) {
				                                    $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
				                                }
				                            }
				                            
				                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"></span><br/> 
				                                     '.$options.'</select>';
				                            break;
				           case 'text' :   

				                            $param = '<span id="'.$row_param['param_id'].'"><br/>Search by code&nbsp<input name="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_paramCheck_'.$row_param['param_id'].'" type="checkbox" value="">
				                                      <br/>
				                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
				                                      </span>';
				                            break;
				           case 'autocomplete' :
				           					    
				                            $param = '<br/><span id="'.$row_param['param_id'].'">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param1_'.$row_param['param_id'].'" type="text" onblur="clearNr(this.id);" style="width: 300px" value="">
				                                      </span>';
				                            break;                 
				           case 'checkbox' :     
				                            $param = '<span id="'.$row_param['param_id'].'"><input name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="checkbox" value=""></span>';
				                            break;
				                            
				           case 'textbox' :    
				                            $param = '<br/><span id="'.$row_param['param_id'].'">
				                                        <input class="segInput" name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
				                                      </span>';
				                            break;
				            //added by daryl
				           case 'month_date' :  
								           for ($m=1; $m<=12; $m++) {
										     $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
										     $month_date .= $m."-".$month.',';
										     }
										   
				                            $option_arr = explode(",", $month_date);
				                            $options="<option value=''>-Select ".$row_param['parameter']."-</option>";
				                            if (count($option_arr)){
				                                while (list($key,$val) = each($option_arr))  {
				                                    $val = substr(trim($val),0,strlen(trim($val)));
				                                    // $val = substr(trim($val),1);
				                                    $val_arr = explode("-", $val);
				                                    $options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
				                                }
				                            }
				                            
				                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">
				                                     '.$options.'</select></span>';
				                            break;

						      case 'year_date' :  
						    		$starting_year  = "2015";
									$ending_year    = "2025";

									for($starting_year; $starting_year <= $ending_year; $starting_year++) {
									    $year_date .= $starting_year.'-'.$starting_year.',';
									}
				                            $option_arr = explode(",", $year_date);
				                            $options="<option value=''>-Select ".$row_param['parameter']."-</option>";
				                            if (count($option_arr)){
				                                while (list($key,$val) = each($option_arr))  {
				                                    $val = substr(trim($val),0,strlen(trim($val)));
				                                    // $val = substr(trim($val),1);
				                                    $val_arr = explode("-", $val);
				                                    $options.='<option value="'.$val_arr[0].'">'.$val_arr[1].'</option>';
				                                }
				                            }
				                            
				                            $param = '<br/><span id="'.$row_param['param_id'].'"><select name="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" id="'.$row['rep_script'].'_param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">
				                                     '.$options.'</select></span>';
				                            break;            
				            //ended by daryl
				                                                                                                                                                    
				           default :        break;                   
				        }
				        
				        $sTemp = $sTemp.$param.'<br/>';
				        
				    }
				}	
	 		}
	 	}

		$data[] = array(
            'report_id' => trim($row['report_id']),
            'rep_script' => trim($row['rep_script']),
			'rep_group' => trim($row['rep_group']),
			'rep_name' => trim($row['rep_name']),
            'with_template' => trim($row['with_template']),
            'query_in_jasper' => trim($row['query_in_jasper']),
            'parameter' => $sTemp,
            'is_have_param' => ($sTemp!=null) ? 1 : 0,
		);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);