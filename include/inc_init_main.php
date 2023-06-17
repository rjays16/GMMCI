<?php
$dbname='gmmci'; //.54, .72 .50 .34 // for remote
// $dbname='ipbm';
// $dbname='hisdb_lite';
// $dbname='hisdb_live'; //.34

// Database user name, default is root or httpd for mysql, or postgres for postgresql
// $dbusername='spmcdev'; $dbpassword='d3v@s3gw0rx'; $dbhost='10.1.80.34'; //.34
// $dbusername='segworksdev'; $dbpassword='d3v@spmc'; $dbhost='10.1.80.21'; //.72 test server
$dbusername='root'; $dbpassword=''; $dbhost='localhost'; //.72 test server/
// $dbusername='hisdbuser'; $dbpassword = 's3gw0rxpr0ds3rv3r'; $dbhost='10.1.80.54'; //.54
// $dbusername='seniordev'; $dbpassword='s3n10r1t0d3v'; $dbhost='10.1.80.50'; // .50 live server
// $dbusername='hisdbuser'; $dbpassword='s3gw0rxtests3rv3r'; $dbhost='0.tcp.eu.ngrok.io'; //for remote
// $dbusername='root'; $dbpassword=''; $dbhost='localhost';

# Database user password, default is empty char
#$dbpassword='s3gw0rxtestserver';
// $dbpassword='s3gw0rxtests3rv3r';  // for remote
// $dbpassword = 's3gw0rxpr0ds3rv3r'; // .54
// $dbpassword='s3gw0rx4dm1n';
// $dbpassword='s3n10r1t0d3v'; //.50
// $dbpassword='s3gw0rxd3v'; //.72
 //.34
// $dbpassword='';

# Database host name, default = localhost

// $dbhost='0.tcp.eu.ngrok.io'; // for remote
// $dbhost='10.1.80.72';
// $dbhost='10.1.80.34';
// $dbhost='10.1.80.54';
// $dbhost='10.1.80.50';
// $dbhost='localhost';
/*$dbhost='192.168.15.113';*/

/*$dbhost='10.1.80.32';*/

# Database session table, default = care_sessions
// $dbsessiontb='care_sessions';

# First key used for simple chaining protection of scripts
$key='3.53020914643E+013';

# Second key used for accessing modules
$key_2level='826165905490';

# 3rd key for encrypting cookie information
$key_login='1.13664924241E+013';

# Main host address or domain
$main_domain='127.0.0.1';

# Host address for images
$fotoserver_ip='127.0.0.1';

# Transfer protocol. Use https if this runs on SSL server
$httprotocol='http';

# Set this to your database type. For details refer to ADODB manual or goto http://php.weblogs.com/ADODB/
$dbtype='mysql';
$dbtypeuse='mysqlt';

# Set this to the FTP's user id.
$ftp_userid = 'segworks';

# Set this to the FTP users' password.
$ftp_passwrd = 's3gw0rx';

#added by VAN 03-13-2012
#transfer method to be used in connecting LIS
#either NFS or SOCKET
#$transfer_method = 'SOCKET';

$debug_env = 1;
define('DEBUG', 1);

# new config variable
$config = array(
	'debug' => 1
);


