<?php
Yii::import('eclaims.services.CF4GeneratorService');
Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
Yii::import('eclaims.services.prenatalConsultaion.physicalExaminationService');
Yii::import('eclaims.services.physicalExamination.peHeentService');
Yii::import('eclaims.services.physicalExamination.peSkinService');
Yii::import('eclaims.services.physicalExamination.peChestLungsService');
Yii::import('eclaims.services.physicalExamination.peGenSurveyService');
Yii::import('eclaims.services.physicalExamination.peCvsService');
Yii::import('eclaims.services.physicalExamination.peAbdomenService');
Yii::import('eclaims.services.physicalExamination.peNeuroService');
Yii::import('eclaims.services.physicalExamination.peRectalService');
Yii::import('eclaims.services.physicalExamination.peGuieService');
Yii::import('eclaims.services.signsAndSymptoms.signsAndSymptomsService');
Yii::import('eclaims.services.prenatalConsultaion.obstetricRiskFactorService');
Yii::import('eclaims.services.prenatalConsultaion.medicalRiskFactorService');
Yii::import('eclaims.services.prenatalConsultaion.deliveryPlanService');
Yii::import('eclaims.services.prenatalConsultaion.prenatalVisitService');
Yii::import('eclaims.services.deliveryOutcome.DTDeliveryOutcomeService');
Yii::import('eclaims.services.deliveryOutcome.DTDischargeOutcomeService');
Yii::import('eclaims.services.deliveryOutcome.PregnancyUterineService');
Yii::import('eclaims.services.deliveryOutcome.BirthOutcomeService');
Yii::import('eclaims.services.deliveryOutcome.SPFDeliveryOutcomeService');
Yii::import('eclaims.services.CourseWard.CourseWardService');
Yii::import('eclaims.services.medicine.MedicineService');
Yii::import('eclaims.models.cf4.YNList');
Yii::import('eclaims.models.cf4.PrenatalConsultationList');
Yii::import('eclaims.models.cf4.MaternalOutcome');
Yii::import('eclaims.models.cf4.CF4Gender');
Yii::import('eclaims.models.cf4.Cf4LibClinicalHistory');
Yii::import('eclaims.models.cf4.SPFDeliveryOutcome');
Yii::import('eclaims.services.postpartumCare.PerinealWoundCareService');
Yii::import('eclaims.services.postpartumCare.SignsOfMaternalService');
Yii::import('eclaims.services.postpartumCare.ScheduleNextPostpartumService');
Yii::import('eclaims.services.postpartumCare.ReferredPartnerPhysicianService');
Yii::import('eclaims.services.postpartumCare.ProvidedFamilyPlanningService');
Yii::import('eclaims.services.postpartumCare.BreastfeedingNutritionService');
Yii::import('eclaims.services.postpartumCare.FamilyPlanningService');
Yii::import('eclaims.services.chiefComplaint.chiefComplaintService');



