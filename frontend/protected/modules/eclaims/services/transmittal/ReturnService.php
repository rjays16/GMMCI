<?php
/**
 * Created by PhpStorm.
 * User: STAR LORD
 * Date: 8/24/2018
 * Time: 8:03 PM
 */


namespace SegHis\modules\eclaims\services\transmittal;

\Yii::import('eclaims.services.ServiceExecutor');
\Yii::import('eclaims.models.ClaimStatus');

use Claim;

class ReturnService
{

    public $claim;

    public $returnFiles;

    public $documentString;

    public $document;

    public function __construct(Claim $claim)
    {

        $this->claim = $claim;
    }


    public function getUploadedFiles()
    {

        $model = new \ClaimAttachment();

        $criteria = new \CDbCriteria;

        $criteria->addColumnCondition(
            array(
                'transmit_no'  => $this->claim->transmit_no,
                'encounter_nr' => $this->claim->encounter_nr,
                'is_return'    => 0,
                'is_uploaded'  => 1,
            )
        );

        $data = $model->findAll($criteria);

        $xmlData = array();
        $document = array();

        foreach ($data as $key => $datum) {
            array_push($document, $datum->id);

            $xmlData[] = array(
                'pDocumentType' => $datum->attachment_type,
                'pDocumentURL' => $datum->getUrl()
            );
        }
        $this->xmlData = $xmlData;
        $this->document = $document;
    }

    public function addReturnedDocument()
    {

        $configModel = new \HospitalConfigForm;
        $hospitalCode = $configModel->hospital_code;

        if (empty($this->xmlData)){
            throw new \Exception('No File For Add Document', 500);
        }

        $params = array(
            'pHospitalCode' => $hospitalCode,
            'pSeriesLhioNo' => $this->claim->claim_series_lhio,
            'xmlData'       => \CJSON::encode($this->xmlData),
        );
        $service = new \ServiceExecutor(
            array(
                'endpoint' => 'hie/document/addDocument',
                'method'   => 'POST',
                'data'     => $params,
            )
        );

        $res = $service->execute();

        if ($res['data']['result']) {
            $attachment = new \ClaimAttachment();
            $attachment->setUploaded($this->document);
        }
    }
}