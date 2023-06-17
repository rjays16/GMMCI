<?php

use SegHis\modules\eclaims\models\EclaimsConfig;

/**
 *
 * ClaimAttachment.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */
Yii::import('eclaims.models.DocumentType');

/**
 * Description of ClaimAttachment
 *
 * @package application.models
 * @property string $file_id
 * @property string $transmit_no Description
 * @property string $encounter_nr Description
 * @property string $attachment_type Description
 * @property string $filename Description
 * @property string $hash Descriptiion
 * @property int $timestamp Description
 * @property string cloud_storage_filename
 * @property int|mixed is_uploaded
 * @property int|mixed is_return
 */
class ClaimAttachment extends CareActiveRecord
{

    public static $REQUIRED_ATTACHMENTS = array('CF1', 'CF2');
    public static $REQUIRED_MEDICAL_ATTACHMENTS = array('CF3');
    public static $REQUIRED_PROCEDURE_ATTACHMENTS = array('OPR');

    /**
     * @see CActiveRecord::tableName
     */
    public function tableName()
    {
        return 'seg_eclaims_claim_attachment';
    }

    /**
     * @see CActiveRecord::rules
     */
    public function rules()
    {
        return array();
    }

    /**
     * @see CActiveRecord::relations.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return CActiveRecord::attributeLabels
     */
    public function attributeLabels()
    {
        return array();
    }

    /**
     *
     */
    public function extractUploadResult($result)
    {
        $this->file_id = $result['id'];
        $this->filename = $result['name'];
        $this->hash = $result['hash'];
        $this->size = $result['size'];
        $this->timestamp = $result['timestamp'];
    }

    /**
     *
     * @return string
     */
    public function getFileSize()
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     *
     */
    public function getAttachmentType()
    {
        $types = self::getAttachmentTypes();
        // return $this->attachment_type;
        return $types[$this->attachment_type];
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Category the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public static function getAttachmentTypes()
    {

        # Mod by jeff 01-02-18 for fetching dynamic data of attachment types from db.

        $criteria = new \CDBCriteria();
        $criteria->addCondition('t.existing = 1');
        $criteria->order = 't.name';

        $doctypes = \DocumentType::model()->findAll($criteria);

        $docs = array();
        foreach ($doctypes as $document) {
            $docs[$document->id] = $document->name;
        }

        return $docs;

        /*return array(
    'CF1' => 'Claim Form 1',
    'CF2' => 'Claim Form 2',
    'CF3' => 'Claim Form 3',
    'CSF' => 'Claim Signature Form',
    'COE' => 'Certificate of Eligibility',
    'SOA' => 'Statement of Account',
    'MDR' => 'Member Data Record',
    'ORS' => 'Official Receipt',
    'POR' => 'PhilHealth Official Receipt',
    'CAE' => 'Certification of Approval/Agreement from the Employer',
    'PIC' => 'Valid PhilHealth Indigent ID',
    'MBC' => 'Member Birth Certificate',
    'MMC' => 'Member Marriage Contract',
    'CAB' => 'Clinical Abstract',
    'CTR' => 'Confirmatory Test Result by SACCL or RITM',
    'DTR' => 'Diagnostic Test Result',
    'MEF' => 'Member Empowerment Form',
    'MSR' => 'Malarial Smear Result',
    'MWV' => 'Waiver for Consent for Release of Confidential Patient Health Information',
    'NTP' => 'NTP Registry Card',
    'OPR' => 'Operative Record',
    'PAC' => 'Pre-authorization Clearance',
    'PBC' => 'Patient Birth Certificate',
    'STR' => 'HIV Screening Test Result',
    'TCC' => 'TB-Diagnostic Committee Certification',
    'TYP' => 'Three Years Payment of (2400 x 3 years of proof of payment)'
    );*/
    }

    /**
     * @param $types []
     * ['CF1', 'CF2']
     *
     * @return Array ['CF1' => 'Claim 1', 'CF2' => 'Claim 2']
     * @author Jolly Caralos
     */
    public static function getAttachmentTypesByKeys(array $types = array())
    {
        $_allTypes = self::getAttachmentTypes();
        $result = array();
        foreach ($types as $type) {
            $result[$type] = $_allTypes[$type];
        }

        return $result;
    }

    /**
     *
     * @return [type] [description]
     */
    public function getUrl()
    {
        Yii::import('eclaims.models.HospitalConfigForm');
        $form = new HospitalConfigForm;
        $url = $form->files_url;

        $eclaimsConfig = new EclaimsConfig();
        $cloudStorage = $eclaimsConfig->cloudStorageEnabled();

        if (!empty($this->cloud_storage_filename) && $cloudStorage) {
            return $this->file_id;
        }

        if ($url) {
            if (substr($url, -1, 1) !== '/') {
                $url .= '/';
            }
            return $url . $this->file_id . '.html';
        } else {
            return null;
        }
    }

    /**
     * Returns key => value pair of default required attachments
     *
     * @return Array
     * @author Jolly Caralos
     */
    public static function getDefaultRequiredAttachments()
    {
        return self::getAttachmentTypesByKeys(self::$REQUIRED_ATTACHMENTS);
    }

    /**
     * @param CaseRatePackage $package
     *
     * @return Array
     * @author Jolly Caralos
     */
    public static function getRequiredAttachmentsByCaseType(CaseRatePackage $package = null)
    {
        $result = array();
        switch ($package->case_type) {
            case CaseRatePackage::CASE_TYPE_MEDICAL:
                $result = self::getAttachmentTypesByKeys(self::$REQUIRED_MEDICAL_ATTACHMENTS);
                break;
            case CaseRatePackage::CASE_TYPE_PROCEDURE:
                $result = self::getAttachmentTypesByKeys(self::$REQUIRED_PROCEDURE_ATTACHMENTS);
        }

        return $result;
    }

    /**
     * set return to Uploaded if phic result is success
     * @param array $id
     */
    public function setUploaded($id = array())
    {
        $criteria = new \CDBCriteria();

        $criteria->addInCondition('id', $id);

        $data = $this->findAll($criteria);

        foreach ($data as $key => $datum) {
            $datum->is_uploaded = 0;
            $datum->is_return = 1;
            $datum->save();
        }
    }

}
