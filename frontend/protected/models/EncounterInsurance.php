<?php

/**
 * This is the model class for table "seg_encounter_insurance".
 *
 * The followings are the available columns in table 'seg_encounter_insurance':
 * @property string $encounter_nr
 * @property integer $hcare_id
 * @property integer $priority
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property integer $remarks
 */
class EncounterInsurance extends CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_encounter_insurance';
	}

	/**
	 * @return array validation rules for model  attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, hcare_id', 'required' , 'message' => '{attribute} cannot be blank'),
			array('hcare_id, priority', 'numerical', 'integerOnly'=>true),
			array('encounter_nr', 'length', 'max'=>12),
			array('remarks', 'length', 'max'=>200),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_dt, create_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('encounter_nr, hcare_id, priority, modify_id, modify_dt, create_id, create_dt, remarks', 'safe', 'on'=>'search'),
			array('priority', 'default', 'value' => 1, 'on' => 'insert')
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'encounter_nr' => 'Encounter Nr',
			'hcare_id' => 'Hcare',
			'priority' => 'Priority',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'remarks' => 'Remarks',
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

		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('hcare_id',$this->hcare_id);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('remarks',$this->remarks);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EncounterInsurance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function scopes() 
	{
		$alias = $this->tableAlias;
		return array(
			'recently' => array(
				'order' => $alias.".create_dt desc"
			)
		);
	}

	public function getEncounterInsuranceByProvider(InsuranceProvider $insuranceProvider = null) {
        if(!$insuranceProvider)
            return null;


        $criteria = new CDbCriteria();

        $criteria->params = array(
            ':hcare' => $insuranceProvider->hcare_id,
            ':encounterNo' => $this->encounter_nr
        );

        $criteria->addCondition('t.hcare_id = :hcare AND t.encounter_nr =:encounterNo' , 'AND');

        $data = EncounterInsurance::model()->find($criteria);

        return $data;
//        $data = EncounterInsurance::model()->findByAttributes(array(
//            'encounter_nr' => $this->encounter_nr,
//            'hcare_id'     => $insuranceProvider->hcare_id,
//        ), array(
//            'order' => 'modify_dt DESC'
//        ));



    }

}
