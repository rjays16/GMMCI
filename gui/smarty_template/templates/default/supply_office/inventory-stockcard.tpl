{{$sFormStart}}
<script type="text/javascript">
$J( function() {
	Calendar.setup ({
		inputField : "from_date",
		ifFormat : "%Y-%m-%d",
		showsTime : false,
		button : "from_date_trigger",
		singleClick : true,
		step : 1
	});

	Calendar.setup ({
		inputField : "to_date",
		ifFormat : "%Y-%m-%d",
		showsTime : false,
		button : "to_date_trigger",
		singleClick : true,
		step : 1
	});
});
</script>

<div style="width:500px">
	<table width="100%" border="0" style="font-size:12px; margin-top:5px" cellspacing="1" cellpadding="2">
        <tbody>
            <tr>
				<td align="left" class="segPanelHeader" colspan="2" width="300"><strong>Stock Card Options</strong></td>
            </tr>
			<tr id="expdate_row" style="">
				<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel" valign="middle">Select item:</td>
				<td width="70%" nowrap="nowrap" class="segPanel" valign="baseline">{{$sSCItemHidden}}{{$sSCItemInput}}<span style="vertical-align:top;">{{$sSCItemIcon}}</span></td>
            </tr>
			<tr id="area_row" style="">
				<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel" valign="middle">Select Area:</td>
				<td width="70%" nowrap="nowrap" class="segPanel" valign="middle">{{$sSCSelectArea}}</td>
            </tr>
			<tr id="date_row" style="">
				<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel">From</td>
				<td width="70%" nowrap="nowrap" class="segPanel">
					<input class="segInput" type="text" id="from_date" name="from_date" value="{{$data.from_date}}" size="10" />
					<img src="../../gui/img/common/default/calendar.png" id="from_date_trigger" align="absmiddle" style="cursor:pointer" />
					<span class="small">[YYYY-mm-dd]</span>
				</td>
            </tr>
			<tr id="date_row" style="">
				<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel">To</td>
				<td width="70%" nowrap="nowrap" class="segPanel">
					<input class="segInput" type="text" id="to_date" name="to_date" value="{{$data.to_date}}" size="10" />
					<img src="../../gui/img/common/default/calendar.png" id="to_date_trigger" align="absmiddle" style="cursor:pointer" />
					<span class="small">[YYYY-mm-dd]</span>
                </td>
            </tr>
            <tr>
				<td colspan="2" align="center" class="segPanel">
					<button class="segButton">View report</button>
				</td>
            </tr>
        </tbody>
    </table>
    {{$sHiddenInputs}}
</div>
{{$sFormEnd}}
{{$sTailScripts}}
