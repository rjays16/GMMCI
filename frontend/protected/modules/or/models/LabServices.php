<?php

/**
 * This is the model class for table "seg_lab_services".
 *
 * The followings are the available columns in table 'seg_lab_services':
 * @property integer $code_num
 * @property string $service_code
 * @property string $group_code
 * @property string $name
 * @property string $price_cash
 * @property string $price_charge
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property integer $is_socialized
 * @property integer $is_ER
 * @property integer $is_package
 * @property integer $only_in_clinic
 * @property integer $with_result
 * @property integer $female_only
 * @property integer $male_only
 * @property integer $is_other
 * @property integer $with_inventory
 * @property string $remarks
 * @property string $oservice_code
 * @property string $ipdservice_code
 * @property string $erservice_code
 * @property string $area
 * @property integer $in_lis
 * @property integer $is_med
 * @property integer $has_group_stat
 * @property integer $has_param_group
 * @property integer $in_phs
 * @property integer $is_serial
 * @property integer $no_serial
 * @property integer $is_blood_product
 * @property integer $is_profile
 *
 * The followings are the available model relations:
 * @property BloodReceivedSampleH[] $segBloodReceivedSampleHs
 * @property LabErTest $labErTest
 * @property LabGroup[] $labGroups
 * @property LabResultGroup[] $labResultGroups
 * @property LabServ[] $segLabServs
 * @property LabServiceGroups $groupCode
 */
class LabServices extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_lab_services';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_code, group_code, name, modify_id, create_id', 'required'),
			array('code_num, is_socialized, is_ER, is_package, only_in_clinic, with_result, female_only, male_only, is_other, with_inventory, in_lis, is_med, has_group_stat, has_param_group, in_phs, is_serial, no_serial, is_blood_product, is_profile', 'numerical', 'integerOnly'=>true),
			array('service_code, group_code, price_cash, price_charge, oservice_code, ipdservice_code, erservice_code', 'length', 'max'=>10),
			array('name', 'length', 'max'=>100),
			array('status, modify_id, create_id', 'length', 'max'=>35),
			array('area', 'length', 'max'=>2),
			array('history, modify_dt, create_dt, remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('code_num, service_code, group_code, name, price_cash, price_charge, status, history, modify_id, modify_dt, create_id, create_dt, is_socialized, is_ER, is_package, only_in_clinic, with_result, female_only, male_only, is_other, with_inventory, remarks, oservice_code, ipdservice_code, erservice_code, area, in_lis, is_med, has_group_stat, has_param_group, in_phs, is_serial, no_serial, is_blood_product, is_profile', 'safe', 'on'=>'search'),
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
			'segBloodReceivedSampleHs' => array(self::MANY_MANY, 'BloodReceivedSampleH', 'seg_blood_received_sample_d(service_code, refno)'),
			'labErTest' => array(self::HAS_ONE, 'LabErTest', 'service_code'),
			'labGroups' => array(self::HAS_MANY, 'LabGroup', 'service_code'),
			'labResultGroups' => array(self::HAS_MANY, 'LabResultGroup', 'service_code'),
			'segLabServs' => array(self::MANY_MANY, 'LabServ', 'seg_lab_servdetails(service_code, refno)'),
			'groupCode' => array(self::BELONGS_TO, 'LabServiceGroups', 'group_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'code_num' => 'Code Num',
			'service_code' => 'Service Code',
			'group_code' => 'Group Code',
			'name' => 'Name',
			'price_cash' => 'Price Cash',
			'price_charge' => 'Price Charge',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'is_socialized' => 'Is Socialized',
			'is_ER' => 'Is Er',
			'is_package' => 'Is Package',
			'only_in_clinic' => 'Only In Clinic',
			'with_result' => 'With Result',
			'female_only' => 'Female Only',
			'male_only' => 'Male Only',
			'is_other' => 'Is Other',
			'with_inventory' => 'With Inventory',
			'remarks' => 'Remarks',
			'oservice_code' => 'Oservice Code',
			'ipdservice_code' => 'Ipdservice Code',
			'erservice_code' => 'Erservice Code',
			'area' => 'Area',
			'in_lis' => 'In Lis',
			'is_med' => 'Is Med',
			'has_group_stat' => 'Has Group Stat',
			'has_param_group' => 'Has Param Group',
			'in_phs' => 'In Phs',
			'is_serial' => 'Is Serial',
			'no_serial' => 'No Serial',
			'is_blood_product' => 'Is Blood Product',
			'is_profile' => 'Is Profile',
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

		$criteria->compare('code_num',$this->code_num);
		$criteria->compare('service_code',$this->service_code,true);
		$criteria->compare('group_code',$this->group_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price_cash',$this->price_cash,true);
		$criteria->compare('price_charge',$this->price_charge,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('is_socialized',$this->is_socialized);
		$criteria->compare('is_ER',$this->is_ER);
		$criteria->compare('is_package',$this->is_package);
		$criteria->compare('only_in_clinic',$this->only_in_clinic);
		$criteria->compare('with_result',$this->with_result);
		$criteria->compare('female_only',$this->female_only);
		$criteria->compare('male_only',$this->male_only);
		$criteria->compare('is_other',$this->is_other);
		$criteria->compare('with_inventory',$this->with_inventory);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('oservice_code',$this->oservice_code,true);
		$criteria->compare('ipdservice_code',$this->ipdservice_code,true);
		$criteria->compare('erservice_code',$this->erservice_code,true);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('in_lis',$this->in_lis);
		$criteria->compare('is_med',$this->is_med);
		$criteria->compare('has_group_stat',$this->has_group_stat);
		$criteria->compare('has_param_group',$this->has_param_group);
		$criteria->compare('in_phs',$this->in_phs);
		$criteria->compare('is_serial',$this->is_serial);
		$criteria->compare('no_serial',$this->no_serial);
		$criteria->compare('is_blood_product',$this->is_blood_product);
		$criteria->compare('is_profile',$this->is_profile);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LabServices the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function labSearch($query = array()) {
		$criteria = new CDbCriteria();

		if (!empty($query)) {
			$criteria->addSearchCondition('name', $query['name'], true, 'AND');
		}

		// $criteria->addCondition('is_deleted = 0', 'AND');

		return $this->findAll($criteria);
	}

	public function getItem_code(){
		return $this->service_code;
	}
}
