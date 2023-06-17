<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibClinicalHistory');
Yii::import('eclaims.models.cf4.PregnancyUterine');
Yii::import('eclaims.services.CF4HeaderService');


class PregnancyUterineService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = PregnancyUterine::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
            $model = new PregnancyUterine;
        }

        return $model;
    }

    public function save($data)
    {

        $model = PregnancyUterine::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new PregnancyUterine;
        }

        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->maternal_outcome = $data['maternal_outcome'];
        $model->aog_by_lmp = $data['aog_by_lmp'];
        $model->manner_of_delivery = $data['manner_of_delivery'];
        $model->presentation = $data['presentation'];
        $ok = $model->save();

        if ($ok) {
            return true;
        }

        // return false;
        throw new \Exception("Error in saving Pregnancy Uterine!");
    }


    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }
}
