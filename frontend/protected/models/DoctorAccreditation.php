<?php

/**
 * This is the model class for table "seg_dr_accreditation".
 *
 * The followings are the available columns in table 'seg_dr_accreditation':
 * @property integer $dr_nr
 * @property integer $hcare_id
 * @property string $accreditation_nr
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $accreditation_start
 * @property string $accreditation_end
 *
 * The followings are the available model relations:
 * @property CarePersonell $drNr
 */
class DoctorAccreditation extends CActiveRecord{

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){

		return 'seg_dr_accreditation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dr_nr, hcare_id, accreditation_nr', 'required'),
			array('dr_nr, hcare_id', 'numerical', 'integerOnly'=>true),
			array('accreditation_nr, status', 'length', 'max'=>20),
			array('modify_id, create_id', 'length', 'max'=>300),
			array('history, modify_dt, create_dt, accreditation_start, accreditation_end', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('dr_nr, hcare_id, accreditation_nr, status, history, modify_id, modify_dt, create_id, create_dt, accreditation_start, accreditation_end', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'personnel' => array(self::BELONGS_TO, 'Personnel', 'dr_nr'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'dr_nr' => 'Dr Nr',
			'hcare_id' => 'Hcare',
			'accreditation_nr' => 'Accreditation Nr',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'accreditation_start' => 'Accreditation Start',
			'accreditation_end' => 'Accreditation End',
		);
	}

    /**
     *
     * @param array $result
     */
    public function extractResult($result) {
        $start = strtotime(str_replace('-', '/', $result["eACCREDITATION"]["@attributes"]["pAccreditationStart"]));
        $end = strtotime(str_replace('-', '/', $result["eACCREDITATION"]["@attributes"]["pAccreditationEnd"]));

        if ($start) {
            $this->accreditation_start = date('Ymd', $start);
        } else {
            $this->accreditation_start = null;
        }


        if ($end) {
            $this->accreditation_end = date('Ymd', $end);
        } else {
            $this->accreditation_end = null;
        }

        $this->accreditation_nr = $result["eACCREDITATION"]["@attributes"]["pDoctorAccreCode"];
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SegDrAccreditation the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}


}
