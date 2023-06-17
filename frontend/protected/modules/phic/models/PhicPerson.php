<?php
/**
 *
 * PhicPerson.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('phic.models.PhicMember');
Yii::import('phic.models.PhicPersonnel');

/**
 * Description of PhicPerson
 *
 * @package application.models
 */
class PhicPerson extends Person {

    /**
	 * @see CActiveRecord::relations.
	 */
	public function relations() {
        $phic = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);
		return array_merge(parent::relations(), array(
            'phicMember' => array(self::HAS_ONE, 'PhicMember', 'pid','condition'=>'hcare_id = :hcare_id', 'params'=>array(':hcare_id'=>$phic->hcare_id)),
		));
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
