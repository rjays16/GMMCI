<?php
/**
* GUI Options for patient with temporary pid
* burn added : July 25, 2007
*/
#echo "pid = ".$pid;
#echo "enc = ".$encounter_type;

global $allow_updateData, $allow_add_charges, $allow_consult_admit, $allow_only_clinic, $allow_phs_user, $allow_ipddiscancel, $allow_receive, $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;

?>
<FONT face="Verdana,Helvetica,Arial" size=2 color="#cc0000">
<?php

		#edited by VAN 03-26-08
		#if (($fromtemp) || (($fromtemp)&&($dept_belong['id']=="Medocs")) || (($isDied)&&($dept_belong['id']=="Medocs")) || (($discharged)&&($dept_belong['id']=="Medocs"))){
		#if (($fromtemp) || (($fromtemp)&&($dept_belong['id']=="Medocs")) || (($isDied)&&($dept_belong['id']=="Medocs")) || ($discharged) || !($discharged)){
#if (!$allow_only_clinic){
		if (($fromtemp) || (($fromtemp)&&($allow_medocs_user)) || ((($patient_result['result_code']==4)||($patient_result['result_code']==8)||($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($allow_medocs_user)) || (($discharged)&&($allow_medocs_user))
				|| ((!($discharged) && (($encounter_type_a==3)||($encounter_type_a==4))) && !($isDied) && ($allow_medocs_user))){

?>
<?php echo $LDOptsForPerson ?>  <a href="javascript:gethelp('preg_options.php')"><img <?php echo createComIcon($root_path,'frage.gif','0','absmiddle',TRUE) ?>></a>
<?php } ?>
</font>

	<TABLE border=0 cellPadding=0 cellSpacing=0 bgcolor="#F4F7FB">
		<!--added by VAN 02-23-08 -->
		<?php

								$isdischarged = 0;
								$row_ipd = $encounter_obj->getLatestEncounter($pid);
								if (!$_GET['list']){
										 if (($current_encounter)&&((($allow_er_user)&&($ptype=='er'))||(($allow_ipd_user)&&($ptype=='ipd')))) {
												$row = $encounter_obj->getPatientEncounter($current_encounter);
												#echo "d = ".$current_encounter;
												$enctype = $row["encounter_type"];
												#echo "sql = ".$encounter_obj->sql;

												#if ($row["is_discharged"])
												if (($row["is_discharged"]) && (($row["encounter_type"]==1)||($row["encounter_type"]==3)||($row["encounter_type"]==4)))
														$isdischarged = 1;
										 }else{
												 $enctype =  $row_ipd['encounter_type'];
												 if (($row_ipd["is_discharged"]) && (($row_ipd["encounter_type"]==1)||($row_ipd["encounter_type"]==3)||($row_ipd["encounter_type"]==4)))
														$isdischarged = 1;
										 }

								}else{
										$enctype =  $row_ipd['encounter_type'];
										if (($row_ipd["is_discharged"]) && (($row_ipd["encounter_type"]==1)||($row_ipd["encounter_type"]==3)||($row_ipd["encounter_type"]==4)))
												$isdischarged = 1;
								}

								#$row_ipd = $encounter_obj->getLatestEncounter($pid);

								if (empty($source)){
										if (($allow_consult_admit)&&(($allow_er_user)||($allow_opd_user)||($allow_ipd_user)||($allow_phs_user))){
													#if ((($allow_ipd_user)&&($ptype=='ipd')&&(($row_ipd["encounter_type"]==2)||(empty($row_ipd["encounter_type"]))))
													if ((($allow_ipd_user)&&($ptype=='ipd'))
																||(($allow_er_user)&&($ptype=='er')) || (($allow_opd_user)&&($ptype=='opd')) || (($allow_phs_user)&&($ptype=='phs'))){
		?>
				<TR>
								<?php
								#((($allow_ipd_user)&&($ptype=='ipd')&&($isdischarged))||(($enctype==2)||(empty($enctype))))
								if (((($allow_ipd_user)&&($ptype=='ipd')&&($isdischarged))||(($enctype==2)||(empty($enctype))|| ($enctype==12)))
										|| ((($allow_er_user)&&($ptype=='er')&&($isdischarged))||(($allow_er_user)&&($ptype=='er')&&(($enctype==2)||(empty($enctype)))))
										|| ((($allow_opd_user)&&($ptype=='opd')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_opd_user)&&($ptype=='opd')&&(($enctype==2)||(empty($enctype)))))
					|| ((($allow_phs_user)&&($ptype=='phs')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_phs_user)&&($ptype=='phs')&&(($enctype==2)||(empty($enctype)))))){
						?>
			<td align=center><img <?php echo createComIcon($root_path,'post_discussion.gif','0','',FALSE) ?>></td>
			<? } ?>
						<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
				 <!--<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&ptype=<?=$ptype?>"><?php echo $LDAdmission.' - '.$LDStationary; ?></a>-->
<?php
														}
			#if (($allow_er_user)&&($ptype=='er')&&($isdischarged)){
						if ((($allow_er_user)&&($ptype=='er')&&($isdischarged))||(($allow_er_user)&&($ptype=='er')&&(($enctype==2)||(empty($enctype)) ||($enctype==12)))){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&ptype=<?=$ptype?>"><?php echo $LDERConsultation; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=1&ptype=<?=$ptype?>';  //shortcut key PageUp
			#}elseif (($allow_opd_user)&&($ptype=='opd')){
						#}elseif (($allow_opd_user)&&($ptype=='opd')&&($isdischarged)){
						}elseif ((($allow_opd_user)&&($ptype=='opd')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_opd_user)&&($ptype=='opd')&&(($enctype==2)||(empty($enctype))||($enctype==12)))){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>"><?php echo $LDOPDConsultation; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2';  //shortcut key PageUp
			#}elseif (($allow_ipd_user)&&(($ptype=='ipd')||($ptype=='newborn'))&&($isdischarged)){
						#||(($row_ipd["encounter_type"]==2)||(empty($row_ipd["encounter_type"])))
			}elseif ((($allow_phs_user)&&($ptype=='phs')&&(($enctype!=3)||($enctype!=4)||($enctype!=1))&&($isdischarged))||(($allow_phs_user)&&($ptype=='phs')&&(($enctype==2)||(empty($enctype))||($enctype==12)))){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype=<?=$ptype?>"><?php echo "PHS Consultation"; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=2';  //shortcut key PageUp
			#}elseif (($allow_ipd_user)&&(($ptype=='ipd')||($ptype=='newborn'))&&($isdischarged)){
						#||(($row_ipd["encounter_type"]==2)||(empty($row_ipd["encounter_type"])))
						}elseif ((($allow_ipd_user)&&($ptype=='ipd')&&($isdischarged))||(($enctype==2)||(empty($enctype))||($enctype==12))){
?>
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&encounter_type=3&seg_direct_admission=1&ptype=<?=$ptype?>"><?php echo $LDDirectAdmission; ?></a>
<?php
				$redirectAdConsult = 'aufnahme_start.php'.URL_APPEND.'&pid='.$pid.'&origin=patreg_reg&encounter_class_nr=1&encounter_type=3&seg_direct_admission=1';   //shortcut key shift+c 67
			}
?>
				</nobr>
				</FONT>
			</TD>
		</TR>
		<?php } ?>
				<TR>
			<td align=center><img <?php echo createComIcon($root_path,'qkvw.gif','0','',FALSE) ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
				<nobr>
					<a href="show_encounter_list.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>&ptype=<?=$ptype?>"><?php echo $LDListEncounters ?></a>
				</nobr>
				</FONT>
			</TD>
		</TR>
		<?php } ?>
		<!----------------------------------->
		<!--added by VAN 02-15-08-->
		<?php if ($fromtemp){?>
		<TR>

			<td align=center><img <?php echo createComIcon($root_path,'new_address.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
					<!--<a href="show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDBirthDetails ?></a>-->
					<!--<a href="../../modules/registration_admission/show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo ($source=='medocs')?'search':$target ?>"><?php echo $LDBirthDetails ?></a>-->
					<a href="../../modules/registration_admission/show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&ptype=<?=$ptype?>&target=<?php echo ($allow_medocs_user)?'new':'search' ?>"><?php echo $LDBirthDetails ?></a>
				</FONT>
			</TD>
		</TR>
		<?php if ($allow_medocs_user){ ?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_birth_interface.php".URL_APPEND."&pid=$pid\" target=_blank>Birth Certificate</a>";
?>
				</FONT>
			</TD>
		</TR>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_birth_interface_new.php".URL_APPEND."&pid=$pid\" target=_blank>Birth Certificate (New)</a>";
?>
                </FONT>
            </TD>
        </TR>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
        //edited by jasper 02/27/2013
        #echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_birth_erroneous_pdf_jasper.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Erroneous Entry on Birth Cert.</a>";

        #edited by VAN 03-01-2013
        echo "<a id=\"errBirth\" href=\"javascript:void(0);\" onclick=\"viewBirthError();\">Erroneous Entry on Birth Cert.</a>";
        //echo "<a id=\"erroneousBirth\" href=\"#\">Erroneous Entry on Birth Cert.</a>";
        //echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_Birth_erroneousEntry_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Erroneous Entry on Birth Cert.</a>";
?>
				</FONT>
			</TD>
		</TR>

<!--- added by pet --- trial --- for viewing only --->
<?php
	#added by VAN 07-02-08
	#echo "temp = ".$fromtemp;
	#edited by VAN 08-01-08
	#if ($isDied){
	#if ((($death_date!='0000-00-00')||($isDied))&&($dept_belong['id']=="Medocs")&&($fromtemp)) {
	#edited by VAN 08-01-08
	if (((($death_date!='0000-00-00')||($isDied))&&($allow_medocs_user)&&($fromtemp))||(($allow_medocs_user)&&($fromtemp))) {
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_fetal_interface.php".URL_APPEND."&pid=$pid\" target=_blank>Fetal Death Certificate</a>";
?>
				</FONT>
			</TD>
		</TR>
<?php } ?>
<!--- until here only --- pet --->

<?php }} ?>
<?php
	#if (($isDied)&&($dept_belong['id']=="Medocs")) {
	#if ((($patient_result['result_code']==4)||($patient_result['result_code']==8)||($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($dept_belong['id']=="Medocs")) {
	#if ((($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($dept_belong['id']=="Medocs")) {
	#if ($allow_medocs_user) {
		if ((($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')||($isDied))&&($allow_medocs_user)) {
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
	if ($enc_Info['is_DOA']==1){
		echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_DOA_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate</a>";
	}else{
		echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_interface.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate</a>";
	}

?>
				</FONT>
			</TD>
		</TR>
        <!-- added by jasper 01/05/12 -->
        <TR>
            <td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
            <TD vAlign=top >
                <FONT face="Verdana,Helvetica,Arial" size=2>
<?php
        #echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_Death_erroneousEntry_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Erroneous Entry on Death Certificate</a>";
        echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_death_interface_new.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate(New)</a>";
?>
                </FONT>
            </TD>
        </TR>

        <!-- added by jasper 01/05/12 -->
		<!-- added by VAN -->
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
		#echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_Death_erroneousEntry_pdf.php".URL_APPEND."&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Erroneous Entry on Death Certificate</a>";
		echo "<a href=\"javascript:void(0);\" onclick=\"viewDeathError();\">Erroneous Entry on Death Certificate</a>";
?>
				</FONT>
			</TD>
		</TR>
		<!-- -->

<?php }
 #Referral

 #edited by VAN 09-22-09
if ($allow_referral && !($discharged) && (($encounter_type_a==2) || (($encounter_type_a==1)&&($enc_Info['is_DOA']!=1)) || ((($encounter_type_a==3)||($encounter_type_a==4))&&($isDied==0))))
 {
		 ?>
				<TR>
						<td align=center><img <?php echo createComIcon($root_path,'hfolder.gif','0'); ?>></td>
						<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"ReferItem();\" onmouseout=\"nd();\">Refer/Transfer Department</a>";
?>
								</FONT>
						</TD>
				</TR>
				<TR>
						<td align=center><img <?php echo createComIcon($root_path,'hfolder.gif','0'); ?>></td>
						<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"ReferOtherItem();\" onmouseout=\"nd();\">Refer/Transfer to Other Hospitals</a>";
?>
								</FONT>
						</TD>
				</TR>
<?php
}

	#if (($discharged)&&($dept_belong['id']=="Medocs")){
	#if ($dept_belong['id']=="Medocs"){
	if (($allow_medocs_user)&&(($encounter_type_a==2) || ($encounter_type_a==1) || ($discharged)) && ($encounter_status<>'cancelled')){
		#if ($encounter_nr){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
#echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_med_interface.php?encounter_nr=$encounter_nr\" target=_blank>Medical Certificate</a>";
echo "<a href=\"javascript:void(0);\" onclick=\"viewCertMed($pid);\">Medical Certificate</a>";
?>
				</FONT>
			</TD>
		</TR>
<?php }?>
<!-- added by VAN 03-27-08-->
<?php
		//echo "enctype = ".$encounter_type_a;
		if ((!($discharged) && (($encounter_type_a==3)||($encounter_type_a==4))) && !($isDied) && ($allow_medocs_user)){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
#echo "<a href=\"".$root_path."modules/registration_admission/certificates/cert_conf_interface.php?encounter_nr=$encounter_nr\" target=_blank>Cert. of Confinement</a>";
echo "<a href=\"javascript:void(0);\" onclick=\"viewCertConf();\">Cert. of Confinement</a>";
?>
				</FONT>
			</TD>
		</TR>
<?php }?>


<?php
               
                if (($allow_medocs_user) && ($encounter_nr)){
?>
                 <tr>
                        <td align=center><img <?php echo createComIcon($root_path,'folder_page.png','0'); ?>>&nbsp;</td>
                             <td vAlign=top >
                                <font face="Verdana,Helvetica,Arial" size=2>
<?php
   echo "<a href=\"javascript:void(0);\" onclick=\"ConfinementHistory($pid);\">History of Confinement</a>";

?>
                                 </font>
                             </td>
                </tr>
<?php }#}?>




<?php
		if (($allow_receive)&&((($encounter_type_a==3)||($encounter_type_a==4)) && ($allow_medocs_user))){
?>
		<TR>
			<td align=center><img <?php echo createComIcon($root_path,'check.gif','0'); ?>></td>
			<TD vAlign=top >
				<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"updateReceivedDate(".$encounter_nr.");\">Received Patient's Chart</a>";
?>
				</FONT>
			</TD>
		</TR>
		<?php if (($received) && ($received!='not yet')){?>
				<TR>
							<td align=center><img <?php echo createComIcon($root_path,'delete.gif','0'); ?>></td>
							<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
										<?php
										echo "<a href=\"javascript:void(0);\" onclick=\"cancelReceivedDate(".$encounter_nr.");\">Cancel Received Chart</a>";
										?>
								</FONT>
							</TD>
						</TR>
		<?php } ?>
<?php }?>

<?php

				if (($allow_ipddiscancel) && ($discharged) && ($allow_medocs_user) && ($encounter_type_a) && ($encounter_status<>'cancelled')){
?>
				<TR>
						<td align=center><img <?php echo createComIcon($root_path,'manager.gif','0'); ?>></td>
						<TD vAlign=top >
								<FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"cancelDischarged(".$encounter_nr.");\">Cancel Discharge</a>";
?>
								</FONT>
						</TD>
				</TR>
<?php }#}?>


 <?php
                //added by shand 05-21-2013
                #can undo MGH
                if (($allow_MGH) && ($encounter_nr) && ($discharged==0) && ($is_maygohome)){
?>
                 <tr>
                        <td align=center><img <?php echo createComIcon($root_path,'arrow_undo.png','0'); ?>></td>
                             <td vAlign=top >
                                <font face="Verdana,Helvetica,Arial" size=2>
<?php
   echo "<a href=\"javascript:void(0);\" onclick=\"undoMGH(".$encounter_nr.");\">Undo MGH</a>";

?>
                                 </font>
                             </td>
                </tr>
<?php }#}?>






<?php
                //added by jarel 03-04-2013
                if (($allow_CancelDeath) && $death_date!='0000-00-00' && ($encounter_type_a) && ($allow_ipddiscancel)){
?>
                <TR>
                        <td align=center><img <?php echo createComIcon($root_path,'blackcross_sm.gif','0'); ?>></td>
                        <TD vAlign=top >
                                <FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"cancelDeath(".$encounter_nr.",".$pid.");\">Cancel Death</a>";
?>
                                </FONT>
                        </TD>
                </TR>
<?php }#}?>

<!-- added by VAN 12-20-2011 -->
<?php
                if (($allow_ipddiscancel) && ($allow_medocs_user) && ($encounter_status=='cancelled')){
?>
                <TR>
                        <td align=center><img <?php echo createComIcon($root_path,'manager.gif','0'); ?>></td>
                        <TD vAlign=top >
                                <FONT face="Verdana,Helvetica,Arial" size=2>
<?php
echo "<a href=\"javascript:void(0);\" onclick=\"undoCancellation(".$encounter_nr.",".$pid.");\">Undo Case Cancellation</a>";
?>
                                </FONT>
                        </TD>
                </TR>
<?php }#}?>

<!-- -->

	</TABLE>

<?php include_once($root_path.'modules/registration_admission/include/yh_options.php') ?>