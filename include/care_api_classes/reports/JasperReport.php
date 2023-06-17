<?php

require('./roots.php');
include_once('SegReport.php');
require_once($root_path.'include/error_handlers/permission_handler.php');


class JasperReport extends SegReport {

    private $dataSource;
    private $jasperClassPath;
    private $jasperCache;
    private $javaInclude;
    private $dbConnection;
    private $javaResource;
    private $javaTmp;
    private $filename;
    private $javaParams;

    function __construct(Array $dataSource = array()) {
        $this->dataSource = isset($dataSource) ? $dataSource : array();
        $this->jasperCache = java_cache;
        $this->jasperClassPath = java_classpath;
        $this->javaInclude = java_include;
        $this->dbConnection = java_dbaccess;
        $this->javaResource = java_resource;
        $this->javaTmp = java_tmp;
        require_once($this->javaInclude);
        $this->javaParams = new Java("java.util.HashMap");
    }

    public function setParams(Array $map){
        //$p['SUBREPORT_DIR']= Yii::app()->jasper->cache;
        if(sizeof($map)>0){
            foreach ($map as $key => $value) {
                $this->javaParams->put($key, $value);
            }
        }
    }

    public function setJrxmlFilePath($filename) {
        $this->filename = $this->javaResource . $filename;
    }

    public function setData(Array $data) {
        $this->dataSource = $data;
    }

    public function run() {


    $errorObject = new PermissionHandler();
    $hasSession = $errorObject->hasSession(); //true //false return
        if($hasSession['hasSession']){
            try {
                $this->setParams($this->getHospitalInfo());
                $jCollection = new Java("java.util.ArrayList");

                foreach ($this->dataSource as $row) {
                    $jMap = new Java('java.util.HashMap');
                    foreach ($row as $field => $value ) {
                        $jMap->put($field, $value);
                    }
                    $jCollection->add($jMap);
                }

                $compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");

                $report = $compileManager->compileReport(realpath($this->filename));
                $fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
                $jMapCollectionDataSource = new Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $jCollection);
                $jasperPrint = $fillManager->fillReport($report, $this->javaParams, $jMapCollectionDataSource);

                $tempFile = tempnam($this->javaTmp, '');
                chmod($tempFile, 0777);
                $exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
                $exportManager->exportReportToPdfFile($jasperPrint, $tempFile);

                header("Content-type: application/pdf");
                readfile($tempFile);
                unlink($tempFile);
                return $tempFile;
            } catch(Exception $e) {
                echo $e;
                die;
                //printR($e, true);
            }

            return false;
        } else {

                echo $hasSession['message'];
                die();
        }
    }
} 