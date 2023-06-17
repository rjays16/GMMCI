{{*created by cha Feb 9, 2010*}}
{{$sFormStart}}
<div style="width:500px; margin-top:10px; height:200px" align="center" class="segPanel">
		<div style="width:500px; margin-top:10px" align="center">
<table border="0" cellspacing="2" cellpadding="2" align="center" width="95%;margin:4px" style="font:normal 12px Arial; padding:4px">
		<tr>
			<td align="right"><strong/><label>Patient ID : </label></td>
			<td valign="middle">{{$sPID}}</td>
			<td><div  style="width:50px;"></div></td>
			<td>{{$sStartDate}}{{$sCalendarIcon}}{{$jsCalendarSetup}}</td>
		</tr>
</table>
</div>
<div class="segPanel" style="padding:4px;height:140px;overflow-x:hidden;overflow-y:auto;">
	<table class="segList" width="85%" border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr class="nav">
			<th colspan="10">
				<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
					<img title="First" src="../../../images/start.gif" border="0" align="absmiddle"/>
					<span title="First">First</span>
				</div>
				<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
					<img title="Previous" src="../../../images/previous.gif" border="0" align="absmiddle"/>
					<span title="Previous">Previous</span>
				</div>
				<div id="pageShow" style="float:left; margin-left:10px">
					<span></span>
				</div>
				<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
					<span title="Last">Last</span>
					<img title="Last" src="../../../images/end.gif" border="0" align="absmiddle"/>
				</div>
				<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
					<span title="Next">Next</span>
					<img title="Next" src="../../../images/next.gif" border="0" align="absmiddle"/>
				</div>
			</th>
		</tr>
	</thead>
	</table>
	<table id="prescriptionlist" class="jedList" width="85%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr>
					<th width="8%">Encounter #</th>
					<th width="10%">Date</th>
					<th width="15%">Complaint</th>
					<th width="30%">Prescription Summary</th>
					<th width="20%">Options</th>
				</tr>
		</thead>
		<tbody id="prescriptionlist-body">
				<tr><td colspan="6" style="">No prescription added..</td></tr>
		</tbody>
	</table>
	</div>
</div>
{{$sFormEnd}}
