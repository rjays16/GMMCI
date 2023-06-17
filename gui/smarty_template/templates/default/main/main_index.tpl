{{* main_index.tpl *}}
<html>
<head>
<title>SegHIS - Gonzales Maranan Medical Center Inc.</title>
<link rel="stylesheet" href="images/template_css.css" type="text/css" />

</style>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><style type="text/css">body {
	margin-left: 0px;
	margin-right: 0px;
}
</style>

<script type="text/javascript" src="js/jsprototype/prototype.js"></script>
<script type="text/javascript">
	function resizeContent() {
		$('contentMenu').style.height=(window.innerHeight-$('banner').height-4)+'px';
		$('contentFrame').style.height=(window.innerHeight-$('banner').height-4)+'px';
	}
</script>

</head>

<body style="overflow:hidden " onLoad="resizeContent();" onResize="resizeContent();">

	<table align="center" border="0" cellpadding="0" cellspacing="0" height="*" width="100%">
		<tbody>
			<tr>
				<td height="*" valign="top" bgcolor="#FFFFFF">
					<a name="up" id="up"></a>
					<iframe src="login_lnk.php" id="banner" name="banner" width="100%" frameborder="0" scrolling="no" height="124">banner frame</iframe>

					<table style="border: 1px solid rgb(153, 160, 170);" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
						<tbody>
							<tr>
								<td valign="top">
									<div id="contentMenu" style="width:150px;overflow-x:visible;overflow-y:hidden">
										<table align="center"  border="0" cellpadding="0" cellspacing="0" width="100%" style="height:100%;overflow-x:display;overflow-y:scroll;">
											<tbody>
												<tr>
													<td width="20%" height="100%" valign="top" style="border-right: 0px solid rgb(153, 160, 170); border-bottom: 1px solid rgb(255, 255, 255);">
														<table width="150" height="100%" border="0" cellpadding="0" cellspacing="0">
															<tbody>
																<tr>
																	<td valign="top" height="100%" style="">
																		<table width="100%" border="0" height="100%" cellpadding="0" cellspacing="0">
																			<tbody>
																				<tr>
																					<td valign="top" style="height:100%;">
																						{{$sMainMenu}}
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</td>
								<td width="100%" align="center" valign="top" bgcolor="#D2DEE3" style="border-left: 0px solid rgb(255, 255, 255); border-right: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);">
									<iframe id="contentFrame" src="{{$startPage|default:"main/startframe.php"}}" name="contframe" width="100%" height="700" frameborder="0" scrolling="auto">***</iframe>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>

</html>