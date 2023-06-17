<div id="px-list-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-{{$dashlet.id}}", {
	id:'px-obj-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientLabResults/Listgen.php",
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
                
         /*   if (row["withresult"]==1)
					//return '<button class="button" onclick="PatientLab_OpenResult(\''+row["refno"]+'\',\''+row["lis_order_no"]+'\',\''+row["pid"]+'\');return false;"><img class="link" src="../../gui/img/common/default/page_white_acrobat.png" />Results</button>';
                    return '<button class="button" onclick="PatientLab_OpenResult(\''+row["filename"]+'\');return false;"><img class="link" src="../../gui/img/common/default/page_white_acrobat.png" />Results</button>';
                else
                	return '<button class="button" onclick="PatientLab_OpenResult(\''+row["filename"]+'\');return false;"><img class="link" src="../../gui/img/common/default/page_white_acrobat.png" />Results</button>';*/

                 return '<button class="button" onclick="PatientLab_OpenResult(\''+row["refno"]+'\',\''+row["pid"]+'\',\''+row["service_code"]+'\',\''+row["group_id"]+'\');return false;"><img class="link" src="../../gui/img/common/default/page_white_acrobat.png" />Results</button>';
			}


		}
	]
});

function PatientLab_OpenResult(refno,pid,service_code,group_id) {
  	var url = '../../modules/repgen/pdf_lab_results.php';
	var options = {
		//url: '../../modules/laboratory/seg-lab-result-pdf.php',
        //url: '../../modules/laboratory/seg-lab-result-pdf-link.php',
        //edited by VAN 02-06-2013
        url: url,
		data: {
			refno:refno,
			pid:pid,
			service_code: service_code,
			group_id: group_id
			}
	};
	Dashboard.openWindow(options);
}

function PromptMsg(){
    alert("No Result fetch from the LIS yet . \nOr the result is manually generated. \nPlease ask the Laboratory for the result.");
}
</script>