<?php

Yii::import('eclaims.models.cf4.postpartumCare.ReferredPartnerPhysician');
Yii::import('eclaims.services.CF4HeaderService');


class ReferredPartnerPhysicianService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = ReferredPartnerPhysician::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
            $model = new ReferredPartnerPhysician;
        }

        return $model;
    }

    public function save($data)
    {

        $model = ReferredPartnerPhysician::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new ReferredPartnerPhysician;
        }

        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->is_done = $data['is_done'];
        $model->remarks = $data['remarks'];

        $ok = $model->save();

        if (!$ok) {
            throw new Exception('Error in Saving Referred Partner Physician!');
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
