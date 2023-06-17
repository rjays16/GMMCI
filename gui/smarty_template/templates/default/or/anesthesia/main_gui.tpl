{{*created by cha 05-20-2009*}}
{{$sFormStart}}
<div style="width:100%;">
	<table border="0" width="60%" cellpadding="0" cellspacing="0" style="font:12px bold Arial">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="5">Search existing procedures</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table width="100%" cellpadding="0" cellspacing="0" style="font:12px bold Arial">
						<tr>
							<td align="center"><label>Anesthesia procedure name</label></td>
							<td align="left" style="white-space:nowrap" valign="middle">{{$sSearchKey}}&nbsp;{{$sSearchBtn}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="segContentPane">
		<table border="0" width="75%">
			<tbody>
				<tr>
					<td width="16%"></td>
					<td>{{$sAddBtn}}</td>
				</tr>
			</tbody>
		</table>
		<div id="anesthesia_list" style="width:50%;border:0px solid black">
		</div>
</div>
{{$sFormEnd}}