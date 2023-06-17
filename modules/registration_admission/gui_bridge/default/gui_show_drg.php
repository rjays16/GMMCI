<?php
#---------- added by vanessa 03-14-07------------
require_once($root_path.'include/care_api_classes/class_encounter.php');
//$encounter_obj=new Encounter($encounter_nr);

if($rows){
?>
<script language="javascript" >
<!-- 
function openDRGComposite(enr,edit,isd){
<?php if($cfg['dhtml'])
	echo '
			w=window.parent.screen.width;
			h=window.parent.screen.height;';
	else
	echo '
			w=800;
			h=650;';
?>
	
	drgcomp_<?php echo $HTTP_SESSION_VARS['sess_pid'].$sid; ?>=window.open("<?php echo $root_path ?>modules/drg/drg-composite-start.php<?php echo URL_REDIRECT_APPEND."&display=composite&pn=\"+enr+\"&edit=\"+edit+\"&is_discharged=\"+isd+\"&ln=$name_last&fn=$name_first&bd=$date_birth"; ?>","drgcomp_<?php echo $encounter_nr.$sid; ?>","menubar=no,resizable=yes,scrollbars=yes, width=" + (w-15) + ", height=" + (h-60));
	window.drgcomp_<?php echo $HTTP_SESSION_VARS['sess_pid'].$sid; ?>.moveTo(0,0);
} 
//-->
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
  <tr bgcolor="#f6f6f6" valign="top">
    <td <?php echo $tbg; ?>><FONT SIZE=-1  FACE="Arial" color="#000066">&nbsp;</td>
    <td <?php echo $tbg; ?>><FONT SIZE=-1  FACE="Arial" color="#000066"><?php echo $LDDate; ?></td>
    <td <?php echo $tbg; ?>><FONT SIZE=-1  FACE="Arial" color="#000066"><?php echo $LDEncounterNr; ?></td>
	 <td <?php echo $tbg; ?>><FONT SIZE=-1  FACE="Arial" color="#000066"><?php echo $LDDept; ?></td>
	 <td <?php echo $tbg; ?>><FONT SIZE=-1  FACE="Arial" color="#000066"><?php echo $LDShow; ?></td>
	 
  </tr>
<?php
		//$encounter_obj=new Encounter($encounter_nr);
		//$patient_enc = $encounter_obj->getPatientEncounter($encounter_nr);
		//&mode=<?php if($row['is_discharged']==0)echo "new"; else echo "details"; 

		$encounter_obj=new Encounter();
		
		while($row=$drg_obj->FetchRow()){
			//print_r($row);
			$buf=1;
			if($row['is_discharged']) $buf=0;
		// <a href="patient_register_search.php<?php echo URL_APPEND; "><?php// echo $LDPatientSearch </a>
	//	header("location:".$thisfile.URL_REDIRECT_APPEND."&target=$target&mode=details&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&nr=".$HTTP_POST_VARS['ref_notes_nr']);
	//<a href="<?= $root_path >modules/medocs/show_medocs.php<?= URL_APPEND >&pid=<?=$pid >&encounter_nr=<?= $row['encounter_nr']>&target=entry&mode=<?php if($row['is_discharged']==0) echo "new"; else echo "show"; >&encounter_type=<?php $patient_enc = $encounter_obj->getPatientEncounter($row['encounter_nr']); echo $patient_enc['encounter_type']; >&current_dept_nr=<?=$row['current_dept_nr'] >&is_discharged=<?= $row['is_discharged'] >"><?= $row['encounter_nr']></a></td>
?>
	<tr bgcolor="#fefefe" valign="top">
    <td><FONT SIZE=-1  FACE="Arial"><?php if($buf) echo '<img '.createComIcon($root_path,'check2.gif','0','',TRUE).'>'; else echo '&nbsp;'; ?></td>
    <td><FONT SIZE=-1  FACE="Arial"><?php echo @formatDate2Local($row['encounter_date'],$date_format); ?></td>
    <td><font size=-1 face="Arial"><?= $row['encounter_nr']?></td>
    <td><?php 
				$dept = $encounter_obj->getEncounterDept($row['encounter_nr']);
 				echo $dept['name_formal'];
		  ?></td>
    <td><font size=-1 face="Arial"><a href="<?= $root_path ?>modules/medocs/show_medocs.php<?= URL_APPEND ?>&pid=<?=$pid ?>&encounter_nr=<?= $row['encounter_nr']?>&target=entry&encounter_type=<?php $patient_enc = $encounter_obj->getPatientEncounter($row['encounter_nr']); echo $patient_enc['encounter_type']; ?>&current_dept_nr=<?=$row['current_dept_nr'] ?>&is_discharged=<?= $row['is_discharged'] ?>"><img <?php echo createComIcon($root_path,'info2.gif','0','',TRUE); ?>></a></td>
		<!-- <td><FONT SIZE=-1  FACE="Arial"><a href="javascript:openDRGComposite('<?php// echo $row['encounter_nr'] ?>','<?php //echo $buf; ?>','<?php //echo $row['is_discharged'] ?>')"><?php //echo $row['encounter_nr']; ?></a></td> -->
   <!-- <td><a href="javascript:openDRGComposite('<?php //echo $row['encounter_nr'] ?>','<?php echo $buf; ?>','<?php echo $row['is_discharged'] ?>')"><img <?php //echo createComIcon($root_path,'info2.gif','0','',TRUE); ?></td> -->  
  
  </tr>
	
 	
<?php
	}
?>
</table>
<?php
}
?>

