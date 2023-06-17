<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/reports/ajax/report.common.php");
require_once($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);

$local_user='ck_ic_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];

$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$date_format2 = '%m/%d/%Y';

$thisfile='report_launcher.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Report Launcher";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Onload Javascript code
$smarty->assign('sOnLoadJs','onLoad="preset();"');

require_once($root_path.'include/care_api_classes/class_repgen.php');
$repgen_obj=new RepGen;

include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

#temp, will be updated later
#get the dept nr
$dept = $_GET['dept_nr'];
 
# Collect javascript code
ob_start();
# Load the javascript code  
?>

<script language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.blockUI.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>

<link rel="stylesheet" href="<?= $root_path ?>css/seg/wirecake.css" type="text/css"/>
<link rel="stylesheet" href="<?= $root_path ?>css/seg/wireframe.css" type="text/css"/>
<link rel="stylesheet" href="<?= $root_path ?>css/seg/hiscss.css" type="text/css"/>

<script type="text/javascript" src="<?= $root_path ?>modules/reports/js/report_launcher.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/dateformat.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/datefuncs.js" ></script>

<style>
    .ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
}
/* IE 6 doesn't support max-height
* we use height instead, but this forces the menu to always be this tall
*/
    * html .ui-autocomplete {
    height: 100px;
}
</style> 

<script type="text/javascript">

var $J = jQuery.noConflict();

jQuery(function($){
     $J("#datefrom").mask("99/99/9999");
});

jQuery(function($){
     $J("#dateto").mask("99/99/9999");
});

function initialize(){
	load_report_list();
}


 /*$(document).ready(function() { 
 
        $J('#rptbtn').click(function() { 
            // update the block message 
            $.blockUI({ message: "<h1>Generating the report is in progress...</h1>" }); 
 
            $.ajax({ 
                url: 'wait.php', 
                cache: false, 
                complete: function() { 
                    // unblock when remote call returns 
                    $.unblockUI(); 
                } 
            });
            alert('yes???');
        }); 
 });*/


/* $J(function() {
        
        // for ICD
        if ($J( "#param1_icd10" )){
            $J( "#param1_icd10" ).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                $J.getJSON( "ajax/ajax_ICD10.php?iscode="+$('paramCheck_icd10').checked+"", request, function( data, status, xhr ) {
                    response( data );
                });
            },
            select: function( event, ui ) {
                //alert(ui.item.id);
                $('param_icd10').value = ui.item.id;
            }
            });
            
            $J('#paramCheck_icd10').click(function(){
                $('param1_icd10').value = '';
                $('param1_icd10').focus();    
            });
        }   
        //---------------
        
        
        // for ICP
        if ($J( "#param1_icpm" )){
            $J( "#param1_icpm" ).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                $J.getJSON( "ajax/ajax_ICPM.php?iscode="+$('paramCheck_icpm').checked+"", request, function( data, status, xhr ) {
                    response( data );
                });
            },
            select: function( event, ui ) {
                //alert(ui.item.id);
                $('param_icpm').value = ui.item.id;
            }
            });
            
            $J('#paramCheck_icpm').click(function(){
                $('param1_icpm').value = '';
                $('param1_icpm').focus();    
            });    
        }   
        // ---------------
        
        //added by VAN 03-02-2013
        //DEMOGRAPHICS
        //for province
        if ($J( "#param1_provnr" )){
            $J( "#param1_provnr" ).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                $J.getJSON( "ajax/ajax_Province.php", request, function( data, status, xhr ) {
                    response( data );
                });
            },
            select: function( event, ui ) {
                $('param_provnr').value = ui.item.id;
            }
            });
        }
        
        //for municipal and city
        if ($J( "#param1_munnr" )){
            $J( "#param1_munnr" ).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                $J.getJSON( "ajax/ajax_Municipality.php?prov_nr="+$J('#param_provnr').val(), request, function( data, status, xhr ) {
                    response( data );
                });
            },
            select: function( event, ui ) {
                $('param_munnr').value = ui.item.id;
                xajax_getProvince(ui.item.id);
            }
            });
        }
        
        //for Barangay
        if ($J( "#param1_brgynr" )){
            $J( "#param1_brgynr" ).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                $J.getJSON( "ajax/ajax_Barangay.php?prov_nr="+$J('#param_provnr').val()+"&mun_nr="+$J('#param_munnr').val(), request, function( data, status, xhr ) {
                    response( data );
                });
            },
            select: function( event, ui ) {
                $('param_brgynr').value = ui.item.id;
                xajax_getMuniCityandProv(ui.item.id);
            }
            });
        }
});*/


