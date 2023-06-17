<div style="padding:10px">
<div id="PatientList-{{$dashlet.id}}" style="width:100%; overflow:visible; padding:0;"></div>
</div>

<style type="text/css" media="screen">
div.PatientList_item {
	padding: 4px;
	border: 1px solid #888;
	margin: 4px;


	background: -webkit-gradient(linear, 0 0, 0 bottom, from(#fff), to(#ddd));

	-moz-box-sizing: border-box;
	-moz-border-radius: 4px;
	-moz-box-shadow: 0 0 4px rgba(0,0,0,0.5);

	-moz-box-sizing: border-box;
	-moz-border-radius: 4px;
	-moz-box-shadow: 0 0 4px rgba(0,0,0,0.5);
}

div.PatientList_item span {
	color: #3d3d3d;
	text-shadow: 0 1px 0 #fff;
}


div.PatientList_item:hover {
	/*background: -moz-linear-gradient(top, #ffe7c1, #fbd594);*/
	/*border: 1px solid #d09760;*/
	background: -moz-linear-gradient(top, #e4eeff, #c0dffc);
	background: -webkit-gradient(linear, 0 0, 0 bottom, from(#e4eeff), to(#c0dffc));

	border: 1px solid #4f88bd;
}

div.PatientList_item:hover span {
	/*color: #c75f00;*/
	color: #386fa5;
}

div.PatientList_item-selected {
	padding: 4px;
	border: 1px solid #565e85;
	margin: 4px;


	background: -moz-linear-gradient(top, #70a2e1, #5c83bf);
	background: -webkit-gradient(linear, 0 0, 0 bottom, from(#70a2e1), to(#5c83bf));
	-moz-box-sizing: border-box;
	-moz-border-radius: 4px;
	-moz-box-shadow: 0 0 4px rgba(0,0,0,0.5);
	-webkit-box-sizing: border-box;
	-webkit-border-radius: 4px;
	-webkit-box-shadow: 0 0 4px rgba(0,0,0,0.5);
}

div.PatientList_item-selected span {
	color: #fff;
}
</style>
<script type="text/javascript">
ListGen.create("PatientList-{{$dashlet.id}}", {
	id:'PatientListObject-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientList/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	params: {
		filter: '{{$settings.filter}}'
	},
	autoLoad: true,
	maxRows: {{$settings.pageSize|default:"5"}},
	rowHeight: 32,
	pageStat: 'Items {from}-{to} of {total}',
	layout: [
		//['<h1>My Patients</h1>'],
		['#pagestat', '#first', '#prev', '#next', '#last'],
		['<div align="left" style="padding:2px">'+
				'<input type="text" class="input" size="20" value="{{$session.search}}" onkeyup="if (event.keyCode==$J.ui.keyCode.ENTER) $J(this).next().click()" /> '+
				'<button class="lg-toolbar-button" '+
					'onclick="$(\'PatientList-{{$dashlet.id}}\').list.params.key = $(this).previous().value; $(\'PatientList-{{$dashlet.id}}\').list.refresh();return false;">'+
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
				return '<div '+(selected ? 'class="PatientList_item-selected"' : 'class="PatientList_item" onclick="Dashboard.dashlets.sendAction(\'{{$dashlet.id}}\', \'openFile\', {file:\''+row['encounter']+'\'}); return false;"')+' style="cursor: pointer;">'+
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