<?php /* Smarty version 2.6.0, created on 2017-11-28 16:37:24
         compiled from ../../../modules/dashboard/dashlets/PatientLabResults/templates/ListView.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/PatientLabResults/templates/ListView.tpl', 13, false),)), $this); ?>
<div id="px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", {
	id:'px-obj-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientLabResults/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: <?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['pageSize'])) ? $this->_run_mod_handler('default', true, $_tmp, '5') : smarty_modifier_default($_tmp, '5')); ?>
,
	rowHeight: 32,
	layout: [
		//['<h1>My Patients</h1>'],
		['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
		['#thead'],
		['#tbody']
	],
	columnModel:[
		{
			name: "date",
			label: "Result Received",
			width: 100,
			styles: {
				color: "#000080",
				textAlign: "center"
			},
			sorting: ListGen.SORTING.desc,
			sortable: true,
			visible: true
		},
		{
			name: "service",
            //name: "filename",
			label: "Service(s) requested",
            //label: "Filename",
			width: 150,
			sortable: false,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "options",
			label: '',
			width: 80,
            //width: 110,
			sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];
                
                if (row["withresult"]==1)
					//return '<button class="button" onclick="PatientLab_OpenResult(\''+row["refno"]+'\',\''+row["lis_order_no"]+'\',\''+row["pid"]+'\');return false;"><img class="link" src="../../gui/img/common/default/page_white_acrobat.png" />Results</button>';
                    // return '<button class="button" onclick="PatientLab_OpenResult(\''+row["filename"]+'\');return false;"><img class="link" src="../../gui/img/common/default/page_white_acrobat.png" />Results</button>';
                    return '<button class="button" onclick="PatientLab_OpenResult2(\''+row["pid"]+'\',\''+row["lis_order_no"]+'\');return false;"><img class="link" src="../../gui/img/common/default/page_white_acrobat.png" />Results</button>';
                else
                    return '<button class="button" onclick="PromptMsg();" style="cursor:default" title="No Result fetch from the LIS yet . \nOr the result is manually generated. \nPlease ask the Laboratory for the result."><img src="../../images/cashier_view_red.gif" />No Result</button>';
			}
		}
	]
});

function PatientLab_OpenResult(filename) {
	var options = {
		//url: '../../modules/laboratory/seg-lab-result-pdf.php',
        //url: '../../modules/laboratory/seg-lab-result-pdf-link.php',
        //edited by VAN 02-06-2013
        url: '../../modules/laboratory/seg-lab-result-view.php',
		data: {
			filename:filename
			}
	};
	Dashboard.openWindow(options);
}

function PatientLab_OpenResult2(pid,lis_no){
	warn(function(){
		window.open("../../modules/laboratory/seg-lab-report-hl7.php?pid="+pid+"&lis_order_no="+lis_no+"&showBrowser=1","viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
	});
}

function PromptMsg(){
    alert("No Result fetch from the LIS yet . \nOr the result is manually generated. \nPlease ask the Laboratory for the result.");
}

function warn(callback) {
	callback(); //updated by nick 1-15-2016, remove prompt
//	jQuery('<div></div>')
//			.html('<strong style="color: #f00; font-size: 14pt;">To verify the result, please contact the laboratory department.</strong>')
//			.dialog({
//				modal: true,
//				title: 'Warning',
//				position: 'top',
//				buttons: {
//					Ok: function () {
//						callback();
//						jQuery(this).dialog('close');
//					}
//				}
//			});
}

</script>