function load_report_list(){
    ListGen.create($('report-list'),{
        id: 'reportlist',
		url: '<?=$root_path?>modules/reports/ajax/ajax_report_list.php',
		params: {'dept_nr':$J('#SourceDepartment').val(),'rep_category':$J('#SourceReportcategory').val(), 'search':$J('#Search').val()},
		width: 800,
		height: 'auto',
		autoLoad: true,
		effects: true,
		rowHeight: 30,
		columnModel: [
			{
				name: 'rep_name',
				label: 'Report Name',
				width: 550,
				sortable: false,
                //sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'left',
					fontWeight: 'bold'
				}
			},
            {
                name: 'rep_group',
                label: 'Report Group',
                width: 170,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'bold'
                }
            },
			{
				name: 'options',
				label: 'Action',
				width: 80,
				sortable: false,
                render: function(data, index){
					var row = data[index];
    
                    if (row['rep_script'] == "MMHR")
                        var span_hide = 'style="display:none"';

						return '<div align="center">'+
								   '<span>'+
                                        '<img id="rptbtn" onclick="genReport(\''+row['rep_script']+'\',\''+row['with_template']+'\',\''+row['query_in_jasper']+'\',\'pdf\',\''+row['is_have_param']+'\');" title="PDF Format!" src="<?=$root_path?>img/icons/preview-icon.png" style="cursor:pointer">'+
                                   '</span>&nbsp;'+
                                   '<span '+span_hide+'>'+
                                        '<img id="rptbtn" onclick="genReport(\''+row['rep_script']+'\',\''+row['with_template']+'\',\''+row['query_in_jasper']+'\',\'excel\',\''+row['is_have_param']+'\');" title="Excel Format!" src="<?=$root_path?>img/icons/excel32.png" style="cursor:pointer">'+
                                    '</span>'+ 
                                '</div>'+
                                '<div class="segPanel" id="addParameters'+row['rep_script']+'" style="display:none" align="left">'+
                                    '<div align="center" style="overflow:hidden">'+
                                        '<table class="data-grid rounded-borders-bottom" id="parameter_list'+row['rep_script']+'">'+
                                            '<tbody>'+
                                                '<tr height="100px" id="params">'+   
                                                    '<td width="33%">'+row['parameter']+'</td>'+ 
                                                '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>';
				}
			}
		]
	});
}

function refreshreportlist(rep){
	$('report-list').list.refresh();
}

function searchSource(){
	 $('report-list').list.params={'dept_nr':$J('#SourceDepartment').val(),'rep_category':$J('#SourceReportcategory').val(), 'search':$J('#Search').val()};
	 $('report-list').list.refresh();
}

//load jquery dom
$J(function() {
		$J("#tabs").tabs({
			selected:0,
		});
});

document.observe('dom:loaded', initialize);

</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

#report category
$category=&$repgen_obj->getReportCategory();
$category_option="<option value=''>-Select a Category-</option>";
if (is_object($category)){
    while ($row_category=$category->FetchRow()) {
        $category_option.='<option value="'.$row_category['code'].'">'.$row_category['name'].'</option>';
    }
}    
$category_selection = '<select name="SourceReportcategory" id="SourceReportcategory" onchange="getReports();" style="width: 420px" class="segInput">
                        '.$category_option.'
                    </select>';
$smarty->assign('category_selection', $category_selection);

#department
if($dept)
    $disabled = 'disabled';

