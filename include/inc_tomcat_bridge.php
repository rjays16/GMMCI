<?php
# Added by Jeff 04-09-18

#production and test server configuration
define('java_dbaccess', "jdbc:mysql://$dbhost:3306/$dbname?user=$dbusername&password=$dbpassword");
define('java_include', 'http://localhost:8080/JavaBridge/java/Java.inc');


define('java_classpath', '/usr/local/tomcat/webapps/JavaBridge/WEB-INF/lib/');
define('java_resource', '/usr/local/tomcat/webapps/JavaBridge/resource/');
define('java_tmp', '/usr/local/tomcat/webapps/JavaBridge/tmp');
define('java_cache', '/usr/local/tomcat/webapps/JavaBridge/cache/');

#for local xampp configuration
/*define('java_dbaccess', "jdbc:mysql://$dbhost:3306/$dbname?user=$dbusername&password=$dbpassword");
define('java_include', 'http://localhost:8080/JavaBridge/java/Java.inc');
define('java_classpath', 'C:/xampp/tomcat/webapps/JavaBridge/WEB-INF/lib/');
#define('java_resource', 'C:/xampp/tomcat/webapps/JavaBridge/resource/');
#define('java_resource', 'C:/xampp/htdocs/spmc/hisspmc4dev/reports/');
define('java_tmp', 'C:/xampp/tomcat/webapps/JavaBridge/tmp');
define('java_cache', 'C:/xampp/tomcat/webapps/JavaBridge/cache/');*/

#change jrxml source

#get relative name of script case sensitive from script_filename provided by filesystem by getting position of script_name in url and substracting the filesystem path
$scriptRelativeName = substr($_SERVER['SCRIPT_FILENAME'], strripos($_SERVER['SCRIPT_FILENAME'], $_SERVER['SCRIPT_NAME']));
//subtract the scriptrelativename from script_filename to get the root folder
$rootFolder = str_ireplace($scriptRelativeName, "", $_SERVER['SCRIPT_FILENAME']); 

$location = dirname(dirname(__FILE__));
$location = str_ireplace("\\", "/", $location);

define('java_resource_new', $location.'/reports/');
// var_dump(11);die;
