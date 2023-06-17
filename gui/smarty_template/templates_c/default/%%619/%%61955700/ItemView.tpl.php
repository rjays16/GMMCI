<?php /* Smarty version 2.6.0, created on 2018-01-09 23:50:50
         compiled from ../../../modules/dashboard/dashlets/PatientList/templates/ItemView.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/PatientList/templates/ItemView.tpl', 69, false),)), $this); ?>
<div style="padding:10px">
<div id="PatientList-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:visible; padding:0;"></div>
</div>

<style type="text/css" media="screen">
div.PatientList_item,div.PatientList_item-selected { {
	padding: 4px;
	border: 1px solid #888;
	margin: 4px;

	background: -webkit-gradient(linear, 0 0, 0 bottom, from(#fff), to(#ddd));
	background: -webkit-linear-gradient(bottom, from(#fff), to(#ddd));
	background: -moz-linear-gradient(bottom, from(#fff), to(#ddd));
	background: linear-gradient(bottom, from(#fff), to(#ddd));

	-webkit-box-shadow: 0 0 4px rgba(0,0,0,0.5);
	-moz-box-shadow: 0 0 4px rgba(0,0,0,0.5);
	box-shadow: 0 0 4px rgba(0,0,0,0.5);
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}

div.PatientList_item span {
	color: #3d3d3d;
	text-shadow: 0 1px 0 #fff;
}

div.PatientList_item:hover {
	background: -webkit-gradient(linear, 0 0, 0 bottom, from(#e4eeff), to(#c0dffc));
	background: -webkit-linear-gradient(top, #e4eeff, #c0dffc);
	background: -moz-linear-gradient(top, #e4eeff, #c0dffc);
	background: linear-gradient(top, #e4eeff, #c0dffc);
	border: 1px solid #4f88bd;
}

div.PatientList_item:hover span {
	/*color: #c75f00;*/
	color: #386fa5;
}

div.PatientList_item-selected {
	background: -webkit-gradient(linear, 0 0, 0 bottom, from(#70a2e1), to(#5c83bf));
	background: -webkit-linear-gradient(top, #70a2e1, #5c83bf);
	background: -moz-linear-gradient(top, #70a2e1, #5c83bf);
	background: linear-gradient(top, #70a2e1, #5c83bf);
}

div.PatientList_item-selected span {
	color: #fff;
}
</style>
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
	effects: true,
	params: {
		filter: '<?php echo $this->_tpl_vars['settings']['filter']; ?>
'
	},
	autoLoad: true,
	maxRows: <?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['pageSize'])) ? $this->_run_mod_handler('default', true, $_tmp, '5') : smarty_modifier_default($_tmp, '5')); ?>
,
	rowHeight: 32,
	pageStat: 'Items {from}-{to} of {total}',
	layout: [
		//['<h1>My Patients</h1>'],
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
			name: "name",
			label: "",
			width: "100%",
			styles: {
				textAlign: "center",
				padding: 0
			},
			sorting: ListGen.SORTING.asc,
			sortable: true,
			visible: true,
			render: function(data, index)
			{
				var row = data[index];
				var selected = (row['encounter'] == row['active']);
				return '<div '+(selected ? 'class="PatientList_item-selected"' : 'class="PatientList_item" onclick="Dashboard.dashlets.sendAction(\'<?php echo $this->_tpl_vars['dashlet']['id']; ?>
\', \'openFile\', {file:\''+row['encounter']+'\'}); return false;"')+' style="cursor: pointer;">'+
						'<table border="0" cellpadding="1" cellspacing="1" width="100%">'+
							'<tr>'+
								'<td width="*"><span style="font:bold 11px Tahoma">'+row['date']+'</span></td>'+
								'<td rowspan="3" width="54" align="center" cvalign="top"><img src="../../fotos/photo.php?pid='+row['pid']+'&w=50"></td>'+
							'</tr>'+
							'<tr>'+
								'<td><span style="font:bold 12px Arial">'+row['name']+'</span></td>'+
							'</tr>'+
							'<tr>'+
								'<td><span style="font:bold 12px Arial; color: #c00000">'+row['confinement']+'</span></td>'+
							'</tr>'+
						'</table>'
					'</div>';
			}
		}
	]
});
</script>