$department=&$dept_obj->getAllDeptObject();
$department_option="<option value=''>-Select a Department-</option>";
if (is_object($department)){
    while ($row_department=$department->FetchRow()) {
        $selected='';
        if ($dept==$row_department['nr'])
            $selected='selected';
        $department_option.='<option '.$selected.' value="'.$row_department['nr'].'">'.$row_department['name_formal'].'</option>';
    }
}
$department_selection = '<select '.$disabled.' name="SourceDepartment" id="SourceDepartment" onchange="getReports();" style="width: 420px" class="segInput">
                        '.$department_option.'
                    </select>';
$smarty->assign('department_selection', $department_selection);

#search key
$smarty->assign('search_input', '<input type="text" id="Search" maxlength="255" size="50" name="Search" class="segInput">');

#report period
$datefrom_text = '<div class="input text">
                    <div style="display:inline-block">
                        <input type="text" maxlength="10" size="8" id="datefrom" name="datefrom" class="segInput">
                        <br>
                        <span style="margin-left:2px; font:normal 10px Tahoma; color:#447BC4" class="small">[mm/dd/yyyy]</span>
                    </div>
                    <button id="datefrom-trigger" style="margin-left: 4px; cursor: pointer;" onclick="return false" title="Select Start Date">
                        <span class="icon calendar"></span>Select
                    </button>
                  </div>
                  ';

$dateto_text = '<div class="input text">
                    <div style="display:inline-block">
                        <input type="text" maxlength="10" size="8" id="dateto" name="dateto" class="segInput">
                        <br>
                        <span style="margin-left:2px; font:normal 10px Tahoma; color:#447BC4" class="small">[mm/dd/yyyy]</span>
                    </div>    
                        <button id="dateto-trigger" style="margin-left: 4px; cursor: pointer;" onclick="return false" title="Select End Date">
                            <span class="icon calendar"></span>
                                Select
                            </button>
                 </div>
                 ';

$jsCalScript  = '<script type="text/javascript">
                    now = new Date();
                    Calendar.setup ({
                            inputField: "datefrom",
                            dateFormat: "'.$date_format2.'",
                            trigger: "datefrom-trigger",
                            showTime: false,
                            fdow: 0,
                            /*max : Calendar.dateToInt(now),*/
                            onSelect: function() { this.hide() }
                    });

                    Calendar.setup (
                    {
                            inputField: "dateto",
                            dateFormat: "'.$date_format2.'",
                            trigger: "dateto-trigger",
                            showTime: false,
                            fdow: 0,
                            /*max : Calendar.dateToInt(now),*/
                            onSelect: function() { this.hide() }
                    }
                    );
                </script>
                ';

$smarty->assign('jsCalendarSetup', $jsCalScript.'<input type="hidden" id="date_format" name="date_format" value="'.$date_format.'">');
  
$smarty->assign('datefrom_fld', $datefrom_text);
$smarty->assign('dateto_fld', $dateto_text);
     
