<?php
/**
 * Cf4LibClinicalHistory.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */


/**
 * This is the model class for table "seg_cf4_lib_clinical_history".
 *
 * The followings are the available columns in table 'seg_cf4_lib_clinical_history':
 * @property integer $id
 * @property string $name
 * @property integer $is_active
 * @property integer $ordering
 * @property string $risk_factor
 */
class Cf4LibClinicalHistory extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_cf4_lib_clinical_history';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('is_active, ordering', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 200),
            array('risk_factor', 'length', 'max' => 10),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, is_active, ordering, risk_factor', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
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
            'ordering' => 'Ordering',
            'risk_factor' => 'Risk Factor',
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
        $criteria->compare('ordering', $this->ordering);
        $criteria->compare('risk_factor', $this->risk_factor, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Cf4LibClinicalHistory the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getObstetricData()
    {

        $entries = array();
        $model = $this->findAll(
            array(
                'condition' => "t.risk_factor = 'obstetric'"
            )
        );
        foreach ($model as $key => $entry) {
            if ($entry->is_active == 1) {
                $entries[$entry->id] = $entry->name;
            }
        }

        return $entries;
    }

    public function getMedicalData()
    {

        $entries = array();
        $model = $this->findAll(
            array(
                'condition' => "t.risk_factor = 'medical'"
            )
        );
        foreach ($model as $key => $entry) {
            if ($entry->is_active == 1) {
                $entries[$entry->id] = $entry->name;
            }
        }

        return $entries;
    }
}
