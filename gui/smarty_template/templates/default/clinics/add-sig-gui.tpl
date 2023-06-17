{{*created by cha Feb 4, 2010*}}
{{$sFormStart}}
<div style="width:400px; margin-top:10px;" align="center" class="segPanel">
<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:2px" style="font:normal 12px Arial; padding:4px">
		<tr>
			<td align="right"><strong/><label>Drug Name : </label></td>
			<td valign="middle">{{$sDrugName}}</td>
		</tr>
		<tr>
			<td align="right" valign="middle"><strong/><label>Quantity : </label></td>
			<td valign="middle">
			{{$sQuantity}}
			<strong/><label>Unit : </label>
			{{$sQuantityUnits}}
		</tr>
		<tr>
			<td align="right"><strong/><label>Dosage : </label</td>
			<td valign="middle">{{$sDosage}}</td>
			<td></td>
			<td></td>
		</tr>
</table>
<div></div><br/>
<div style="width:95%; text-align:right; padding:2px 4px">
			<img src="../../../images/btn_add.gif" style="cursor:pointer" align="middle" id="save_dosage">
			<img src="../../../images/btn_cancelorder.gif" style="cursor:pointer" align="middle" id="cancel_dosage">
	</div>
</div>
{{$sFormEnd}}
