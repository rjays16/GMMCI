<?php

Yii::import('eclaims.models.cf4.PhysicalExamination');
Yii::import('eclaims.services.CF4HeaderService');


class physicalExaminationService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = PhysicalExamination::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
            $model = new PhysicalExamination;
        }

        return $model;
    }

    public function save($data)
    {

        $model = PhysicalExamination::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new PhysicalExamination;
        }
        $entry_id = $this->getEntryId($data);
        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->is_normal = $data['is_normal'];
        $model->is_low_risk = $data['is_low_risk'];

        $ok = $model->save();

        if (!$ok) {
            throw new Exception('Error in Saving Physical Examination!');
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
