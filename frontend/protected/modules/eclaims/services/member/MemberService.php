<?php
/**
 * Created by PhpStorm.
 * User: ger
 * Date: 2/4/2018
 * Time: 8:36 PM
 */

namespace SegHis\modules\eclaims\services\member;

use EclaimsPerson;
use EclaimsPhicMember2;
use CException;
use MemberPinForm;
use PhicMember;
use PhicMember2;
use SegHis\modules\eclaims\models\ConfigGlobal;
use SegHis\modules\eclaims\models\EclaimsPersonInsurance;
use ServiceCallException;
use ServiceExecutor;

class MemberService
{

    public $person;

    public $member;

    public $phicPin;


    public function __construct(EclaimsPerson $person)
    {
        $this->person = $person;

        $this->getMemberData();

    }

    /*
     * Instantiates EclaimsPhicMember2 model
     *
     *
     * */

    public function getMemberData()
    {
        $member = EclaimsPhicMember2::model()->findByAttributes(array(
          'pid' => $this->person->pid,
          'hcare_id' => PhicMember2::HCARE_ID,
        ));


        if (empty($member)) {
            $member = new PhicMember2();
            $member->pid = $this->person->pid;
        }

        $this->member = $member;
    }


    /*
     * Saves Member info,
     * @var $postData EclaimsPhicMember2
     *
     * throws Exception
     * */
    public function saveMember($postData)
    {

        $config = new ConfigGlobal();

        /* Global config for Self Insurance  */
        $selfMember = $config->findByAttributes(array(
          'type' => 'eclaims_member_self'
        ))->value;

        $phicMember = PhicMember::model()->findByAttributes(array(
          'pid' => $this->person->pid,
          'encounter_nr' => $this->person->latestEncounter->encounter_nr
        ));

        $phicMember->attributes = $postData;

        $this->member->attributes = $postData;
        $this->member->street_name = $this->person->street_name;
        $this->member->brgy_nr = $this->person->brgy_nr;
        $this->member->mun_nr = $this->person->mun_nr;

        /* Checks if relation is self, Updates care person insurance is_principal  */
        if ($postData['relation'] == $selfMember) {

            $this->savePersonInsurance();
        } else {

            if (empty($postData['patient_pin'])) {
                throw new CException('Patient Pin is Required!');
            }
            if (strlen($postData['patient_pin']) < 12) {
                throw new CException('Patient Pin must be 12 characters!');
            }
        }

        if (!empty($postData['birth_date'])) {
            $birthDate = \DateTime::createFromFormat('m/d/Y', $postData['birth_date']);
            $this->member->birth_date = $birthDate->format('Y-m-d');
            $phicMember->birth_date = $birthDate->format('Y-m-d');
        }

        if (!$this->member->save()) {
            throw new CException('Member was not saved!');
        }

        if (!$phicMember->save()) {
            throw new CException('Member was not saved!');
        }
    }


    /*@property $member MemberPinForm */
    public function checkMemberPin(MemberPinForm $member)
    {
        /* Check form if passed validations */
        if ($member->validate()) {

            /* Call check member pin */
            $service = new ServiceExecutor(
              array(
                'endpoint' => 'hie/eligibility/getpin',
                'params' => $member->getPinParams()
              )
            );
            $response = $service->execute();

            if ($response['success']) {

                /* if person is not walkin */
                if (!$this->person->getIsNewRecord()) {


                    $this->member->insurance_nr = $response['data'];
                    if (!$this->member->save()) {
                        throw new CException('Cannot Update Insurance', '409');
                    }

                }
                /* Assign PHIC generated pin from api */
                $this->phicPin = $response['data'];

            } else {

                throw new CException($response['data'], '400');
            }

        } else {

            /* Display validation errors */
            $errors = '';

            foreach ($member->attributeNames() as $attr) {

                $err = $member->getErrors();
                $errors .= $member->hasErrors($attr) ? '<li>' . $err[$attr][0] . '</li>' : '';
            }


            throw new CException($errors, '406');
        }
    }


    public function savePersonInsurance()
    {

        $config = new ConfigGlobal();

        /* Global config for Self Insurance  */
        $phicID = $config->findByAttributes(array(
          'type' => 'eclaims_phic_id'
        ))->value;

        $personInsurance = EclaimsPersonInsurance::model()->findByAttributes(
          array(
            'pid' => $this->person->pid
          )
        );

        if (empty($personInsurance)) {
            $personInsurance = new EclaimsPersonInsurance();

            $personInsurance->pid = $this->person->pid;
            $personInsurance->priority = 0;
            $personInsurance->create_id = $_SESSION['sess_login_userid'];
            $personInsurance->modify_id = $_SESSION['sess_login_userid'];
            $personInsurance->create_time = date('Y-m-d H:i:s');
            $personInsurance->modify_time = date('Y-m-d H:i:s');

        }
        $personInsurance->is_principal = 1;
        $personInsurance->hcare_id = $phicID;

        if (!$personInsurance->save()) {
            throw new CException('Person Insurance was not saved!');
        }

    }

}

