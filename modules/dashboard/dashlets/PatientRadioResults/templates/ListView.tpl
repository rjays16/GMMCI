<div id="px-list-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-{{$dashlet.id}}", {
	id:'px-obj-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientRadioResults/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: {{$settings.pageSize|default:"5"}},
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
					return '<button class="button" onclick="PatientRadio_OpenResult(\''+row["refno"]+'\',\''+row["pid"]+'\');return false;"><img class="link" src="../../gui/img/common/default/film.png" />Results</button>';
			}
		}
	]
});

function PatientRadio_OpenResult(refno, pid) {
	var options = {
		url: '../../modules/radiology/certificates/seg-radio-report-pdf.php',
		data: {
			batch_nr_grp:refno,
			pid:pid
			}
	};
	Dashboard.openWindow(options);
}
</script>