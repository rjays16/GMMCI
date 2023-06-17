	<form method="post" action="{{$thisfile}}" name="aufnahmeform" onSubmit="return chkform(this)">
		<table border="0" cellspacing="1" cellpadding="1" width="100%">

				<!-- The insurance class  -->
			 {{if $LDBillType}}
				<tr>
					<td class="adm_item">
						{{$LDBillType}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sBillTypeInput}}&nbsp;&nbsp;<span name="iconIns" id="iconIns" style="display:none">{{$sBtnAddItem}}</span>
					</td>
					<!--<td>{{$sBtnAddItem}}</td>-->
				</tr>
			 {{/if}}
				<!-- edited 03-06-07------------->
				
			 {{if $LDInsuranceNr}}
				<tr>
					<td class="adm_item" width="20%">
						Registered {{$LDInsuranceNr}}:
					</td>
					<td colspan=2 class="adm_input">
						<!--{{$insurance_nr}}-->
						<!-- -->
						
						<table id="reg-order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="reg-order-list-header">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
											<th width="20%" nowrap align="right">&nbsp;&nbsp;Insurance No.</th>
											<th width="18%" nowrap align="right">&nbsp;&nbsp;Principal Holder</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								{{$sOrderItemsreg}}
							
						</table>
						
						<!-- -->
					</td>
					
				</tr>
				<tr>
					<td colspan="3" class="adm_item">&nbsp;</td>
				</tr>
				<tr>
					<td class="adm_item">
						{{$LDInsuranceNr}} to be used:
					</td>
					<td colspan=2 class="adm_input">
						<!--{{$insurance_nr}}-->
						<!-- -->
						
						<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
											<th width="20%" nowrap align="right">&nbsp;&nbsp;Insurance No.</th>
											<th width="18%" nowrap align="right">&nbsp;&nbsp;Principal Holder</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								{{$sOrderItems}}
							
						</table>
						
						<!-- -->
					</td>
					
				</tr>
			  {{/if}}
				
				{{$sHiddenInputs}}

				<tr>
					<td colspan="3">&nbsp;
						
				  </td>
				</tr>
				<tr>
					<td>
						{{$pbSave}}
					</td>
					<td align="right">&nbsp;
						
					</td>
					<td align="right">
						{{$pbCancel}}					</td>
				</tr>

		</table>
	
			{{$sErrorHidInputs}}
			{{$sUpdateHidInputs}}
	
	</form>