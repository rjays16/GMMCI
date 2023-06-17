<?php

Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.models.cf4.Cf4ClinicalRecord');
Yii::import('eclaims.models.cf4.Cf4PastMedHistory');
Yii::import('eclaims.models.cf4.Cf4VitalSigns');
Yii::import('eclaims.services.CF4HeaderService');

class clinicalRecordService
{
    public $encounter_nr;
    public $cf4Header;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->cf4Header = new Cf4;
    }

    public function getData()
    {
        $clinical_records = $this->getClinicalRecord();
        $past_med = $this->getPastMedHis();
        $vital_signs = $this->getVitalSigns();

        return $data = array(
            'clinical_records' => $clinical_records,
            'past_med' => $past_med,
            'vital_signs' => $vital_signs,
        );
    }

    public function getClinicalRecord()
    {
        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);

        if ($cf4Header) {
            $model = Cf4ClinicalRecord::model()->find(
                array(
                    'condition' => "t.entry_id = :entry_id",
                    'params' => array(':entry_id' => $cf4Header->id),
                )
            );
        }


        if (count($model) == 0) {
            $model = new Cf4ClinicalRecord;
        }

        return $model;
    }

    public function getPastMedHis()
    {
        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);

        if ($cf4Header) {
            $model = Cf4PastMedHistory::model()->find(
                    array(
                        'condition' => "t.entry_id = :entry_id",
                        'params' => array(':entry_id' => $cf4Header->id),
                    )
            );
        }

        if (count($model) == 0) {
            $model = new Cf4PastMedHistory;
        }

        return $model;
    }

    public function getVitalSigns()
    {
        $cf4Header = $this->cf4Header->checkCf4HeaderDetails($this->encounter_nr);

        if ($cf4Header) {
            $model = Cf4VitalSigns::model()->find(
                    array(
                        'condition' => "t.entry_id = :entry_id",
                        'params' => array(':entry_id' => $cf4Header->id),
                    )
            );
        }

        if (count($model) == 0) {
            $model = new Cf4VitalSigns;
        }

        return $model;
    }


    public function saveClinicalRecord($data)
    {
        $model = Cf4ClinicalRecord::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new Cf4ClinicalRecord;
            $model->id = $model->getUuid();
        }

        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->present_illness = $data['present_illness'] ? $data['present_illness'] : '' ;

        $ok = $model->save();

        if (!$ok) {
            throw new Exception("Error Processing Request", 1);
        }

    }

    public function savePastMedHis($data)
    {
        $model = Cf4PastMedHistory::model()->find(
            array(
                'condition' => "t.encounter_nr = :encounter_nr",
                'params' => array(':encounter_nr' => $this->encounter_nr),
            )
        );

        if (count($model) == 0) {
            $model = new Cf4PastMedHistory;
            $model->id = $model->getUuid();
        }
        
        $entry_id = $this->getEntryId($data);

        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->pertinent = $data['pertinent'] ? $data['pertinent'] : '' ;
        $model->disease_code = $data['disease_code'] ? $data['disease_code'] : '' ;

        $ok = $model->save();

        if (!$ok) {
            throw new Exception("Error Processing Request", 1);
        }

    }

    // public function saveVitalSigns($data)
    // {
    //     $model = Cf4VitalSigns::model()->find(
    //         array(
    //             'condition' => "t.encounter_nr = :encounter_nr",
    //             'params' => array(':encounter_nr' => $this->encounter_nr),
    //         )
    //     );

    //     if (count($model) == 0) {
    //         $model = new Cf4VitalSigns;
    //         $model->id = $model->getUuid();
    //     }

    //     $entry_id = $this->getEntryId($data);

    //     $model->entry_id = $entry_id;
    //     $model->encounter_nr = $this->encounter_nr;
    //     $model->bp = $data['blood_pressure'] ? $data['blood_pressure'] : '' ;
    //     $model->cr = $data['heart_rate'] ? $data['heart_rate'] : '';
    //     $model->rr = $data['resp_rate'] ? $data['resp_rate'] : '' ;
    //     $model->temperature = $data['temperature'] ? $data['temperature'] : '' ;

    //     $ok = $model->save();

    //     if (!$ok) {
    //         throw new Exception("Error Processing Request", 1);
    //     }
        
    // }

    public function checkCf4HeaderDetails()
    {
        $cf4Header = Cf4::model()->findByAttributes(
            array(
                'encounter_nr' => $this->encounter_nr
            )
        );

        return $cf4Header;
    }

    public function getEntryId($data)
    {

        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();


        return $service->getId();
    }

}
