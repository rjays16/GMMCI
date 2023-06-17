 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.prenatalConsultaion.physicalExaminationService');
    class PhysicalController extends Controller
    {
        public function actionSavePhysicalExamination()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->savePhysicalExamination($_POST);
                $transaction->commit();

                echo CJSON::encode(array(
                    'message' => 'Successfully Saved Physical Examination',
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

        public function savePhysicalExamination($data)
        {
            $service = new physicalExaminationService($data['encounter_nr']);
            $data = $service->save($data);
        }
    }
