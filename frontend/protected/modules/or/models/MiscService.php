<?php
Yii::import('or.models.MiscServiceDetails');
Yii::import('or.models.OtherServices');

/**
 * This is the model class for table "seg_misc_service".
 *
 * The followings are the available columns in table 'seg_misc_service':
 * @property string $refno
 * @property string $chrge_dte
 * @property string $encounter_nr
 * @property string $pid
 * @property string $discountid
 * @property string $discount
 * @property integer $is_cash
 * @property string $request_source
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $area
 *
 * The followings are the available model relations:
 * @property CareEncounter $encounterNr
 * @property TypeRequestSource $requestSource
 * @property MiscServiceDetails[] $miscServiceDetails
 */
class MiscService extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_misc_service';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, chrge_dte, encounter_nr', 'required'),
			array('is_cash', 'numerical', 'integerOnly'=>true),
			array('refno, encounter_nr, pid', 'length', 'max'=>12),
			array('discountid, discount, request_source, area', 'length', 'max'=>10),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('history, create_dt', 'safe'),
			array('modify_dt, create_dt','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_id, create_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_dt','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'update'),
			array('modify_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, chrge_dte, encounter_nr, pid, discountid, discount, is_cash, request_source, history, modify_id, modify_dt, create_id, create_dt, area', 'safe', 'on'=>'search'),
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
			'encounterNr' => array(self::BELONGS_TO, 'CareEncounter', 'encounter_nr'),
			'requestSource' => array(self::BELONGS_TO, 'TypeRequestSource', 'request_source'),
			'miscServiceDetails' => array(self::HAS_MANY, 'MiscServiceDetails', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'chrge_dte' => 'Chrge Dte',
			'encounter_nr' => 'Encounter Nr',
			'pid' => 'Pid',
			'discountid' => 'Discountid',
			'discount' => 'Discount',
			'is_cash' => 'Is Cash',
			'request_source' => 'Request Source',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'area' => 'Area',
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

		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('chrge_dte',$this->chrge_dte,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('discountid',$this->discountid,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('is_cash',$this->is_cash);
		$criteria->compare('request_source',$this->request_source,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('area',$this->area,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MiscService the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getPk($chrge_dte){
		$criteria = new CDbCriteria();
		$criteria->condition = "refno LIKE CONCAT(YEAR('{$chrge_dte}'), LPAD(CAST(MONTH('{$chrge_dte}') AS CHAR), 2, '0'), '%')";
		$criteria->order = "refno DESC";
		$result = $this->find($criteria);

		if($result)
			return $result->refno + 1;
		else
			return date('Y', strtotime($chrge_dte)) . date('m', strtotime($chrge_dte)) . '000001';
	}

	public function getEntry($enc_no){
		$criteria = new CDbCriteria();

		$criteria->with = array(
			'miscServiceDetails' => array('joinType' => 'INNER JOIN'),
		);

		$criteria->addSearchCondition('encounter_nr', $enc_no, true, 'AND');

		$criteria->order = "miscServiceDetails.entry_no DESC";
		$result = $this->find($criteria);

		if($result)
			return $result->miscServiceDetails->entry_no + 1;

		return 1;
	}
}
