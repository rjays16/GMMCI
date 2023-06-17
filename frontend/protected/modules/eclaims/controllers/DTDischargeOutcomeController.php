 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.deliveryOutcome.DTDischargeOutcomeService');
    class DTDischargeOutcomeController extends Controller
    {
        public function actionSaveDTDischargeOutcome()
        {
            // \CVarDumper::dump($_POST);die;
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->saveDTDischargeOutcome($_POST);

                $transaction->commit();
                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully saved Date and Time Discharge Outcome',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();

                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function saveDTDischargeOutcome($data)
        {
            $service = new DTDischargeOutcomeService($data['encounter_nr']);
            $data = $service->save($data);
        }
    }
