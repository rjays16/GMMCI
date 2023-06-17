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
		<li><a href="#test_group"><span>Services with groups</span></a></li>
		<li><a href="#test_service"><span>Services without groups</span></a></li>
		<!-- <li><a href="#test_service2"><span>Services report groups</span></a></li> -->
	</ul>
	<div id="test_group">
		<div>
		 <table align="center" cellpadding="2" cellspacing="2" border="0" width="82%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
				<tbody>
						<tr>
							<td class="segPanelHeader" colspan="2"><strong>Search service with test group</strong></td>
						</tr>
						<tr>
							<td class="segPanel">
								<table align="center" width="82%" style="font:bold 12px Arial;">
									<tbody>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Section</b></td>
											<td style="width:400px" nowrap="nowrap">{{$sectionsWith}}</td>
										</tr>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Service Name</b></td>
											<td style="width:400px" nowrap="nowrap">{{$testGroupSearch}}&nbsp;{{$groupSearchBtn}}&nbsp;{{$toolsBtn}}</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
				</tbody>
			</table>
			<br/>
			<div id="test_grp_list" align="center"></div>
		 </div>
	</div>
	<div class="blues" id="test_service">
		<div>
		 <table align="center" cellpadding="2" cellspacing="2" border="0" width="82%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
				<tbody>
						<tr>
							<td class="segPanelHeader" colspan="2"><strong>Search service without test group</strong></td>
						</tr>
						<tr>
							<td class="segPanel">
								<table align="center" width="82%" style="font:bold 12px Arial;">
									<tbody>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Section</b></td>
											<td style="width:400px" nowrap="nowrap">{{$sectionsWitho}}</td>
										</tr>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Service Name</b></td>
											<td style="width:400px" nowrap="nowrap">{{$testServiceSearch}}&nbsp;{{$serviceSearchBtn}}</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
				</tbody>
			</table>
			<br/>
			<div id="test_srv_list" align="center"></div>
		 </div>
	</div>
	<!-- <div class="blues" id="test_service2">
		<div>
		 <table align="center" cellpadding="2" cellspacing="2" border="0" width="82%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
				<tbody>
						<tr>
							<td class="segPanelHeader" colspan="2"><strong>Search service without test group</strong></td>
						</tr>
						<tr>
							<td class="segPanel">
								<table align="center" width="82%" style="font:bold 12px Arial;">
									<tbody>
										<tr>
											<td style="width:90px" nowrap="nowrap" align="right"><b>Service Form</b></td>
											<td style="width:400px" nowrap="nowrap">{{$GroupFromSearch}}&nbsp;{{$GroupFormSearchBtn}}{{$AddFormBtn}}</td>
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
	</div> -->
</div>
<br/>
{{$formend}}

</body>

</html>