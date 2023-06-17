<?php

/**
 *
 * EclaimsDoctorAccreditation.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Description of EclaimsDoctorAccreditation
 *
 * @package
 */
class EclaimsDoctorAccreditation extends DoctorAccreditation {


    /**
     * Extract accreditation data from the array result returned from the
     * eClaims web service eligibility check call
     *
     * @param array $result Array result from web service call
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
     * Returns the accreditation record of the doctor specified by $personnelNr
     *
     * @param string $personnelNr
     * @return EclaimsDoctorAccreditation
     */
    public static function getDoctorPhicAccreditation($personnelNr , $accreditationNr) {

        $phic = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);
        $accreditation = self::model()->findbyAttributes(array(
            'dr_nr' => $personnelNr,
            'hcare_id' => $phic->hcare_id
        ));
        // var_dump($phic);die();
        // var_dump($accreditation);die();

        if (!$accreditation) {
            $accreditation = new EclaimsDoctorAccreditation;
            $accreditation->personnel_nr = $personnelNr;
            $accreditation->hcare_id = $phic->hcare_id;
            $accreditation->accreditation_nr = $accreditationNr;
 
            $accreditation->save();

            return $accredidation;

        }

        return $accreditation;
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
