<?php

namespace SegHis\modules\eclaims\services\cf4\nodes\services;


class MedicalHistoryService
{

    public $encounter;

    public function __construct(\EclaimsEncounter $encounter)
    {

        $this->encounter = $encounter;
    }


    /*
     * Returns Menstrual History CF4
     * @param void
     * @return $array
     * */
    public function getMenshistory()
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_cf4_menstrual_history t');
        $command->where('t.encounter_nr = :encounter_nr AND (t.is_deleted != 1 OR t.is_deleted is NULL)');
        $command->order('t.created_at DESC');

        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;
        return $command->queryRow();
    }

    /*
     * Returns Pregnant History CF4
     * @param void
     * @return $array
     *
     * */
    public function getPregHistory()
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_cf4_obstetric_history t');
        $command->where('t.encounter_nr = :encounter_nr');
        $command->order('t.created_at DESC');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;
        return $command->queryRow();
    }


    public function getPastMedicalHistory()
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_cf4_past_med_history t');
        $command->where('t.encounter_nr = :encounter_nr');
        $command->order('t.created_at DESC');
        $command->params[':encounter_nr'] = $this->encounter->encounter_nr;
        return $command->queryRow();
    }


}