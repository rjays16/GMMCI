<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibChiefComplaint');
Yii::import('eclaims.models.cf4.Cf4PertinentSignSymptoms');
Yii::import('eclaims.services.CF4HeaderService');


class signsAndSymptomsService
{
    public $encounter_nr;
    public $cf4Header;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->cf4Header = new Cf4;
        $this->model = new Cf4PertinentSignSymptoms;
    }
    
    public function getSignsAndSymptomsLibraryOne()
    {
        $lib = new Cf4LibChiefComplaint();

        $data = $lib->getDefaultDataRowOne();

        return $data;
    }

    public function getSignsAndSymptomsLibraryTwo()
    {
        $lib = new Cf4LibChiefComplaint();

        $data = $lib->getDefaultDataRowTwo();

        return $data;
    }

    public function getSignsAndSymptomsLibraryThree()
    {
        $lib = new Cf4LibChiefComplaint();

        $data = $lib->getDefaultDataRowThree();

        return $data;
    }

    public function getSignsAndSymptomsLibraryFour()
    {
        $lib = new Cf4LibChiefComplaint();

        $data = $lib->getDefaultDataRowFour();

        return $data;
    }


    public function getSelectedSignsAndSymptoms()
    {

        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);

        if ($cf4Header) {
            $models = Cf4PertinentSignSymptoms::model()->findAllByAttributes(
                array(
                    'entry_id' => $cf4Header->id,
                    'is_deleted' => '0'
                )
            );
        }

        foreach ($models as $key => $model) {
            
            $entries[$key] = $model->sign_symptoms;
            
            if($model->sign_symptoms == 'X'){
                $other_remarks = $model;
            }

            if ($model->sign_symptoms == '38') {
                $pain_remarks = $model;
            }
            
    
        }

        if($other_remarks == null) {
            $other_remarks = new Cf4PertinentSignSymptoms;
        }
        if($pain_remarks == null) {
            $pain_remarks = new Cf4PertinentSignSymptoms;
        }

        return array(
            'entries' => $entries,
            'for_pains' => $pain_remarks,
            'for_others' => $other_remarks,
        );
    }

    public function save($data)
    {

        $status = false;
        $models = Cf4PertinentSignSymptoms::model()->findAllByAttributes(
            array(
                'encounter_nr' => $this->encounter_nr
            )
        );

        if (count($model) == 0) {
            foreach ($data['signs_data'] as $key => $signs_id) {
                $model = new Cf4PertinentSignSymptoms;
                $entry_id = $this->getEntryId($data);
                $model->id = $this->model->getUuid();
                $model->entry_id = $entry_id;
                $model->encounter_nr = $this->encounter_nr;
                $model->sign_symptoms = $signs_id;

                if($signs_id == 'X'){
                    $model->others = $data['others'];
                }
                if($signs_id == '38'){
                    $model->pains = $data['pains'];
                }

                if ($model->save()) {
                    $status = true;
                }

            }
        }

        if (count($models) > 0){
            foreach ($models as $key => $sign) {
                $signs = Cf4PertinentSignSymptoms::model()->findByPk($sign->id);
                $signs->is_deleted = '1';
                $ok = $signs->save();
            }
        }
        
        if (!$status) {
            if($data['signs_data'] == null){
                return 'no data';
            }else{
                throw new Exception("Error Processing Request");
            }
            
        } else {
            return true;
        }
        
    }

    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }
}
