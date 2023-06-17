<?php

Yii::import('eclaims.models.cf4.Cf4ChiefcomplaintData');
Yii::import('eclaims.models.EclaimsEncounter');
Yii::import('eclaims.services.CF4HeaderService');


class chiefComplaintService
{
    public $encounter_nr;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {

        $model = Cf4ChiefcomplaintData::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        $encounter = EclaimsEncounter::model()->findByPk($this->encounter_nr);
        $read_only = false;

        if($encounter->encounter_type == '1') {
            $data = $encounter;
            $read_only = false;
        }elseif ($encounter->encounter_type == '2') {
            $data = $encounter;
            $read_only = true;
        }else{
            $data = $model;
            $read_only = false;
        }

        if (count($data) == 0) {
            $data = new Cf4ChiefcomplaintData;
        }

        return array( 'data' => $data, 'read_only' => $read_only );
    }

    public function saveChiefComplaint($data)
    {
        $model = Cf4ChiefcomplaintData::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        $encounter = \Encounter::model()->findByPk($this->encounter_nr);
        
        $encounter->chief_complaint = $data['chief_complaint'];
        $enc_ok = $encounter->save();

        if (count($model) == 0) {
            $model = new Cf4ChiefcomplaintData;
            $model->id = $model->getUuid();
        }
        

        $entry_id = $this->getEntryId($data);
        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->chief_complaint = $data['chief_complaint'];


        $ok = $model->save();

        if (!$ok) {

            throw new Exception('Error in Saving Chief Complaint!');
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
