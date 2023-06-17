<?php

/**
 * OperationRvs.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */


/**
 * This is the model class for table "seg_ops_rvs".
 *
 * @property string $code
 * @property strong $description
 * @property int $rvu
 * @property int $is_active
 */

class OperationRvs extends CareActiveRecord {

    /**
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'seg_ops_rvs';
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
        return array(
            //'relation' => array(self::BELONGS_TO, 'RelatedModel', 'primary_key'),
        );
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
     * @param string  active record class name.
     * @return Claim the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

}

