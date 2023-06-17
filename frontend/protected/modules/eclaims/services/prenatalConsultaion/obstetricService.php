<?php

Yii::import('eclaims.models.cf4.ObstetricHistory');
Yii::import('eclaims.models.cf4.MenstrualHistory');
Yii::import('eclaims.services.CF4HeaderService');


class obstetricService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = ObstetricHistory::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
            $model = new ObstetricHistory;
        }

        return $model;
    }

    public function save($data)
    {
        $model = ObstetricHistory::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        $this->updateIsApplicable();

        if (count($model) == 0) {
            $model = new ObstetricHistory;
            $model->created_at = date("Y-m-d H:i:s");
            $model->created_by = $_SESSION['sess_login_userid'];
        }
        $entry_id = $this->getEntryId($data);

        $model->id = $model->getUuid();
        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->gravida = $data['gravida'];
        $model->parity = $data['parity'];
        $model->term_births = $data['term_births'];
        $model->preterm_births = $data['preterm_births'];
        $model->abortion = $data['abortion'];
        $model->living_children = $data['living_children'];
        // $model->number_stillbirth = $data['number_stillbirth'];
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = $_SESSION['sess_login_userid'];

        $ok = $model->save();

        if (!$ok) {
            throw new Exception('Error in Saving Obstetric History!');
        }

        return true;
    }

    public function updateIsApplicable()
    {
        $model = MenstrualHistory::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );
        if (count($model) == 0) {
           $model = new MenstrualHistory;
        }

        $model->is_applicable = 'Y';
        $model->save();
    }
    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }
}
