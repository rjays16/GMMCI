<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add Social Service Classification</title>

<?php
require('./roots.php');
//include xajax_common
require_once($root_path.'modules/social_service/ajax/social_add_common_ajx.php');

$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
	function submitAddForm(){
		$('is_forall').value = ($('is_forall').checked)? 1:0;
		xajax_processForm(xajax.getFormValues("ssAddForm"));
		return false;
	}
	//<!-- onclick="xajax_refresh();"-->
	
	function preSet(code){
		//alert('code = '+code);
		if (code)
			document.getElementById('service_desc').focus();
		else
			document.getElementById('service_code').focus();	
	}
	
</script>

</head>
<body onload="preSet('<?=$_GET['code']?>');"  onunload="window.parent.xajax_listRow();">
	<table width="98%"cellpadding="2" cellspacing="2" style="margin:1%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
					<form id="ssAddForm" action="javascript:void(null);" onsubmit="submitAddForm();">
					<table width="98%" border="0" cellpadding="0" cellspacing="2" style="font:bold 12px Arial; color:#2d2d2d; margin:1%">
						<tr>
							<td width="10%" align="right">Code</td>
							<td valign="middle" width="*">
								<?php
									if ($_GET['code'])
										$readonly='readonly';
									else	
										$readonly='';
								?>
							  <input id="service_code" name="service_code" class="segInput" type="text" style="width:20%; font: bold 12px Arial" <?=$readonly?> align="absmiddle" value="<?=$_GET['code']?>" />
								&nbsp;
								<input id="is_forall" name="is_forall" class="segInput" type="checkbox" value="this.checked" <?php if($_GET['forall']=='1')echo 'checked';?>  /><span style="font-style:inherit">Is applied to all?</span>	
							</td>
						</tr>
						<tr>
							<td width="10%" align="right" valign="top">Description</td>
							<td><textarea id="service_desc" name="service_desc" cols="3" rows="2" style="width:98%; font:bold 12px Arial"><?=$_GET['desc']?></textarea> 
							</td>
						</tr>
						<tr>
							<td width="10%" align="right">Discount</td>
							<td valign="middle" width="*">
								<input id="service_discount" name="service_discount" class="segInput" type="text" style="width:10%; font:bold 12px Arial" align="absmiddle" value="<?=$_GET['discount']?>" />%							</td>
						</tr>
						<tr>
							<td colspan="3" align="right">
								<span>
									<input id="submitBtn" type="submit" value="Save" />
								</span>
								<span>
									<input id="cancelBtn" type="button" value="Cancel" onclick="xajax_refresh()" />
								</span>							</td>
						</tr>
					</table>
										
					<input type="hidden" id="sid" name="sid" value="<?php echo $_GET['sid'];?>" />
					<input type="hidden" id="lang" name="lang" value="<?php echo $_GET['lang'];?>" />
					<input type="hidden" id="encoder" name="encoder" value="<?php echo $_GET['create_id'];?>" />
					<input type="hidden" id="mode" name="mode" value="<?php echo $_GET['mode'];?>" />
					
					</form>

				</td>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
