<?php

    Yii::import('eclaims.models.cf4.Cf4PertinentSignSymptoms');
    Yii::import('eclaims.models.cf4.Cf4LibChiefComplaint');
    Yii::import('eclaims.models.cf4.Cf4Guie');
    Yii::import('eclaims.models.cf4.Cf4LibGuie');
    Yii::import('eclaims.models.cf4.Cf4Abdomen');
    Yii::import('eclaims.models.cf4.Cf4LibAbdomen');
    Yii::import('eclaims.models.cf4.Cf4VitalSigns');
    Yii::import('eclaims.models.cf4.Cf4Heent');
    Yii::import('eclaims.models.cf4.Cf4LibHeent');
    Yii::import('eclaims.models.cf4.Cf4Chest');
    Yii::import('eclaims.models.cf4.Cf4LibChest');
    Yii::import('eclaims.models.cf4.Cf4Neuro');
    Yii::import('eclaims.models.cf4.Cf4LibNeuro');
    Yii::import('eclaims.models.cf4.Cf4Skin');
    Yii::import('eclaims.models.cf4.Cf4LibSkin');
    Yii::import('eclaims.models.cf4.Cf4Heart');
    Yii::import('eclaims.models.cf4.Cf4LibHeart');
    Yii::import('eclaims.models.cf4.Cf4GeneralSurvey');
    Yii::import('eclaims.models.cf4.Cf4LibGenSurvey');
    Yii::import('eclaims.models.cf4.Cf4PastMedHistory');
    Yii::import('eclaims.models.cf4.Cf4ClinicalRecord');
    Yii::import('eclaims.models.cf4.ObstetricHistory');
    Yii::import('eclaims.models.cf4.MenstrualHistory');
    Yii::import('eclaims.models.EclaimsPhicMember');
    Yii::import('eclaims.models.EclaimsEncounter');
    Yii::import('eclaims.models.ConfigGlobal');

    /**
     * 
     */
    class Cf4PrintOutService
    {
        public $encounter_nr;

        function __construct($encounter_nr)
        {
            $this->encounter_nr = $encounter_nr;
        }

        public function getPHICLogo()
        {
            $top_dir = 'frontend';
            $baseurl = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_ADDR'],
            substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
            );
            #Logo of PHIC
            // $logo_path = $baseurl.'images/phic_logo.png'; #<-- Comment this for LOCAL TESTING!

            return array(
                "logo_path" => $logo_path
            );
        }

        public function getHci()
        {
            $data = array();
            $addr_arr = array(
                'building_name',
                'city',
                'province'
              );

            $hci_name = ConfigGlobal::model()->findAllByAttributes(
                array(
                    "type" => 'hie_service_hospital_name',
                )
            );

            $hci_addr = ConfigGlobal::model()->findAllByAttributes(
                array(
                    "type" => 'main_info_address',
                )
            );

            $address = explode(",", $hci_addr[0]['value']);
            for ($i=0; $i < count($address) ; $i++) { 
                $data[$addr_arr[$i]] = $address[$i];
            }

            $data['hci_name'] = $hci_name[0]['value'];

            return $data;
        }

        public function getAdmissionDischargeDt()
        {
            $data = array();
            $model = EclaimsEncounter::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $admission_dt = $model[$i]->admission_dt;
                $discharge_date = $model[$i]->discharge_date;
                $discharge_time = $model[$i]->discharge_time;

                $data['admission_date'] = date('m-d-Y h:i:a', strtotime($admission_dt));
                $data['date_discharged'] = date('m-d-Y', strtotime($discharge_date))." ".date('h:i:a', strtotime($discharge_time));
            }

            $data['date_signed'] = date('m-d-Y');

            return $data;
        }

        public function getPresentIllness()
        {
            $data = array();
            $model = Cf4ClinicalRecord::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $present = $model[$i]->present_illness;

                $data['present_illness'] = $present;
            }

            return $data;
        }

        public function getPastMedHistory()
        {
            $data = array();
            $model = Cf4PastMedHistory::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $pertinent = $model[$i]->pertinent;

                $data['medical_history'] = $pertinent;
            }

            return $data;
        }

        public function getOBHistory()
        {
            $data = array();
            $model = ObstetricHistory::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $gravida = $model[$i]->gravida;
                $parity = $model[$i]->parity;
                $t = $model[$i]->term_births;
                $p = $model[$i]->preterm_births;
                $a = $model[$i]->abortion;
                $l = $model[$i]->living_children;

                $data['date_gravity'] = $gravida;
                $data['date_parity'] = $parity;
                $data['T'] = $t;
                $data['P'] = $p;
                $data['A'] = $a;
                $data['L'] = $l;
            }

            $lmp_model = MenstrualHistory::model()->findAllByAttributes(
                array(
                    "encounter_nr" => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($lmp_model) ; $i++) { 
                $lmp = $lmp_model[$i]->date_of_lmp;
                $is_applicable = $lmp_model[$i]->is_applicable;

                $data['last_period_menstrual'] = $lmp;
                $data['is_applicable'] = $is_applicable;
            }

            return $data;

        }

        public function getSignSymptoms()
        {
            $data = array();
            $model = Cf4PertinentSignSymptoms::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );


            for ($i=0; $i <count($model) ; $i++) { 
                $id = $model[$i]->sign_symptoms;

                if($model[$i]->pains){
                    $pains = $model[$i]->pains;
                }
                if($model[$i]->others){
                    $others = $model[$i]->others;
                }
            

                $lib_model= Cf4LibChiefComplaint::model()->findAllByAttributes(
                    array(
                        'id' => $id
                    )
                );

                $data['sign_and_symp_'.''.$id] = 1;
                $data['opt_2_values'] = $pains;
                $data['opt_3_values'] = $others;

            }
            
             return $data;
        }

        public function patientInfo()
        {
            $person = EclaimsPhicMember::model()->findByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            return array(
                'patient_name_last' => $person->member_lname,
                'patient_name_first' => $person->member_fname,
                'patient_name_middle' => $person->member_mname,
                'patient_name_suffix' => $person->suffix,
                'sex' => $person->sex
            );
        }

        public function generalSurvey()
        {
            $gs_labels = array(
                'finding_1' => 'Awake and alert'
            );
            
            $model = Cf4GeneralSurvey::model()->findByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            $lib_model = Cf4LibGenSurvey::model()->findByPk(
                array(
                    'id' => $model->gen_survey_id
                )
            );

            foreach ($gs_labels as $key => $value) {
                if ($value == $lib_model->name) {
                    return array();
                }else{
                    return array(
                        'finding_2' => 1,
                        'value_2_Ge' => $model->remarks
                    );
                }
            }

        }

        public function vitalSigns()
        {

            $model = Cf4VitalSigns::model()->findByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            return array(
                'vital_bp' => $model ? $model->systolic / $model->diastolic : null,
                'vital_hr' => $model->cr,
                'vital_rr' => $model->rr,
                'vital_temp' => $model->temperature,
                'height' => $model->height,
                'weight' => $model->weight
            );
            
        }

        public function getHeent()
        {

            $data = array();
            $model = Cf4Heent::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i <count($model) ; $i++) { 
                $id = $model[$i]->heent_id;
                $remarks = $model[$i]->remarks;
                $lib_model= Cf4LibHeent::model()->findAllByAttributes(array('id' => $id));

                $data ['heent_finding_'.''.$id] = 1;
                $data ['value_others_HE'] = $remarks;
            }

             return $data;
        }

        public function getChest()
        {
            $data = array();
            $model = Cf4Chest::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $id = $model[$i]->chest_id;
                $remarks = $model[$i]->remarks;
                $lib_model = Cf4LibChest::model()->findAllByAttributes(array('id' => $id));

                $data['chest_finding_'.''.$id] = 1;
                $data['value_others_Ch'] = $remarks;
            }

            return $data;
        }

        public function getHeart()
        {
            $data = array();
            $model = Cf4Heart::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $id = $model[$i]->heart_id;
                $remarks = $model[$i]->remarks;
                $lib_model = Cf4LibHeart::model()->findAllByAttributes(
                    array('id' => $id
                    )
                );

                $data['cv_finding_'.''.$id] = 1;
                $data['value_others_CV'] = $remarks;
            }

            return $data;
        }

        public function getAbdomen()
        {
            $data = array();
            $model = Cf4Abdomen::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $id = $model[$i]->abdomen_id;
                $remarks = $model[$i]->remarks;
                $lib_model = Cf4LibAbdomen::model()->findAllByAttributes(
                    array(
                        'id' => $id
                    )
                );

                $data['abdomen_finding_'.''.$id] = 1;
                $data['value_others_AB'] = $remarks;
            }

            return $data;
        }

        public function getGuie()
        {
            $data = array();
            $model = Cf4Guie::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $id = $model[$i]->guie_id;
                $remarks = $model[$i]->remarks;
                $lib_model = Cf4LibGuie::model()->findAllByAttributes(
                    array(
                        'id' => $id
                    )
                );

                $data['guie_finding_'.''.$id] = 1;
                $data['value_others_GU'] = $remarks;
            }

            return $data;
        }

        public function getSkin()
        {
            $data = array();
            $model = Cf4Skin::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $id = $model[$i]->skin_id;
                $remarks = $model[$i]->remarks;
                $lib_model = Cf4LibSkin::model()->findAllByAttributes(
                    array(
                        'id' => $id
                    )
                );

                $data['skin_finding_'.''.$id] = 1;
                $data['value_others_SK'] = $remarks;
            }

            return $data;
        } 
        
        public function getNeuro()
        {
            $data = array();
            $model = Cf4Neuro::model()->findAllByAttributes(
                array(
                    'encounter_nr' => $this->encounter_nr
                )
            );

            for ($i=0; $i < count($model) ; $i++) { 
                $id = $model[$i]->neuro_id;
                $remarks = $model[$i]->remarks;
                $lib_models = Cf4LibNeuro::model()->findAllByAttributes(
                    array(
                        'id' => $id
                    )
                );

                $data['ne_finding_'.''.$id] = 1;
                $data['value_others_NE'] = $remarks;
            }

            return $data;
        }
    }