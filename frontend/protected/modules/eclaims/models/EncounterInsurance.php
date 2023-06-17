<?php

// namespace SegHis\models\insurance;
// use SegHis\models\insurance\InsuranceProvider;

/**
 * This is the model class for table "seg_insurance_member_info".
 *
 * The followings are the available columns in table 'seg_insurance_member_info':
 * @property string $pid
 * @property string $hcare_id
 * @property string $insurance_nr
 * @property string $member_lname
 * @property string $member_fname
 * @property string $member_mname
 * @property string $suffix
 * @property string $birth_date
 * @property string $street_name
 * @property string $brgy_nr
 * @property string $mun_nr
 * @property string $relation
 * @property string $member_type
 * @property string $employer_no
 * @property string $employer_name
 * @property string $create_date
 *
 * The followings are the available model relations:
 * @property InsuranceProvider $provider
 */

class EncounterInsurance extends \PhicMember
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_insurance_member_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('pid, hcare_id, insurance_nr', 'required'),
			array('pid', 'length', 'max'=>12),
			array('hcare_id', 'length', 'max'=>8),
			array('insurance_nr, employer_no', 'length', 'max'=>25),
			array('member_lname, member_fname, member_mname, street_name, employer_name', 'length', 'max'=>150),
			array('suffix', 'length', 'max'=>10),
			array('brgy_nr, mun_nr', 'length', 'max'=>11),
			array('relation', 'length', 'max'=>1),
			array('member_type', 'length', 'max'=>5),
			array('birth_date, remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pid, hcare_id, insurance_nr, member_lname, member_fname, member_mname, suffix, birth_date, street_name, brgy_nr, mun_nr, relation, member_type, employer_no, employer_name, create_date', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			'person' => array(self::BELONGS_TO, 'SegHis\models\person\Person', 'pid'),
            'provider' => array(self::BELONGS_TO, 'SegHis\models\insurance\InsuranceProvider', 'hcare_id'),
            'municipality' => array(self::BELONGS_TO, 'AddressMunicipality', 'mun_nr'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'pid' => 'PHN',
			'hcare_id' => 'Provider',
			'insurance_nr' => 'PIN',
			'member_lname' => 'Member Lname',
			'member_fname' => 'Member Fname',
			'member_mname' => 'Member Mname',
			'MemberFullName'=>'Member Name',
			'suffix' => 'Suffix',
			'birth_date' => 'Birth Date',
			'street_name' => 'Street Name',
			'brgy_nr' => 'Brgy Nr',
			'mun_nr' => 'Mun Nr',
			'relation' => 'Relation',
			'member_type' => 'Member Type',
			'employer_no' => 'Employer No',
			'employer_name' => 'Employer Name',
			'create_date' => 'Create Date',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PersonInsurance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}    

	public function getMemberType(){
		switch($this->member_type){
			case 'S':
				return 'Private Employee';
				break;
			case 'G':
				return 'Government Employee';
				break;
			case 'I':
				return 'Indigent';
				break;
			case 'NS':
				return 'Individually Paying';
				break;
			case 'NO':
				return 'OFW';
				break;
			case 'PS':
				return 'Non Paying Private Employee';
				break;
			case 'PG':
				return 'Non Paying Government Employee';
				break;
		}
	}

    /**
     * @return string
     */
	public function getMemberRelation(){
		switch($this->relation){
			case "M":
				return 'Self';
				break;
			case "S":
				return 'Spouse';
				break;
			case "C":
				return 'Child';
				break;
			case "P":
				return 'Parent';
				break;
		}

	}
	

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->provider;
    }

    public function getMemberInsuranceNr($pid =''){
    	 $data = \Yii::app()->db->createCommand("Select 
                    insurance_nr,
                    relation
                from seg_insurance_member_info
                WHERE pid = '$pid'")->queryAll();
            return $data[0];

    }
}
