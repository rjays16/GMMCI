<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibClinicalHistory');
Yii::import('eclaims.models.cf4.DeliveryPlan');
Yii::import('eclaims.services.CF4HeaderService');


class deliveryPlanService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = DeliveryPlan::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
            $model = new DeliveryPlan;
        }

        return $model;
    }

    public function save($data)
    {

        $model = DeliveryPlan::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new DeliveryPlan;
        }

        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->is_benefit = $data['is_benefit'];
        $model->edc = $data['edc'];
        $ok = $model->save();

        if ($ok) {
            return true;
        }
        throw new Exception('Error in Saving Delivery Plan!');
    }


    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }
}
