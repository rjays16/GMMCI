<?php

/**
 *
 * EclaimsPhicMember.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('phic.models.PhicMember');

/**
 * Description of EclaimsPhicMember
 *
 * @package eclaims.model
 */
class EclaimsPhicMember extends PhicMember {

    /**
     * @author Jolly Caralos
     */
    public function rules()
    {
        $_rules = array(
            array(
                'employer_no, employer_name', 'requiredByFieldValue',
                'dependOn' => 'member_type',
                'dependValues' => array(
                    's', 'g'
                ),
                'message' => 'cannot be blank'
            ),
            array(
                'employer_no, employer_name', 'defaultByFieldValue',
                'dependOn' => 'member_type',
                'dependValues' => array(
                    'i', 'ns', 'no', 'ps', 'pg', 'ns'
                ),
                'value' => null
            )
        );

        return CMap::mergeArray(parent::rules(), $_rules);
    }

    public function relations() 
    {
        $_relations = array(
            'eclaimPerson' => array(self::BELONGS_TO, 'EclaimsPerson', 'pid'),
        );
        return CMap::mergeArray(parent::relations(), $_relations);
    }

    public function beforeSave() {
        if(parent::beforeSave()) {

            /* Mirror saving with PhicMember2 */
            return $this->saveEclaimsPhicMember2();

        }
        return false;
    }

    public function saveEclaimsPhicMember2() 
    {
        $phicMember = $this->eclaimPerson->phicMember2;
        if(empty($this->eclaimPerson->phicMember2)) {
            $phicMember = new EclaimsPhicMember2;
        } else {
            $phicMember = $this->eclaimPerson->phicMember2;
        }
        $phicMember->attributes = $this->attributes;

        return $phicMember->save();
    }

    /**
     * Validator method
     * Required fields if "dependOn" value 
     * is in the "dependValues".
     * 
     * @author Jolly Caralos
     */
    public function requiredByFieldValue($attribute, $params)
    {
        $_requiredIfType = $params['dependValues'];
        if(empty($this->{$params['dependOn']})) {
            return false;
        }
        if(in_array(strtolower($this->{$params['dependOn']}), $_requiredIfType) && 
            empty($this->{$attribute})) {
            $_message = $this->getAttributeLabel($attribute) . ' ' . $params['message'];
            $this->addError($attribute, $_message);
        }
    }

    /**
     * Validator method
     * Default values if "dependOn" value 
     * is in the "dependValues".
     * 
     * @author Jolly Caralos
     */
    public function defaultByFieldValue($attribute, $params)
    {
        $_defaultIfType = $params['dependValues'];
        if(empty($this->{$params['dependOn']})) {
            return false;
        }
        
        if(in_array(strtolower($this->{$params['dependOn']}), $_defaultIfType) && 
            !empty($this->{$attribute})) {
            $this->{$attribute} = $params['value'];
        }
    }

    /**
     *
     * @return array
     */
    public function getPinParams() {
        $date = strtotime($this->birth_date);

        $result = array(
            'pMemberFirstName' => $this->member_fname,
            'pMemberMiddleName' => $this->member_mname,
            'pMemberLastName' => $this->member_lname,
            'pMemberSuffix' => $this->suffix,
            'pMemberBirthDate' => ($date!==false) ? date('m-d-Y', $date) : '',
        );

        array_walk($result, function(&$value, $key) {
            if (empty($value)) {
                $value = '';
            }

            if (mb_detect_encoding($value, 'UTF-8', true) === false) {
                $value = mb_convert_encoding($value, 'UTF-8');
            }

            $value = mb_strtoupper($value);
        });

        return $result;
    }
}
