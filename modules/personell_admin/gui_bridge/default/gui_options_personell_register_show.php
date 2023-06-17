<!---- add 02-22-07 --------->
<script type="text/javascript">
function blocking(objectName, flag){
	 if (document.layers) {
			document.layers[objectName].display = (flag) ? '' : 'none';
	 } else if (document.all) {
			document.all[objectName].style.display = (flag) ? '' : 'none';
	 } else if (document.getElementById) {
			document.getElementById(objectName).style.display = (flag) ? '' : 'none';
	 }
}/* end of function blocking*/

function hideThis(label_role){
 var allow_dependent_only = '<?=$allow_dependent_only?>';
 
   if (allow_dependent_only==0){   
	 switch(label_role){
			case "doctor": 
				 blocking("doctor_option",true); /* display */ 
				 blocking("nurse_option",false);  /* not display */
			blocking("others_option",false);  /* not display */
			break;
			
		case "nurse": 
				 blocking("doctor_option",false); /* display */ 
				 blocking("nurse_option",true);  /* not display */
			blocking("others_option",false);  /* not display */
		break;
		
		case "others": 
				 blocking("doctor_option",false); /* display */ 
				 blocking("nurse_option",false);  /* not display */
			blocking("others_option",true);  /* not display */
		 break;
	 }/* end of switch statement*/
   }  
}/*end of function hideThis*/

function preSet(){
	var label_role;

	if (document.forms["form_option"].short_id.value.match("D")!=null)
		label_role = "doctor";
	else if (document.forms["form_option"].short_id.value.match("N")!=null)
		label_role = "nurse";
	else
		label_role = "others";
		
	hideThis(label_role);
}
</script>

<!---- add 02-22-07 --------->

<body onLoad="preSet();">

<?php
#echo "van : ".$short_id;
$hideinfo = '';
if ($allow_dependent_only){
    $hideinfo = 'style="display:none"';   
}    

function Spacer()
{
/*?>
<TR bgColor=#dddddd height=1>
								<TD colSpan=3><IMG height=1 
									src="../../gui/img/common/default/pixel.gif" 
									width=5></TD></TR>
<?php
*/}

?>
<img <?php echo createComIcon($root_path,'angle_left_s.gif',0); ?>>
<br>
<FONT color="#cc0000">
<?php echo $LDOptions4Employee; ?>
</font>
<form name="form_option" id="form_option">

<TABLE cellSpacing=0 cellPadding=0 bgColor=#999999 border=0>
				<TBODY>
				<TR>
					<TD>
						<TABLE cellSpacing=1 cellPadding=2 bgColor=#999999 
						border=0>
							<TBODY>
					
							 <TR <?php echo $hideinfo?> bgColor=#eeeeee id="doctor_option"> <td align=center><img <?php echo createComIcon($root_path,'man-whi.gif','0') ?>></td>
								<TD vAlign=top >

<!-- 				 <a href="javascript:alert('Function not  available yet');"><?php echo $LDAssignDoctorDept; ?></a>
 -->				 <a href="<?php echo $root_path; ?>modules/doctors/doctors-select-dept.php<?php echo URL_APPEND."&target=plist&nr=$personell_nr&user_origin=personell_admin"; ?>"><?php echo $LDAssignDoctorDept; ?></a></TD>
								</TR>
				 
					 <?php Spacer(); ?>
					
						 <TR <?php echo $hideinfo?> bgColor=#eeeeee id="nurse_option"><td align=center><img <?php echo createComIcon($root_path,'nurse.gif','0') ?>></td>
								<TD vAlign=top width=150> 
									 
				<a href="<?php echo $root_path; ?>modules/nursing_or/nursing-or-select-dept.php<?php echo URL_APPEND."&target=plist&nr=$personell_nr&user_origin=personell_admin"; ?>"><?php echo $LDAssignNurseDept; ?></a>
					 </FONT></TD>
								</TR>
				 
					 <?php Spacer(); ?>
				
					 <TR <?php echo $hideinfo?> bgColor=#eeeeee id="others_option"><td align=center><img <?php echo createComIcon($root_path,'authors.gif','0') ?>></td>
								<TD vAlign=top width=150> 
									 
				<a href="<?php echo $root_path; ?>modules/staff/staff-select-dept.php<?php echo URL_APPEND."&target=plist&nr=$personell_nr&user_origin=personell_admin"; ?>"><?php echo $LDAssignStaffDept; ?></a>
					 </FONT></TD>
								</TR>
				 
					 <?php Spacer(); ?>
				
							<TR <?php echo $hideinfo?> bgColor=#eeeeee>  <td align=center><img <?php echo createComIcon($root_path,'violet_phone.gif','0') ?>></td>
								<TD vAlign=top > 
									 
			 <a href="<?php echo $root_path.'modules/phone_directory/phone_edit.php'.URL_APPEND.'&user_origin=pers&nr='.$personell_nr; ?>"><?php echo $LDAddPhoneInfo ?></a>
					 </FONT></TD>  
								</TR>				 
				 
