<?php

/**
 *
 * PhicHospitalBill.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('billing.models.HospitalBill');
Yii::import('phic.models.HospitalBillCaseRate');
Yii::import('phic.models.CaseRatePackage');

/**
 * Description of PhicHospitalBill
 *
 * @package phic.models
 */
class PhicHospitalBill extends HospitalBill {

    /**
     * [relations description]
     * @return [type] [description]
     */
    public function relations() {
        return array_merge(parent::relations(), array(
            'caseRate' => array(
                self::HAS_MANY,
                'HospitalBillCaseRate',
                'bill_nr'
            ),
            'caseRatePackage' => array(
                self::HAS_MANY,
                'CaseRatePackage',
                array('package_id' => 'code'),
                'through' => 'caseRate'
            )
        ));
    }

    /**
     * Returns the total amount of the caseRates.
     * 
     * @return int
     * @author Jolly Caralos
     */
    public function getTotalCaseRateAmount() 
    {
        $_total = 0;
        foreach($this->caseRate as $caseRate) {
            $_total += $caseRate->amount;
        }
        return $_total;
    }

    /**
     * Returns an array of caseRates in First and Second case rate order.
     * 
     * @return Array caseRate[]
     * @author Jolly Caralos
     */
    public function getCaseRateInOrder() {
        /* Check first item is a second rate,If true; swap. */
        if($_firstItem = reset($this->caseRate)) {
            if($_firstItem->rate_type == 2)
                $this->caseRate = array_reverse($this->caseRate);
        }
        return $this->caseRate;
    }
}
