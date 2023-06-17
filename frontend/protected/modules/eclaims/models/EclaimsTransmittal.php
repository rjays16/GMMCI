<?php

/**
 *
 * EclaimsTransmittal.php
 *
 * @author Alvin Jay C. Cosare <ajunecosare15@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.Claim');
Yii::import('eclaims.models.ClaimFactory');
Yii::import('eclaims.models.EclaimsTransmittalDetails');
Yii::import('eclaims.models.EclaimsTransmittalExt');
Yii::import('phic.models.Transmittal');
Yii::import('eclaims.models.HospitalConfigForm');
use SegHis\modules\eclaims\models\ConfigGlobal;

/**
 * Description of EclaimsTransmittal
 *
 * @package application.models
 */
class EclaimsTransmittal extends Transmittal
{

    protected $_ext;

    /**
     *
     * @var [type]
     */
    protected $xmlCache = null;

    /* XML elements */
    private $dom;

    // only once
    private $eClaims;

    // only once - $dom
    private $eTransmittal;

    // only once - $eClaims



    /**
     * @var string[] $xmlValidationErrors contains validation errors of Transmittal XML
     */
    protected $xmlValidationErrors = array();

    /**
     * @var string $xml transmittal XML string
     */
    private $xml;

    /**
     * Overrides the default
     * @param [type] $value [description]
     */
    public function getext() {
        if (empty($this->extend)) {
            if (empty($this->_ext)) {
                $this->_ext = new EclaimsTransmittalExt;
                $this->_ext->transmit_no = $this->transmit_no;
            }
            return $this->_ext;
        } else {
            return $this->extend;
        }
    }

    /**
     * @see CActiveRecord::relations.
     */
    public function relations() {
        return array_merge(parent::relations() , array(
            'details' => array(
                self::HAS_MANY,
                'EclaimsTransmittalDetails',
                'transmit_no'
            ),
            'detailsCount' => array(
                self::STAT,
                'EclaimsTransmittalDetails',
                'transmit_no'
            ),
            'extend' => array(
                self::HAS_ONE,
                'EclaimsTransmittalExt',
                'transmit_no'
            ),
        ));
    }

