<?php
/**
 * ClaimFactory.php
 *
 * @author Alvin Jay C. Cosare <ajunecosare15@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * The ClaimFactory class is responsible for generating the type of claim
 * based on a patient's encounter.
 *
 * @package eclaims.models
 */
class ClaimFactory
{

    /**
     * Creates a new claim record based on a given case number.
     *
     * @todo Handle other Claim types (Z-BENEFIT, etc)
     * @param string $transmitNo Transmittal ID
     * @param string $encounterNr Case number of the new claim
     * @return Claim
     */
    public static function createClaim($transmitNo, $encounterNr) {

        Yii::import('eclaims.models.claims.CaseRateClaim');
        $claim = CaseRateClaim::model()->findByAttributes(array(
            'transmit_no' => $transmitNo,
            'encounter_nr' => $encounterNr
        ));
        if (empty($claim)) {
            $claim = new CaseRateCLaim();
            $claim->transmit_no = $transmitNo;
            $claim->encounter_nr = $encounterNr;
            $claim->save();
        }

        return $claim;
    }

}
