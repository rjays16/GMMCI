{{*created by cha Feb 4, 2010*}}
{{$sFormStart}}
<br/>
<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
							<tr>
								<td class="segPanelHeader" colspan="2">
									Prescription Details
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<table width="100%" border="0" cellpadding="2" cellspacing="3" style="font:normal 12px Arial; padding:4px" >
										<tr>
											<td align="left"><label>Dosage:</label></td>
											<td valign="middle">{{$sDosage}}</td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td align="left" valign="middle"><label>Quantity:</label></td>
											<td valign="middle">
											{{$sQuantity}}
											<label>Unit:</label>
											{{$sQuantityUnits}}
										</tr>
										<tr>
											<td><label>Route:</label></td>
											<td valign="middle">{{$sRoute}}</td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td><label>Period:</label></td>
											<td valign="middle">
											{{$sPeriodDays}}
											{{$sPeriod}}
											</td>
											<td></td>
										</tr>
										<tr>
											<td><label>Refills:</label></td>
											<td valign="middle">
											{{$sRefill}}
											<label>Start On:</label>
											{{$sStartDate}}{{$sCalendarIcon}}{{$jsCalendarSetup}} 
											</td>
										</tr>
										<tr>
											<td><label>Reason for Drug:</label</td>
											<td valign="middle">{{$sDrugReason}}</td>
										</tr>
										<tr>
											<td></td>
											<td>{{$sSaveDosage}}<strong><label>Save as new standard dosage </label></strong></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
						<table border="0" cellspacing="2" cellpadding="2" align="center" width="150%;margin:4px" style="font:normal 12px Arial; padding:4px">
							<tr>
								<td align="left" width="20%"><strong/><label>Search Drug Name : </label></td>
								<td valign="middle">{{$sSearchDrug}} <!--{{$sSearchBtn}}-->{{$sSigBtn}}</td>
								<td></td>
							</tr>
						</table>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-x:hidden;overflow-y:auto; width:100%; background-color:#e5e5e5">
					<table class="segList" cellpadding="1" cellspacing="1" width="100%">
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
					<table id="prescriptionlist" class="jedList" width="*" border="0" cellpadding="0" cellspacing="0">
						<thead>
								<tr>
									<th width="*%" nowrap="nowrap">Drug Name</th>
									<th width="7%">Availability</th>
									<!--<th width="15%">Dosage</th>-->
									<th width="8%">Quantity</th>
									<th width="8%">Unit</th>
									<th width="3%">Options</th>
								</tr>
						</thead>
						<tbody id="prescriptionlist-body">
								<tr><td colspan="5" style="">No drug(s) added..</td></tr>
						</tbody>
					</table>
						<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
					</div>
				</td>
			</tr> 
		</tbody>
	</table>
<div style="width:96%; text-align:right; padding:2px 4px">
	<img src="../../../images/btn_add.gif" style="cursor:pointer" align="middle" id="save_dosage">
	<img src="../../../images/btn_cancelorder.gif" style="cursor:pointer" align="middle" id="cancel_dosage">
</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
		
{{$sFormEnd}}
{{$sTailScripts}} 	