class GeneratorController extends Controller
{
    public function actionIndex()
    {

        $encounter_nr = $_GET['encounter_nr'];
        $service = new CF4GeneratorService();

        $person_data = $service->getDetails($encounter_nr);
        $encounter_data = $service->getEncounterDetails($encounter_nr);
        $patient_info = $this->getPatientRecord($encounter_nr);
        $menstrualHistory = $this->getMenstrualHistory($encounter_nr);
        $obstetricHistory = $this->getObstetricHistory($encounter_nr);
        $physicalExamination = $this->getPhysicalExamination($encounter_nr);
        $ynlist = $this->getYNList();
        $prenatalConsultaionNoList = $this->getPrenatalConsultationNoList();
        $maternalOutcome = $this->getMaternalOutcomeList();
        $genderList = $this->getGenderList();
        $clinicalLibMedical = $this->getClinicalLibMedical($encounter_nr);
        $clinicalLibObstetric = $this->getClinicalLibObstetric($encounter_nr);
        $obstetricRiskFactor = $this->getObstetricRiskFactor($encounter_nr);
        $medicalRiskFactor = $this->getMedicalRiskFactor($encounter_nr);
        $deliveryPlan = $this->getDeliveryPlan($encounter_nr);
        $prenatalVisit = $this->getPrenatalVisit($encounter_nr);
        $birthoutcome = $this->getBirthOutcome($encounter_nr);
        $birthoutcomes = $this->getBirthOutcomes($encounter_nr);
        $prenatalVisits = $this->getPrenatalVisits($encounter_nr);
        $dtDeliveryOutcome = $this->getDTDeliveryOutcome($encounter_nr);
        $dtDischargeOutcome = $this->getDTDischargeOutcome($encounter_nr);
        $pregnancyUterine = $this->getPregnancyUterine($encounter_nr);
        $spfdeliveryoutcome = $this->getSPFDeliveryOutcome($encounter_nr);


        // POSTPARTUM CARE
        $perinealwoundcare = $this->getPerinealWoundCare($encounter_nr);
        $signsofmaternal = $this->getSignsOfMaternal($encounter_nr);
        $breastfeedingnutrition = $this->getBreastFeedingNutrition($encounter_nr);
        $familyplanning = $this->getFamilyPlanning($encounter_nr);
        $providedfamilyplanning = $this->getProvidedFamilyPlanning($encounter_nr);
        $referredpartnerphysician = $this->getReferredPartnerPhysician($encounter_nr);
        $schedulenextpostpartum = $this->getScheduleNextPostpartum($encounter_nr);

        $courseWard = $this->getCourseWard($encounter_nr);
        $medicine = $this->getMedicine($encounter_nr);
        $medicine_library = $this->getMedicineLibrary($encounter_nr);

        // Physical Examinations ..
        $heent = $this->getHeent($encounter_nr);
        $selected_heent = $this->getSelectedHeent($encounter_nr);
        $skin = $this->getSkin($encounter_nr);
        $selected_skin = $this->getSelectedSkin($encounter_nr);
        $chest = $this->getChestLungs($encounter_nr);
        $selected_chest = $this->getSelectedChestLungs($encounter_nr);
        $gen_survey = $this->getGenSurvey($encounter_nr);
        $selected_gensurvey = $this->getSelectedGenSurvey($encounter_nr);
        $peCvs = $this->getCvs($encounter_nr);
        $selected_cvs = $this->getSelectedCvs($encounter_nr);
        $peAbdomen = $this->getAbdomen($encounter_nr);
        $selected_abdomen = $this->getSelectedAbdomen($encounter_nr);
        $peNeuro = $this->getNeuro($encounter_nr);
        $selected_neuro = $this->getSelectedNeuro($encounter_nr);
        $peRectal = $this->getRectal($encounter_nr);
        $selected_rectal = $this->getSelectedRectal($encounter_nr);
        $peGuie = $this->getGuie($encounter_nr);
        $selected_guie = $this->getSelectedGuie($encounter_nr);

        // Pertinent Signs and Symptoms
        $getSelectedSignsAndSymptoms = $this->getSelectedSignsAndSymptoms($encounter_nr);
        $getSignsAndSymptomsOne = $this->getSignsAndSymptomsOne($encounter_nr);
        $getSignsAndSymptomsTwo = $this->getSignsAndSymptomsTwo($encounter_nr);
        $getSignsAndSymptomsThree = $this->getSignsAndSymptomsThree($encounter_nr);
        $getSignsAndSymptomsFour = $this->getSignsAndSymptomsFour($encounter_nr);

        // Chief Complaint
        $getChiefComplaint = $this->getChiefComplaint($encounter_nr);

        $this->render(
            'cf4Form',
            array(
                'person' => $person_data,
                'encounter' => $encounter_data,
                'patient_info' => $patient_info,
                'menstrualHistory' => $menstrualHistory,
                'obstetricHistory' => $obstetricHistory,
                'physicalExamination' => $physicalExamination,
                'ynlist' => $ynlist,
                'prenatalConsultaionNoList' => $prenatalConsultaionNoList,
                'genderList' => $genderList,
                'clinicalLibMedical' => $clinicalLibMedical,
                'clinicalLibObstetric' => $clinicalLibObstetric,
                'obstetricRiskFactor' => $obstetricRiskFactor,
                'medicalRiskFactor' => $medicalRiskFactor,
                'peHeent' => $heent,
                'selected_heent' => $selected_heent,
                'peSkin' => $skin,
                'selected_skin' => $selected_skin,
                'peChest' => $chest,
                'selected_chest' => $selected_chest,
                'peGen_survey' => $gen_survey,
                'selected_gensurvey' => $selected_gensurvey,
                'peCvs' => $peCvs,
                'selected_cvs' => $selected_cvs,
                'peAbdomen' => $peAbdomen,
                'selected_abdomen' => $selected_abdomen,
                'peNeuro' => $peNeuro,
                'selected_neuro' => $selected_neuro,
                'peRectal' => $peRectal,
                'selected_rectal' => $selected_rectal,
                'peGuie' => $peGuie,
                'selected_guie' => $selected_guie,
                'getSelectedSignsAndSymptoms' => $getSelectedSignsAndSymptoms,
                'getSignsAndSymptomsOne' => $getSignsAndSymptomsOne,
                'getSignsAndSymptomsTwo' => $getSignsAndSymptomsTwo,
                'getSignsAndSymptomsThree' => $getSignsAndSymptomsThree,
                'getSignsAndSymptomsFour' => $getSignsAndSymptomsFour,
                'courseWard' => $courseWard,
                'medicine' => $medicine,
                'medicine_library' => $medicine_library,
                'deliveryPlan' => $deliveryPlan,
                'prenatalVisit' => $prenatalVisit,
                'birthoutcome' => $birthoutcome,
                'dtDeliveryOutcome' => $dtDeliveryOutcome,
                'dtDischargeOutcome' => $dtDischargeOutcome,
                'maternalOutcome' => $maternalOutcome,
                'pregnancyUterine' => $pregnancyUterine,
                'spfdeliveryoutcome' => $spfdeliveryoutcome,
                'perinealwoundcare' => $perinealwoundcare,
                'signsofmaternal' => $signsofmaternal,
                'breastfeedingnutrition' => $breastfeedingnutrition,
                'familyplanning' => $familyplanning,
                'providedfamilyplanning' => $providedfamilyplanning,
                'referredpartnerphysician' => $referredpartnerphysician,
                'schedulenextpostpartum' => $schedulenextpostpartum,
                'getChiefComplaint' => $getChiefComplaint,
                'prenatalVisits'      => new \CArrayDataProvider($prenatalVisits, array(
                    'pagination' => array(
                        'pageSize' => 12,
                    ),
                )),
                'birthoutcomes'      => new \CArrayDataProvider($birthoutcomes, array(
                    'pagination' => array(
                        'pageSize' => 5,
                    ),
                )),
            )
        );
    }

