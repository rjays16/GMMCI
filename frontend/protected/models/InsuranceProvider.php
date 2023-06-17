<?php

/**
 *
 * InsuranceProvider.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Represents a registered insurance provider used in hospital transactions.
 * This class maps to the <code>care_insurance_firm</code> table.
 *
 * @package billing.models
 * @property int $hcare_id The system generated ID of the provider
 *
 */
class InsuranceProvider extends CareActiveRecord {

    const INSURANCE_PHIC = 'PhilHealth';

    /**
     * Cache of provider data retrieved using the getProvider static
     * method.
     *
     * @var array
     */
    protected static $providers = array();

    /**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'care_insurance_firm';
	}

    	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Personel the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}


    /**
     * Retrieves a specific provider identified with a convenient naming
     * method in order to circumvent having to query insurance provider
     * information through their system generated IDs.
     *
     * Current valid values for the $firmId parameter are as follows:
     * <dl>
     *   <dt>phic</dt>
     *   <dd>PhilHealth Insurance Company</dd>
     * </dl>
     *
     * @param string $firmId
     * @return InsuranceProvider|null
     */
    public static function getProviderByShortFirmId($firmId) {
        if (!isset(self::$providers[$firmId])) {
            $provider = null;
            $provider = self::model()->findByAttributes(array(
                'firm_id' => $firmId
            ));

            if (!$provider) {
                return null;
            }

            self::$providers[$firmId] = $provider;

        }

        return self::$providers[$firmId];
    }

}

