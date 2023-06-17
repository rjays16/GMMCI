<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibAbdomen');
Yii::import('eclaims.models.cf4.Cf4Abdomen');
Yii::import('eclaims.services.CF4HeaderService');


class peAbdomenService
{
    public $encounter_nr;
    public $cf4Header;
    public $model;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->cf4Header = new Cf4;
        $this->model = new Cf4Abdomen;
    }

    public function getAbdomenLibrary()
    {
        $lib = new Cf4LibAbdomen();

        $data = $lib->getDefaultData();

        return $data;
    }

    public function getSelectedAbdomen()
    {
        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);

        if ($cf4Header) {
            $models = Cf4Abdomen::model()->findAllByAttributes(
                array(
                    'entry_id' => $cf4Header->id,
                    'is_deleted' => '0'
                )
            );
        }

        foreach ($models as $key => $model) {
            
            $entries[$key] = $model->abdomen_id;
            
            if($model->abdomen_id == '99'){
                $remarks = $model;
            }else{
                $remarks = new Cf4Abdomen();
            }
            
            
        }

        if($remarks == null) {
            $remarks = new Cf4Abdomen;
        }

        return array(
            'entries' => $entries,
            'for_remarks' => $remarks
        );
    }

    public function save($data)
    {

        $status = false;
        $models = Cf4Abdomen::model()->findAllByAttributes(
            array(
                'encounter_nr' => $this->encounter_nr
            )
        );

        $other_id = array('0' => '99');
        $remarks = $data['Cf4Abdomen']['remarks'];
        $entry_id = $this->getEntryId($data);

        if(!empty($remarks)){
            $merge = CMap::mergeArray($data['Abdomen']['abdomen_data'], $other_id);
        }else{
            $merge = $data['Abdomen']['abdomen_data'];
        }

        // This is just a trick, $model is just a trap dont make it a mistake .. :)
        if (count($model) == 0) {
            foreach ($merge as $key => $abdomen_id) {
                $model = new Cf4Abdomen;
                $entry_id = $entry_id;
                $model->id = $this->model->getUuid();
                $model->entry_id = $entry_id;
                $model->encounter_nr = $this->encounter_nr;
                $model->abdomen_id = $abdomen_id;
                
                if($abdomen_id == '99'){
                    $model->remarks = $remarks;
                }

                if ($model->save()) {
                    $status = true;
                }

            }
        }

        // Deleting old data that fetch on $models ..
        if (count($models) > 0){
            foreach ($models as $key => $abdomen) {
                $abdomens = Cf4Abdomen::model()->findByPk($abdomen->id);
                $abdomens->is_deleted = '1';
                $ok = $abdomens->save();
            }
        }
        
        if (!$status) {
            if($data['abdomen_data'] == null){
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