    public function getChiefComplaint($encounter_nr)
    {
        $service = new chiefComplaintService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getYNList()
    {
        $model = YNList::model()->findAll();
        return $model;
    }
    public function getPrenatalConsultationNoList()
    {
        $model = PrenatalConsultationList::model()->findAll(array(
            'order' => 'id asc'
        ));
        return $model;
    }

    public function getMaternalOutcomeList()
    {
        $model = MaternalOutcome::model()->findAll();
        return $model;
    }

    public function getGenderList()
    {
        $model = CF4Gender::model()->findAll();
        return $model;
    }

    public function getMenstrualHistory($encounter_nr)
    {
        $service = new menstrualService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getObstetricHistory($encounter_nr)
    {
        $service = new obstetricService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getPhysicalExamination($encounter_nr)
    {
        $service = new physicalExaminationService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function actionSaveObstetric()
    {
        $service = new obstetricService($_POST['encounter_nr']);
        $data = $service->save($_POST);
        if ($data) {
            echo \CJSON::encode(true);
        } else {
            echo \CJSON::encode(false);
        }
    }

    public function getPatientRecord($encounter_nr)
    {
        $service = new clinicalRecordService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getClinicalLibMedical($encounter_nr)
    {
        $service = new medicalRiskFactorService($encounter_nr);
        $data = $service->getMedicalData();
        return $data;
    }

    public function getClinicalLibObstetric($encounter_nr)
    {
        $service = new obstetricRiskFactorService($encounter_nr);
        $data = $service->getObstetricData();
        return $data;
    }

    public function getObstetricRiskFactor($encounter_nr)
    {
        $service = new obstetricRiskFactorService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getMedicalRiskFactor($encounter_nr)
    {
        $service = new medicalRiskFactorService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getHeent($encounter_nr)
    {
        $service = new peHeentService($encounter_nr);
        $data = $service->getHeentLibrary();
        return $data;
    }

    public function getSelectedHeent($encounter_nr)
    {
        $service = new peHeentService($encounter_nr);
        $data = $service->getSelectedHeent();
        return $data;
    }

    public function getSkin($encounter_nr)
    {
        $service = new peSkinService($encounter_nr);
        $data = $service->getSkinLibrary();
        return $data;
    }

    public function getSelectedSkin($encounter_nr)
    {
        $service = new peSkinService($encounter_nr);
        $data = $service->getSelectedSkin();
        return $data;
    }

    public function getChestLungs($encounter_nr)
    {
        $service = new peChestLungsService($encounter_nr);
        $data = $service->getChestLungsLibrary();
        return $data;
    }

    public function getSelectedChestLungs($encounter_nr)
    {
        $service = new peChestLungsService($encounter_nr);
        $data = $service->getSelectedChestLungs();
        return $data;
    }

    public function getGenSurvey($encounter_nr)
    {
        $service = new peGenSurveyService($encounter_nr);
        $data = $service->getGenSurveyLibrary();
        return $data;
    }

    public function getSelectedGenSurvey($encounter_nr)
    {
        $service = new peGenSurveyService($encounter_nr);
        $data = $service->getSelectedGenSurvey();
        return $data;
    }

    public function getCvs($encounter_nr)
    {
        $service = new peCvsService($encounter_nr);
        $data = $service->getCvsLibrary();
        return $data;
    }

    public function getSelectedCvs($encounter_nr)
    {
        $service = new peCvsService($encounter_nr);
        $data = $service->getSelectedCvs();
        return $data;
    }

    public function getAbdomen($encounter_nr)
    {
        $service = new peAbdomenService($encounter_nr);
        $data = $service->getAbdomenLibrary();
        return $data;
    }

    public function getSelectedAbdomen($encounter_nr)
    {
        $service = new peAbdomenService($encounter_nr);
        $data = $service->getSelectedAbdomen();
        return $data;
    }

    public function getNeuro($encounter_nr)
    {
        $service = new peNeuroService($encounter_nr);
        $data = $service->getNeuroLibrary();
        return $data;
    }

    public function getSelectedNeuro($encounter_nr)
    {
        $service = new peNeuroService($encounter_nr);
        $data = $service->getSelectedNeuro();
        return $data;
    }

    public function getRectal($encounter_nr)
    {
        $service = new peRectalService($encounter_nr);
        $data = $service->getRectalLibrary();
        return $data;
    }

    public function getSelectedRectal($encounter_nr)
    {
        $service = new peRectalService($encounter_nr);
        $data = $service->getSelectedRectal();
        return $data;
    }

    public function getGuie($encounter_nr)
    {
        $service = new peGuieService($encounter_nr);
        $data = $service->getGuieLibrary();
        return $data;
    }

    public function getSelectedGuie($encounter_nr)
    {
        $service = new peGuieService($encounter_nr);
        $data = $service->getSelectedGuie();
        return $data;
    }

    // Pertinent Signs and Symptoms
    public function getSelectedSignsAndSymptoms($encounter_nr)
    {
        $service = new signsAndSymptomsService($encounter_nr);
        $data = $service->getSelectedSignsAndSymptoms();
        return $data;
    }

    public function getSignsAndSymptomsOne($encounter_nr)
    {
        $service = new signsAndSymptomsService($encounter_nr);
        $data = $service->getSignsAndSymptomsLibraryOne();
        return $data;
    }

    public function getSignsAndSymptomsTwo($encounter_nr)
    {
        $service = new signsAndSymptomsService($encounter_nr);
        $data = $service->getSignsAndSymptomsLibraryTwo();
        return $data;
    }

    public function getSignsAndSymptomsThree($encounter_nr)
    {
        $service = new signsAndSymptomsService($encounter_nr);
        $data = $service->getSignsAndSymptomsLibraryThree();
        return $data;
    }

    public function getSignsAndSymptomsFour($encounter_nr)
    {
        $service = new signsAndSymptomsService($encounter_nr);
        $data = $service->getSignsAndSymptomsLibraryFour();
        return $data;
    }

    /**
     * @param $encounter_nr
     * @return CActiveRecord|Cf4CourseInTheWard
     */
    public function getCourseWard($encounter_nr)
    {
        $service = new CourseWardService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getMedicine($encounter_nr)
    {
        $service = new MedicineService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getMedicineLibrary($encounter_nr)
    {
        $service = new MedicineService($encounter_nr);
        $data = $service->getLibrary();
        return $data;
    }

    public function getDeliveryPlan($encounter_nr)
    {
        $service = new deliveryPlanService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getPrenatalVisit($encounter_nr)
    {
        $service = new prenatalVisitService($encounter_nr);
        $data = $service->initData();
        return $data;
    }

    public function getPrenatalVisits($encounter_nr)
    {
        $service = new prenatalVisitService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getDTDeliveryOutcome($encounter_nr)
    {
        $service = new DTDeliveryOutcomeService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getDTDischargeOutcome($encounter_nr)
    {
        $service = new DTDischargeOutcomeService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getPregnancyUterine($encounter_nr)
    {
        $service = new PregnancyUterineService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getBirthOutcome($encounter_nr)
    {
        $service = new BirthOutcomeService($encounter_nr);
        $data = $service->initData();
        return $data;
    }

    public function getBirthOutcomes($encounter_nr)
    {
        $service = new BirthOutcomeService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getSPFDeliveryOutcome($encounter_nr)
    {
        $service = new SPFDeliveryOutcomeService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getPerinealWoundCare($encounter_nr)
    {
        $service = new PerinealWoundCareService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getSignsOfMaternal($encounter_nr)
    {
        $service = new SignsOfMaternalService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getBreastFeedingNutrition($encounter_nr)
    {
        $service = new BreastfeedingNutritionService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getFamilyPlanning($encounter_nr)
    {
        $service = new FamilyPlanningService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getProvidedFamilyPlanning($encounter_nr)
    {
        $service = new ProvidedFamilyPlanningService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getReferredPartnerPhysician($encounter_nr)
    {
        $service = new ReferredPartnerPhysicianService($encounter_nr);
        $data = $service->getData();
        return $data;
    }

    public function getScheduleNextPostpartum($encounter_nr)
    {
        $service = new ScheduleNextPostpartumService($encounter_nr);
        $data = $service->getData();
        return $data;
    }
}
