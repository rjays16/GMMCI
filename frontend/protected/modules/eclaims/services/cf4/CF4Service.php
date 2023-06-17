<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/18/2019
 * Time: 4:54 PM
 */

namespace SegHis\modules\eclaims\services\cf4;

use EclaimsEncounter;
use SegHis\modules\eclaims\models\EclaimsCf4;
use SegHis\modules\eclaims\models\EclaimsConfig;

class CF4Service
{

    public $encounter;


    /**
     * Algorithm for getting the phciTransno on CF4
     * */
    public static function getpHciTransNo($encounterNo)
    {
        return date('m-Y') . '-' . $encounterNo;
    }

    public function saveCF4Xml($xml, $encounter, $transmittalNo)
    {

        $transNo = self::getpHciTransNo($encounter);
        $model = EclaimsCf4::model()->findbyPk($transNo);


        if (empty($model)) {
            $model = new EclaimsCf4();
            $model->phic_trans_no = self::getpHciTransNo($encounter);
            $model->created_at = date('Y-m-d H:i:s');
        }

        $model->encounter_nr = $encounter;
        $model->transmit_no = $transmittalNo;
        $model->xml = $xml;
        $model->is_uploaded = 0;
        $model->updated_at = date('Y-m-d H:i:s');


        if (!$model->save()) {
            throw new \Exception('Unable to save CF4 Xml');
        }
    }

    public static function getMemberPin($encounter)
    {
        $result = \Yii::app()->db->createCommand()
            ->select('insurance_nr')
            ->from('seg_encounter_insurance_memberinfo')
            ->where('encounter_nr=:encounter', array(':encounter' => $encounter))
            ->queryRow();

        return $result['insurance_nr'];
    }

    public static function getPatientPin($encounter)
    {
        $result = \Yii::app()->db->createCommand()
            ->select('t.patient_pin , t.relation')
            ->from('seg_encounter_insurance_memberinfo t')
            ->where('encounter_nr=:encounter AND t.hcare_id=:hcare_id', array(':encounter' => $encounter, ':hcare_id' => '18'))
            ->queryRow();

        if ($result['relation'] == self::getMemberSelf())
            return self::getMemberPin($encounter);
        else
            return $result['patient_pin'];
    }

    public static function getPatientType($encounter)
    {

        $result = \Yii::app()->db->createCommand()
            ->select('*')
            ->from('seg_encounter_insurance_memberinfo t')
            ->where('t.encounter_nr=:encounter', array(':encounter' => $encounter))
            ->queryRow();


        if ($result['relation'] == self::getMemberSelf())
            return EclaimsConfig::model()->findByAttributes(array(
                'type' => 'eclaims_cf4_patient_type_member'
            ))->value;
        else
            return EclaimsConfig::model()->findByAttributes(array(
                'type' => 'eclaims_cf4_patient_type_dependent'
            ))->value;

    }

    public static function getMemberSelf()
    {
        $self = EclaimsConfig::model()->findByAttributes(array(
            'type' => 'eclaims_cf4_patient_member_self'
        ))->value;

        return $self;
    }


}