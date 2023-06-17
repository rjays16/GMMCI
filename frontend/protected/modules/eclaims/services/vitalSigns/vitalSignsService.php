<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4VitalSigns');
Yii::import('eclaims.services.CF4HeaderService');


class vitalSignsService
{
    public $encounter_nr;
    public $cf4Header;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->cf4Header = new Cf4;
    }
    
    public function save($data)
    {
        // \CVarDumper::dump($data, 10, true);die;
        $status = false;
 
        $model = new Cf4VitalSigns;
        $entry_id = $this->getEntryId($data);
        $model->id = $model->getUuid();
        $model->encounter_nr = $data['encounter_nr'];
        $model->systolic = $data['systolic'];
        $model->diastolic = $data['diastolic'];
        $model->cr = $data['heart_rate'];
        $model->rr = $data['resp_rate'];
        $model->temperature = $data['temp'];
        $model->weight = $data['weight'];
        $model->height = $data['height'];

        if ($model->save()) {
            $status = true;
        }
 
        if (!$status) {
            throw new Exception("Error Processing Request"); 
        } else {
            return true;
        }
        
    }

    public function delete($data)
    {
        $date = date('Y-m-d H:i:s');
        $model = Cf4VitalSigns::model()->findByAttributes(array(
            'entry_id' => $this->cf4Header->id,
            'encounter_nr' => $this->encounter_nr,
            'id' => $data['id']
        ));

        $model->modify = $date;
        $model->modified_by = $_SESSION['sess_login_username'];
        $model->deleted_by = $_SESSION['sess_login_username'];
        $model->deleted_at = $date;
        $model->is_deleted = 1;

        if (!$model->save())
            throw new Exception("Error Processing Request", 1);

        return true;
    }

    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }

}
