<body {{$sOnLoadJs}} oncontextmenu="return false" onselectstart="return false">
	<html>
		<div id="parseresult">
			<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
				<thead align="center">
					<tr><td colspan="4"><strong>{{$shosp_country}}</strong></td></tr>
					<tr><td colspan="4"><strong>{{$shosp_agency}}</strong></td></tr>
					<tr><td colspan="4"><strong>{{$shosp_name}}</strong></td></tr>
					<tr><td colspan="4"><strong>{{$shosp_addr1}}</strong></td></tr>
				</thead>	
			</table>
			<br>
			
			{{$sResultDetails}}
			
			<br>
			<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
				<tr align="center">
					<td><i>{{$sDisclaimer}}</i></td>
				</tr>
			</table>			
		<div>		
	</html>
</body>	