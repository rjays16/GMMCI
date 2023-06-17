 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.prenatalConsultaion.deliveryPlanService');
    class DeliveryPlanController extends Controller
    {

        public function actionSaveDeliveryPlan()
        {
            $transaction = Yii::app()->db->beginTransaction();

            try {
                $this->saveDeliveryPlan($_POST);
                $transaction->commit();

                echo CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully Saved Delivery Plan',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();
                echo CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function saveDeliveryPlan($data)
        {
            $service = new deliveryPlanService($data['encounter_nr']);
            $data = $service->save($data);
        }
    }
