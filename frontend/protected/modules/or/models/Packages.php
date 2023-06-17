<?php

/**
 * This is the model class for table "seg_packages".
 *
 * The followings are the available columns in table 'seg_packages':
 * @property integer $package_id
 * @property string $package_name
 * @property double $package_price
 * @property integer $is_surgical
 * @property string $pkg_phiccode
 * @property integer $is_zpackage
 * @property string $create_id
 * @property string $modify_id
 * @property string $create_time
 * @property string $modify_time
 * @property string $history
 * @property integer $clinic_id
 *
 * The followings are the available model relations:
 * @property BillingPkg[] $billingPkgs
 * @property HcareBsked[] $segHcareBskeds
 * @property PackageDetails[] $packageDetail
 * @property PackagesClinics[] $packagesClinics
 */
class Packages extends CActiveRecord
{
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_packages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('package_name', 'required'),
			array('is_surgical, is_zpackage, clinic_id', 'numerical', 'integerOnly'=>true),
			array('package_price', 'numerical'),
			array('package_name', 'length', 'max'=>100),
			array('pkg_phiccode', 'length', 'max'=>10),
			array('create_id, modify_id', 'length', 'max'=>60),
			array('package_id,create_time, modify_time, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('package_id, package_name, pkg_phiccode', 'safe', 'on'=>'search'),
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
			'billingPkgs' => array(self::HAS_MANY, 'BillingPkg', 'package_id'),
			'segHcareBskeds' => array(self::MANY_MANY, 'HcareBsked', 'seg_hcare_packages(package_id, bsked_id)'),
			'packageDetails' => array(self::HAS_MANY, 'PackageDetails', 'package_id'),
			'packagesClinics' => array(self::HAS_MANY, 'PackagesClinics', 'package_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'package_id' => 'Package ID',
			'package_name' => 'Package Name',
			'package_price' => 'Package Price',
			'is_surgical' => 'Surgical?',
			'pkg_phiccode' => 'PHIC Code',
			'is_zpackage' => 'ZPackage?',
			'create_id' => 'Create',
			'modify_id' => 'Modify',
			'create_time' => 'Create Time',
			'modify_time' => 'Modify Time',
			'history' => 'History',
			'clinic_id' => 'Clinic',
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

		$criteria=new CDbCriteria;
		$criteria->compare('package_id',$this->package_id,true);
		$criteria->compare('package_name',$this->package_name,true);
        $criteria->order = 'package_name ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Packages the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function actionGetAllItems(){
        $data = Yii::app()->db->createCommand()
        ->select('name_first')
        ->from('care_person')
        ->query();
        return CJSON::encode($data);
    }

    public function getIsPackageText(){
        return ($this->is_surgical == 1)?"Yes":"No";
    }

    public function getIsZpackageText(){
        return ($this->is_zpackage == 1)?"Yes":"No";
    }
}
