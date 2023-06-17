<?php

if(!isset($GLOBAL_CONFIG)) {
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
        $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('patient_%');
}
//if(!$encounter_class_nr) $encounter_class_nr=1;
switch ($encounter_class_nr)
{
    case '2' : #$headframe_title="$LDAdmission :: $LDAmbulant";   # burn commented : May 15, 200
	 				$headframe_title="$LDConsultation :: $LDAmbulant";   # burn added : May 15, 2007
					if($encounter_nr){
						//$full_en = $encounter_nr + $GLOBAL_CONFIG['patient_outpatient_nr_adder'];
	               $full_en = $encounter_nr;
						$headframe_append= ' - '.$LDUpdateData.' ('.$full_en.')';
					}
					break;
	case '1' : #$headframe_title="$LDAdmission :: $LDStationary";   # burn commented : May 15, 2007
	 				if ($seg_direct_admission)
						$headframe_title="$LDDirectAdmission";   # burn added : May 15, 2007
					else
						$headframe_title="$LDConsultation :: $LDStationary";   # burn added : May 15, 2007
					if($encounter_nr){
						// $full_en = $encounter_nr + $GLOBAL_CONFIG['patient_inpatient_nr_adder'];
						$full_en = $encounter_nr;
						$headframe_append= ' - '.$LDUpdateData.' ('.$full_en.')';
					}
					break;
	default: $headframe_title=$LDAdmission;
}
?>
