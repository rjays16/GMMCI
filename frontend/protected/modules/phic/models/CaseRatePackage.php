<?php

/**
 * This is the model class for table "seg_case_rate_packages".
 *
 * The followings are the available columns in table 'seg_case_rate_packages':
 * @property integer $package_id
 * @property string $code
 * @property string $description
 * @property string $group
 * @property double $package
 * @property double $hf
 * @property double $pf
 * @property double $shf
 * @property double $spf
 * @property string $case_type
 * @property integer $special_case
 * @property integer $for_infirmaries
 * @property integer $for_laterality
 * @property integer $is_allowed_second
 *
 * The followings are the available model relations:
 */
class CaseRatePackage extends ActiveRecord
{

	const CASE_TYPE_MEDICAL = 'm';
	const CASE_TYPE_PROCEDURE = 'p';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_case_rate_packages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, description, package, hf, pf, shf, spf, case_type', 'required'),
			array('special_case, for_infirmaries, for_laterality, is_allowed_second', 'numerical', 'integerOnly'=>true),
			array('package, hf, pf, shf, spf', 'numerical'),
			array('code ,alt_code' , 'length', 'max'=>15),
			array('case_type', 'length', 'max'=>1),
			array('group', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('package_id, code, description, group, package, hf, pf, shf, spf, case_type, special_case, for_infirmaries, for_laterality, is_allowed_second', 'safe', 'on'=>'search'),
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
			'diagnosis' => array(self::HAS_MANY, 'EncounterDiagnosis', 'code'),
			'details' => array(self::HAS_ONE, 'MiscellaneousOperationsDetails', 'code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'package_id' => 'Package',
			'code' => 'Code',
			'description' => 'Description',
			'group' => 'Group',
			'package' => 'Package',
			'hf' => 'Hf',
			'pf' => 'Pf',
			'shf' => 'Shf',
			'spf' => 'Spf',
			'case_type' => 'Case Type',
			'special_case' => 'Special Case',
			'for_infirmaries' => 'For Infirmaries',
			'for_laterality' => 'For Laterality',
			'is_allowed_second' => 'Is Allowed Second',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CaseRatePackage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}
