<?php

require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

abstract class SegReport {

    public function getHospitalInfo() {
        $hospitalConfig = new Hospital_Admin();
        $hospitalInfo = $hospitalConfig->getAllHospitalInfo();
        return array(
            'hospcountry' => @$hospitalInfo['hosp_country'],
            'hospagency' => @$hospitalInfo['hosp_agency'],
            'hospname' => @$hospitalInfo['hosp_name'],
            'hospaddr' => @$hospitalInfo['hosp_addr1'],
            'imagepath' => $this->getLogoPath()
        );
    }

    public abstract function run();

    public function getLogoPath() {
        return dirname(dirname(dirname(dirname(__FILE__)))) . '/gui/img/logos/gmmci_logo.png';
    }
} 