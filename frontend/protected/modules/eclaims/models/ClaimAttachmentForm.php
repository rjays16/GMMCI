<?php

/**
 *
 * ClaimAttachmentForm.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Description of ClaimAttachmentForm
 *
 * @package eclaims.models
 */
class ClaimAttachmentForm extends CFormModel {

    public $transmit_no;
    public $encounter_nr;
    public $type;
    public $attachment;

    /**
     *
     * @return array
     */
    public function rules() {
        return array(
            array('attachment', 'file', /* 'types' => 'pdf,', */ 'maxSize'=>10*1024*1024),
        );

    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'attachment'=>'File attachment',
            'type'=>'Document type',
        );
    }

    /**
     * [getUploadParams description]
     * @return array the service call parameters in array form
     */
    public function getUploadParams() {
        $contentType = CFileHelper::getMimeTypeByExtension($this->attachment->getName());
        if (empty($contentType)) {
            $contentType = 'text/plain';
        }
        $configModel = new \HospitalConfigForm;
        $hospitalCode = $configModel->hospital_code;
        return array(
            'name' => $this->attachment->getName(),
            'owner' => $this->transmit_no . '/' . $this->encounter_nr,
            'contentType' => $contentType,
            'attachment' => '@' . $this->attachment->getTempName(),
            'pHospitalCode' => $hospitalCode
        );
    }

}
