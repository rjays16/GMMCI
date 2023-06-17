<?php
/*
created by Nick 1/30/2014
1. include this file
2. call showReport(....)
*/

require_once('roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/error_handlers/permission_handler.php');
require_once($root_path.'include/inc_report.php');

interface ReportDataSource {
    public function toArray();
}

class ReportGenerator {
    protected $_dataSource;
    public function __construct(&$dataSource) {
    $this->_dataSource = $dataSource;
    }
}

function checkJavaExtension()
{
    if(!extension_loaded('java'))
    {
        $sapi_type = php_sapi_name();
        $port = (isset($_SERVER['SERVER_PORT']) && (($_SERVER['SERVER_PORT'])>1024)) ? $_SERVER['SERVER_PORT'] : '8080';
        if ($sapi_type == "cgi" || $sapi_type == "cgi-fcgi" || $sapi_type == "cli")
        {
            require_once(java_include);
            return true;
        }
        else
        {
            if(!(@require_once(java_include)))
            {
                require_once(java_include);
            }
        }
    }
    if(!function_exists("java_get_server_name"))
    {
        return "The loaded java extension is not the PHP/Java Bridge";
    }
    return true;
}

function seg_ucwords($str) {
    $words = preg_split("/([\s,.-]+)/", mb_strtolower($str), -1, PREG_SPLIT_DELIM_CAPTURE);
    $words = @array_map('ucwords',$words);
    return implode($words);
}

function convertValue($value, $className){
    try{
        if ($className == 'java.lang.String'){
            $temp = new Java('java.lang.String', $value);
            return $temp;
        }else if ($className == 'java.lang.Boolean' ||
                    $className == 'java.lang.Integer' ||
                    $className == 'java.lang.Long' ||
                    $className == 'java.lang.Short' ||
                    $className == 'java.lang.Double' ||
                    $className == 'java.math.BigDecimal')
        {
            $temp = new Java($className, $value);
            return $temp;
        }else if ($className == 'java.sql.Timestamp' ||
            $className == 'java.sql.Time')
        {
            $temp = new Java($className);
            $javaObject = $temp->valueOf($value);
            return $javaObject;
        }else if ($className == "java.util.Date"){
            #$temp = new Java('java.text.DateFormat');
            $temp = new Java('java.text.SimpleDateFormat("MM/dd/yyyy")');
            $javaObject = $temp->parse($value);
            return $javaObject;
        }
    }catch (Exception $err){
        echo (  'unable to convert value, ' . $value .
                ' could not be converted to ' . $className);
        return false;
    }
    echo (  'unable to convert value, class name '.$className.' not recognised');
    return false;
}

/**
 * SHOW REPORT USING JASPER
 * @param  [STRING] $template_name [ex. 'monthly_reports']
 * @param  [ARRAY]  $parameters    [ex. $params = array("param_name"=>$value)]
 * @param  [ARRAY]  $tableData     [ex. $data[0] = array("field_name"=>$value)]
 * @param  [STRING] $repFormat     [ex. 'pdf']
 */
function showReport($template_name,$parameters,$tableData=array(),$repFormat = 'pdf'){
    
    $errorObject = new PermissionHandler();
    $hasSession = $errorObject->hasSession(); //true //false return

    if($hasSession['hasSession']){
        $x = checkJavaExtension();
        $report = $template_name;

        global $db;
        $newObj = new Report;
        $path = $newObj->select_global($db, $report);
        
        $compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
        $report = $compileManager->compileReport(realpath($path.$report.'.jrxml'));
        java_set_file_encoding("ISO-8859-1");
        $fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
        $params = new Java("java.util.HashMap");
        $start = microtime(true);

        #------------- DATA -------------------------------------------------------------------------------------
        #------------- DATA -------------------------------------------------------------------------------------

            foreach ($parameters as $key => $value) {
                $params->put($key,$value);
            }

            $data = $tableData;

            #------------- DATA -------------------------------------------------------------------------------------
            #------------- DATA -------------------------------------------------------------------------------------
            $jCollection = new Java("java.util.ArrayList");
            foreach ($data as $i => $row) {
                $jMap = new Java('java.util.HashMap');
                foreach ( $row as $field => $value ) {
                    $jMap->put($field, $value);
                }
                $jCollection->add($jMap);
            }

            $jMapCollectionDataSource = new Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $jCollection);
            $jasperPrint = $fillManager->fillReport($report, $params, $jMapCollectionDataSource);
            $end = microtime(true);
            $outputPath  = tempnam(java_tmp, '');
            chmod($outputPath, 0777);

            if(strtoupper($repFormat) == 'PDF'){
                $exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
                $exportManager->exportReportToPdfFile($jasperPrint, $outputPath);
                header("Content-type: application/pdf;");
            }else{
                $exportManager = new java("net.sf.jasperreports.engine.export.JRXlsExporter");
                $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->JASPER_PRINT, $jasperPrint);
                $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->OUTPUT_FILE_NAME, $outputPath);
                $exportManager->exportReport();
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=output.xls");
            }

            readfile($outputPath);
            unlink($outputPath);
    } else{
        echo $hasSession['message'];
        die();
    }
}

?>
