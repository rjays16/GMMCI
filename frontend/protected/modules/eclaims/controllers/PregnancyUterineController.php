 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.deliveryOutcome.PregnancyUterineService');
    class PregnancyUterineController extends Controller
    {
        public function actionSavePregnancyUterine()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->savePregnancyUterine($_POST);
                $transaction->commit();

                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully saved Pregnancy Uterine',
                ));
            } catch (\Exception $e) {

                $transaction->rollback();

                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function savePregnancyUterine()
        {
            $service = new PregnancyUterineService($_POST['encounter_nr']);
            $data = $service->save($_POST);
        }
    }