#Commented By Jarel 05/03/2013
#list of parameters
/*$parameter=&$repgen_obj->getReportParameter();
$no_param_list = $repgen_obj->count;

if(is_object($parameter)){
    $sTemp = '';
    $count=0;
    $limit_no = floor($no_param_list/3);
    $limit_no2 = floor($no_param_list/3)*2;
    
    while($row_param=$parameter->FetchRow()) {
        $sTemp = $sTemp.'<b id="T'.$row_param['param_id'].'">'.$row_param['parameter'].'</b>  ';
        
        #identify the attribute tag for a certain parameter
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
                            
                            $param = '<br/><div id="'.$row_param['param_id'].'"><select name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" style="width: 300px" class="segInput">
                                     '.$options.'</select></div>';
                            break;
           case 'time' :    
                            $jav =  '<script type="text/javascript">
                                        jQuery(function($){
                                            $J("#param_'.$row_param['param_id'].'_from").mask("99:99");
                                        });
                                        jQuery(function($){
                                            $J("#param_'.$row_param['param_id'].'_to").mask("99:99");
                                        });
                                    </script>';
                            $param = $jav.'<div id="'.$row_param['param_id'].'">
                                                <input class="segInput" maxlength="5" size="2" name="param_'.$row_param['param_id'].'_from" id="param_'.$row_param['param_id'].'_from" type="text" value="">
                                                <select class="segInput" name = "param_'.$row_param['param_id'].'_meridian_from" id="param_'.$row_param['param_id'].'_meridian_from">
                                                    <option value = "AM">AM</option>
                                                    <option value = "PM">PM</option>
                                                </select>
                                                To
                                                <input class="segInput" maxlength="5" size="2" name="param_'.$row_param['param_id'].'_to" id="param_'.$row_param['param_id'].'_to" type="text" value="">
                                                <select class="segInput" name = "param_'.$row_param['param_id'].'_meridian_to" id="param_'.$row_param['param_id'].'_meridian_to">
                                                    <option value = "AM">AM</option>
                                                    <option value = "PM">PM</option>
                                                </select>
                                           </div>';
                            break; 
           case 'date' :    
                            $param = '<div id="'.$row_param['param_id'].'"><input class="segInput" maxlength="10" size="8" name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" type="text" value=""></div>';
                            break; 
           
           case 'boolean' : 
                            $param = '<div id="'.$row_param['param_id'].'"><input class="segInput" name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" type="checkbox" value="1"></div>';
                            break;
           case 'radio' :   
                            $param = '<div id="'.$row_param['param_id'].'"><input class="segInput" name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" type="radio" value="1"></div>';;
                            break;                                                                     
           case 'sql' :     
                            $option_sql=$db->Execute($row_param['choices']);
                            $options="<option value=''>-Select a ".$row_param['parameter']."-</option>";
                            if (is_object($option_sql)){
                                while ($row_option=$option_sql->FetchRow()) {
                                    $options.='<option value="'.$row_option['id'].'">'.$row_option['id'].'-'.$row_option['namedesc'].'</option>';
                                }
                            }
                            
                            $param = '<br/><div id="'.$row_param['param_id'].'"><select name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" style="width: 300px" class="segInput"><br/> 
                                     '.$options.'</select></div>';
                            break;
           case 'text' :    
                            $param = '<br/><div id="'.$row_param['param_id'].'">Search by code&nbsp<input name="paramCheck_'.$row_param['param_id'].'" id="paramCheck_'.$row_param['param_id'].'" type="checkbox" value="">
                                      <br/>
                                        <input class="segInput" name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
                                        <input class="segInput" name="param1_'.$row_param['param_id'].'" id="param1_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
                                      </div>';
                            break;
           case 'autocomplete' :    
                            $param = '<br/><div id="'.$row_param['param_id'].'">
                                        <input class="segInput" name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" type="hidden" style="width: 300px" value="">
                                        <input class="segInput" name="param1_'.$row_param['param_id'].'" id="Tparam1_'.$row_param['param_id'].'" type="text" onblur="clearNr(this.id);" style="width: 300px" value="">
                                      </div>';
                            break;                 
           case 'checkbox' :     
                            $param = '<div id="'.$row_param['param_id'].'"><input name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" type="checkbox" value=""></div>';
                            break;
                            
           case 'textbox' :    
                            $param = '<br/><div id="'.$row_param['param_id'].'">
                                        <input class="segInput" name="param_'.$row_param['param_id'].'" id="param_'.$row_param['param_id'].'" type="text" style="width: 300px" value="">
                                      </div>';
                            break;
                                                                                                                                                    
           default :        break;                   
        }
        
        $sTemp = $sTemp.$param.'<br>';
        if($count<=$limit_no){
            $paramRow1 =$sTemp;
            if($count==$limit_no){$sTemp='';}
        }else if($count<=($limit_no2)){
            $paramRow2 =$sTemp;
            if($count==$limit_no2){$sTemp='';}   
        }else{ 
            $paramRow3 =$sTemp; 
        }
            $count++;
    }
}*/

//$smarty->assign('paramRow1',$paramRow1.$paramRow2.$paramRow3);
//$smarty->assign('paramRow2',$paramRow2);
//$smarty->assign('paramRow3',$paramRow3);        

ob_start();
$sTemp='';

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','reports/report_launcher.tpl');
$smarty->display('common/mainframe.tpl');

?>