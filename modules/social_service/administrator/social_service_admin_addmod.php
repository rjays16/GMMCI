<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
//include xajax_common
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/social_service/ajax/social_mod_add_common_ajx.php');

require_once($root_path.'include/care_api_classes/class_social_service.php');
//Instantiate social service class
$objSS = new SocialService;

$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
	function submitAddForm(){
		//$('is_forall').value = ($('is_forall').checked)? 1:0;
		xajax_processForm(xajax.getFormValues("ssAddForm"));
		return false;
	}
	//<!-- onclick="xajax_refresh();"-->
	
	function preSet(code){
		if (document.getElementById('mode').value=="update")
			document.getElementById('modcode').disabled= true;
		else
			document.getElementById('modcode').disabled= false;	
	}
	
</script>

</head>

<!--<body onload="preSet('<?=$_GET['code']?>');"  onunload="window.parent.xajax_listModifierRow(1);">-->
<body onload="preSet('<?=$_GET['modi']?>');">

	<table width="98%"cellpadding="2" cellspacing="2" style="margin:1%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
					<form id="ssAddForm" action="javascript:void(null);" onsubmit="submitAddForm();">
					<table width="98%" border="0" cellpadding="0" cellspacing="2" style="font:bold 12px Arial; color:#2d2d2d; margin:1%">
						<tr>
							<td width="10%" align="right">Modifier</td>
							<td valign="middle" width="*">	
								<select id="modcode" name="modcode" onChange="document.getElementById('mod_code').value=this.value">
									<option value="0">-Select a Modifier-</option>
									<?php 
										$modSSinfo = $objSS->getAllModifiers();
	
										while($row=$modSSinfo->FetchRow()){
											if ($_GET['modi']==$row['mod_code'])
												echo '<option value="'.$row['mod_code'].'" selected>'.$row['mod_desc'].'</option>';	
											else
												echo '<option value="'.$row['mod_code'].'">'.$row['mod_desc'].'</option>';		
											#echo "<br>here = ".$row['mod_code']." - ".$row['mod_desc'];
										}
									?>
								</select>
								<input type="hidden" id="mod_code" name="mod_code" value="<?=$_GET['modi']?>">
								<input type="hidden" id="mod_subcode" name="mod_subcode" value="<?=$_GET['subcode']?>">
							</td>
						</tr>
						<tr>
							<td width="10%" align="right" valign="top">Description</td>
							<td><textarea id="mod_desc" name="mod_desc" cols="3" rows="2" style="width:98%; font:bold 12px Arial"><?=$_GET['desc']?></textarea> 
							</td>
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
