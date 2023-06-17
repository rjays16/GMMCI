<div style="width:100%; display:table; padding:0">
	<ul class="dashlet-contents-tabs">
		<li><a href="#DoctorsNotes-subjective-tab">Subjective</a></li>
		<li><a href="#DoctorsNotes-objective-tab">Objective</a></li>
		<li><a href="#DoctorsNotes-assessment-tab">Assessment</a></li>
		<li><a href="#DoctorsNotes-plan-tab">Plan</a></li>
	</ul>

	<div class="dashlet-contents-tabs-container">
		<div id="DoctorsNotes-subjective-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-subjective" onsubmit="return false;">
				<table width="100%" cellpadding="0" cellspacing="5">
							<tr>
								<td style="font:normal 18px Arial">Chief Complaint</td>
							</tr>
							<tr>
									<td><textarea rows="8" style="width:100%; font: normal 14px 'Courier New'; overflow:visible;" name="chief_complaint" class="segInput" onblur="DoctorsNotes_SaveNote('DoctorsNotes-subjective')" spellcheck="false">{{$data.chief_complaint}}</textarea></td>
							</tr>
				</table>
		</form>
		</div>
		<div id="DoctorsNotes-objective-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-objective" onsubmit="return false;">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tr>
					<td style="font:normal 18px Arial">Pertinent Physical Examination</td>
				</tr>
				<tr>
						<td><textarea rows="8" style="width:100%; font:normal 14px 'Courier New'; overflow:visible;" name="physical_examination" class="segInput" onblur="DoctorsNotes_SaveNote('DoctorsNotes-objective')" spellcheck="false">{{$data.physical_examination}}</textarea></td>
				</tr>
				</table>
			</form>
		</div>
		<div id="DoctorsNotes-assessment-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-assessment" onsubmit="return false;">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tr>
					<td style="font:normal 18px Arial">Search Diagnosis</td>
				</tr>
				<tr>
					<td><input type="text" class="segInput" name="diagnosis" id="DoctorsNotes-diagnosis-{{$dashlet.id}}" style="width:100%; font: bold 16px 'Times';" /></td>
				</tr>
				<tr>
					<div id="DoctorsNotes-diagnosis-list-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0; margin-top:10px"></div>
				</tr>
			</table>
			</form>
		</div>
		<div id="DoctorsNotes-plan-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-plan" onsubmit="return false;">
				<table width="100%" cellpadding="0" cellspacing="5">
					<tr>
						<td style="font:normal 18px Arial">Progress Notes/Clinical Summary</td>
					</tr>
					<tr>
						<td><textarea rows="8" style="width:100%; font: normal 14px 'Courier New'; overflow:visible;" name="clinical_summary" class="segInput" onblur="DoctorsNotes_SaveNote('DoctorsNotes-plan')" spellcheck="false">{{$data.clinical_summary}}</textarea></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
function DoctorsNotes_SaveNote(form_id) {
	Dashboard.dashlets.sendAction("{{$dashlet.id}}", "saveDrNote", {
		data:$J('#'+form_id).serializeArray()
	});
}

function DoctorsNotes_DeleteDiagnosis(code) {
	Dashboard.dashlets.sendAction("{{$dashlet.id}}", "deleteDrDiagnosis", {
		data:code
	});
}

function DoctorsNotes_SaveIcdCode(code) {
	Dashboard.dashlets.sendAction("{{$dashlet.id}}", "saveDrDiagnosis", {
		data:code
	});
}

function DoctorsNotes_refreshIcdList() {
	$('DoctorsNotes-diagnosis-list-{{$dashlet.id}}').list.refresh();
	$('DoctorsNotes-diagnosis-{{$dashlet.id}}').value="";
	$('DoctorsNotes-diagnosis-{{$dashlet.id}}').focus();
}

//initialize list gen
ListGen.create("DoctorsNotes-diagnosis-list-{{$dashlet.id}}", {
	id:'DoctorsNotes-diagnosis-listgen-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/DoctorsNotes/Listgen.php",
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
			name: "",
			label: '',
			width: 30,
			sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];
					return '<img class="link" src="../../images/cashier_delete_small.gif" onclick="DoctorsNotes_DeleteDiagnosis(\''+row["code"]+'\');return false;"/>';
			}
		},
		{
			name: "code",
			label: "ICD10",
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
			name: "description",
			label: "Description",
			width: 300,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		}
	]
});
//end for list gen

//for tabs
	//When page loads...
	$J(".dashlet-contents-tabs-content").hide(); //Hide all content
	$J("ul.dashlet-contents-tabs li:first").addClass("active").show(); //Activate first tab
	$J(".dashlet-contents-tabs-content:first").show(); //Show first tab content

	//On Click Event
	$J("ul.dashlet-contents-tabs li").click(function() {

		$J("ul.dashlet-contents-tabs li").removeClass("active"); //Remove any "active" class
		$J(this).addClass("active"); //Add "active" class to selected tab
		$J(".dashlet-contents-tabs-content").hide(); //Hide all tab content

		var activeTab = $J(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$J(activeTab).show(); //Fade in the active ID content
		return false;
	});
//end for tabs

//for autocomplete
$J('#DoctorsNotes-diagnosis-{{$dashlet.id}}').autocomplete({
		minLength: 1,
		source: '../../modules/dashboard/dashlets/DoctorsNotes/icd10List.php',
		select: function(event, ui) {
			// NOTE: put onSelect logic here
			DoctorsNotes_SaveIcdCode(ui.item.icd_code)
			return false;
		}
	})
	.data( "autocomplete" )._renderItem = function( ul, item ) {

		return $J( "<li></li>" )
			.data( "item.autocomplete", item )
			.append(
				"<a>" +
					'<span style="font-weight:bold;color:#000066">' + item.description+ '</span>' +
					"<br/>" +
					'<span style="font:normal 10px Arial;color:#404040">' + item.icd_code+'</span>' +
				"</a>" )
			.appendTo( ul );
	};
	//end for autocomplete
</script>