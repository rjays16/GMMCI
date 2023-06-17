<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
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
$title = "Laboratory Results";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Onload Javascript code
$smarty->assign('sOnLoadJs','onLoad="DisabledSearch();"');

include_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj = new SegLab;

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

<script type="text/javascript" src="<?= $root_path ?>modules/laboratory/js/seg-lab-result-list.js" ></script>

<script type="text/javascript">

var $J = jQuery.noConflict();

function initialize(){
	load_labresult_list();
}

function populateResult(){
    alert('here');
}


function load_labresult_list(){
    ListGen.create($('labresult-list'),{
        id: 'reportlist',
		url: '<?=$root_path?>modules/laboratory/ajax/ajax_labresult_list.php',
		params: {'search':$J('#Search').val()},
		width: 1000,
		height: 'auto',
		autoLoad: true,
		effects: true,
		rowHeight: 30,
		columnModel: [
            {
                name: 'refno',
                label: 'Batch No.',
                width: 80,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'normal'
                }
            },
			{
				name: 'patient',
				label: 'Patient\'s Name',
				width: 170,
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
                name: 'pid',
                label: 'HRN',
                width: 70,
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
                name: 'age',
                label: 'Age',
                width: 40,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'normal'
                }
            },
            {
                name: 'sex',
                label: 'Sex',
                width: 30,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'normal'
                }
            },
            {
                name: 'patient_type',
                label: 'Patient Type',
                width: 80,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'normal'
                }
            },
            {
                name: 'location',
                label: 'Location',
                width: 140,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'normal'
                }
            },
            {
                name: 'service',
                label: 'Service(s) requested',
                width: 200,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'normal'
                }
            },
            {
                name: 'date',
                label: 'Result Received',
                width: 100,
                sortable: false,
                //sorting: ListGen.SORTING.asc,
                styles: {
                    color: '#000000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'normal'
                }
            },
			{
				name: 'options',
				label: 'Result',
				width: 100,
				sortable: false,
                render: function(data, index){
					var row = data[index];
						return '<div align="center">'+
								   '<span>'+
                                        '<img id="rptbtn" onclick="viewResult(\''+row['pid']+'\',\''+row['refno']+'\',\''+row['group_id']+'\',\''+row['code']+'\');" title="Lab Result in PDF Format!" src="<?=$root_path?>img/icons/pdf_icon.gif" style="cursor:pointer">'+
                                    '</span>'+
                                '</div>';

				}
			}
		]
	});
}

function refreshreportlist(rep){
	alert(rep)
	$('labresult-list').list.refresh();
}

function searchSource(){
	 $('labresult-list').list.params={'search':$J('#Search').val()};
	 $('labresult-list').list.refresh();
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
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

ob_start();
$sTemp='';

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/seg-lab-result-list.tpl');
$smarty->display('common/mainframe.tpl');

?>