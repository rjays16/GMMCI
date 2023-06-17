<?php

/**
 *
 * EclaimsPerson.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.EclaimsPhicMember');
Yii::import('eclaims.models.EclaimsPhicMember2');
Yii::import('eclaims.models.EclaimsEncounter');
Yii::import('eclaims.models.ReligionCatalog');

/**
 * Description of EclaimsPerson
 *
 * @package
 */
class EclaimsPerson extends Person
{

    /**
     *
     */
    public function relations()
    {
        $phic = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);
        return array_merge(parent::relations(), array(
            /**
             * Returns latest encounter that is not yet dischared and bill.
             * Used in Eclaims module: Add Insurance
             */
            'activeInsuranceEncounter' => array(
                self::HAS_ONE, 'EclaimsEncounter', 'pid',
                'scopes' => array('active', 'notBilled'),
                'order' => 'activeInsuranceEncounter.create_time DESC',
            ),
            'currentEncounter' => array(
                self::HAS_ONE, 'EclaimsEncounter', 'pid',
                'order' => 'currentEncounter.create_time DESC',
                'condition' => 'currentEncounter.is_discharged IS NULL OR currentEncounter.is_discharged = 0',
            ),
            'latestEncounter' => array(
                self::HAS_ONE, 'EclaimsEncounter', 'pid',
                'order' => 'latestEncounter.create_time DESC'
            ),
            'phicMember' => array(
                self::HAS_ONE, 'EclaimsPhicMember', 'pid',
                'condition' => 'hcare_id = :hcare_id', 'params' => array(':hcare_id' => $phic->hcare_id),
                'order' => 'phicMember.create_dt desc'
            ),
            'phicMember2' => array(
                self::HAS_ONE,
                'EclaimsPhicMember2',
                'pid', 'condition' => 'hcare_id = :hcare_id',
                'params' => array(':hcare_id' => $phic->hcare_id)
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Person the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * Returns an array of this person's related patient information
     * @return array
     */
    public function getPatientInfo($options = array())
    {
        $options = CMap::mergeArray(array(
            'dateFormat' => 'Y-m-d'
        ), $options);
        $encounter = $this->currentEncounter ? $this->currentEncounter : new Encounter;

        return array(
            'id' => $this->pid,
            'lastName' => strtoupper($this['name_last']),
            'firstName' => strtoupper($this['name_first']),
            'middleName' => strtoupper($this['name_middle']),
            'fullName' => $this->getFullName(),
            'sex' => strtoupper($this['sex']),
            'age' => $this->getAge(),
            'birthDate' => $this->formatDateValue($this['date_birth'], $options['dateFormat']),
            'encounterNr' => $encounter->encounter_nr,
            'patientType' => $encounter->getEncounterType(),
            'department' => $encounter->getDepartmentName(),
        );
    }

    /**
     * Returns a list of
     * @param type $term
     */
    public static function searchByName($term, $limit = 10)
    {
        $criteria = new CDbCriteria();
        $criteria->limit = $limit;
        $criteria->order = 'name_last, name_first, name_middle';
        $terms = explode(',', $term, 2);

        // No lastname and firstname in the search query
        if (sizeof($terms) == 0) {
            return array();
        }

        $params = array();
        if (trim($terms[0]) !== '') {
            $criteria->addCondition('name_last LIKE :lastName');
            $params['lastName'] = trim($terms[0]) . '%';
        }

        if (isset($terms[1]) && trim($terms[1]) !== '') {
            $criteria->addCondition('name_first LIKE :firstName');
            $params['firstName'] = trim($terms[1]) . '%';
        }
        $criteria->params = $params;

        return static::model()->findAll($criteria);
    }

    public function getDateBirth()
    {
        if (!((int)$this->date_birth))
            return null;
        return $this->date_birth;
    }


    /**
     * @return EclaimsEncounter
     */
    public function getRecentEncounterInsurance()
    {
        $criteria = new CDbCriteria();

        $criteria->with = array(
            'encounterInsurance' => array(
                'scopes' => 'recently',
                'joinType' => 'INNER JOIN'
            )
        );

        $criteria->addCondition('t.pid=:pid');


        $criteria->params = array(
            ':pid' => $this->pid
        );

        $criteria->scopes = array('active');


        $recentEncounterInsurance = EclaimsEncounter::model()->find($criteria);

        return $recentEncounterInsurance ? $recentEncounterInsurance : $this->latestEncounter;
    }


    /**
     * Returns 'T' if patient is dead else 'F'
     * @return string
     */
    public function getDeathStatus()
    {
        if ($this->death_encounter_nr != '0') {
            return 'T';
        } else {
            return 'F';
        }
    }

    /**
     * Determine if the patient is dead.
     *
     * @return bool True if dead, otherwise false.
     */
    public function isDead()
    {
        return $this->getDeathStatus() == 'T';
    }

    function getReligion($religion_nr)
    {

        $religion = ReligionCatalog::model()->findByPk($religion_nr);
        return $religion->religion_name;
    }
}
