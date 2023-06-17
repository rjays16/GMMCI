<?php /* Smarty version 2.6.0, created on 2017-12-18 13:27:47
         compiled from ../../../modules/dashboard/dashlets/PatientMedicalCert/templates/ListView.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/PatientMedicalCert/templates/ListView.tpl', 13, false),)), $this); ?>
<div id="px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", {
	id:'px-obj-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientMedicalCert/Listgen.php",
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
			label: "Date Prepared",
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
			name: "case_no",
			label: "Case Number",
			width: 100,
			sorting: ListGen.SORTING.none,
			// sortable: true,
			visible: true,
			styles: {
				fontSize: "12px"
			}
		},
		{
			name: "DateAdmitted",
			label: "Date Admitted",
			width: 100,
			sorting: ListGen.SORTING.none,
			// sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#000080"
			}
		},
		{
			name: "department",
			label: "Department",
			width: 100,
			sorting: ListGen.SORTING.none,
			// sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "prepared",
			label: "Prepared by",
			width: 116,
			sorting: ListGen.SORTING.none,
			// sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
			}
		},
		{
			name: "",
			label: '',
			width: 30,
			// sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];
               
                    image = "../../images/cashier_edit.gif";
                    action = 'openMedCert(\''+row["case_no"]+'\',\''+row["cert_nr"]+'\');return false;';           
               		return '<img class="link" onclick="'+action+'" src="'+image+'">';               		
			}			
		},
		{
			name: "",
			label: '',
			width: 30,
			// sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];
              
                    image1 = "../../images/cashier_delete_small.gif";
                    action1 = 'deleteMedCert(\''+row["case_no"]+'\',\''+row["cert_nr"]+'\');return false;';

               		return '<img class="link" onclick="'+action1+'" src="'+image1+'">';
			}			
		}
	]
});

function openMedCert(case_no,cert_nr)
{
	var url = '../../modules/registration_admission/certificates/cert_med_interface.php?case_no='+case_no+'&cert_nr='+cert_nr+'&from=dashboard';
	$J('<div></div>').html('<iframe style="width:100%;height:100%" src="'+url+'"></iframe>').dialog({
		title: "Medical Certificate",
		width:850,
		height:450,
		resizable: false,
		draggable: false
	});
}

//Added by borj: 1-19-15
function deleteMedCert(case_no, cert_nr)
{	
	var answer = confirm("Cannot delete Medical Certificate. Please contact Medical Records.");
}

//"Comment Syntax" reason: For safety purposes as stated by medical records.
// function deleteMedCert(case_no, cert_nr)
// {	
// 	var answer = confirm("Are you sure you want to delete the medical certificate with a certificate no. "+(cert_nr)+"?");
// 	if (answer)
// 	{
// 		Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "deleteCertificate", {
// 			case_no : case_no,
// 			cert_nr : cert_nr
// 		});
// 	}

// }
//end borj

function MedCertRefresh(){
	$('px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').list.refresh();
}

</script>