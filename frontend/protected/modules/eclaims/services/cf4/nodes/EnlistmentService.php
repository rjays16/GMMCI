<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/14/2019
 * Time: 3:34 PM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;

use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\CF4XmlService;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class EnlistmentService extends XmlWriter
{


    /* DOMDocument */
    public $document;
    /* EclaimsEncounter */
    public $encounter;
    /*CF4Xml Service*/
    public $service;

    /* XML NODES*/
    public $enlistments;
    public $enlistment;

    /*TRANSMITTAL NO*/
    public $transmittalNo;


    public function __construct(
        \DOMDocument $document,
        \EclaimsEncounter $encounter, $transmittalNo)
    {

        $this->transmittalNo = $transmittalNo;
        $this->encounter = $encounter;
        $this->document = $document;
    }


    public function enlistmentNode()
    {
        $enlistments = $this->_createNode(
            $this->document,
            'ENLISTMENTS',
            array()
        );

        $this->appendNode(
            $enlistments,
            $enlistment,
            'ENLISTMENT',
            $this->getEnlistmentData()
        );

        $this->document->appendChild($enlistments);

        return $enlistments;
    }

    public function getEnlistmentData()
    {
        $member = $this->getMemberInfo();
        $patient = $this->getPatientInfo();

        $enlistment = array(
            'pEClaimId' => $this->encounter->encounter_nr,
            'pEClaimsTransmittalId' => $this->transmittalNo,
            'pHciCaseNo' => $this->encounter->encounter_nr,
            'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
            'pEffYear' => CF4Helper::getYear(),
            'pEnlistStat' => CF4Helper::getDefaultEnlistStat(),
            'pEnlistDate' => date('Y-m-d', strtotime($this->encounter->encounter_date)),
            'pPackageType' => CF4Helper::getDefaultPackageType(),
            'pWithConsent' => CF4Helper::getNotApplicable(),
            'pWithLoa' => CF4Helper::getNotApplicable(),
            'pWithDisability' => CF4Helper::getNotApplicable(),
            'pDependentType' => CF4Helper::getNotApplicable(),
            'pTransDate' => CF4Helper::getDate(),
            'pCreatedBy' => $_SESSION['sess_login_username'],
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => "",
            'pAvailFreeService' => CF4Helper::getNotApplicable(),
        );
        return $enlistment + $member + $patient;
    }

    public function getMemberInfo()
    {

        return array(
            'pMemPin' => "",
            'pMemFname' => "",
            'pMemMname' => "",
            'pMemLname' => "",
            'pMemExtname' => "",
            'pMemDob' => "",
            'pMemCat' => "",
            'pMemNcat' => "",
        );
    }

    public function getPatientInfo()
    {
        return array(
            'pPatientPin' => CF4Service::getPatientPin($this->encounter->encounter_nr),
            'pPatientFname' => $this->encounter->person->name_first,
            'pPatientMname' => $this->encounter->person->name_middle,
            'pPatientLname' => $this->encounter->person->name_last,
            'pPatientExtname' => $this->encounter->person->suffix,
            'pCivilStatus' => CF4Helper::getDefaultReportStatus(),
            'pPatientType' => CF4Service::getPatientType($this->encounter->encounter_nr),
            'pPatientSex' => strtoupper($this->encounter->person->sex),
            'pPatientContactno' => "NA",
            'pPatientDob' => date('Y-m-d', strtotime($this->encounter->person->date_birth)),
            'pPatientAddbrgy' => "",
            'pPatientAddmun' => "",
            'pPatientAddprov' => "",
            'pPatientAddreg' => "",
            'pPatientAddzipcode' => "",
        );

    }
}