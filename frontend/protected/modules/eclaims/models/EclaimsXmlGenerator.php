<?php

/**
 * EclaimsXmlGenerator.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.ClaimFactory');
Yii::import('eclaims.models.EClaimsTransmittal');

/**
 *
 * @package eclaims.models
 */

class EclaimsXmlGenerator extends CComponent {

    static $xmlCache;

    /**
     * Generates the Transmittal xml
     *
     * @param EclaimsTransmittal $transmittal
     * @param  boolean $regernate whether to generate a new XML or return a cached result
     * @return string returns the generated XML if successful, null if the generation fails
     */
    public static function generateXml($transmittal, $regenerate = false) {

        if ($transmittal instanceof EclaimsTransmittal) {
            return null;
        }

        if (isset(self::$xmlCache[$transmittal->transmit_no] && !$regenerate) {
            return self::$xmlCache[$transmittal->transmit_no];
        }

        $document = self::createXmlDocument();
        $eclaims = self::createEClaimsNode($document);
        $transmittals = self::createETransmittalsNode($document);

        if ($this->details) {

            /* creates a 'CLAIM' XML for each claim */
            foreach ($this->details as $detail) {
                $claim = ClaimFactory::createClaim($detail->encounter_nr);
                if ($claim) {
                    $transmittals->appendChild($document->importNode($claim->generateXml(), $deep = true));
                    if($claim->getXmlValidationErrors()) {
                        array_push($this->xmlValidationErrors, $claim->getXmlValidationErrors());
                    }
                }
            }
        }

        //$this->setXml($this->getXmlBody());
        $xml = $document->saveXml($document->documentElement);
        return $xml;
    }


    /**
     * Creates a new object of type DOMDocument
     *
     * @return void
     */
    private function _createXmlDocument($encoding='UTF-8'){
        //iso-8859-15
        $implementation = new DOMImplementation();
        $dtd = $implementation->createDocumentType('eCLAIMS', '', Yii::getPathOfAlias('eclaims.config').DIRECTORY_SEPARATOR.'eclaims.dtd');
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
    private function _createNode($document, $name, $attrs){
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
    private function _createEClaimsNode($document){
        return self::createNode($document, 'eCLAIMS', array(
            'pUserName'      => '',
            'pUserPassword'  => '',
            'pHospitalCode'  => '',
            'pHospitalEmail' => '',
        ));
    }

    /**
     * Initializes the 'eTRANSMITTAL' node
     *
     * @param  DomDocument $document
     * @return DOMElement
     */
    private function _createETransmittalsNode($document) {
        return self::createNode($document, 'eTRANSMITTAL', array(
            'pHospitalTransmittalNo' => $this->transmit_no,
            'pTotalClaims' => count($this->details)
        ));

    }
}