<!--  			   
					 <?php Spacer(); ?>
					
							 <TR bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'disc_repl.gif','0') ?>></td>
								<TD vAlign=top > 
									 
					<a href="javascript:alert('Function not  available yet')"><?php echo $LDPayrollOptions ?></a>
					 </FONT></TD>
								</TR>
				 
					 <?php Spacer(); ?>
					
					<TR bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'document.gif','0') ?>></td>
								<TD vAlign=top > 
									 <nobr>
				 <a href="javascript:alert('Function not  available yet')"><?php echo $LDLegalDocuments ?></a>
					</nobr> </FONT></TD>
								</TR>
 -->			   
					 <?php Spacer(); ?>
					
					<TR bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'bn.gif','0') ?>></td>
								<TD vAlign=top > 
									 <nobr>
				 <a href="<?php echo "person_register_show.php".URL_REDIRECT_APPEND."&pid=$pid&from=$from"; ?>"><?php echo $LDShowPersonalData ?></a>
					</nobr> </FONT></TD>
								</TR>
				<?php Spacer(); ?>
				<TR bgColor=#eeeeee>
			<td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
			<TD vAlign=top width=209>
				<FONT face="Verdana,Helvetica,Arial" size=2> 
					<!--<a href="seg-dependents.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>">Dependents</a>-->
					<a href="javascript:void(0);" onClick="Dependents();" onMouseOut="nd();">Dependents</a>
				</FONT>
			</TD>
		</TR>
		<!-- added by VAN 12-04-09 -->
					 <?php Spacer(); ?>
					
					<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'application_key.png','0') ?>></td>
								<TD vAlign=top > 
									 <nobr>
					<?php
							global $db;
							$sql = "SELECT * FROM care_users WHERE personell_nr='".$personell_nr."' LIMIT 1";
							$rs = $db->Execute($sql);
							$row = $rs->FetchRow();  
							
							if(!empty($row['login_id'])){
									$with_access = 1;
									$userid = $row['login_id'];
									$username = '';
							}else{
									$with_access = 0;
									$sql_p = "SELECT p.* 
													FROM care_person AS p
													INNER JOIN care_personell AS pr ON pr.pid=p.pid
													WHERE nr='".$personell_nr."'";
													
									$rs_p = $db->Execute($sql_p);
									$row_p = $rs_p->FetchRow();		
									$userid = strtr($row_p['name_last'],' ','_');
									$username = strtr(($row_p['name_first'].' '.$row_p['name_last']),' ','+');
							}		
						
					?>				 
				 <a href="javascript:void(0);" onClick="showPermission('<?=$personell_nr?>','<?=$with_access?>','<?=$userid?>','<?=$username?>');" onMouseOut="nd();">Access Permission</a>
					</nobr> </FONT></TD>
								</TR>			
								
					<!--added by VAN 11-04-09 -->			
					 <?php Spacer(); ?> 
				 
					<?php if (empty($row_per['status'])){ ?> 
							<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'bn.gif','0') ?>></td>
										<TD vAlign=top > 
											 <nobr>
						 <a href="javascript:deactivatePersonnel('<?=$personell_nr?>',1);">Deactivate the personnel employment</a>
							</nobr> </FONT></TD>
										</TR>    
						<?php if ($with_access){?>					
							<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'bn.gif','0') ?>></td>
										<TD vAlign=top > 
											 <nobr>
						 <a href="javascript:changePassword('<?=$personell_nr?>');">Change user password</a>
							</nobr> </FONT></TD>
										</TR>			
					<?php 
								}
						}else{?>		
							<TR <?php echo $hideinfo?> bgColor=#eeeeee><td align=center><img <?php echo createComIcon($root_path,'bn.gif','0') ?>></td>
										<TD vAlign=top > 
											 <nobr>
						 <a href="javascript:deactivatePersonnel('<?=$personell_nr?>',0);">Activate the personnel employment</a>
							</nobr> </FONT></TD>
										</TR>
					<?php } ?>								
					<!-- -->
									</TBODY>
		</TABLE>
		</TD></TR>
		</TBODY>
		</TABLE>
		<input type="hidden" name="short_id" id="short_id" value="<?php echo $short_id; ?>">
</form>
</body>