<div id="px-history-{{$dashlet.id}}" style="border:0; padding:0; width:100%; overflow:hidden;"></div>
<script type="text/javascript">
ListGen.create("px-history-{{$dashlet.id}}", {
	id:'px-hist-obj-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientHistory/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: {{$settings.pageSize|default:"5"}},
	rowHeight: 32,
	layout: [
		['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
		['#thead'],
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
			name: "admission",
			label: "Admission",
			width: 80,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "11px"
			},
			render: function(data, index)
			{
				var row=data[index];
				return '<div>'+row['admission']+'</div>'+
					'<div style="font:normal 11px Tahoma; color:#0000c4">'+row['encounter']+'</div>';
			}
		},
		{
			name: "department",
			label: "Department",
			width: 120,
			sorting: ListGen.SORTING.none,
			sortable: true,
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
				return '<img class="link" src="../../images/cashier_view.gif" onclick="openDrNotesView(\''+row["encounter"]+'\')"/>';
			}
		}
	]
});

function openDrNotesView(encounter_nr)
{
	Dashboard.launcher.launch({
			title:'Doctor\'s Notes',
			href:'../../modules/dashboard/dashlets/PatientHistory/viewDrNotes.php?encounter_nr='+encounter_nr,
			width: 700,
			height: 450
		})
}
</script>