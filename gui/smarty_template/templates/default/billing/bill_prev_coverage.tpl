<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
{{$sFormStart}}
<style type="text/css">
	#coverage_details tr td {
		font:normal 12px Arial, Helvetica, sans-serif;
	}				
</style>
<div align="center" id="mainSection">
	<table class="segPanel" width="90%" id="hdr_section" cellpadding="0" cellspacing="1">
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td align="left" width="70%">&nbsp;&nbsp;{{$sInsuranceCombo}}</td>
			<td align="left" width="*">{{$sDate}}{{$sCalendarIcon}}</td>			
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
	</table>
	<br>
	<table class="segPanel" width="90%" id="detail_section" name="detail_section" cellpadding="0" cellspacing="1" style="visibility:hidden">
		<tbody id="coverage_details">
		</tbody>	
	</table>
	<br>
	<table width="90%" id="footer_view" name="footer_view" cellpadding="0" cellspacing="1" style="display:none">	
		<tr>
			<td width="20%">{{$sPrevLink}}</td>
			<td width="43%">{{$sNextLink}}</td>
			<td align="center" width="*">{{$sAddButton}}</td>
			<td align="center" width="*">{{$sEditButton}}</td>			
			<td align="center" width="*">{{$sDelButton}}</td>		
		</tr>	
	</table>	
	<table width="90%" id="footer_edit" name="footer_edit" cellpadding="0" cellspacing="1" style="display:none">	
		<tr>
			<td width="75%">&nbsp;</td>
			<td align="center" width="*">{{$sSaveButton}}</td>
			<td align="left" width="*">{{$sCancelButton}}</td>			
		</tr>	
	</table>	
</div>

{{$jsCalendarSetup}}

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sHiddenInputs}}
{{$sFormEnd}}
{{$sTailScripts}}