<?php
/**
 * AddressNode.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright Copyright &copy; 2012-2013 Segworks Technologies Corporation
 */

/**
 * Brief description of the class
 *
 * @version 1.0
 * @package models
 */

abstract class AddressNode extends CActiveRecord {

    protected $fullName;

    /**
     * Returns the name of the attribute used by the model to indicate
     * the name of the particular address node
     * 
     * @return string
     */
    abstract public function getNameAttribute();

    /**
     * [findByLocation description]
     * @param  string $locationId
     * @return AddressNode
     */
    public static function findByLocation($locationId) {
        // Must be implemented by implementing classes
        // PHP disallows abstract static functions
        return null;
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
     * @return string the associated database table name
     */
    public function tableName() {
        return null;
    }

    /**
     * [relations description]
     * @return array relational rules
     */
    public function relations() {
        return array();
    }

    /**
     * [getModel description]
     * @param  [type] $node [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public static function getNode($node, $id) {
        $node = ucfirst(strtolower($node));
        $className = 'Address' .$node;
        Yii::import('application.models.demographics.' . $className);
        return $className::findByLocation($id);
    }

    /**
     * [getSiblings description]
     * @param  [type] $searchKey [description]
     * @return [type]            [description]
     */
    public function getSiblings($searchKey = '') {
        if (isset($this->parent)) {
            if (isset($this->parent->children)) {
                $result = $this->parent->children;
            } else {
                $result = array();
            }
        } else {
            $result = $this->findAll(array(
                'condition' => $this->getPrimaryKey().' <> 0',
                'order' => $this->getNameAttribute() . ' ASC'
            ));
        }
        return $result;
    }

    /**
     * Magic method
     * @return string The equivalent string of the object
     */
    public function __toString() {
        $name = $this->getNameAttribute();
        return $this->$name;
    }

    /**
     * [getFullname description]
     * @return [type] [description]
     */
    public function getFullName($separator = ', ') {
        if (!empty($this->fullName)) {
            return $this->fullName;
        }
        $thisNode = $this;
        $fullName = array(trim($this));

        while(isset($thisNode->parent)) {
            $thisNode = $thisNode->parent;
            $fullName[] = trim($thisNode);
        }
        $this->fullName = implode($separator, $fullName);
        return $this->fullName;
    }

    private $locations;

    /**
     *
     * @param string $level
     * @param type $query
     * @return type
     */
    public function getLocations($level = 'Barangay', $query = false) {
        $location_types = array('Barangay', 'Municipality', 'Province', 'Region');

        if(!in_array($level, $location_types))
            $level = 'Barangay';

        $criteria = new CDbCriteria();

        $criteria->condition = "name LIKE :name";

        if($level == "Region") {
            $concat = "concat(t.name, ' - ', t.long_name)";
            $criteria->select = "id, {$concat} as `name`";
            $criteria->condition = "{$concat} LIKE :name";
        }

        $criteria->order = "t.name";
        $criteria->limit = 20;
        $criteria->params = array(
            'name' => "%{$query}%"
        );
        $result = $this->findAll($criteria);

        $this->locations = array(
            'total' => '0',
            'locations' => array(),
        );
        foreach ($result as $v) {
            $est['id'] = $v->id;
            $est['name'] = $v->getFullName();
            $this->locations['locations'][] = $est;
        }
        $this->locations['total'] = count($this->locations['locations']);
        return $this->locations;
    }


    /**
     *
     * @param type $id
     * @param string $level
     * @return type
     */
    public function getItsLocation($id, $level = 'Barangay') {
        $location_types = array('Barangay', 'Municipality', 'Province', 'Region');

        if(!in_array($level, $location_types))
            $level = 'Barangay';

        $criteria = new CDbCriteria();

        if($level == "Region") {
            $concat = "concat(t.name, ' - ', t.long_name)";
            $criteria->select = "id, {$concat} as `name`";
        }

        $result = $this->findByPk($id, $criteria);

        if(isset($result)) {
            $this->locations['id'] = $result->id;
            $this->locations['name'] = $result->getFullName();
        }
        return $this->locations;
    }
}
