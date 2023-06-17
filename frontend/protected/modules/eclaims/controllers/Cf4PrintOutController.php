 <?php
    Yii::import('eclaims.reports.Report');
    Yii::import('eclaims.services.Cf4PrintOutService');

    class Cf4PrintOutController extends Controller
    {

        public function actionPrintCf4()
        {
            $service = new Cf4PrintOutService($_GET['encounter_nr']);
            // \CVarDumper::dump($service->getAdmissionDischargeDt(), 10, true);die;
            $report_path = "/reports/cf4.jrxml";
            $reportClass = new Report;
            //$data = array();

            $parameter = array(
                'path' => getcwd()
            );

            $page_1 = array_merge(
                $service->getOBHistory(),
                $service->getPHICLogo(),
                $service->getHci(),
                $service->getAdmissionDischargeDt(),
                $service->getPresentIllness(),
                $service->getPastMedHistory()
            );
            $pe = array_merge(
                $service->getSignSymptoms(),
                $service->getNeuro(), 
                $service->getSkin(), 
                $service->getGuie(), 
                $service->getAbdomen(), 
                $service->getHeart(), 
                $service->getChest(), 
                $service->getHeent(), 
                $service->patientInfo(), 
                $service->generalSurvey(), 
                $service->vitalSigns()
            );

            $params = array_merge(
                $page_1,
                $pe,
                $parameter
            );
            $report = \Yii::createComponent(array(
                'class' => $reportClass,
                'template' => getcwd() . $report_path,
                'format' => '',
                //'data' => $data,
                'params' => $params,
            ));
            $report->display();
        }

    }
