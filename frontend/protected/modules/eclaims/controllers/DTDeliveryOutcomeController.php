 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.deliveryOutcome.DTDeliveryOutcomeService');
    class DTDeliveryOutcomeController extends Controller
    {
        public function actionSaveDTDeliveryOutcome()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->saveDTDeliveryOutcome($_POST);

                $transaction->commit();
                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully saved Date and Time Delivery Outcome',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();

                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function saveDTDeliveryOutcome($data)
        {
            $service = new DTDeliveryOutcomeService($data['encounter_nr']);
            $data = $service->save($data);
        }
    }
