{{* Template for reports (notes) *}}
	<br>
	{{if $isOpdInpatient}}
		<table border=0 cellpadding=4 cellspacing=1  width= 100%>
			<tr>
				<td align="left" width="10%"><b>{{$segOpdBtn}}</b></td>
				<td align="left" ><b>{{$segInpatientBtn}}</b></td>
			</tr>
		</table>
	{{/if}}
	<b>{{$segHeadingPrincipal}}</b>
	<table border=0 cellpadding=4 cellspacing=1 width=100%>
		<tr class="adm_item">
			<td align="center" width="50%"><b>{{$LDDiagnosis}}</b></td>
			<td align="center"><b>{{$LDTherapy}}</b></td>
		</tr>
		{{$sDocsListRowsPrincipal}}
	</table>
	<br>
    <b>{{$segHeadingOthers}}</b>
	<table border=0 cellpadding=4 cellspacing=1 width=100%>
		<tr class="adm_item">
			<td align="center" width="50%"><b>{{$LDDiagnosis}}</b></td>
			<td align="center"><b>{{$LDTherapy}}</b></td>
		</tr>	
		{{$sDocsListRowsOthers}}
	</table>
    <!-- notification -->
    <br>
    <b>{{$segHeadingNotification}}</b>
    <table border=0 cellpadding=4 cellspacing=1 width=100%>
        <tr class="adm_item">
            <td align="center" width="15%"><b>Date</b></td>
            <td align="center"><b>Notification</b></td>
        </tr>
        {{$sNotificationListRows}}
    </table>
    <br>
    <b>{{$segHeadingOperation}}</b>
    <table border=0 cellpadding=5 cellspacing=1 width=100%>
        <tr class="adm_item">
            <td align="center" width="*"><b>Operations</b></td>
            <td align="center" width="15%"><b>Code</b></td>
            <td align="center" width="15%"><b>RVU</b></td>
            <td align="center" width="15%"><b>Date of Operations</b></td>
            <td align="center" width="10%"><b>Quantity</b></td>
        </tr>
        {{$sOperationListRows}}
    </table>
    <br><br>
    <!-- -->
{{$sDetailsIcon}}


