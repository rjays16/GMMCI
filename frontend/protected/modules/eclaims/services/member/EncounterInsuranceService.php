<?php
/**
 * Created by PhpStorm.
 * User: ger
 * Date: 16/4/2018
 * Time: 3:03 AM
 */

namespace SegHis\modules\eclaims\services\member;


use EclaimsEncounter;
use CException;
use EclaimsPerson;
use EclaimsPhicMember;
use EclaimsEncounterInsurance;
use EncounterMemcategory;
use MemberPinForm;
use PhicMember;
use SegHis\modules\eclaims\models\ConfigGlobal;
use SegHis\modules\eclaims\models\EclaimsPersonInsurance;


class EncounterInsuranceService
{

    public $encounter;

    public $phic;

    public $person;

    public $phicMember2;

    public $memberCategory;

    public $encounterInsurance;

    public function __construct(EclaimsEncounter $encounter)
    {

        $this->encounter = $encounter;

        /* instance of EclaimsPerson */
        $this->person = $encounter->person;

        /* Instance of EclaimsPhicMember2 */
        $this->phicMember2 = $encounter->person->phicMember2;

        /* Assigns Phic if person has PHIC member  */
        $this->getPhicMember();

        /* Assigns Member Category if person has Member Category  */
        $this->getMemberCategory();

        /* Assigns Member Category if person has Member Category  */
        $this->getEncounterInsurance();

    }

    public function getPhicMember()
    {
        $phic = PhicMember::model()->findByAttributes(
            array(
                'encounter_nr' => $this->encounter->encounter_nr,
            )
        );

        if (!$phic) {
            $phic = new PhicMember;
            $phic->create_id = $_SESSION['sess_user_name'];
            $phic->create_dt = date("Y-m-d H:i:s");
        }

        $this->phic = $phic;
    }

    public function getMemberCategory()
    {
        $memberCategory = EncounterMemcategory::model()->findByAttributes(
            array(
                'encounter_nr' => $this->encounter->encounter_nr,
            )
        );

        if (!$memberCategory) {
            $memberCategory = new EncounterMemcategory;
            $memberCategory->encounter_nr = $this->encounter->encounter_nr;
        }

        $this->memberCategory = $memberCategory;
    }

    public function getEncounterInsurance()
    {
        $encounterInsurance = EclaimsEncounterInsurance::model()
            ->findByAttributes(
                array(
                    'encounter_nr' => $this->encounter->encounter_nr,
                    'hcare_id'     => $this->getPHIC(),
                )
            );


        if (!$encounterInsurance) {

            $encounterInsurance = new EclaimsEncounterInsurance;
            $encounterInsurance->hcare_id = $this->getPHIC();
            $encounterInsurance->encounter_nr = $this->encounter->encounter_nr;
            $encounterInsurance->create_id = $_SESSION['sess_user_name'];
            $encounterInsurance->create_dt = date("Y-m-d H:i:s");
        }

        $this->encounterInsurance = $encounterInsurance;
    }


    public function addInsurance()
    {


        if (empty($this->person->phicMember2->insurance_nr)) {
            throw new CException('Insurance Number Must not be Empty!');

        }

        /* Saving of PHIC Member */
        $this->phic->sex = $this->person->sex;
        $this->phic->attributes = $this->person->phicMember2->attributes;
        $this->phic->encounter_nr = $this->encounter->encounter_nr;

        $text = "Created by " . $_SESSION['sess_login_username'] . " on " .
            date("F j, Y, g:i a") . "\nencounter_nr=" .
            $this->encounter->encounter_nr . ",pid=" . $this->person->pid
            . ",insurance_nr=" .
            $this->person->phicMember2->insurance_nr . "\n\n";

        /* Saves audit trail */
        $this->phic->history = $text;

        if (!$this->phic->save()) {

            throw new CException('Phic Member was not Saved!');

        }

        $model = EclaimsPersonInsurance::model()->findByAttributes(
            array(
                'pid' => $this->person->pid,
            )
        );

        $model->hcare_id = $this->getPHIC();
        $model->priority = 0;
        $model->insurance_nr = $this->phic->insurance_nr;
        $model->modify_time = date("Y-m-d H:i:s");
        $model->modify_id = $_SESSION['sess_user_name'];

        if (!$model->save()) {

            throw new CException('Person Insurance was not Saved!');
        }

        $this->memberCategory->memcategory_id
            = $this->phic->memberCategoryByCode->memcategory_id;

        if (!$this->encounterInsurance->save()) {
            throw new CException('Unable to Save Encounter Insurance');
        }

        if (!$this->memberCategory->save()) {
            throw new CException('Member Category was not Saved!');
        }

    }


    public function removeInsurance($selected, $reason)
    {


        if (!$this->encounterInsurance->delete()) {
            throw new \CException('Insurance was not Removed');
        }


        if ($selected == 'Others') {
            $text = "Deleted by " . $_SESSION['sess_login_username'] . " on "
                . date("F j, Y, g:i a") .
                " Other reason:" . $reason .
                "\nencounter_nr=" . $this->encounter->encounter_nr . ",pid="
                . $this->person->pid . ",reason=" .
                $selected . ",other_reason=" . $reason . ",insurance_nr=" .
                $this->person->phicMember2->insurance_nr . "\n\n";
        } else {
            $text = "Deleted by " . $_SESSION['sess_login_username'] . " on "
                . date("F j, Y, g:i a") .
                " Reason:" . $reason . "\nencounter_nr=" .
                $this->encounter->encounter_nr . ",pid=" .
                $this->person->pid . ",reason=" . $reason . ",other_reason=" .
                $reason . ",insurance_nr="
                . $this->person->phicMember2->insurance_nr . "\n\n";
        }


        $this->phic->history = $text;

        if (!$this->phic->save()) {
            throw new \CException('Unable to update Audit trail!');
        }

    }

    /* Returns PHIC id based on config GLobal */
    public function getPHIC()
    {
        $config = new ConfigGlobal();

        /* Global config for Self Insurance  */
        $phicID = $config->findByAttributes(
            array(
                'type' => 'eclaims_phic_id',
            )
        )->value;

        return $phicID;
    }

}