<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/30/2019
 * Time: 6:35 PM
 */

namespace SegHis\modules\eclaims\services\cf4;


use SegHis\modules\eclaims\helpers\cf4\CF4Helper;

class CF4DataService
{

    const MEDICINE_TYPE = 'M';

    public $document;

    public $encounter;

    /* Initializes Class for SOAP Service*/
    public function __construct(
      \EclaimsEncounter $encounter
    ) {
        $this->encounter = $encounter;
    }


    /* Generation of Vital Signs of Patient
     * @params void
     * @return array
     * */
    public function getVitalSigns()
    {
        $command = \Yii::app()->db->createCommand();

        $command->from('seg_cf4_vital_signs vs');
        $command->where('vs.encounter_nr = :encounter_nr AND vs.is_deleted != 1');
        $command->order('created_at ASC');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;

        $result = $command->queryRow();

        $data = array(
          'pSystolic' => $result['systolic'] ?: 0,
          'pDiastolic' => $result['diastolic'] ?: 0,
          'pHr' => $result['cr'] ? $result['cr'] : 0,
          'pRr' => $result['rr'] ? $result['rr'] : 0,
          'pHeight' => $result['height'] ? $result['height'] : 0,
          'pWeight' => $result['weight'] ? $result['weight'] : 0,
          'pTemp' => $result['temperature'] ? $result['temperature'] : 0,
          'pVision' => '',
          'pLength' => '',
          'pHeadCirc' => null,
          'pReportStatus' => CF4Helper::getDefaultReportStatus(),
          'pDeficiencyRemarks' => ''
        );


        return $data;
    }


    /* Generation of Medicine of Patient CF4
     * @params void
     * @return array
     * */
    public function getMedicines()
    {
        $command = \Yii::app()->db->createCommand();

        $command->from('seg_cf4_medicine medicine');
        $command->where('medicine.encounter_nr = :encounter_nr AND medicine.is_deleted != 1');
        $command->order('created_at DESC');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;
        $result = $command->queryAll();

        return $result;
    }


    /* Get Pertinent Signs */
    /*@param string $examination*/
    /*@return array $data*/
    public function getPertinentSigns()
    {
        $command = \Yii::app()->db->createCommand();

        $command->from('seg_cf4_pertinent_sign_symptoms t');
        $command->select('sign_symptoms , pains, others');
        $command->where('t.encounter_nr = :encounter_nr AND t.is_deleted != 1');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;

        return $command->queryAll();
    }

    public function getChiefComplaint()
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_cf4_chiefcomplaint_data t');
        $command->select('chief_complaint');
        $command->where('t.encounter_nr = :encounter_nr AND t.is_deleted != 1');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;
        return $command->queryRow();
    }

    public function getClinicalRecord()
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_cf4_clinical_record t');
        $command->where('t.encounter_nr = :encounter_nr AND t.is_deleted != 1');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;

        return $command->queryRow();
    }


}