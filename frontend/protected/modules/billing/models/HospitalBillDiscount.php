<?php
/**
 *
 * HospitalBillDiscount.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Description of HospitalBillDiscount
 *
 * @package billing.models
 */
class HospitalBillDiscount extends CareActiveRecord {

    /**
	 * @see CActiveRecord::tableName
	 */
    public function tableName()  {
        return 'seg_billingcomputed_discount';
    }

    /**
	 * @see CActiveRecord::relations.
	 */
	public function relations() {
		return array(
		);
	}

    /**
     * Returns the total amound discounted for all areas
     */
    public function getDiscountedAmount() {
        $areas = array('acc', 'med', 'sup', 'srv', 'ops', 'd1', 'd2', 'd3', 'd4', 'msc');
        $total = 0;
        foreach ($areas as $area) {
            $var = 'total_' . $area . '_discount';
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
