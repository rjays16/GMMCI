<?php

class IReport
{

    const PDF = 'pdf';
    const EXCEL = 'excel';

    /**
     * @var string template name without the '.jrxml' extension
     */
    public $template;
    /**
     * @var string[] $parameters
     */
    public $parameters;
    /**
     * @var string[] $data
     */
    public $data;
    /**
     * @var string excel | pdf
     */
    public $format;

    public $encoding = 'ISO-8859-1';

    private static function checkJavaExtension()
    {
        if (!extension_loaded('java')) {
            $sapi_type = php_sapi_name();

            if ($sapi_type == "cgi" || $sapi_type == "cgi-fcgi" || $sapi_type == "cli") {
                require_once(java_include);
                return true;
            } else {
                if (!(@require_once(java_include))) {
                    require_once(java_include);
                }
            }
        }
        if (!function_exists("java_get_server_name")) {
            return "The loaded java extension is not the PHP/Java Bridge";
        }
        return true;
    }

    public function show()
    {
        try {
            self::checkJavaExtension();

            $compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
            $report = $compileManager->compileReport(realpath(java_resource . $this->template . '.jrxml'));

            java_set_file_encoding($this->encoding);
            $fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
            $params = new Java("java.util.HashMap");

            foreach ($this->parameters as $key => $value) {
                $params->put($key, $value);
            }

            $jCollection = new Java("java.util.ArrayList");
            foreach ($this->data as $i => $row) {
                $jMap = new Java('java.util.HashMap');
                foreach ($row as $field => $value) {
                    $jMap->put($field, $value);
                }
                $jCollection->add($jMap);
            }

            $jMapCollectionDataSource = new Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $jCollection);
            $jasperPrint = $fillManager->fillReport($report, $params, $jMapCollectionDataSource);

            $outputPath = tempnam(java_tmp, '');
            chmod($outputPath, 0777);

            if ($this->format == self::PDF) {
                $exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
                $exportManager->exportReportToPdfFile($jasperPrint, $outputPath);
                header("Content-type: application/pdf;");
            } else if($this->format == self::EXCEL){
                $exportManager = new java("net.sf.jasperreports.engine.export.JRXlsExporter");
                $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->JASPER_PRINT, $jasperPrint);
                $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->OUTPUT_FILE_NAME, $outputPath);
                $exportManager->exportReport();
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=output.xls");
            } else{
                die('Invalid report format');
            }

            readfile($outputPath);
            unlink($outputPath);

        } catch (Exception $e) {
            echo $e;
        }
    }
}//end class