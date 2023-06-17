<?php
#---------- added by vanessa 03-14-07------------
require_once($root_path.'include/care_api_classes/class_encounter.php');
$encounter_obj=new Encounter($encounter_nr);

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department();

global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;

#echo "here = ".$ptype;
if($rows){
?>

<table border=0 cellpadding=0 cellspacing=1 width=100% >
  <tr class="reg_list_titlebar">
    <td>&nbsp;</td>
    <td><?php echo $LDDate; ?></td>
    <td><?php echo $LDEncounterNr; ?></td>
    <td><?php echo $LDAdmitType; ?></td>
    <td><?php echo $LDDept; ?></td>
    <td><?php echo $LDDischargeDate; ?></td>
  </tr>
<?php
		while($row=$list_obj->FetchRow()){
# echo "gui_show_encounter_list.php :  row : <br>\n"; print_r($row); echo "<br> \n";
			$buf=1;
			if($row['is_discharged']) $buf=0;
            
        #added by VAN 01-09-2013
        if (($row['encounter_type']==3) || ($row['encounter_type']==4))   
            $encounter_date = date("m/d/Y h:iA",strtotime($row['admission_dt']));    
        else
            $encounter_date = date("m/d/Y h:iA",strtotime($row['encounter_date']));
            
?>
  <tr bgcolor="#fefefe" valign="top">
    <td><?php if($buf) echo '<img '.createComIcon($root_path,'check2.gif','0','',TRUE).'>'; else echo '&nbsp;'; ?></td>
    <!--<td><?php echo @formatDate2Local($row['encounter_date'],$date_format); ?></td>-->
	<td><?php echo $encounter_date; ?></td>
    <td>
	<a href="aufnahme_daten_zeigen.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $row['encounter_nr']; ?>&origin=patreg_reg&ptype=<?=$ptype?>"><?php echo $row['encounter_nr'];	?></a>
	</td>
    <td>
	<?php 
# 		if (isset($$enc_class[$row['encounter_class_nr']]['LD_var'])&&!empty($$enc_class[$row['encounter_class_nr']]['LD_var'])) echo $$enc_class[$row['encounter_class_nr']]['LD_var'];    # burn commented : May 12, 2007
#    		else echo  $enc_class[$row['encounter_class_nr']]['name'];   # burn commented : May 12, 2007
		if ($row['encounter_type']==1){
			echo "<font color='red'>ER Consultation</font>";
		}elseif ($row['encounter_type']==2){
			echo "<font color='blue'>Outpatient</font>";
		}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
			echo "<font color='green'>Inpatient</font>";
		}
	?>
	</td>
    <!--<td><?php if($row['is_discharged']) echo $LDDischarged; ?></td>-->
	 <td>
	 		<?php 
				$dept = $encounter_obj->getEncounterDept($row['encounter_nr']);
 				echo $dept['name_formal'];
			?>
	 </td>
    <td>
		 <?php 
#		 		if($row['discharge_date']&&$row['discharge_date']!='0000-00-00') echo @formatDate2Local($row['discharge_date'],$date_format);     # burn commented : May 12, 2007
		 		if ($row['is_discharged']) echo @formatDate2Local($row['discharge_date'],$date_format);
		 ?>
	 </td>
    <td></td>
  </tr>

<?php
$result = $encounter_obj->getReferrals($row['encounter_nr']);
while($referral = $result->FetchRow())
{
    ?>
    <tr bgcolor="#fefefe" valign="top">
    <td><?php if($buf) echo '<img '.createComIcon($root_path,'check2.gif','0','',TRUE).'>'; else echo '&nbsp;'; ?></td>
    <td><?php echo @formatDate2Local($referral['referral_date'],$date_format); ?></td>
    <td>
    <a href="aufnahme_daten_zeigen.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $row['encounter_nr']; ?>&origin=patreg_reg"><?php echo $row['encounter_nr'] ." (".$referral["referral_nr"] .")";    ?></a>
    </td>
    <td>
    <?php 
        if ($referral['is_referral']==0){
            echo "Transfer";
        }elseif ($referral['is_referral']==1){
            echo "Referral";
        }
    ?>
    </td>
     <td>
             <?php 
                 $dept = $dept_obj->getDeptAllInfo($referral['referrer_dept']);
                 echo $dept["name_formal"];
            ?>
     </td>
    <td>
         <?php 
                 if ($row['is_discharged']) echo @formatDate2Local($row['discharge_date'],$date_format);
         ?>
     </td>
    <td></td>
  </tr>
    <?php
}
        }
?>
</table>
<?php
}
?>

