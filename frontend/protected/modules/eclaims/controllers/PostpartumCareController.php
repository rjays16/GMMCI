 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.postpartumCare.FamilyPlanningService');
    Yii::import('eclaims.services.postpartumCare.PerinealWoundCareService');
    Yii::import('eclaims.services.postpartumCare.SignsOfMaternalService');
    Yii::import('eclaims.services.postpartumCare.BreastfeedingNutritionService');
    Yii::import('eclaims.services.postpartumCare.ProvidedFamilyPlanningService');
    Yii::import('eclaims.services.postpartumCare.ReferredPartnerPhysicianService');
    Yii::import('eclaims.services.postpartumCare.ScheduleNextPostpartumService');


    class PostpartumCareController extends Controller
    {
        public function actionSavePostpartumCare()
        {
            $transaction = Yii::app()->db->beginTransaction();
            $familyplanning = array(
                'pid' => $_POST['pid'],
                'encounter_nr' => $_POST['encounter_nr'],
                'is_done' => $_POST['is_done_family_planning'],
                'remarks' => $_POST['remarks_family_planning'],
            );
            $perinealwoundcare = array(
                'pid' => $_POST['pid'],
                'encounter_nr' => $_POST['encounter_nr'],
                'is_done' => $_POST['is_done_perineal_wound_care'],
                'remarks' => $_POST['remarks_perineal_wound_care'],
            );

            $signsofmaternal = array(
                'pid' => $_POST['pid'],
                'encounter_nr' => $_POST['encounter_nr'],
                'is_done' => $_POST['is_done_signs_of_maternal'],
                'remarks' => $_POST['remarks_signs_of_maternal'],
            );

            $breastfeedingnutrition = array(
                'pid' => $_POST['pid'],
                'encounter_nr' => $_POST['encounter_nr'],
                'is_done' => $_POST['is_done_breastfeeding_nutrition'],
                'remarks' => $_POST['remarks_breastfeeding_nutrition'],
            );

            $providedfamilyplanning = array(
                'pid' => $_POST['pid'],
                'encounter_nr' => $_POST['encounter_nr'],
                'is_done' => $_POST['is_done_provided_family_planning'],
                'remarks' => $_POST['remarks_provided_family_planning'],
            );

            $referredpartnerphysician = array(
                'pid' => $_POST['pid'],
                'encounter_nr' => $_POST['encounter_nr'],
                'is_done' => $_POST['is_done_referred_partner_physician'],
                'remarks' => $_POST['remarks_referred_partner_physician'],
            );

            $schedulenextpostpartum = array(
                'pid' => $_POST['pid'],
                'encounter_nr' => $_POST['encounter_nr'],
                'is_done' => $_POST['is_done_schedule_next_postpartum'],
                'remarks' => $_POST['remarks_schedule_next_postpartum'],
            );


            try {

                $this->saveFamilyplanning($familyplanning);
                $this->savePerinealWoundCare($perinealwoundcare);
                $this->saveSignsOfMaternal($signsofmaternal);
                $this->saveBreastfeedingNutrition($breastfeedingnutrition);
                $this->saveProvidedFamilyPlanning($providedfamilyplanning);
                $this->saveReferredPartnerPhysician($referredpartnerphysician);
                $this->saveScheduleNextPostpartum($schedulenextpostpartum);

                $transaction->commit();
                echo CJSON::encode(array(
                    'message' => 'Successfully Saved Postpartum Care',
                    'status' => true,
                ));
            } catch (\Exception $e) {
                $transaction->rollback();

                echo CJSON::encode(array(
                    'message' => $e->getMessage(),
                    'status' => false
                ));
            }
        }

        public function saveFamilyplanning($familyplanning)
        {
            $service = new FamilyPlanningService($familyplanning['encounter_nr']);
            $service->save($familyplanning);
        }
        public function savePerinealWoundCare($perinealwoundcare)
        {
            $service = new PerinealWoundCareService($perinealwoundcare['encounter_nr']);
            $service->save($perinealwoundcare);
        }
        public function saveSignsOfMaternal($signsofmaternal)
        {
            $service = new SignsOfMaternalService($signsofmaternal['encounter_nr']);
            $service->save($signsofmaternal);
        }

        public function saveBreastfeedingNutrition($breastfeedingnutrition)
        {
            $service = new BreastfeedingNutritionService($breastfeedingnutrition['encounter_nr']);
            $service->save($breastfeedingnutrition);
        }

        public function saveProvidedFamilyPlanning($breastfeedingnutrition)
        {
            $service = new ProvidedFamilyPlanningService($breastfeedingnutrition['encounter_nr']);
            $service->save($breastfeedingnutrition);
        }

        public function saveReferredPartnerPhysician($referredpartnerphysician)
        {
            $service = new ReferredPartnerPhysicianService($referredpartnerphysician['encounter_nr']);
            $service->save($referredpartnerphysician);
        }

        public function saveScheduleNextPostpartum($schedulenextpostpartum)
        {
            $service = new ScheduleNextPostpartumService($schedulenextpostpartum['encounter_nr']);
            $service->save($schedulenextpostpartum);
        }
    }
