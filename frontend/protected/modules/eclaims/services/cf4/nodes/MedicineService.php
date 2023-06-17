<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/16/2019
 * Time: 6:29 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;

use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\services\cf4\CF4DataService;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class MedicineService extends XmlWriter
{

    public $document;

    public $encounter;

    public function __construct(
        \DOMDocument $document,
        \EclaimsEncounter $encounter
    ) {
        $this->document = $document;
        $this->encounter = $encounter;
    }

    public function generateHeader()
    {
        $header = $this->_createNode(
            $this->document,
            'MEDICINES',
            array()
        );

        return $header;
    }

    public function generateNode()
    {
        $header = $this->generateHeader();
        $service = new CF4DataService($this->encounter);
        $orders = $service->getMedicines($this->encounter->encounter_nr);

        $pApplicable = 'Y';

        if (empty($orders)) {
            $orders[] = array();
            $pApplicable = 'N';
        }

        foreach ($orders as $order) {
            $frequency = $order['frequency'];
            $route = $order['route'];
            $drug = $this->getPhilMedData($order['drug_code']);
            $this->appendNode(
                $header,
                $meds,
                'MEDICINE',
                array(
                    'pModule' => 'CF4',
                    'pHciCaseNo' => $this->encounter->encounter_nr,
                    'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
                    'pDrugCode' => !empty($order['drug_code']) ? $order['drug_code'] : (!empty($order['generic']) && empty($order['drug_code']) ? "" : CF4Helper::getNomedsDrugCode()),
                    'pGenericCode' => !empty($drug['gen_code']) ? $drug['gen_code'] : (!empty($order['generic']) && empty($order['gen_code']) ? "" : CF4Helper::getNomedsGenericCode()),
                    'pGenericName' => !empty($order['generic']) ? $order['generic'] : CF4Helper::getNomedsGeneric(),
                    'pStrengthCode' => !empty($drug['strength_code']) ? $drug['strength_code'] : "00000",
                    'pFormCode' => !empty($drug['form_code']) ? $drug['form_code'] : "00000",
                    'pPackageCode' => !empty($drug['package_code']) ? $drug['package_code'] : "00000",
                    'pQuantity' => !empty($order['quantity']) ? $order['quantity'] : 0,
                    'pUnitCode' => !empty($drug['unit_code']) ? $drug['unit_code'] : "00000",
                    'pRoute' => !empty($route) ? $route : "-",
                    'pSaltCode' => !empty($drug['salt_code']) ? $drug['salt_code'] : "00000",
                    'pActualUnitPrice' => null,
                    'pCoPayment' => null,
                    'pTotalAmtPrice' => $order['cost'] === 0 || empty($order['cost']) ? "0.00" : $order['cost'],
                    'pInstructionQuantity' => null,
                    'pInstructionStrength' => null,
                    'pInstructionFrequency' => !empty($frequency) ? $frequency : "-",
                    'pPrescPhysician' => null,
                    'pIsApplicable' => $pApplicable,
                    'pDateAdded' => !empty($order) ? date('Y-m-d', strtotime($order['created_at'])) : date('Y-m-d', strtotime($this->encounter->encounter_date)),
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null,
                )
            );
        }

        return $header;
    }

    protected function getPhilMedData($drugCode)
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_phil_medicine t');
        $command->where('t.drug_code = :drugCode');
        $command->params[':drugCode'] = $drugCode;

        return $command->queryRow();
    }
}
