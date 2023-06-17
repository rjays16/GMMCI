<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>

{{foreach from=$css_and_js item=script}}
		{{$script}}
{{/foreach}}

</head>

<body>
{{$formstart}}
<div id="lab_test" align="center" style="width:90%;">
	<ul>
		<li><a href="#test_service2"><span>Services report groups</span></a></li>
	</ul>
	
	<div class="blues" id="test_service2">
		<div>
		 <table align="center" cellpadding="2" cellspacing="2" border="0" width="82%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
				<tbody>
						<tr>
							<td class="segPanelHeader" colspan="2"><strong>Search service without test group</strong></td>
						</tr>
						<tr>
							<td class="segPanel">
								<table align="center" width="82%" colspan='2' style="font:bold 12px Arial;">
									<tbody>
										<tr>
											<td width="41%" nowrap="nowrap" align="right"><b>Service Form</b></td>
											<td align="left" nowrap="nowrap">{{$GroupFromSearch}}</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
				</tbody>
			</table>
			<br/>
			<div id="Form_list" align="center"></div>
		 </div>
	</div>
</div>
<br/>
{{$formend}}

</body>

</html>