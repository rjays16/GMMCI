<?php
/**
 * AddressRegion.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright Copyright &copy; 2012-2013 Segworks Technologies Corporation
 */

Yii::import('application.models.address.AddressNode');
Yii::import('application.models.address.AddressProvince');

/**
 * Brief description of the class
 *
 * @version 1.0
 * @package default
 */

class AddressRegion extends AddressNode {

    /**
     * @see AddressNode::getNameAttribute
     * @return string
     */
    public function getNameAttribute() {
        return 'region_name';
    }

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
        return 'seg_regions';
    }

    /**
     * @see AddressNode::relations
     * @return array
     */
    public function relations() {
        return array(
            'children' => array(self::HAS_MANY, 'AddressProvince', 'region_nr')
        );
    }

    /**
     * @see AddressNode::findByLocation
     * @param  string $location
     * @return AddressNode
     */
    public static function findByLocation($location) {
        return self::model()->findByPk(substr($location, 0, 2) . '0000000');
    }
}
