<?php
/**
 *
 * HospitalBillCoverage.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Description of HospitalBillCoverage
 *
 * @package billing.models
 */
class HospitalBillCoverage extends CareActiveRecord {

    /**
	 * @see CActiveRecord::tableName
	 */
    public function tableName()  {
        return 'seg_billing_coverage';
    }

    /**
     * Returns the total amound covered for all areas
     */
    public function getCoveredAmount() {
        $areas = array('services', 'acc', 'med', 'sup', 'srv', 'ops', 'd1', 'd2', 'd3', 'd4', 'msc'); //updated by michelle 03-10-15
        $total = 0;
        foreach ($areas as $area) {
            $var = 'total_' . $area . '_coverage';
            $total += $this->$var;
        }
        return $total;
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
