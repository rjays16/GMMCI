<?php

/**
 * User.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

/**
 * Description of User
 *
 * @package
 */
class User extends ActiveRecord {


/**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'care_users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array();
    }

    /**
     * @todo Compose PHP doc
     */
    public function validatePassword($password){
        return $this->hashPassword($password)===$this->password;
    }

    /**
     * @todo Compose PHP doc
     */
    public function hashPassword($password){
        return md5($password);
    }

    /**
     *
     * @return type
     */
    public function getFullName() {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function getId() {
        return $this->login_id;
    }

    /**
     *
     * @return string
     */
    public function getUserName() {
        return $this->login_id;
    }
}
