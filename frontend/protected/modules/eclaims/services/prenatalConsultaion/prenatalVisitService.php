<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibClinicalHistory');
Yii::import('eclaims.models.cf4.PrenatalVisits');
Yii::import('eclaims.services.CF4HeaderService');


class prenatalVisitService
{
    public $encounter_nr;
    // public $menstrual;
    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    public function getData()
    {
        $model = PrenatalVisits::model()->findAll(
            array(
                'condition' => "t.encounter_nr = :encounter_nr AND t.is_deleted = 0",
                'params' => array(':encounter_nr' => $this->encounter_nr),
                'order' => 'prenatal_consultation_no asc'
            )
        );
        if (count($model) == 0) {
            $model = new PrenatalVisits;
        }

        return $model;
    }
    public function initData()
    {

        $model = new PrenatalVisits;


        return $model;
    }

    public function save($data)
    {

        $check = PrenatalVisits::model()->findAll(
            array(
                'condition' => 't.encounter_nr = :encounter_nr AND t.prenatal_consultation_no = :prenatal_consultation_no AND t.is_deleted = :is_deleted',
                'params' => array(
                    ':encounter_nr' => $this->encounter_nr,
                    ':prenatal_consultation_no' => $data['prenatal_consultation_no'],
                    ':is_deleted' => '0',
                )
            )
        );
        if(count($check) > 0){
            throw new Exception('Prenatal Consultaion No: '.$data['prenatal_consultation_no']." is already exist, Please check it below.");
        }
        

        $model = new PrenatalVisits;
        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->date_visit = $data['date_visit'];
        $model->aog = $data['aog'];
        $model->weight = $data['weight'];
        $model->cardiac_rate = $data['cardiac_rate'];
        $model->respiratory_rate = $data['respiratory_rate'];
        $model->bp = $data['bp'];
        $model->temperature = $data['temperature'];
        $model->prenatal_consultation_no = $data['prenatal_consultation_no'];
        $ok = $model->save();

        if ($ok) {
            return true;
        }

        throw new Exception('Error in saving Prenatal Visit!');
    }

    public function update($data)
    {

        $model = PrenatalVisits::model()->findByPk($data['prenatal_visit_id']);
        // $entry_id = $this->getEntryId($data);

        // $model->entry_id = $entry_id;
        // $model->encounter_nr = $this->encounter_nr;
        $model->date_visit = $data['date_visit'];
        $model->aog = $data['aog'];
        $model->weight = $data['weight'];
        $model->cardiac_rate = $data['cardiac_rate'];
        $model->respiratory_rate = $data['respiratory_rate'];
        $model->bp = $data['bp'];
        $model->temperature = $data['temperature'];
        // $model->prenatal_consultation_no = $data['prenatal_consultation_no'];
        $ok = $model->save();

        if ($ok) {
            return true;
        }

        throw new Exception('Error in updating Prenatal Visit!');
    }

    public function delete($data)
    {
        $model = PrenatalVisits::model()->findByPk($data['prenatal_visit_id']);
        if (count($model) > 0) {
            $model->is_deleted = 1;
            $ok = $model->save();
        }
        if ($ok) {
            return true;
        }
        throw new Exception('Error in deleting Prenatal Visits!');
    }


    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }
}
