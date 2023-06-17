<?php

Yii::import('eclaims.models.cf4.MenstrualHistory');
Yii::import('eclaims.services.CF4HeaderService');


class menstrualService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
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

        return $model;
    }

    public function save($data)
    {

        $model = MenstrualHistory::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new MenstrualHistory;
            $model->created_at = date("Y-m-d H:i:s");
            $model->created_by = $_SESSION['sess_login_userid'];
        }

        $entry_id = $this->getEntryId($data);

        $model->id = $model->getUuid();
        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->init_prenatal_cons = $data['init_prenatal_cons'];
        $model->date_of_lmp = $data['date_of_lmp'];
        $model->age_of_menarche = $data['age_of_menarche'];
        $model->period_duration = $data['period_duration'];
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = $_SESSION['sess_login_userid'];
        $model->is_applicable = $data['is_applicable'];

        $ok = $model->save();

        if (!$ok) {
            throw new Exception('Error in Saving Menstrual History!');
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
