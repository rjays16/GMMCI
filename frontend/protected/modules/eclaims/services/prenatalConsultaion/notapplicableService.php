<?php

Yii::import('eclaims.models.cf4.MenstrualHistory');
Yii::import('eclaims.models.cf4.ObstetricHistory');
Yii::import('eclaims.services.CF4HeaderService');


class notapplicableService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }



    public function save($data)
    {

        $this->saveMenstrual($data);
        $this->saveObstetric($data);
    }

    public function saveMenstrual($data)
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
        $model->init_prenatal_cons = '0000-00-00';
        $model->date_of_lmp = '0000-00-00';
        $model->age_of_menarche = null;
        $model->period_duration = null;
        $model->is_applicable = $data['is_applicable'];

        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = $_SESSION['sess_login_userid'];

        $ok = $model->save();

        if (!$ok) {
            throw new Exception('Error in Saving Menstrual History!');
        }

        return true;
    }

    public function saveObstetric($data)
    {
        $model = ObstetricHistory::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new ObstetricHistory;
            $model->created_at = date("Y-m-d H:i:s");
            $model->created_by = $_SESSION['sess_login_userid'];
        }
        $entry_id = $this->getEntryId($data);
        $model->id = $model->getUuid();
        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->gravida = 0;
        $model->parity = 0;
        $model->term_births = 0;
        $model->preterm_births = 0;
        $model->abortion = 0;
        $model->living_children = 0;
        $model->number_stillbirth = 0;
        $model->updated_at = date("Y-m-d H:i:s");
        $model->updated_by = $_SESSION['sess_login_userid'];


        $ok = $model->save();

        if (!$ok) {
            throw new Exception('Error in Saving Obstetric History!');
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
