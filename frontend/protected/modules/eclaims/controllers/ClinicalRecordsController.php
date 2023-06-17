 <?php


    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.chiefComplaint.chiefComplaintService');
    
    class ClinicalRecordsController extends Controller
    {
        public function actionSaveClinicalRecords()
        {

            $transaction = Yii::app()->db->beginTransaction();
            try {

                $service = new clinicalRecordService($_POST['encounter_nr']);
                $chief_service = new chiefComplaintService($_POST['encounter_nr']);

                $chiefComplaint = $chief_service->saveChiefComplaint($_POST);
                $clinical_records = $service->saveClinicalRecord($_POST);
                $past_med = $service->savePastMedHis($_POST);
                // $vital_signs = $service->saveVitalSigns($_POST);

                // \CVarDumper::dump($_POST, 10, true);die;

                $transaction->commit();
                echo CJSON::encode(array(
                    'status' => true
                ));
            } catch (\Exception $e) {
                $transaction->rollback();
                echo CJSON::encode(array(
                    'message' => $e->getMessage(),
                    'status' => false
                ));
            }
        }
    }
