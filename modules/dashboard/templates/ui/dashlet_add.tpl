<div class="data-form">
	<form id="form-{{$suffix}}" method="post" action="./">
		<div style="padding:4px">Select a Dashlet to add:</div>
		<div id="accordion-{{$suffix}}" style="width:100%">
{{foreach from=$categories item=category}}
			<h3><a href="#">{{$category.name}}</a></h3>
			<div style="padding:0; margin:0">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tbody>
{{foreach from=$category.dashlets item=dashlet}}
						<tr height="24">
							<td width="20%" align="center" style="border-bottom:1px solid #bebebe;">
								<img src="{{$sRootPath}}gui/img/common/default/{{$dashlet.icon}}" align="absmiddle" border="0"/>
							</td>
							<td align="left" style="border-bottom:1px solid #bebebe;">
								<a id="add-{{$dashlet.id}}-{{$suffix}}" href="#" onclick="Dashboard.dialog.close(); Dashboard.dashlets.add({name:'{{$dashlet.id}}'}); return false;">
									<span style="font:bold 12px Arial">{{$dashlet.name}}</span>
								</a>
							</td>
						</tr>
{{/foreach}}
					</tbody>
				</table>
			</div>
{{/foreach}}
		</div>
	</form>
</div>

<script type="text/javascript">
(function($) {
	$("#accordion-{{$suffix}}").accordion({
		autoHeight: false,
		animated: "slide",
	});
})(jQuery);

</script>