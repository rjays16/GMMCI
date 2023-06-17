<?php /* Smarty version 2.6.0, created on 2018-04-30 12:25:03
         compiled from cashier/gui_cashier_info.tpl */ ?>
<style type="text/css">
<!--
	.tabFrame {
		padding:5px;
		min-height:140px;
	}

-->
</style>
<script language="javascript" type="text/javascript">
<!--

	function tabClick(obj) {
		if (obj.className=='segActiveTab') return false;
		var dList = obj.parentNode;
		var tab;
		if (dList) {
			var listItems = dList.getElementsByTagName("LI");
			if (obj) {
				for (var i=0;i<listItems.length;i++) {
					if (obj!=listItems[i]) {
						listItems[i].className = "";
						tab = listItems[i].getAttribute('segTab');
						if ($(tab))
							$(tab).style.display = "none";
					}
				}
				tab = obj.getAttribute('segTab');
				if ($(tab))	$(tab).style.display = "block";
				obj.className = "segActiveTab";
			}
		}
	}

	function toggleTBody(list) {
		var dTable = $(list);
		if (dTable) {
			var dBody = dTable.getElementsByTagName("TBODY")[0];
			if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
		}
	}

	function enableInputChildren(id, enable) {
		var el=$(id);
		if (el) {
			var children = el.getElementsByTagName("INPUT");
			if (children) {
				for (i=0;i<children.length;i++) {
					children[i].disabled = !enable;
				}
				return true;
			}
		}
		return false;
	}
-->
</script>

<ul id="cashier-tabs" class="segTab" style="padding-left:10px">
	<li class="segActiveTab" onclick="tabClick(this)" segTab="tab0">
		<h2 class="segTabText">Payor Information</h2>
	</li>
	<li onclick="tabClick(this)" segTab="tab1">
		<h2 class="segTabText">Check/Credit Card</h2>
	</li>
</ul>

<div class="segTabPanel" style="padding:1px; width:100%">
	<div id="tab0" class="tabFrame" style="display:block" >
		<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" style="">
			<tbody>
				<tr height="5"></tr>
				<tr>
					<td width="53%" valign="top">
						<table border="0" cellspacing="1" cellpadding="1" width="100%" style="font-family:Arial, Helvetica, sans-serif">
							<tr valign="middle">
								<td nowrap="nowrap" align="right"><strong>O.R. No.</strong></td>
								<td nowrap="nowrap" valign="middle">
									<input type="hidden" id="warn-text" value="" />
									<?php echo $this->_tpl_vars['sORNo']; ?>

									<span id="warn-icon">
									<?php echo $this->_tpl_vars['sImgWarn']; ?>

									<?php echo $this->_tpl_vars['sImgOK']; ?>

									</span>
								</td>
							</tr>
							<tr height="30">
								<td></td>
								<td valign="top"><?php echo $this->_tpl_vars['sResetOR']; ?>
</td>
							</tr>
							<tr>
								<td></td>
								<td valign="top">
									<?php echo $this->_tpl_vars['sORWalkin']; ?>

									<label class="segInput" for="search-walkin">Search walk-in</label>
								</td>
							</tr>
							<tr valign="top">
								<td width="1%" nowrap="nowrap" align="right"><strong>Name</strong></td>
								<td align="left" valign="middle" nowrap="nowrap" width="1%">
									<?php echo $this->_tpl_vars['sOREncNr']; ?>

									<?php echo $this->_tpl_vars['sOREncID']; ?>

									<?php echo $this->_tpl_vars['sORDiscountID']; ?>

									<?php echo $this->_tpl_vars['sORDiscount']; ?>

									<?php echo $this->_tpl_vars['sORName']; ?>

								</td>
								<td width="1%"><?php echo $this->_tpl_vars['sSelectEnc']; ?>
</td>
								<td width="*"><?php echo $this->_tpl_vars['sClearEnc']; ?>