    /**
     *
     * @return array
     */
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), array(
            'detailsCount' => '# of Claims'
        ));
    }

    /**
     *
     */
    public function searchTransmittalDetails() {
        return new CActiveDataProvider('EclaimsTransmittalDetails', array(
            'criteria' => array(
                'condition' => 't.transmit_no=:transmitNumber',
                'params' => array(':transmitNumber' => $this->transmit_no),
                'order' => 'memcategory.memcategory_desc, person.name_last',
                'with' => array(
                    'encounter' => array(
                        'select' => 'encounter_nr, encounter_type, pid, discharge_date, discharge_time',
                        'with' => array(
                            'person',
                            'encounterMemCategory' => array(
                                'select' => 'memcategory_id',
                                'with' => 'memcategory',
                            ),
                        ),
                    ),
                ),
            )
        ));

    }

    /**
     *@author: Jasper Ian Q. Matunog 11/25/2014
     *@return:
     */
    public function findTransmittal($id) {
        return $this->find(array(
            'condition' => 't.transmit_no=:transmitNumber',
            'params' => array(':transmitNumber' => $id),
            'order' => 'memcategory.memcategory_desc, person.name_last',
            'with' => array(
                'details' => array(
                    'select' => 'transmit_no',
                    'with' => array(
                        'encounter' => array(
                            'select' => 'encounter_nr, encounter_type',
                            'with' => array(
                                'person',
                                'encounterMemCategory' => array(
                                    'select' => 'memcategory_id',
                                    'with' => 'memcategory',
                                ),
                            ),
                        ),
                    ),
                )
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Category the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     *
     * @return
     */
    protected function afterSave() {
        parent::afterSave();

        // Save the ext data each time you save the main transmittal data
        $this->ext->save();
    }

    /**
     * Retrieves upload status of transmittal
     * @return string
     */
    public function getStatus() {
        if ($this->ext->is_uploaded == 0) {
            return 'Not Uploaded';
        } elseif ($this->ext->is_mapped == 0) {
            return 'Not Mapped';
        } else {
            return 'Mapped';
        }
    }

    /**
     * Generates the Transmittal xml
     *
     * @return boolean returns TRUE if the process was successful
     */
    public function generateXml($regenerate = false) {

        if ($this->xmlCache && !$regenerate) {
            return $this->xmlCache;
        }

        $document = self::_createXmlDocument();

        $eclaims = $this->_createEClaimsNode($document);
        $transmittals = $this->_createETransmittalsNode($document);

        if ($this->details) {

            /* creates a 'CLAIM' XML for each claim */
            foreach ($this->details as $detail) {
                $claim = ClaimFactory::createClaim($detail->transmit_no, $detail->encounter_nr);
                if ($claim) {
                    $xml = $claim->generateXml();
                    $claimDocument = DOMDocument::loadXml($xml);
                    $transmittals->appendChild($document->importNode($claimDocument->documentElement, $deep = true));

                    // Check for validation errors
                    $result = Claim::validateXml($xml);
                    if ($result !== true) {
                        $this->xmlValidationErrors[$detail->encounter_nr] = $result;
                    }
                }
            }
        }

        //$this->setXml($this->getXmlBody());
        $eclaims->appendChild($transmittals);
        $document->appendChild($eclaims);

        $xml = $document->saveXml($document->documentElement);
        return $xml;
    }

    /**
     * Returns the validation errors from the latest generateXml call if there any.
     *
     * @return array
     */
    public function getValidationErrors() {
        return $this->xmlValidationErrors;
    }

    /**
     * Creates a new object of type DOMDocument
     *
     * @return void
     */
    protected static function _createXmlDocument($encoding = 'UTF-8') {
        $implementation = new DOMImplementation();
        $dtd = $implementation->createDocumentType('CLAIM', '', 'frontend/protected/modules/eclaims/config/eclaims.dtd');
        $document = $implementation->createDocument(null, null, $dtd);
        $document->preserveWhiteSpace = true;
        $document->formatOutput = true;
        $document->encoding = $encoding;
        return $document;
    }

    /**
     * Creates a new XML node with given name and attributes
     *
     * @param DomDocument the original XML document
     * @param string $name tag name for the new node
     * @param array $attrs array holding the attributes of the child node
     * @return void
     */
    private function _createNode($document, $name, $attrs) {
        $node = $document->createElement($name);
        foreach ($attrs as $akey => $value) {
            $node->setAttribute($akey, $value);
        }
        return $node;
    }

    /**
     * Initializes the 'eCLAIMS' node of transmittal xml
     *
     * @param  DomDocument $document
     * @return DOMElement
     */
    private function _createEClaimsNode($document) {
        # Modified by monmon : fetch hospital code from global db.
        # Mod by jeff 02-28-18 for other added attributes.
        $config = new HospitalConfigForm;
        return $this->_createNode($document, 'eCLAIMS', array(
            'pUserName' => '',
            'pUserPassword' => '',
            'pHospitalCode' => $config->hospital_code, #Yii::app()->params['HOSPITAL_CODE'],
            'pServiceProvider' => 'SEGWORKS',
            'pHospitalEmail' => 'eclaims@segworks.com',
//            'pCertificateId' => 'ECS002031213001A'
        ));
    }

    /**
     * Initializes the 'eTRANSMITTAL' node
     *
     * @param  DomDocument $document
     * @return DOMElement
     */
    private function _createETransmittalsNode($document) {
        return $this->_createNode($document, 'eTRANSMITTAL', array(
            'pHospitalTransmittalNo' => $this->transmit_no,
            'pTotalClaims' => count($this->details)
        ));
    }

    private static function _isValidXML($xml) {
        $doc = @simplexml_load_string($xml);
        if ($doc) {
            return true; //this is valid
        } else {
            return false; //this is not valid
        }
    }

    /**
     *
     * @return array
     */
    public static function validateXml($xml) {
        $document = self::_createXmlDocument();
        /* Check if the xml string is a valid xml, w/o the DTD */
        if(!self::_isValidXML($xml)) {
            return array(
                'code' => 500,
                'reason' => 'Invalid XML Format'
            );
        }
        $document->loadXml($xml);
        if ($document === false) {
            return false;
        }

        $xPath = new DOMXpath($document);

        $claims = $xPath->query('eTRANSMITTAL/CLAIM');
        $errors = array();
        foreach ($claims as $claim) {
            $claimsXml = $claim->ownerDocument->saveXml($claim);
            $result = Claim::validateXml($claimsXml);
            if ($result !== true) {
                $errors[$claim->getAttribute('pClaimNumber') ] = $result;
            }
        }

        return $errors ? $errors : true;
    }

    /**
     * Checks if all of the transmittal's claims have attachments
     *
     * @return boolean
     */
    public function isValidAttachments() {
        foreach ($this->details as $details) {
            if (empty($details->attachments)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Function countTransmittalStatuses
     *
     * Gets the count of different kinds of transmittal status
     *
     * @return array counts of transmittal statuses
     */
    public static function countTransmittalsByStatus() {
        return array(
            'notUploaded' => self::model()->with('extend')->count(array(
                'condition' => 'extend.is_uploaded = 0 && extend.is_mapped = 0',
            )) ,
            'Uploaded' => self::model()->with('extend')->count(array(
                'condition' => 'extend.is_uploaded = 1 && extend.is_mapped = 0',
            )) ,
            'Mapped' => self::model()->with('extend')->count(array(
                'condition' => 'extend.is_uploaded = 1 && extend.is_mapped = 1',
            )) ,
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {

        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->with = 'extend';
        $criteria->compare('t.transmit_no', $this->transmit_no, true);
        $criteria->compare('transmit_dte', $this->transmit_dte, true);
        $criteria->compare('extend.control_no', $this->ext->control_no, true);
        $criteria->compare('extend.ticket_no', $this->ext->ticket_no, true);
        // $criteria->order = 't.transmit_dte DESC';

        $sort = new CSort();
        $sort->attributes = array(
            'transmit_no' => array(
                'asc' => 't.transmit_no',
                'desc' => 't.transmit_no desc',
            ) ,
            'transmit_dte' => array(
                'asc' => 't.transmit_dte',
                'desc' => 't.transmit_dte desc',
            ) ,
            'Status' => array(
                'asc' => 'extend.is_uploaded',
                'desc' => 'extend.is_uploaded desc',
            ) ,
        );
        $sort->defaultOrder = array(
            'transmit_dte' => CSort::SORT_DESC,
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort
        ));
    }

    /**** DOM Document functions ****/

    /**
     * Retrieve xml string w/o headers
     *
     * @return string - xml body in string
     */

    // public function getXmlBody(){
    //  return $this->dom->saveXML($this->dom->documentElement);
    // }



    /**
     * Handles checking xml validity against dtd
     *
     * @param string $xmlString
     * @return boolean - true if xml has no validation errors, 'invalid' if there were php errors while processing
     */
    public function processValidateXml($xmlString) {
        $this->setXml($xmlString);
        if (!Transmittal::isCleanXml($xmlString)) {
            return false;
        }

        $transmittalDoc = Transmittal::loadXml($xmlString);
        $size = count($this->details);

        if ($transmittalDoc) {
            $validationErrors = Transmittal::validateClaims($transmittalDoc, $size);
            if (is_array($validationErrors) && !empty($validationErrors)) {
                $this->saveXmlValidity(false);
                return $validationErrors;
            } else if (is_array($validationErrors) && empty($validationErrors)) {
                $this->saveXmlValidity(true);
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Creates a new DOM document from xml string
     * @param string $xmlString
     * @return boolean $result - true if DOM document was successfully created
     */
    public static function loadXml($xmlString) {
        $doc = new DOMDocument;
        $result = @$doc->loadXml($xmlString);
        return (($result) ? $doc : false);
    }

    /**
     * Handles validating each claim tag and saves claim validation errors
     * @param DOMDocument $doc - document containing xml
     * @param int $size - number of claims in the transmittal
     * @return string[] $xmlValidationErrors | boolean - false if there were php errors
     */
    public static function validateClaims($doc, $size) {
        $xmlValidationErrors = array();
        $xpath = new DOMXpath($doc);
        try {
            for ($i = 1; $i <= $size; $i++) {

                /* retrieve i-th <CLAIM> tag as a DOM element */
                $claimNode = $xpath->query('//eTRANSMITTAL/CLAIM[' . $i . ']');
                @$element = print_r($claimNode->item(0)->C14N(true, true) , true);
                $element = htmlentities($element);

                /* retrieve encounter_nr of <CLAIM> (pClaimNumber) */
                $claimNumber = $xpath->query('//eTRANSMITTAL/CLAIM[' . $i . ']/@pClaimNumber');
                $encounter_nr = $claimNumber->item(0)->value;

                /* if type equal to ALLCASERATE */
                $claim = new CaseRateClaim();

                /* if type equal to Z-BENEFIT insert this to Z-BENEFIT CLASS */

                // $claim - new ZBenefitClaim(); #TODO

                $claim->setEncounterNr($encounter_nr);
                $claim->createXml($element, $encounter_nr);
                $claim->validateXml();

                if ($claim->getXmlValidationErrors()) {
                    $xmlValidationErrors[] = Transmittal::prepareErrors($claim->getXmlValidationErrors());
                }
            }
            return $xmlValidationErrors;
        }
        catch(Exception $e) {
            return false;
        }
    }

    /**
     * Prepares and arranges validation errors of a CLAIM in an array ready for display
     * @param string[] $xmlValidationErrors - validation errors for a specific claim
     * @return string[] $errors
     */
    public static function prepareErrors($xmlValidationErrors) {
        $errors = array();
        $errors['claimNumber'] = $xmlValidationErrors[0];
        $errors['errors'] = array();
        for ($i = 1; $i < count($xmlValidationErrors); $i++) {
            $errors['errors'][] = $xmlValidationErrors[$i];
        }

        return $errors;
    }

    /**
     * Handles checking xml string for any syntax errors and saves it if no errors found
     * @param string $xmlString
     * @return boolean - true if there was no syntax errors and saving was successful
     */
    public function processSaveXml($xmlString) {
        $this->setXml($xmlString);
        if (!Transmittal::isCleanXml($xmlString)) {
            return false;
        }

        if (Transmittal::loadXml($xmlString)) {
            if ($this->saveXmlData()) {
                return true;
            } else {

                //TODO catch sql exception


            }
        } else {
            return false;
        }
    }

    /**
     * Checks if xml string has any characters in between end tags(>) and start tags(<)
     * @param string $xmlString
     * @return boolean - true if $xmlString is clean
     */
    public static function isCleanXml($xmlString) {
        $cleanedXml = preg_replace('/>(.*)/', '>', $xmlString);

        if (strcmp($cleanedXml, $xmlString) != 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Cleans xml string of any characters in between end tags(>) and start tags(<)
     * @param string $xmlString
     * @return string - cleaned $xmlString
     */
    public static function cleanXml($xmlString) {
        return preg_replace('/>(.*)/', '>', $xmlString);
    }

    /**
     * Saves transmittal xml to database
     * @param string $xmlString
     * @return boolean $result - true if saving was successful
     */
    public function saveXmlData() {
        $transaction = Yii::app()->getDb()->beginTransaction();
        try {
            $result = true;

            if (!empty($this->xml)) {
                $this->ext->xml_data = $this->getXml();
                $result = $this->save();
            } else {
                $result = false;
            }
        }
        catch(Exception $e) {
            $result = false;
        }

        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollback();
        }

        return $result;
    }

    /**
     * Saves transmittal xml validity to database
     * @param boolean $validity
     * @return boolean $result - true if saving was successful
     */
    public function saveXmlValidity($validity) {
        $transaction = Yii::app()->getDb()->beginTransaction();
        try {
            $result = true;
            $this->xml_is_valid = $validity;
            $result = $this->save();
        }
        catch(Exception $e) {
            $result = false;
        }

        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollback();
        }

        return $result;
    }

    /**
     * function compactUpload
     *
     * creates an array with the parameters for accessing the upload function of
     * the HIE web service
     *
     * @param Transmittal $transmittal
     * @return array Data for uploading the transmittal to the HIE web service
     */
    public function compactUpload() {
        return array(
            'pUserName' => '',
            'pPassword' => '',
            'pHospitalCode' => $this->getHospitalCode(),
            'pXML' => $this->ext->xml_cache
        );
    }
    #monmon
    private function getHospitalCode(){
        Yii::import('eclaims.models.HospitalConfigForm');
        $configModel = new HospitalConfigForm;
        $hospitalCode = $configModel->hospital_code;
        return $hospitalCode;
    }
    /**
     * function extractUploadResult
     *
     * converts the success result into an array. Also saves first time data
     * into the database
     *
     * @param int $transmittal - transmittal number that was uploaded
     * @param array $result - result returned by the HIE web service
     *
     */
    public function extractUploadResult($result) {
        $attributes = @$result["eRECEIPT"]["@attributes"];
        $this->ext->total_claims = @$attributes["pTotalClaims"];
        $this->ext->control_no = @$attributes["pTransmissionControlNumber"];
        $this->ext->transmission_date = date("Y-m-d", strtotime(str_replace('-', '/', @$attributes["pTransmissionDate"]))) . ' ' . date("H:i:s", strtotime(@$attributes["pTransmissionTime"]));

        if (isset($attributes['pReceiptTicketNumber'])) {
            $this->ext->ticket_no = @$attributes["pReceiptTicketNumber"];
            $this->ext->is_uploaded = 1;
        }
    }

    /**
     * Function extractUploadRemarks
     *
     * Extracts the remarks generated to an array
     *
     * @param array result - resulted remarks
     *
     * @return string errors - converted string
     */
    public static function extractUploadRemarks($result) {

        if (!empty($result["eRECEIPT"])) {

            $eReceipt = @$result["eRECEIPT"];

            /* Remarks data is extracted and returned as array. */
            $remarks = @$eReceipt["REMARKS"];
            if (!is_array($remarks)) {
                $remarks = array();
            }

            $errors = array();

            /* Error code and description is extracted for each index. */
            foreach ($remarks as $remark) {
                if(isset($remark['@attributes'])) {
                    $attributes = $remark['@attributes'];
                } else {
                    $attributes = $remark;
                }

                $errors[] = array(
                    "code" => $attributes["pErrCode"],
                    "description" => $attributes["pErrDescription"]
                );
            }
            return $errors;
        } else {
            return array();
        }
    }

    /**
     * function compactMap
     *
     * creates an array containing the parameters for accessing the mapping function of
     * the HIE web service
     *
     * @param Transmittal $transmittal
     *
     * @return array - data parameter for mapping the transmittal to HIE web service
     */
    public function compactMap($transmittal) {
        $transmittal = (empty($transmittal)) ? new Transmittal : $transmittal;

        $insurance = new InsuranceProvider;
        $accreditationNo = empty(InsuranceProvider::model()->findByPk($transmittal->hcare_id)->accreditation_no) ?
            '' : InsuranceProvider::model()->findByPk($transmittal->hcare_id)->accreditation_no;

        $config = new ConfigGlobal();

        /* Global config hospital accreditation code  */
        $hospitalCode = $config->findByAttributes(array(
            'type' => 'hie_service_hospital_code'
        ))->value;

        $result = array(
            'pUserName' => '',
            'pPassword' => '',
            'pHospitalCode' => $hospitalCode, #
            'pReceiptTicketNumber' => $transmittal->ext->ticket_no,
        );

        return $result;
    }

    /**
     * function extractMapResult
     *
     * Converts the result into an array. 
     * Also saves($this->ext->is_mapped = 1) first time data into the database
     *
     * @param int $transmittal - transmittal number that was being mapped
     * @param array $result - result returned by the HIE web service
     * @return boolean $ok - parameter whether the map function was successful
     * @throws Exception
     */
    public function extractMapResult($transmittalNumber, $result) {
        $transaction = Yii::app()->getDb()->beginTransaction();
        try {
            $ok = true;

            if ($ok) {

                /* Extracts the map data directly. */
                $mapping = @$result["data"]["eCONFIRMATION"]["MAPPING"];
                /* If Mapping contains a single record of claim, convert to multi maps; else, DO nothing... */
                if(isset($mapping['@attributes']))
                    $mapping = array($mapping);

                if (!empty($mapping) && is_array($mapping)) {

                    /* If only one map exists, then convert to an array of array*/
                    $mapping = (is_string(key($mapping))) ? array(
                        $mapping
                    ) : $mapping;

                    /* Each claim's claim series lhio is saved for each claim number. */
                    foreach ($mapping as $map) {
                        $attributes = @$map["@attributes"];

                        $claim = Claim::model()->find(array(
                            'condition' => 'encounter_nr=:encounter_nr AND transmit_no=:transmit_no',
                            'params' => array(
                                ':encounter_nr' => $attributes["pClaimNumber"],
                                ':transmit_no'  => $transmittalNumber
                            )
                        ));
                        $claim->claim_series_lhio = @$attributes["pClaimSeriesLhio"];
                        $ok = $claim->save();

                        /* If each claim is successfully saved, map status is updated. */
                        if ($ok) {
                            $this->ext->is_mapped = 1;
                            $ok = $this->save();
                        }
                    }
                }
            }
        }
        catch(Exception $e) {
            $ok = false;
        }

        if ($ok) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollback();
            return false;
        }
    }

    /**
     * Function toResponseArray
     *
     * Converts the active record, from the claimsUpload response to an array
     *
     * @return array
     */
    public function toResponseArray() {

        /* This active record (transmittal) is called to retrieve specific data. */
        $insurance = new InsuranceProvider;
        $accreditationNo = empty(InsuranceProvider::model()->findByPk($this->hcare_id)->accreditation_no) ?
            '' : InsuranceProvider::model()->findByPk($this->hcare_id)->accreditation_no;

        // CVarDumper::dump($this->ext, 10, true); die;

        $result = array(
            'hospital_code' => $accreditationNo,
            'transmit_no' => $this->transmit_no,
            'no_claim' => $this->ext->total_claims,
            'transmit_dte' => $this->transmit_dte,
            'control_no' => $this->ext->control_no,
            'ticket_no' => $this->ext->ticket_no,
        );

        /* Each string is converted to an upper case string*/
        array_walk($result, function (&$value, $key) {
            $value = (empty($value)) ? '' : strtoupper($value);
        });

        return $result;
    }

        public function getClaimStatusReturn($encounter_nr)
    {

        $model = new Claim;
        $criteria = new CDbCriteria();

        $criteria->with = array(
            'status',
            'status.return' => array('joinType' => 'INNER JOIN'),
        );

        $criteria->params = array(
            ':encounter_nr' => $encounter_nr,
        );


        $criteria->condition = 't.encounter_nr = :encounter_nr';

        $data = $model->find($criteria);

        if ( ! empty($data->id)) {
            return true;
        }

        return false;

    }
}
