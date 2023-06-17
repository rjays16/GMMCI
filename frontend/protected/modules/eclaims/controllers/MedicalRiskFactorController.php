 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.prenatalConsultaion.medicalRiskFactorService');
    class MedicalRiskFactorController extends Controller
    {

        public function actionSaveMedicalRiskFactor()
        {
            $transaction = Yii::app()->db->beginTransaction();

            try {
                $this->saveMedicalRiskFactor($_POST);
                $transaction->commit();

                echo CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully Saved Medical Risk Factor',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();
                echo CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function saveMedicalRiskFactor($data)
        {
            $service = new medicalRiskFactorService($data['encounter_nr']);
            $data = $service->save($data);
        }
    }
