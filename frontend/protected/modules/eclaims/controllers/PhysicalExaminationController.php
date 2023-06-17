 <?php


    // Yii::import('eclaims.services.CF4GeneratorService');
    // Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    // Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    // Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    // Yii::import('eclaims.services.prenatalConsultaion.physicalExaminationService');

    Yii::import('eclaims.services.physicalExamination.peHeentService');
    Yii::import('eclaims.services.physicalExamination.peSkinService');
    Yii::import('eclaims.services.physicalExamination.peChestLungsService');
    Yii::import('eclaims.services.physicalExamination.peGenSurveyService');
    Yii::import('eclaims.services.physicalExamination.peCvsService');
    Yii::import('eclaims.services.physicalExamination.peAbdomenService');
    Yii::import('eclaims.services.physicalExamination.peNeuroService');
    Yii::import('eclaims.services.physicalExamination.peRectalService');
    Yii::import('eclaims.services.physicalExamination.peGuieService');


    class PhysicalExaminationController extends Controller
    {
        public function actionSaveGenSurvey()
        {
            // CVarDumper::dump($_POST, 10, true);die;
            $transaction = Yii::app()->db->beginTransaction();
            try {

                $peGenSurveyService = new peGenSurveyService($_POST['encounter_nr']);
                $peHeentService = new peHeentService($_POST['encounter_nr']);
                $peSkinService = new peSkinService($_POST['encounter_nr']);
                $peChestLungsService = new peChestLungsService($_POST['encounter_nr']);
                $peCvsService = new peCvsService($_POST['encounter_nr']);
                $peAbdomenService = new peAbdomenService($_POST['encounter_nr']);
                $peNeuroService = new peNeuroService($_POST['encounter_nr']);
                $peRectalService = new peRectalService($_POST['encounter_nr']);
                $peGuieService = new peGuieService($_POST['encounter_nr']);

                $gen_survey_data = $peGenSurveyService->save($_POST);
                $heent_data = $peHeentService->save($_POST);
                $skin_data = $peSkinService->save($_POST);
                $lungs_data = $peChestLungsService->save($_POST);
                $cvs_data = $peCvsService->save($_POST);
                $abdomen_data = $peAbdomenService->save($_POST);
                $neuro_data = $peNeuroService->save($_POST);
                $rectal_data = $peRectalService->save($_POST);
                $guie_data = $peGuieService->save($_POST);

                $transaction->commit();
                echo CJSON::encode(array(
                    'message' => "Physical Examination Successfully Saved",
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
