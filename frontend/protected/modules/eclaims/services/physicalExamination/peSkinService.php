<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4LibSkin');
Yii::import('eclaims.models.cf4.Cf4Skin');
Yii::import('eclaims.services.CF4HeaderService');


class peSkinService
{
    public $encounter_nr;
    public $cf4Header;
    public $model;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->cf4Header = new Cf4;
        $this->model = new Cf4Skin;
    }

    public function getSkinLibrary()
    {
        $lib = new Cf4LibSkin();

        $data = $lib->getDefaultData();

        return $data;
    }

    public function getSelectedSkin()
    {
        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);

        if ($cf4Header) {
            $models = Cf4Skin::model()->findAllByAttributes(
                array(
                    'entry_id' => $cf4Header->id,
                    'is_deleted' => '0'
                )
            );
        }

        foreach ($models as $key => $model) {
            
            $entries[$key] = $model->skin_id;
            
            if($model->skin_id == '99'){
                $remarks = $model;
            }else{
                $remarks = new Cf4Skin();
            }
            
            
        }

        if($remarks == null) {
            $remarks = new Cf4Skin;
        }

        return array(
            'entries' => $entries,
            'for_remarks' => $remarks
        );
    }

    public function save($data)
    {

        $status = false;
        $models = Cf4Skin::model()->findAllByAttributes(
            array(
                'encounter_nr' => $this->encounter_nr
            )
        );

        $other_id = array('0' => '99');
        $remarks = $data['Cf4Skin']['remarks'];
        $entry_id = $this->getEntryId($data);

        if(!empty($remarks)){
            $merge = CMap::mergeArray($data['Skin']['skin_data'], $other_id);
        }else{
            $merge = $data['Skin']['skin_data'];
        }
        // This is just a trick, $model is just a trap dont make it a mistake .. :)
        if (count($model) == 0) {
            foreach ($merge as $key => $skin_id) {

                $model = new Cf4Skin;
                $entry_id = $entry_id;
                $model->id = $this->model->getUuid();
                $model->entry_id = $entry_id;
                $model->encounter_nr = $this->encounter_nr;
                $model->skin_id = $skin_id;
                
                if($skin_id == '99'){
                    $model->remarks = $remarks;
                }

                if ($model->save()) {
                    $status = true;
                }

            }
        }

        // Deleting old data that fetch on $models ..
        if (count($models) > 0){
            foreach ($models as $key => $skin) {
                $skins = Cf4Skin::model()->findByPk($skin->id);
                $skins->is_deleted = '1';   
                $ok = $skins->save();
            }
        }
        
        if (!$status) {
            if($data['skin_data'] == null){
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
