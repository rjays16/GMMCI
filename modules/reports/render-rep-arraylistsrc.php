<?php
/*
 * SegHIS - Hospital Information System (BPH Deployment)
 * by Segworks Technologies Corporation
 * 
 * (Using jrxml but with arraylist as datasource)
 * 
 * @author  Bong S. Trazo
 * @uses    Jasper Report
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

require_once(java_include);

//interface ReportDataSource {
//    public function toArray();
//}


//class ReportGenerator {
//    
//    protected $_dataSource;
//    
//    public function __construct(&$dataSource) {
//        $this->_dataSource = $dataSource;
//    }
//    
//}

/**
 * see if the java extension was loaded.
 */
//function checkJavaExtension()
//{
//    if(!extension_loaded('java'))
//    {
//        $sapi_type = php_sapi_name();
//        $port = (isset($_SERVER['SERVER_PORT']) && (($_SERVER['SERVER_PORT'])>1024)) ? $_SERVER['SERVER_PORT'] : '8080';
//        if ($sapi_type == "cgi" || $sapi_type == "cgi-fcgi" || $sapi_type == "cli") 
//        {
//            require_once(java_include);
//            return true;
//        } 
//        else
//        {
//            if(!(@require_once(java_include)))
//                {
//                    require_once(java_include);
//                }
//        }
//    }
//    if(!function_exists("java_get_server_name")) 
//    {
//        return "The loaded java extension is not the PHP/Java Bridge";
//    }
//
//    return true;
//}

/** 
 * convert a php value to a java one... 
 * @param string $value 
 * @param string $className 
 * @returns boolean success 
 */  
function convertValue($value, $className) {
    // if we are a string, just use the normal conversion
    // methods from the java extension...
    try
        {
            if ($className == 'java.lang.String')
            {
                $temp = new Java('java.lang.String', $value);
                return $temp;
            }
            else if ($className == 'java.lang.Boolean' ||
                    $className == 'java.lang.Integer' ||
                    $className == 'java.lang.Long' ||
                    $className == 'java.lang.Short' ||
                    $className == 'java.lang.Double' ||
                    $className == 'java.math.BigDecimal')
            {
                $temp = new Java($className, $value);
                return $temp;
            }
            else if ($className == 'java.sql.Timestamp' ||
                    $className == 'java.sql.Time')
            {
                $temp = new Java($className);
                $javaObject = $temp->valueOf($value);
                return $javaObject;
            }
            else if ($className == "java.util.Date")
            {
            //   $temp = new Java('java.text.DateFormat');
                $temp = new Java('java.text.SimpleDateFormat("MM/dd/yyyy")');
                $javaObject = $temp->parse($value);
                return $javaObject;
            }
        }
        catch (Exception $err)
        {
            echo (  'unable to convert value, ' . $value .
                            ' could not be converted to ' . $className);
            return false;
        }

        echo (  'unable to convert value, class name '.$className.
                        ' not recognised');
        return false;
}



    $errorObject = new PermissionHandler();
    $hasSession = $errorObject->hasSession(); //true //false return

if($hasSession['hasSession']){
$report_name = $report;

$compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");

$report = $compileManager->compileReport(realpath(java_resource.$report_name.'.jrxml'));

java_set_file_encoding("ISO-8859-1");

$fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");

$report_path ='reports/'.$report_name.'.php';

include($report_path);

$javaParams = new Java("java.util.HashMap");

foreach ($params as $v) {
    $v[1] = convertValue($v[1], $v[2]);
    $javaParams->put($v[0], $v[1]);
}

#parameters
//$params->put("from_date", $from_date_format);
//$params->put("to_date", $to_date_format);

#$start = microtime(true);
#call the report query

$jCollection = new Java("java.util.ArrayList");
foreach ($data as $i => $row) {
    $jMap = new Java('java.util.HashMap');
    foreach ( $row as $field => $value ) {
        $jMap->put($field, $value);
    }
    $jCollection->add($jMap);
}

$jMapCollectionDataSource = new Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $jCollection);
$jasperPrint = $fillManager->fillReport($report, $javaParams, $jMapCollectionDataSource);

$outputPath  = tempnam(java_tmp, '');
chmod($outputPath, 0777);

if ($repformat == 'pdf'){

    $exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
    $exportManager->exportReportToPdfFile($jasperPrint, $outputPath);
    header("Content-type: application/pdf");
    #header("Content-Disposition: attachment; filename=output.pdf");
}elseif ($repformat=='excel'){
    #$exportManager = new java("net.sf.jasperreports.engine.JRExporter");
    $exportManager = new java("net.sf.jasperreports.engine.export.JRXlsExporter");
    $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->JASPER_PRINT, $jasperPrint);
    $exportManager->setParameter(java("net.sf.jasperreports.engine.JRExporterParameter")->OUTPUT_FILE_NAME, $outputPath);
    $exportManager->exportReport();
    
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=output.xls");
}

readfile($outputPath);

unlink($outputPath);
} else {
    
    echo $hasSession['message'];
    die();
}