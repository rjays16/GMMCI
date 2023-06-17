<?php

/**
 * EclaimsTransmittalExt.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */


/**
 * This is the model class for table "seg_eclaims_transmittal_ext".
 *
 * The followings are the available columns in table 'seg_eclaims_transmittal_ext':
 * @property type $value
 */

class EclaimsTransmittalExt extends CareActiveRecord {

    /**
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'seg_eclaims_transmittal_ext';
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array();
    }

    /**
     *
     * @return array relational rules.
     */
    public function relations() {
        return array();
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array();
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     * @return Claim the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

}
