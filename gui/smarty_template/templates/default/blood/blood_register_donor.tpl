{{*created by cha 05-20-2009*}}
{{$sFormStart}}
		<div style="padding:10px;width:95%;border:0px solid black">
		<font class="warnprompt"><br></font>
		<table border="0" width="20%" class="Search">
			<tbody>
				<tr>
					<td class="segPanelHeader">Search existing blood donor</td>
				</tr>
				<tr>
					<td class="segPanel" align="left" style="white-space:nowrap">{{$sDonorName}}</td>
				</tr>
				<tr>
					<td class="segPanel" align="center">
						<img class="segSimulatedLink" id="search" name="search" src="../../gui/img/control/default/en/en_searchbtn.gif" border=0 alt="Search data" align="absmiddle"  onclick="startAJAXSearch(0); return false;"/>
					</td>
				</tr>      
			</tbody>
		</table>
</div>
<div style="width:10%; padding:1px;">
	<table width="10%" align="left">
		<tr>
		 <input class="segButton" type="button" value="Register Blood Donor" onclick="registerDonor(); return false;" onmouseover="tooltip('Register donor');" onMouseout="return nd();" /> 
		</tr>
	</table>
</div>
<div class="segContentPane">
<table class="segList" width="85%" border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr class="nav">
			<th colspan="10">
				<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
					<img title="First" src="../../images/start.gif" border="0" align="absmiddle"/>
					<span title="First">First</span>
				</div>
				<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
					<img title="Previous" src="../../images/previous.gif" border="0" align="absmiddle"/>
					<span title="Previous">Previous</span>
				</div>
				<div id="pageShow" style="float:left; margin-left:10px">
					<span></span>
				</div>
				<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
					<span title="Last">Last</span>
					<img title="Last" src="../../images/end.gif" border="0" align="absmiddle"/>
				</div>
				<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
					<span title="Next">Next</span>
					<img title="Next" src="../../images/next.gif" border="0" align="absmiddle"/>
				</div>
			</th>
		</tr>
	</thead>
</table>
<table id="donorlist" class="jedList" width="85%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr>
					<th width="8%" nowrap="nowrap">Donor ID</th>
					<th width="*%">Name</th>
					<th width="*%">Address</th>
					<th width="8%">Age</th>
					<th width="10%">Member Date</th>
					<th width="8%">Blood Type</th>
					<th width="10%"></th>
				</tr>
		</thead>
		<tbody id="donorlist-body">
				<tr><td colspan="10" style="">No Donor ID or Donor Name searched...</td></tr>
		</tbody>
</table>
</div>  

{{$sFormEnd}} 
{{$sTailScripts}}