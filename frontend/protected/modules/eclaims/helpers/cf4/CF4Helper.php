<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/20/2019
 * Time: 12:39 AM
 */

namespace SegHis\modules\eclaims\helpers\cf4;

use SegHis\modules\eclaims\models\EclaimsConfig;

class CF4Helper
{

    public static function getYear()
    {
        return date('Y');
    }

    public static function getDate()
    {
        return date('Y-m-d');
    }

    public static function getDefaultPackageType()
    {

        $packageType = EclaimsConfig::model()->findByAttributes(array(
          'type' => 'eclaims_cf4_packagetype'
        ))->value;

        return $packageType;
    }

    public static function getDefaultEnlistStat()
    {

        $enlistStat = EclaimsConfig::model()->findByAttributes(array(
          'type' => 'eclaims_cf4_enlist_stat'
        ))->value;

        return (int)$enlistStat;
    }

    public static function getNotApplicable()
    {
        $notApplicable = EclaimsConfig::model()->findbyAttributes(array(
          'type' => 'eclaims_cf4_not_applicable'
        ))->value;

        return $notApplicable;
    }


    public static function getDefaultReportStatus()
    {
        $status = EclaimsConfig::model()->findbyAttributes(array(
          'type' => 'eclaims_cf4_default_report_status'
        ))->value;

        return $status;
    }

    public static function getDefaultIcd()
    {
        $data = EclaimsConfig::model()->findbyAttributes(array(
          'type' => 'eclaims_cf4_default_icd_code'
        ))->value;

        return $data;
    }


    public static function getDefaultNAstatus()
    {
        $data = EclaimsConfig::model()->findbyAttributes(array(
          'type' => 'eclaims_cf4_default_na_status'
        ))->value;

        return $data;
    }

    public static function getPersonReligion($religionId)
    {
        $command = \Yii::app()->db->createCommand();

        $command->select('religion_name');
        $command->from('seg_religion t');
        $command->where('t.religion_nr = :religion_nr');
        $command->params[':religion_nr'] = $religionId;

        $result = $command->queryRow();
        return $result['religion_name'];
    }

    public static function getCertificationId()
    {
        $data = EclaimsConfig::model()->findbyAttributes(array(
          'type' => 'eclaims_cf4_certificate_id'
        ))->value;

        return $data;
    }

    public static function getNomedsDrugCode(){

        $data = EclaimsConfig::model()->findbyAttributes(array(
            'type' => 'eclaims_cf4_no_med_drug_code'
        ))->value;

        return $data;
    }

    public static function getNomedsGenericCode(){

        $data = EclaimsConfig::model()->findbyAttributes(array(
            'type' => 'eclaims_cf4_no_med_gen_code'
        ))->value;

        return $data;
    }

    public static function getNomedsGeneric(){

        $data = EclaimsConfig::model()->findbyAttributes(array(
            'type' => 'eclaims_cf4_no_med_generic'
        ))->value;

        return $data;
    }

    public static function getVitalSignsImplementationDate(){
        $data = EclaimsConfig::model()->findbyAttributes(array(
            'type' => 'eclaims_cf4_vital_signs_implementation_date'
        ))->value;

        return $data;
    }
    public static function getAccreditationCode()
    {
        $data = EclaimsConfig::model()->findbyAttributes(array(
          'type' => 'eclaims_cf4_accre_code'
        ))->value;

        return $data;
    }



}