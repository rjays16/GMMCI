<?php
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_person.php';

if (empty($_GET['provider'])) {
    header($_SERVER["SERVER_PROTOCOL"]." 400 Required information not found");
}

$provider = $_GET['provider'];

require_once $root_path.'include/care_api_classes/eclaims/eligibility/class_eligibilityprofile.php';
$eligibilityService = new EligibilityProfile;

$params = array(
    'pMemberLastName' => @$_GET['lastname'],
    'pMemberFirstName' => @$_GET['firstname'],
    'pMemberMiddleName' => @$_GET['middlename'],
    'pMemberSuffix' => @$_GET['suffix'],
    'pMemberBirthDate' => @$_GET['birthdate'],
);
$result = $eligibilityService->getMemberPIN($params);

if (!empty($result['result'])) {
    var_dump($result);
} else {
    header($_SERVER["SERVER_PROTOCOL"]." 500 " . $result['error']['reason']);
}