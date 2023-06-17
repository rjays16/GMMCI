<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibClinicalHistory');
Yii::import('eclaims.models.cf4.DTDischargeOutcome');
Yii::import('eclaims.services.CF4HeaderService');


class DTDischargeOutcomeService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = DTDischargeOutcome::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
            $model = new DTDischargeOutcome;
        }

        return $model;
    }

    public function save($data)
    {
        // \CVarDumper::dump($data);die;
        $model = DTDischargeOutcome::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new DTDischargeOutcome;
        }

        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->date = $data['date'];
        $model->time = $data['time'];
        $ok = $model->save();

        if ($ok) {
            return true;
        }

        throw new \Exception("Error in saving Date and Time Discharge Outcome");
    }


    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }
}
