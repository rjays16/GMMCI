 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricRiskFactorService');
    class ObstetricRiskFactorController extends Controller
    {
        public function actionSaveObstetricRiskFactor()
        {
            $transaction = Yii::app()->db->beginTransaction();

            try {
                $this->saveObstetricRiskFactor($_POST);
                $transaction->commit();

                echo CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully Saved Obstetric Risk Factor',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();
                echo CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function saveObstetricRiskFactor($data)
        {
            $service = new obstetricRiskFactorService($data['encounter_nr']);
            $data = $service->save($data);
        }
    }
