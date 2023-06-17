<?php
#------------------added 03-13-07-----------
include_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

#---------------------------------------------
#echo "dept_belong : ".$dept_belong['id'];


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
<FONT face="Verdana,Helvetica,Arial" size=2 color="#cc0000">
<?php echo $LDOptsForPerson ?>  <a href="javascript:gethelp('preg_options.php')"><img <?php echo createComIcon($root_path,'frage.gif','0','absmiddle',TRUE) ?>></a>
</font>


	<TABLE border=0 cellPadding=0 cellSpacing=0 bgcolor="#F4F7FB">
          
<?php
	#echo "dept belong".$dept_belong['id'];
	if($current_encounter){
			
	#if(($current_encounter)||($dept_belong['id'] == "Medocs")){
	#if (($dept_belong['id'] == "ER")||($dept_belong['id'] == "OPD-Triage")){
				
?>
		<TR>
			<td width="32" align=center>&nbsp;</td>
			<td colspan="2" align=center background='<?php echo createComIcon($root_path,'opt_tl.jpg','0','',FALSE) ?>'>&nbsp;</td>
			<TD vAlign=top >&nbsp;</TD>
		</TR>
<?php 
	if ($dept_belong['id'] == "OPD-Triage"){ 
?>
				   <TR>
                 <td rowspan="16" align=center bgcolor="#F4F7FB" background='<?php echo createComIcon($root_path,'opt_r.jpg','0','',FALSE) ?>'>&nbsp;</td> 
                 <td width="32" align=center><img <?php echo createComIcon($root_path,'pdata.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <!-- commented 03-14-07 by vanessa ---->
				 <!--<a href="aufnahme_daten_zeigen.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $current_encounter ?>&origin=patreg_reg"><?php echo $LDPatientData; ?></a>-->
				 <!-- edit by vanessa--->
				 	<?php
						#if ($dept_belong['id'] == "ER"){
				 	?>
				 			<!--<a href="aufnahme_daten_zeigen.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $current_encounter ?>&origin=patreg_reg"><?php echo $LDStationary.' - '.$LDPatientData; ?>-->
			   <?php #}else
						#if ($dept_belong['id'] == "OPD-Triage"){ ?>
				 			<!--<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><?php echo $LDAdmission.' - '.$LDAmbulant; ?></a>-->
							<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><?php echo $LDOPDConsultation; ?></a>
				<?php } #} ?>	
				  </nobr> </FONT></TD>
                <TD width="10" rowspan="16" vAlign=top background='<?php echo createComIcon($root_path,'opt_t.jpg','0','',FALSE) ?>'>&nbsp;</TD>
               </TR>
			   
			<?php
			}elseif(!$death_date || ($death_date == DBF_NODATE)){
				if (($dept_belong['id'] == "ER")||($dept_belong['id'] == "OPD-Triage")){
			?>
               <TR>
                 <td width="32" align=center>&nbsp;</td>
                 <td colspan="2" align=center background='<?php echo createComIcon($root_path,'opt_tl.jpg','0','',FALSE) ?>'>&nbsp;</td>
                 <TD vAlign=top >&nbsp;</TD>
               </TR>
					<TR>
                 <td align=center><img <?php echo createComIcon($root_path,'post_discussion.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <!--<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1"><?php echo $LDAdmission.' - '.$LDStationary; ?></a>-->
				 <?php
				 if ($dept_belong['id'] == "ER"){
				 ?>
				 	<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1"><?php echo $LDERConsultation; ?></a>
				 <?php }elseif ($dept_belong['id'] == "OPD-Triage"){ ?>
				 	<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><?php echo $LDOPDConsultation; ?></a>
				 <?php } ?>	
				  </nobr> </FONT></TD>
              </TR>
			   
           <?php #Spacer(); ?>
				 <!-- 
             <TR>
               <td align=center><img <?php echo createComIcon($root_path,'discussions.gif','0','',FALSE) ?>></td>
                <TD vAlign=top width=209><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				<a href="aufnahme_start.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><?php echo $LDVisit.' - '.$LDAmbulant; ?></a>
				   </FONT></TD>
              </TR>
				-->
			<?php
			}}
			?>
			
           <?php #Spacer(); ?>
			<?php if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs")){ ?>	
				 <!-- 
              <TR>
                <td align=center><img <?php echo createComIcon($root_path,'timeplan.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
			 <a href="show_appointment.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDAppointments ?></a>
				   </FONT></TD>
              </TR>				 
					-->
           <?php Spacer(); } ?>
				  
              <TR>
                <td align=center><img <?php echo createComIcon($root_path,'qkvw.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <a href="show_encounter_list.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDListEncounters ?></a>
				  </nobr> </FONT></TD>
              </TR>
			   
           <?php Spacer(); ?>
				<?php if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs")){ ?>	  
               <TR>
                 <td align=center><img <?php echo createComIcon($root_path,'discussions.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				  <a href="show_medocs.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDMedocs ?></a>
				   </FONT></TD>
              </TR>
			   
           <?php Spacer();  ?>
				  
               <TR>
                 <td align=center><img <?php echo createComIcon($root_path,'eye_s.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				  <a href="show_drg.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDDRG ?></a>
				   </FONT></TD>
              </TR>
				
           <?php Spacer(); 
			  			}
				
						if($dept_belong['id']=="Admission"){
			  ?>
				  
               <TR>
                 <td align=center><img <?php echo createComIcon($root_path,'bubble.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				  <a href="show_diagnostics_result.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDDiagXResults ?></a>
				   </FONT></TD>
              </TR>
			   
<!-- 				   
           <?php Spacer(); ?>
				  
			  <TR><td align=center><img <?php echo createComIcon($root_path,'eye_s.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <a href="show_diagnosis.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDDiagnoses ?></a>
				  </nobr> </FONT></TD>
                </TR>
			   
           <?php Spacer(); ?>

               <TR> <td align=center><img <?php echo createComIcon($root_path,'discussions.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <a href="show_procedure.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDProcedures ?></a>
				  </nobr> </FONT></TD>
                </TR>
			   
 -->           <?php Spacer(); ?>
				  
              <TR>
                <td align=center><img <?php echo createComIcon($root_path,'prescription.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <a href="show_prescription.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDPrescriptions ?></a>
				  </nobr> </FONT></TD>
              </TR>
<!-- 			   
           <?php Spacer(); ?>
				  
              <TR><td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <a href="show_notes.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>&type_nr=21"><?php echo "$LDNotes - $LDPatientDev" ?></a>
				  </nobr> </FONT></TD>
                </TR>
 -->				
      	<?php Spacer(); ?>
				  
              <TR>
                <td align=center><img <?php echo createComIcon($root_path,'new_group.gif','0','',FALSE) ?>></td>
                <TD vAlign=top ><FONT 
                  face="Verdana,Helvetica,Arial" size=2> <nobr>
				 <a href="show_notes.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo "$LDNotes $LDAndSym $LDReports" ?></a>
				  </nobr> </FONT></TD>
              </TR>
           <?php Spacer(); ?>
				  
				  <TR>
				    <td align=center><img <?php echo createComIcon($root_path,'people_search_online.gif','0','',FALSE) ?>></td>
                <TD vAlign=top width=209><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				<a href="show_immunization.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDImmunization ?></a>
				   </FONT></TD>
                </TR>
			   
           <?php Spacer(); ?>
				  
				  <TR>
				    <td align=center><img <?php echo createComIcon($root_path,'people_search_online.gif','0','',FALSE) ?>></td>
                <TD vAlign=top width=209><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				<a href="show_weight_height.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDMeasurements ?></a>
				   </FONT></TD>
                </TR>
				
		  <?php
		  /* If the sex is female, show the pregnancies option link */
		   if($sex=='f') { 
		   ?>
           <?php Spacer(); ?>
				  
				  <TR>
				    <td align=center><img <?php echo createComIcon($root_path,'man-whi.gif','0','',FALSE) ?>></td>
                <TD vAlign=top width=209><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				<a href="show_pregnancy.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDPregnancies ?></a>
				   </FONT></TD>
                </TR>				  
		  <?php } ?>
		  
           <?php Spacer(); ?>
				  
				  <TR>
				    <td align=center><img <?php echo createComIcon($root_path,'new_address.gif','0','',FALSE) ?>></td>
                <TD vAlign=top width=209><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				<a href="show_birthdetail.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&target=<?php echo $target ?>"><?php echo $LDBirthDetails ?></a>
				   </FONT></TD>
                </TR>	

           <?php Spacer(); } ?>
			  <!--
				  <TR>
				    <td align=center><img <?php echo createComIcon($root_path,'new_address.gif','0','',FALSE) ?>></td>
                <TD vAlign=top width=209><FONT 
                  face="Verdana,Helvetica,Arial" size=2> 
				<a href="javascript:popRecordHistory('care_person',<?php echo $pid ?>)"><?php echo $LDRecordsHistory ?></a>
				   </FONT></TD>
                </TR>					
			-->		 
           <?php Spacer(); ?>
				  <TR>
				    <td align=center><img <?php echo createComIcon($root_path,'icon_acro.gif','0','',FALSE) ?>></td>
                <TD vAlign=top width=209>
				
						<FONT face="Verdana,Helvetica,Arial" size=2> 
							<a href="<?php echo $root_path."modules/pdfmaker/registration/regdata.php".URL_APPEND."&pid=".$pid ?>" target=_blank><?php echo $LDPrintPDFDoc ?></a>
						</FONT>
					</TD>
				</TR>
	
				  <TR>
				    <td >&nbsp;</td>
				    <td colspan="2"background="<?php echo createComIcon($root_path,'opt_b.jpg','0','',FALSE) ?>">&nbsp;</td>
				    <td >&nbsp;</td>
			    </TR>					
			
</TABLE>
		
