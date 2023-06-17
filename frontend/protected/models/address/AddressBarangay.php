<?php
/**
 * AddressBarangay.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright Copyright &copy; 2012-2013 Segworks Technologies Corporation
 */


Yii::import('application.models.address.AddressNode');
Yii::import('application.models.address.AddressMunicipality');

/**
 * Brief description of the class
 *
 * @version 1.0
 * @package default
 */

class AddressBarangay extends AddressNode {

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     * @return FieldValueText the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * [tableName description]
     * @return [type] [description]
     */
    public function tableName() {
        return 'seg_barangays';
    }

    /**
     * @see AddressNode::relations
     * @return array
     */
    public function relations() {
        return array(
            'parent' => array(self::BELONGS_TO, 'AddressMunicipality', 'mun_nr')
        );
    }

    /**
     * @see AddressNode::findByLocation
     * @param  string $location
     * @return AddressNode
     */
    public static function findByLocation($location) {
        return self::model()->findByPk($location);
    }

    /**
     * @see AddressNode::getNameAttribute
     * @return string
     */
    public function getNameAttribute() {
        return 'brgy_name';
    }
}
