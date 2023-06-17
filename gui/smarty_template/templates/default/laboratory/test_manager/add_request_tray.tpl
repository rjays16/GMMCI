{{$form_start}}
<div style="width:550px;">
	<table width="100%">
		<tbody>
			<tr>
				<td style="font: bold 12px Arial; background-color: rgb(229, 229, 229); color: rgb(45, 45, 45);">
					<div style="padding:4px 2px; padding-left:10px; ">
						Laboratory Service Section &nbsp;&nbsp;&nbsp;&nbsp;
						{{$labSections}}
						<img src="../../../gui/img/common/default/redpfeil_l.gif">
					</div>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Search Laboratory Test{{$labSearchInput}}{{$labSearchBtn}}
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="service_list">
	</div>
</div>
{{$form_end}}