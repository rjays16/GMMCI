<?php
  /**
   * Created by PhpStorm.
   * User: Bender
   * Date: 3/3/2019
   * Time: 4:01 PM
   */

  namespace SegHis\modules\eclaims\services\cf4;

  use DOMElement;
  use DOMImplementation;
  use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
  use SegHis\modules\eclaims\services\cf4\nodes\CourseWardService;
  use SegHis\modules\eclaims\services\cf4\nodes\EnlistmentService;
  use SegHis\modules\eclaims\services\cf4\nodes\LabResultService;
  use SegHis\modules\eclaims\services\cf4\nodes\MedicineService;
  use SegHis\modules\eclaims\services\cf4\nodes\ProfilingService;
  use SegHis\modules\eclaims\services\cf4\nodes\SOAPService;

  class CF4XmlService extends XmlWriter
  {


    public $encounter;

    public $transmittalNo;

    public function __construct(\EclaimsEncounter $encounter, $trasmittalNo)
    {
      $this->encounter = $encounter;
      $this->transmittalNo = $trasmittalNo;
    }

    protected static function _createXmlDocument($encoding = 'UTF-8')
    {
      $implementation = new DOMImplementation();

      $dtd = $implementation->createDocumentType(
          'EPCB',
          "",
          'frontend/protected/modules/eclaims/config/cf4/cf4.dtd'
      );

      $document = $implementation->createDocument('EPCB', "", $dtd);
      $document->preserveWhiteSpace = true;
      $document->formatOutput = true;
      $document->encoding = 'UTF-8';
      return $document;
    }

    public function createDocument()
    {

      $service = new CF4ApiService($this->encounter);

      $data = $service->getData();

      $document = self::_createXmlDocument();
      $epcb = $this->_createEPCBNode($document);

      /* Create Enlistment Node */
      $enlist = $this->createEnlistmentNode($document, $data, $this->transmittalNo);
      /* Create Profiling Node */
      $profiling = $this->__createPROFILING($document, $data);
      /* Create SOAP Node*/
      $soap = $this->__createSOAP($document, $data);
      /* Create COURSEWARD Node*/
      $courseward = $this->__createCOURSEWARD($document, $data);
      /* Create LABRESULT Node*/
      $labresult = $this->__createLABRESULT($document, $data);
      /* Create MEDICINES Node*/
      $medicine = $this->__createMEDICINE($document);

      /* Append Enlistment Node*/
      $epcb->appendChild($enlist);
      /*Append Profiling Node*/
      $epcb->appendChild($profiling);
      /* Append SOAP Node*/
      $epcb->appendChild($soap);
      /* Append COURSEWARD Node*/
      $epcb->appendChild($courseward);
      /* Append LABRESULT Node*/
      $epcb->appendChild($labresult);
      /* Append MEDICINE Node*/
      $epcb->appendChild($medicine);


      $document->appendChild($epcb);
      $xml = $document->saveXml($document->documentElement);
      $claim = new CF4XmlValidator($document);
      $claim->validate();


      if (!$claim->validate()) {
        \CVarDumper::dump($claim->errors, 10, true);
        die;

        return $claim->errors;
      }

      return $xml;
    }

    /**
     * Initializes the 'eCLAIMS' node of transmittal xml
     * @param  DomDocument $document
     * @params CF4ApiService $data
     * @return DOMElement
     */
    public function _createEPCBNode($document)
    {
      $epcb = $this->_createNode(
          $document,
          'EPCB',
          array(
              'pUsername' => CF4Helper::getCertificationId(),
              'pPassword' => "",
              'pHciAccreNo' => CF4Helper::getAccreditationCode(),
              'pEnlistTotalCnt' => "",
              'pProfileTotalCnt' => "",
              'pSoapTotalCnt' => "",
              'pEmrId' => "",
              'pHciTransmittalNumber' => "",
              'pCertificationId' => CF4Helper::getCertificationId(),
          )
      );

      return $epcb;
    }


    /**
     * Initializes the 'ENLISTMENTS' node
     * @param  DomDocument $document
     * @params CF4ApiService $data
     * @return DOMElement
     */
    public function createEnlistmentNode($document)
    {
      $service = new EnlistmentService($document, $this->encounter, $this->transmittalNo);
      $enlistment = $service->enlistmentNode();
      return $enlistment;
    }

    /**
     * Initializes the 'PROFILING' node
     * @param  DomDocument $document
     * @params CF4ApiService $data
     * @return DOMElement
     */
    public function __createPROFILING($document, $data)
    {
      $service = new ProfilingService($document, $this->encounter, $data);
      $profile = $service->generateNode();
      return $profile;
    }


    /**
     * Initializes the 'SOAP' node
     *
     * @param  DomDocument $document
     * @params CF4ApiService $data
     * @return DOMElement
     */
    public function __createSOAP($document, $data)
    {
      $service = new SOAPService($document, $this->encounter, $data);
      $soap = $service->createSOAP();
      return $soap;
    }

    /**
     * Initializes the 'COURSEWARD' node
     *
     * @param  DomDocument $document
     * @return DOMElement
     */

    public function __createCOURSEWARD($document, $data)
    {
      $service = new CourseWardService($document, $this->encounter, $data);
      $node = $service->generateNode();
      return $node;
    }

    public function __createLABRESULT($document)
    {
      $service = new LabResultService($document, $this->encounter);
      $node = $service->generateNode();
      return $node;
    }

    public function __createMEDICINE($document)
    {
      $service = new MedicineService($document, $this->encounter);
      $node = $service->generateNode();
      return $node;
    }

  }