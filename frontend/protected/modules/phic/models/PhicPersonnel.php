<?php

/**
 *
 * PhicPersonnel.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */


/**
 * Description of PhicPersonnel
 *
 * @package
 */
class PhicPersonnel extends Personnel {

    /**
     *
     * @return type
     */
	public function relations() {
        $phic = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);
        return array_merge(parent::relations(), array(
            'phicAccreditation' => array(self::HAS_ONE, 'DoctorAccreditation', 'dr_nr', 
                'condition'=>'hcare_id=:hcare_id', 'params'=>array(':hcare_id' => '18')),
        ));
    }

    /**
     * @author Jolly Caralos
     */
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }

}
