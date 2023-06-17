<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibNeuro');
Yii::import('eclaims.models.cf4.Cf4Neuro');
Yii::import('eclaims.services.CF4HeaderService');


class peNeuroService
{
    public $encounter_nr;
    public $cf4Header;
    public $model;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->cf4Header = new Cf4;
        $this->model = new Cf4Neuro;
    }

    public function getNeuroLibrary()
    {
        $lib = new Cf4LibNeuro();

        $data = $lib->getDefaultData();

        return $data;
    }

    public function getSelectedNeuro()
    {
        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);

        if ($cf4Header) {
            $models = Cf4Neuro::model()->findAllByAttributes(
                array(
                    'entry_id' => $cf4Header->id,
                    'is_deleted' => '0'
                )
            );
        }

        foreach ($models as $key => $model) {
            
            $entries[$key] = $model->neuro_id;
            
            if($model->neuro_id == '99'){
                $remarks = $model;
            }else{
                $remarks = new Cf4Neuro();
            }
            
            
        }

        if($remarks == null) {
            $remarks = new Cf4Neuro;
        }

        return array(
            'entries' => $entries,
            'for_remarks' => $remarks
        );
    }

    public function save($data)
    {

        $status = false;
        $models = Cf4Neuro::model()->findAllByAttributes(
            array(
                'encounter_nr' => $this->encounter_nr
            )
        );

        $other_id = array('0' => '99');
        $remarks = $data['Cf4Neuro']['remarks'];
        $entry_id = $this->getEntryId($data);

        if(!empty($remarks)){
            $merge = CMap::mergeArray($data['Neuro']['neuro_data'], $other_id);
        }else{
            $merge = $data['Neuro']['neuro_data'];
        }
        // This is just a trick, $model is just a trap dont make it a mistake .. :)
        if (count($model) == 0) {
            foreach ($merge as $key => $neuro_id) {
                $model = new Cf4Neuro;
                $entry_id = $entry_id;
                $model->id = $this->model->getUuid();
                $model->entry_id = $entry_id;
                $model->encounter_nr = $this->encounter_nr;
                $model->neuro_id = $neuro_id;
                
                if($neuro_id == '99'){
                    $model->remarks = $remarks;
                }

                if ($model->save()) {
                    $status = true;
                }

            }
        }

        // Deleting old data that fetch on $models ..
        if (count($models) > 0){
            foreach ($models as $key => $neuro) {
                $neuros = Cf4Neuro::model()->findByPk($neuro->id);
                $neuros->is_deleted = '1';
                $ok = $neuros->save();
            }
        }
        
        if (!$status) {
            if($data['neuro_data'] == null){
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
