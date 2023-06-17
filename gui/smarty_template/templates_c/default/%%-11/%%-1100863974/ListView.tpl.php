<?php /* Smarty version 2.6.0, created on 2017-11-28 16:37:23
         compiled from ../../../modules/dashboard/dashlets/PatientRadioResults/templates/ListView.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/PatientRadioResults/templates/ListView.tpl', 13, false),)), $this); ?>
<div id="px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", {
	id:'px-obj-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientRadioResults/Listgen.php",
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
			label: "Request Date",
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
			label: "Service(s) requested",
			width: 150,
			sortable: false,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "dicom",
			label: "With Dicom",
			width: 80,
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
			width: 85,
			sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];

                if(row['permission'] == 1){
                    image = "../../gui/img/common/default/film.png";
                    action = 'PatientRadio_OpenResult(\''+row["refno"]+'\',\''+row["pid"]+'\'); loadPacsViewer(\''+row["url"]+'\'); return false;';
                }else{
                    image = "../../gui/img/common/default/findnew.gif";
                    action = 'PatientRadio_OpenResultHtml(\''+row["refno"]+'\',\''+row["pid"]+'\'); loadPacsViewer(\''+row["url"]+'\'); return false;';
                }

				return '<button class="button" onclick="'+action+'"><img class="link" src="'+image+'" />Results</button>';
			}
		}
	]
});

function PatientRadio_OpenResult(refno, pid) {
	var options = {
		url: '../../modules/radiology/certificates/seg-radio-unified-report-pdf.php',
		width: 700,
		height: 450,
		data: {
			batch_nr:refno,
			pid:pid
			}
	};
	Dashboard.openWindow(options);
}

function PatientRadio_OpenResultHtml(refno, pid){
    var options = {
        url: '../../modules/radiology/seg-radio-unified-html.php',
        width: 700,
		height: 450,
        data: {
            batch_nr:refno,
            pid:pid
        }
    };
    Dashboard.openWindow(options);
}

//added by VAN 10/10/2014
//PACS
function closePacsViewer(){
	pacsviewer.close();
}

function loadPacsViewer(url){
	if (url!='null')
		pacsviewer = window.open(url,"pacsviewer","width=800,height=550,top=150,left=200,menubar=no,resizable=yes,scrollbars=yes");
}//========================

</script>