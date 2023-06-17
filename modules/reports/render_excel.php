<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
	require_once('./roots.php');
	require($root_path.'include/inc_environment_global.php');

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
//							$temp = new Java('java.text.DateFormat');
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
    
//    $report = "testrep";
    $dbconn = java_dbaccess;
    
	require_once(java_include);
	$jasperClassPath = java_classpath;
	$reportGenerator = new Java('ReportGenerator',
                                java_resource.$report.'.jrxml',
                                java_tmp,
                                $jasperClassPath,
                                java_cache);
	$tempFile = tempnam(java_tmp, '');
	chmod($tempFile, 0777);

	// Pass the parameters for the report ...
	$javaParams = new Java("java.util.HashMap");

	foreach ($params as $v) {
		$v[1] = convertValue($v[1], $v[2]);
		$javaParams->put($v[0], $v[1]);
	}

	$reportGenerator->runReport($tempFile,
                                $javaParams,
                                $dbconn,
                                2);
    header("Content-type: application/vnd.ms-excel");
    #header("Content-Disposition: attachment; filename=\"$excelfile_for_layout\"");
    header("Content-Disposition: attachment; filename=\"test\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    header("Pragma: public"); 
	readfile($tempFile);
	unlink($tempFile);
?>
