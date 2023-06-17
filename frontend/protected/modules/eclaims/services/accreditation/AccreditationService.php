<?php

Yii::import('eclaims.models.EclaimsDoctorAccreditation');
// Yii::import('eclaims.models.InsuranceProvider');
// Yii::import('eclaims.models.DoctorAccreditation');

/**/


class AccreditationService
{

    public $personnel;

    public $pan;

    public function __construct(Personnel $personnel, $pan)
    {

        $this->personnel = $personnel;

        $this->pan = $pan;
    }

    public function saveAccreditation($data)
    {

        $start = strtotime(str_replace('-', '/', $data["eACCREDITATION"]["@attributes"]["pAccreditationStart"]));
        $end = strtotime(str_replace('-', '/', $data["eACCREDITATION"]["@attributes"]["pAccreditationEnd"]));


        $accreditation = EclaimsDoctorAccreditation::getDoctorPhicAccreditation($this->personnel->nr, $this->pan);

        $phic = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);


        if (empty($accreditation)) {

            $model = new DoctorAccreditation;

            $model->dr_nr = $this->personnel->nr;

            $model->accreditation_nr = $this->pan;

            $model->hcare_id = $phic->hcare_id;

            $model->create_id = $_SESSION['sess_user_name'];

            $model->create_dt = date("Y-m-d H:i:s");

            $model->modify_id = $_SESSION['sess_user_name'];

            $model->modify_dt = date("Y-m-d H:i:s");

            if (!$model->save()) {
                return new CException('DoctorAccreditation was not saved');
            }
        } else {


            if ($start) {
                $accreditation->accreditation_start = date('Ymd', $start);
            } else {
                $this->accreditation_start = null;
            }

            if ($end) {
                $accreditation->accreditation_end = date('Ymd', $end);
            } else {
                $this->accreditation_end = null;
            }


            if (!$accreditation->save()) {
                return new CException('DoctorAccreditation was not saved');
            }
        }


    }


}