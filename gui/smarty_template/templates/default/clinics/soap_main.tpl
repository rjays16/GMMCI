{{$form_start}}
<div align="left" id="soap">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left" width="75%" valign="top">
				 <div id="accordion" align="left">
						<h1><a href="#">Subjective</a></h1>

						<div style="padding; 2px">
							<table width="100%" cellpadding="2" cellspacing="0">
								<tr>
									<td align="left" width="*">
										<textarea rows="5" cols="65%" class="segInput" style="overflow-y:auto; overflow-x:hidden" id="subjective_text"></textarea>
									</td>
									<td valign="top">
										<img src="../../gui/img/common/default/disk.png" title="Save" onclick="saveSoap('s');return false;" style="cursor:pointer"/>
										<br/>
										<img src="../../gui/img/common/default/bin_empty.png" title="Clear" onclick="clearText('subjective_text');return false;" style="cursor:pointer"/>
									</td>
								</tr>
							</table>
							<span class="list-title">Notes:</span>
							<div id="subjective-list" style="height:150px; overflow-x:hidden; overflow-y:auto" class="notes-div"></div>
						</div>

						<h1><a href="#">Objective</a></h1>
						<div style="height:150px">
							<table width="100%" cellpadding="2" cellspacing="0">
								<tr>
									<td align="left" width="*">
										<textarea rows="5" cols="65%" class="segInput" style="overflow-y:auto; overflow-x:hidden" id="objective_text"></textarea>
									</td>
									<td valign="top">
										&nbsp;<img src="../../gui/img/common/default/disk.png" title="Save" onclick="saveSoap('o');return false;" style="cursor:pointer"/>
										<img src="../../gui/img/common/default/bin_empty.png" title="Clear" onclick="clearText('objective_text');return false;" style="cursor:pointer"/>
									</td>
								</tr>
							</table>
							<span class="list-title">Notes:</span>
							<div id="objective-list" style="height:150px; overflow-x:hidden; overflow-y:auto" class="notes-div"></div>
						</div>

						<h1><a href="#">Assessment</a></h1>
						<div style="height:150px">
							<table width="100%" cellpadding="2" cellspacing="0">
								<tr>
									<td align="left" width="*">
										<textarea rows="5" cols="65%" class="segInput" style="overflow-y:auto; overflow-x:hidden" id="assessment_text"></textarea>
									</td>
									<td valign="top">
										&nbsp;<img src="../../gui/img/common/default/disk.png" title="Save" onclick="saveSoap('a');return false;" style="cursor:pointer"/>
										<img src="../../gui/img/common/default/bin_empty.png" title="Clear" onclick="clearText('assessment_text');return false;" style="cursor:pointer"/>
									</td>
								</tr>
							</table>
							<span class="list-title">Notes:</span>
							<div id="assessment-list" style="height:150px; overflow-x:hidden; overflow-y:auto" class="notes-div"></div>
						</div>

						<h1><a href="#">Plan</a></h1>
						<div style="height:150px">
							<table width""cellpadding="2" cellspacing="0">
								<tr>
									<td align="left" width="*">
										<textarea rows="5" cols="65%" class="segInput" style="overflow-y:auto; overflow-x:hidden" id="plan_text"></textarea>
									</td>
									<td valign="top">
										<img src="../../gui/img/common/default/disk.png" title="Save" onclick="saveSoap('p');return false;" style="cursor:pointer"/>
										<br/>
										<img src="../../gui/img/common/default/bin_empty.png" title="Clear" onclick="clearText('plan_text');return false;" style="cursor:pointer"/>
									</td>
								</tr>
							</table>
							<span class="list-title">Notes:</span>
							<div id="plan-list" style="height:150px; overflow-x:hidden; overflow-y:auto" class="notes-div"></div>
						</div>
				</div>
			</td>
			<td align="right" valign="top" width="*">
				<table width="100%" cellpadding="0" cellspacing="1" align="right" style="">
					<tr>
						<td class="segPanelHeader"  colspan="2">Doctors</td>
					</tr>
					<tr>
						<td class="segPanel" style="height:372px" valign="top">
							<div id="doctors-list"></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
{{$pid}}
{{$doctor_nr}}
{{$form_end}}