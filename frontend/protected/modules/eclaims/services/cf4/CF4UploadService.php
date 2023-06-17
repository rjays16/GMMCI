<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/19/2019
 * Time: 6:57 AM
 */

namespace SegHis\modules\eclaims\services\cf4;


use ClaimAttachment;
use SegHis\modules\eclaims\models\EclaimsCf4;
use ServiceExecutor;

class CF4UploadService
{

    public $model;

    public $service;

    const CF4_DOCU = 'CF4';

    public function __construct(EclaimsCf4 $cf4)
    {

        $this->model = $cf4;
    }


    public function getCF4contents()
    {

        $content = $this->model->xml;
        $fp = fopen($this->model->phic_trans_no . ".xml", "wb");
        fwrite($fp, $content);
        fclose($fp);
        $temp = tempnam(sys_get_temp_dir(), 'TMP_');
        file_put_contents($temp, file_get_contents($this->model->phic_trans_no . ".xml"));

        return $temp;
    }


    public function uploadXml()
    {
        $config = new \HospitalConfigForm();

        $service = new ServiceExecutor(
            array(
                'endpoint' => 'hie/document/upload',
                'method' => 'POST',
                'data' => array(
                    'name' => $this->model->phic_trans_no . '.xml',
                    'owner' => $this->model->transmit_no . '/' . $this->model->encounter_nr,
                    'attachment' => '@' . $this->getCF4contents(),
                    'pHospitalCode' => $config->hospital_code
                ),
            )
        );

        $result = $service->execute();

        if ($result['success']) {
            $this->saveClaimAttachment($result['data']);
            $this->model->is_uploaded = 1;

            if (!$this->model->save()) {
                throw new \CException("Failed to Save CF4 ");
            }
        }
    }


    public function saveClaimAttachment($result)
    {
        $model = new ClaimAttachment;

        $model->extractUploadResult($result);
        $model->transmit_no = $this->model->transmit_no;
        $model->encounter_nr = $this->model->encounter_nr;
        $model->attachment_type = self::CF4_DOCU;

        if (!$model->save()) {
            throw new \CException("Failed to Save Attachment");
        }
    }

}