</td>
							</tr>
							<!--added/updated by jane 10/18/2013-->
							<tr valign="top">
								<td width="1%" nowrap="nowrap" align="right" rowspan="1"><strong>Address</strong></td>
								<td align="left" valign="middle" colspan="3">
									<?php echo $this->_tpl_vars['sORAddress']; ?>

								</td>
							</tr>
							<tr valign="top">
								<td width="1%" nowrap="nowrap" align="right" rowspan="1"><strong>Company Name</strong></td>
								<td align="left" valign="middle" colspan="3">
									<?php echo $this->_tpl_vars['sPayorTabCompanyName']; ?>

								</td>
							</tr>
						</table>
					</td>
					<td width="*" valign="top">
						<table border="0" cellspacing="2" cellpadding="1" width="100%" style="font-family:Arial, Helvetica, sans-serif">
							<tr valign="top">
								<td width="1%" nowrap="nowrap" align="right"><strong>Date</strong></td>
								<td width="*" align="left" valign="middle">
									<?php echo $this->_tpl_vars['sORDate'];  echo $this->_tpl_vars['sCalendarIcon']; ?>

								</td>
							</tr>
							<tr valign="top">
								<td nowrap="nowrap" align="right"><strong>Remarks</strong></td>
								<td>
									<?php echo $this->_tpl_vars['sRemarks']; ?>

								</td>
							</tr>
							<tr valign="top">
								<td nowrap="nowrap" align="right" valign="middle"><strong>Patient type</strong></td>
								<td valign="middle">
									<?php echo $this->_tpl_vars['sOrderEncType']; ?>

									<span id="encounter_type_show" style="font-weight:bold;color:#000080"><?php echo $this->_tpl_vars['sOrderEncTypeShow']; ?>
</span>
								</td>
							</tr>
							<tr valign="top">
								<td nowrap="nowrap" align="right" valign="middle"><strong>Classification</strong></td>
								<td valign="middle"><span id="sw-class" style="font:bold 12px Arial;color:#006633"><?php echo $this->_tpl_vars['sSWClass']; ?>
</span><?php echo $this->_tpl_vars['sDiscount'];  echo $this->_tpl_vars['sDiscountID']; ?>
&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="tab1" class="tabFrame" style="display:none">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" style="font-family:Arial, Helvetica, sans-serif">
			<tr>
				<td width="45%" valign="top">
					<div style="margin-left:20px"><?php echo $this->_tpl_vars['sCheckOption']; ?>
</div>
					<table id="check-details" border="0" cellpadding="1" cellspacing="2" width="100%" style="font-family:inherit">
						<tbody>
							<tr>
								<td width="60" align="right"></td>
								<td width="*"></td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Check No.</strong></td>
								<td><?php echo $this->_tpl_vars['sCheckNo']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Check Date</strong></td>
								<td><?php echo $this->_tpl_vars['sCheckDate'];  echo $this->_tpl_vars['sCalendarIcon1']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Bank Name</strong></td>
								<td><?php echo $this->_tpl_vars['sCheckBankName']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Company Name</strong></td>
								<td><?php echo $this->_tpl_vars['sCompanyName']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Payor</strong></td>
								<td><?php echo $this->_tpl_vars['sCheckPayee']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Amount</strong></td>
								<td><?php echo $this->_tpl_vars['sCheckAmount']; ?>
</td>
							</tr>

						</tbody>
					</table>
				</td>
				<td valign="top" style="">
					<div style="margin-left:20px"><?php echo $this->_tpl_vars['sCardOption']; ?>
</div>
					<table id="card-details" border="0" cellpadding="1" cellspacing="2" width="100%" style="font-family:inherit">
						<tbody>
							<tr>
								<td width="10%" align="right"></td>
								<td width="*"></td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Card No.</strong></td>
								<td><?php echo $this->_tpl_vars['sCardNo']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Issuing Bank</strong></td>
								<td><?php echo $this->_tpl_vars['sCardIssuingBank']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Card Brand</strong></td>
								<td><?php echo $this->_tpl_vars['sCardBrand']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Cardholder Name</strong></td>
								<td><?php echo $this->_tpl_vars['sCardName']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Expiry Date</strong></td>
								<td nowrap="nowrap">
									<?php echo $this->_tpl_vars['sCardExpiryDate']; ?>

									<strong style="margin-left:10px">Security Code</strong>
									<?php echo $this->_tpl_vars['sCardSecurityCode']; ?>

								</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="right"><strong>Amount</strong></td>
								<td><?php echo $this->_tpl_vars['sCardAmount']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div id="tab2" class="tabFrame" style="display:none">
		<div align="center" style="background-color:#ffffff;border:1px solid #a0a0a0">
			<img src="../../images/under_construction.jpg" />
		</div>
	</div>
</div>