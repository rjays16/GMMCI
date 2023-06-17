<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibGenSurvey');
Yii::import('eclaims.models.cf4.Cf4GeneralSurvey');
Yii::import('eclaims.services.CF4HeaderService');


class peGenSurveyService
{
    public $encounter_nr;
    public $cf4Header;
    public $model;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->cf4Header = new Cf4;
        $this->model = new Cf4GeneralSurvey;
    }

    public function getGenSurveyLibrary()
    {
        $lib = new Cf4LibGenSurvey();

        $data = $lib->getDefaultData();

        return $data;
    }

    public function getSelectedGenSurvey()
    {
        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);
        if ($cf4Header) {
            $models = Cf4GeneralSurvey::model()->findAllByAttributes(
                array(
                    'entry_id' => $cf4Header->id,
                    'is_deleted' => '0'
                )
            );
        }

        foreach ($models as $key => $model) {
            
            $entries[$key] = $model->gen_survey_id;
            
            if($model->gen_survey_id == '2'){
                $remarks = $model;
            }else{
                $remarks = new Cf4GeneralSurvey();
            }
            
            
        }

        if($remarks == null) {
            $remarks = new Cf4GeneralSurvey;
        }

        return array(
            'entries' => $entries,
            'for_remarks' => $remarks
        );
    }

    public function save($data)
    {
        $status = false;
        $models = Cf4GeneralSurvey::model()->findAllByAttributes(
            array(
                'encounter_nr' => $this->encounter_nr
            )
        );
        $gen_data = $data['Gen'];
        $remarks = $data['Cf4GeneralSurvey']['remarks'];
        $entry_id = $this->getEntryId($data);
                // CVarDumper::dump($remarks, 10, true);die;
        // This is just a trick, $model is just a trap dont make it a mistake .. :)
        if (count($model) == 0) {
            $ctr = 0;
            foreach ($gen_data['gen_data'] as $gen_survey_id) {
                $model = new Cf4GeneralSurvey;
                $entry_id = $entry_id;
                $model->id = $this->model->getUuid();
                $model->entry_id = $entry_id;
                $model->encounter_nr = $this->encounter_nr;
                $model->gen_survey_id = $gen_survey_id;
                
                if($gen_survey_id == '2'){
                    $model->remarks = $remarks;
                }

                if ($model->save()) {
                    $status = true;
                }
             $ctr++;
            }
        }

        // Deleting old data that fetch on $models ..
        if (count($models) > 0){
            foreach ($models as $key => $gen) {
                $gens = Cf4GeneralSurvey::model()->findByPk($gen->id);
                $gens->is_deleted = '1';
                $ok = $gens->save();
            }
        }
        
        if (!$status) {
            if($data['gen_data'] == null){
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
