<?php
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/eclaims/eligibility/class_eligibilityprofile.php');
    require_once($root_path.'include/care_api_classes/eclaims/eligibility/class_single_period.php');
    require_once($root_path.'include/care_api_classes/class_person.php');

    $params = $_GET;
    $ep = new EligibilityProfile();
     $person = new Person();
     
    if($params['encounter_nr']){
       $enc = $params['encounter_nr'];  
    } else {
       $enc = $person->CurrentEncounter3($_GET['pid']);
    }
    
    if (!empty($enc)) {
        $ep->setPatientCase($enc);
        unset($params['encounter_nr']);
    } else {
        if (!empty($_GET['pid'])) {
            $ep->setPatient($params['pid']);
            unset($params['pid']);
        }
    }
     
    //die(print_r($output['result'],true));
    $result = $ep->isClaimEligible($params);
    if ($result['result']) { 
        echo base64_encode($result['result']);
    } else {
        header($_SERVER["SERVER_PROTOCOL"]." 500 " . $result['error']['reason']);
        echo $result['error']['reason'];
    }


