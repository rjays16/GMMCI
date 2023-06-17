<script type="text/javascript">
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
	
	function draftPrint(url) {
		if (confirm('Print this OR?')) {
		return overlib(OLiframeContent(url, 250, 60, 'or-draft', 0, 'no'),
			WIDTH,250, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'Printing receipt', 
				REF,'print-draft', REFC,'LR', REFP, 'UR', REFY,4, REFX,25,
				STATUS,'Draft printing OR...');
		}
	}
	function draftPrint2(url) {
		if (confirm('Print this Cash Invoice?')) {
		return overlib(OLiframeContent(url, 250, 60, 'or-draft', 0, 'no'),
			WIDTH,250, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'Printing receipt', 
				REF,'print-draft', REFC,'LR', REFP, 'UR', REFY,4, REFX,25,
				STATUS,'Draft printing OR...');
		}
	}



jQuery(function($) {
	$('#btn-print-draft').click(function(e){
		e.preventDefault();
		draftPrint('{{$draftPrintURL}}');
	});
	
	$('#btn-launch-director').click(function(e){
		e.preventDefault();
		draftPrint('{{$directorURL}}');
	});

	$('#btn-launch-cashinvoice').click(function(e){
		e.preventDefault();
		draftPrint2('{{$draftPrintUrlCI}}');
	});
});

</script>

<div style="text-align:center;width:95%;margin-top:10px">


	<div style="display:none">
		{{include file="cashier/gui_totals.tpl"}}
	</div>
	
	<div align="right">
		{{$CashInvoice}}

		<button style="cursor:pointer" id="btn-print-draft" class="segButton"><img src="../../gui/img/common/default/printer.png" />Print OR</button>
         
       {{$ORprint}}
       
	</div>

	<table width="100%" border="0" cellspacing="5" cellpadding="0">
		<tr>
			<td width="40%" valign="top">
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Payment details</h1></td>
							<td>								
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border:2px solid #cccccc">
						<tbody>
							<tr>
								<td class="segPanel" width="45%">
									<strong>O.R. No.</strong>
								</td>
								<td class="segPanel3" style="color:#008000;font:bold 14px Arial">{{$sORNo}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Date</strong>
								</td>
								<td class="segPanel3">{{$sORDate}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Payor</strong>
								</td>
								<td class="segPanel3">{{$sORName}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>HRN</strong>
								</td>
								<td class="segPanel3">{{$sPID}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Address</strong>
								</td>
								<td class="segPanel3">{{$sORAddress}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>PRICE</strong>
								</td>
								<td class="segPanel3">{{$sPrice}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>VAT</strong>
								</td>
								<td class="segPanel3">{{$sVAT}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>TOTAL</strong>
								</td>
								<td class="segPanel3">{{$sAmountDue}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Tendered</strong>
								</td>
								<td class="segPanel3">{{$sAmountTendered}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Discount</strong>
								</td>
								<td class="segPanel3">{{$sDiscountTendered}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Change</strong>
								</td>
								<td class="segPanel3">{{$sAmountChange}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Remarks</strong>
								</td>
								<td class="segPanel3">{{$sRemarks}}</td>
							</tr>
						</tbody>
					</table>
				</div>
				{{if $sUseCheck}}
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Check information</h1></td>
							<td>								
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border:2px solid #cccccc">
						<tbody>
							<tr>
								<td class="segPanel" width="45%">
									<strong>Check No.</strong>
								</td>
								<td class="segPanel3">{{$sCheckNo}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Check Date</strong>
								</td>
								<td class="segPanel3">{{$sCheckDate}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Bank Name</strong>
								</td>
								<td class="segPanel3">{{$sCheckBank}}</td>
							</tr>
							<!-- Added by Jarel 09/25/2013 -->
							<tr>
								<td class="segPanel">
									<strong>Company Name</strong>
								</td>
								<td class="segPanel3">{{$sCompanyName}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Check Name</strong>
								</td>
								<td class="segPanel3">{{$sCheckName}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Amount</strong>
								</td>
								<td class="segPanel3">{{$sCheckAmount}}</td>
							</tr>
						</tbody>
					</table>
				</div> <!-- Check information dashlet -->
				{{/if}}				
				{{if $sUseCard}}
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Card information</h1></td>
							<td>
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border:2px solid #cccccc">
						<tbody>
							<tr>
								<td class="segPanel" width="45%">
									<strong>Card No.</strong>
								</td>
								<td class="segPanel3">{{$sCardNo}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Issuing Bank</strong>
								</td>
								<td class="segPanel3">{{$sCardBank}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Card Brand</strong>
								</td>
								<td class="segPanel3">{{$sCardBrand}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Cardholder</strong>
								</td>
								<td class="segPanel3">{{$sCardName}}</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Amount</strong>
								</td>
								<td class="segPanel3">{{$sORAddress}}</td>
							</tr>
						</tbody>
					</table>
				</div> <!-- Card information dashlet -->
				{{/if}}
			</td>
			<td width="*" valign="top">
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Particulars</h1></td>
							<td></td>
						</tr>
					</table>
					{{$sItemList}}
				</div>
			</td>
		</tr>
	</table>
</div>
<br />