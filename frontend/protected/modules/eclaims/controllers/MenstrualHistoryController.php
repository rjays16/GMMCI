<?php


Yii::import('eclaims.services.CF4GeneratorService');
Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
Yii::import('eclaims.services.prenatalConsultaion.prenatalVisitService');

class MenstrualHistoryController extends Controller
{
    public function actionSaveMenstrualHistory()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {

            $this->saveMenstrualHistory($_POST);
            $transaction->commit();

            echo CJSON::encode(array(
              'message' => 'Successfully Saved Menstrual History',
              'status' => true,
            ));
        } catch (\Exception $e) {

            $transaction->rollback();

            echo CJSON::encode(array(
              'message' => $e->getMessage(),
              'status' => false,
            ));
        }
    }

    public function saveMenstrualHistory($data)
    {

        $service = new menstrualService($data['encounter_nr']);
        $service->save($data);
    }
}
