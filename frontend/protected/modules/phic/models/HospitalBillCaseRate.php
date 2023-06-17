<?php
/**
 *
 * HospitalBillCaseRate.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Description of HospitalBillCaseRate
 *
 * @package application.models
 *
 * @property int $bill_nr Description
 * @property string $package_id Description
 * @property int $rate_type Description
 * @property float $amount Description
 */
class HospitalBillCaseRate extends CareActiveRecord {

    /**
	 * @see CActiveRecord::tableName
	 */
    public function tableName()  {
        return 'seg_billing_caserate';
    }

    /**
	 * @see CActiveRecord::rules
	 */
	public function rules() {
		return array();
	}

    /**
	 * @see CActiveRecord::relations.
	 */
	public function relations() {
		return array(
            'package' => array(self::BELONGS_TO, 'CaseRatePackage', array('package_id' => 'code')),
		);
	}

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Category the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

}
