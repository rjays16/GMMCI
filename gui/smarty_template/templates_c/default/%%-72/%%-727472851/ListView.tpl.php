<?php /* Smarty version 2.6.0, created on 2017-11-28 16:37:22
         compiled from ../../../modules/dashboard/dashlets/PatientList/templates/ListView.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/PatientList/templates/ListView.tpl', 16, false),)), $this); ?>
<div id="PatientList-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("PatientList-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", {
	id:'PatientListObject-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientList/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	params: {
		filter: '<?php echo $this->_tpl_vars['settings']['filter']; ?>
'
	},
	pageStat: 'Items {from}-{to} of {total}',
	effects: true,
	autoLoad: true,
	maxRows: <?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['pageSize'])) ? $this->_run_mod_handler('default', true, $_tmp, '5') : smarty_modifier_default($_tmp, '5')); ?>
,
	rowHeight: 32,
	layout: [
		['#pagestat', '#first', '#prev', '#next', '#last'],
		['<div align="left" style="padding:2px">'+
				'<input type="text" class="input" size="20" value="<?php echo $this->_tpl_vars['session']['search']; ?>
" onkeyup="if (event.keyCode==$J.ui.keyCode.ENTER) $J(this).next().click()" /> '+
				'<button class="lg-toolbar-button" '+
					'onclick="$(\'PatientList-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
\').list.params.key = $(this).previous().value; $(\'PatientList-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
\').list.refresh();return false;">'+
				'<img src="../../gui/img/common/default/magnifier.png"/>Search</button>'+
			'</div>'
		],
		['#tbody']
	],
	columnModel:[
		{
			name: "date",
			label: "Case Date",
			width: 80,
			styles: {
				color: "#000080",
				textAlign: "center"
			},
			sorting: ListGen.SORTING.desc,
			sortable: true,
			visible: true
		},
		{
			name: "name",
			label: "Patient name",
			width: 140,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			render: function(data, index)
			{
				var row = data[index], selected = (row['encounter'] == row['active']);
				return '<div style="color:#2d2d2d; font-size:11px">'+row['name']+'</div>'+
					'<div style="font-size:10px; color: #c00000">'+row['confinement']+'</div>';
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
				var selected = (row['encounter'] == row['active']);
				if (selected)
					return '<button class="button" onclick="return false;" disabled="disabled"><img src="../../gui/img/common/default/emoticon_smile.png" />Active</button>';
				else
					return '<button class="button" onclick="Dashboard.dashlets.sendAction(\'<?php echo $this->_tpl_vars['dashlet']['id']; ?>
\', \'openFile\', {file:\''+row['encounter']+'\'}); return false;"><img src="../../gui/img/common/default/accept.png" />Select</button>';
			}
		},
		// added by : syboy 06/13/2015
		{
			name : "is_discharged",
			label : 'Status',
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				textAlign: "center"
			},
			render: function(data, index)
			{
				var row = data[index], selected = (row['encounter'] == row['active']);
				if (row['is_discharged'] == 1) {
					return '<div style="color:#2d2d2d; font-size:11px">Discharged</div>';
				} else {
					return '<div style="color:#2d2d2d; font-size:11px"></div>';
				}
			}
		}
		// end
	]
});
</script>