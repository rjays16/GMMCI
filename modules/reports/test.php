<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');


interface ReportDataSource {
    public function toArray();
}


class ReportGenerator {
    
    protected $_dataSource;
    
    public function __construct(&$dataSource) {
        $this->_dataSource = $dataSource;
    }
    
}



/**
 * see if the java extension was loaded.
 */
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
//                            $temp = new Java('java.text.DateFormat');
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


$x = checkJavaExtension();
$report = 'vanreport';
$compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
$report = $compileManager->compileReport(realpath(java_resource.$report.'.jrxml'));
java_set_file_encoding("ISO-8859-1");
$fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");

$params = new Java("java.util.HashMap");
$params->put("title", "Test Van Report");
#$params->put("text", "This is a test string");
#$params->put("number", 3.00);
#$params->put("date", convertValue("2007-12-31 0:0:0", "java.sql.Timestamp"));

#$start = microtime(true);

#$data = array();
/*for ($i=0; $i<2; $i++) {
    $data[$i] = array('id' => rand(0, 1000), 
                        'username' => 'Segworks21', 
                        'password' => 'Test21', 
                        'dummy1' => 'dummy121', 
                        'dummy2' => 'dummy221');
}*/

/*global $db;

$sql = "SELECT * FROM care_users LIMIT 10";
$rs = $db->Execute($sql);

$i = 0;
while($row=$rs->FetchRow()){
    $data[$i] = array('id' => $row['personell_nr'], 
                        'username' => $row['login_id'], 
                        'password' => $row['password'], 
                        'dummy1' => $row['name'],
                        'dummy2' => 'dummy');
   $i++;                     
}*/

include('reports/test.php');


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

#$end = microtime(true);
#printf('Time elapsed: %s seconds', $end-$start);
#die();


$outputPath  = tempnam(java_tmp, '');
chmod($outputPath, 0777);

#$repformat = 'excel';
$repformat = 'pdf';

if ($repformat=='pdf'){
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