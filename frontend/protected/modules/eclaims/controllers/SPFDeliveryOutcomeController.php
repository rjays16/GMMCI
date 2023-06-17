 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.deliveryOutcome.SPFDeliveryOutcomeService');
    class SPFDeliveryOutcomeController extends Controller
    {
        public function actionSaveSPFDeliveryOutcome()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->saveSPFDeliveryOutcome($_POST);
                $transaction->commit();

                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully saved scheduled postpartum followup',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();

                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function saveSPFDeliveryOutcome($data)
        {
            $service = new SPFDeliveryOutcomeService($_POST['encounter_nr']);
            $data = $service->save($_POST);
        }
    }
