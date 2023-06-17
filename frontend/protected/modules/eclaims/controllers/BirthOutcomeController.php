 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.deliveryOutcome.BirthOutcomeService');
    class BirthOutcomeController extends Controller
    {
        public function actionSaveBirthOutcome()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->saveBirthOutcome($_POST);
                $transaction->commit();

                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully saved Birth Outcome!',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();

                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function actionDeleteBirthOutcome()
        {
            $transaction = Yii::app()->db->beginTransaction();

            try {
                $this->deleteBirthOutcome($_POST);
                $transaction->commit();

                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully deleted Birth Outcome',
                ));
            } catch (\Exception $e) {

                $transaction->rollback();

                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function saveBirthOutcome($data)
        {
            $service = new BirthOutcomeService($_POST['encounter_nr']);
            $data = $service->save($_POST);
        }

        public function deleteBirthOutcome($data)
        {
            $service = new BirthOutcomeService($_POST['encounter_nr']);
            $data = $service->delete($_POST);
        }
    }
