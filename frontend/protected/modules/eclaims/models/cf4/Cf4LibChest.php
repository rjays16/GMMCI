<?php
/**
 * Cf4LibChest.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_cf4_lib_chest".
 *
 * The followings are the available columns in table 'seg_cf4_lib_chest':
 * @property integer $id
 * @property string $name
 * @property integer $is_active
 * @property integer $is_normal
 * @property integer $ordering
 *
 * The followings are the available model relations:
 * @property Cf4Chest[] $Cf4Chests
 */
class Cf4LibChest extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_cf4_lib_chest';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'required'),
            array('id, is_active, is_normal, ordering', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 200),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, is_active, is_normal, ordering', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'Cf4Chests' => array(self::HAS_MANY, 'Cf4Chest', 'chest_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'is_active' => 'Is Active',
            'is_normal' => 'Is Normal',
            'ordering' => 'Ordering',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('is_normal', $this->is_normal);
        $criteria->compare('ordering', $this->ordering);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Cf4LibChest the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function scopes()
    {
        return array(
            'byOrdering' => array('order' => 'ordering ASC'),
        );
    }

    public function getDefaultData()
    {

        $entries = array();
        foreach ($this->byOrdering()->findAll() as $key => $entry) {
            if ($entry->is_active == 1) {
                $entries[$entry->id] = $entry->name;
            }
        }

        return $entries;

    }
}
