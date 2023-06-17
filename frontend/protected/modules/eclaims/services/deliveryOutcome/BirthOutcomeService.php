<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibClinicalHistory');
Yii::import('eclaims.models.cf4.BirthOutcome');
Yii::import('eclaims.services.CF4HeaderService');


class BirthOutcomeService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = BirthOutcome::model()->findAll(
            array(
                'condition' => "t.encounter_nr = :encounter_nr AND t.is_deleted = 0",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
            $model = new BirthOutcome;
        }

        return $model;
    }
    public function initData()
    {

        $model = BirthOutcome::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr AND t.is_deleted = 0",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if(count($model) < 0 OR !$model){
            $model = new BirthOutcome;
        }


        return $model;
    }

    public function save($data)
    {

        // $model = new BirthOutcome;
        $model = BirthOutcome::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr AND t.is_deleted = 0",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        
        if(!$model){
            $model = new BirthOutcome;
        }
        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->fetal_outcome = $data['fetal_outcome'];
        $model->sex = $data['sex'];
        $model->birth_weight = $data['birth_weight'];
        $model->apgar_score = $data['apgar_score'];
        $ok = $model->save();

        if ($ok) {
            return true;
        }

        throw new \Exception("Error in saving Birth Outcome");
    }

    public function delete($data)
    {
        $model = BirthOutcome::model()->findByPk($data['birth_outcome_id']);
        if (count($model) > 0) {
            $model->is_deleted = 1;
            $ok = $model->save();

            if (!$ok) {
                throw new \Exception("Error in deleting Birth Outcome!");
            }
        }


        return true;
    }


    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }
}
