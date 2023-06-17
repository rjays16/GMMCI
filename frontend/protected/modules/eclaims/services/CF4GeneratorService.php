<?php

Yii::import('eclaims.models.EclaimsTransmittal');
Yii::import('eclaims.models.EclaimsPerson');
Yii::import('eclaims.models.EclaimsEncounter');
Yii::import('eclaims.models.cf4.Cf4PatientInfo');
Yii::import('eclaims.models.cf4.Cf4');

class CF4GeneratorService
{
    public function getDetails($encounter_nr)
    {

        $encounter = EclaimsEncounter::model()->findByPk($encounter_nr);
        $person = EclaimsPerson::model()->findByPk($encounter->pid);
        $religion = $person->getReligion($person->religion);
        $details = $person;
        $data = array(
            'person' => $details,
            'religion' => $religion
        );
        return $data;
    }


    public function getEncounterDetails($encounter_nr)
    {
        $encounter = EclaimsEncounter::model()->findByPk($encounter_nr);
        $details = $encounter;
        return $details;
    }

}
