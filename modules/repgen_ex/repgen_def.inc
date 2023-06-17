<?php
/* Include Datei repgen_def.inc for PHP Report Generator
   Bauer, 09.11.2002
   Version 0.44
*/
///////////////////////////////////////////////////////////////
define("DBDRIVER","db_mysql.inc");  // db-Driver
//define("DBDRIVER","db_pgsql.inc");  // db-Driver
//define("DBDRIVER","db_odbc.inc");  // db-Driver
///////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
define("REPGENDIR","");  // Directory of Repgen, should be altered if you use another directory
//////////////////////////////////////////////////////////////////////////
require_once(DBDRIVER);
define("VERSION","Version 0.44 Alpha");    // Version Number 
// Functions for repgen-definition 
function set_session_data() 
{  // set the data of variables to session variable $create_allg 
 global $create_allg;   // session variable 
 global $id_neu, $short, $long, $author,$datum, $print_format, $print_size, $report_type; 
 global $sql, $group, $group_type; 
 $create_allg = $id_neu."|".$short."|".$long."|".$author."|".$datum."|".$print_format."|".$print_size; 
 $create_allg .= "|".$report_type."|".$sql."|".$group."|".$group_type; 
} 
function get_session_data() 
{  // get the data of variables from previous stored session variable $create_allg 
 global $create_allg; 
 global $id_neu, $short, $long, $author,$datum, $print_format, $print_size, $report_type; 
 global $sql, $group, $group_type; 
 
  $h = explode("|",$create_allg); 
  $id_neu = $h[0]; 
  $short  = $h[1]; 
  $long  = urldecode($h[2]); 
  $author  = $h[3]; 
  $datum  = $h[4]; 
  $print_format  = $h[5]; 
  $print_size  = $h[6]; 
  $report_type  = $h[7]; 
  $sql  = urldecode(stripslashes($h[8])); 
  $group  = $h[9]; 
  $group_type=$h[10]; 
} 
 
// Definition of a Database class to test connection 
 
class DB_Repgen extends DB_Sql { 
  var $classname = "DB_Repgen"; 

  function set_variables($d,$h,$u,$p)
  {   // sets the variables for DB-Connection 
      $this->Host = $h; 
      $this->Database = $d; 
      $this->User = $u; 
      $this->Password = $p; 
  } 
 

  function haltmsg($msg) 
  {    // does not stop the work, switches to index.php
          global $HTTP_HOST; 
          $m1 = "<B>Database Error: </B>".$msg." ".DATABASE." Error: ".$this->Error; 
          $m = urlencode($m1); 
					echo $m1;
          $url=REPGENDIR."/index.php?".SID."&msg=".$m; 	  
          # header("Location: http://$HTTP_HOST".$url);  // switches to repgen_main.php 
          exit; 
  } 

} 
// Definiton of a Database Class to report DB-Errors 
class DB_Rep extends DB_Sql { 
  var $classname = "DB_Rep"; 
  var $Host      = ""; 
  var $Database  = ""; 
  var $User      = ""; 
  var $Password  = ""; 
  var $error; 
  function get_error() 
  { // report the error-condition 
    return $this->error; 
  } 
 
  function set_variables($d,$h,$u,$p) 
  {  // sets the variables for DB-Connection 
      $this->Host = $h; 
      $this->Database = $d; 
      $this->User = $u; 
      $this->Password = $p; 
      $this->error = false; 
  } 

  function haltmsg($msg)
  {  global $HTTP_HOST; 
 
          $m = "<B> ".DATABASE." Error: </B> ".$this->Error; 
          echo '<table border=0 bgcolor="#eeeeee" align="center" cellspacing=0 cellpadding=4 width=540>'; 
          echo '<tr><td><font color=#FF2020>'; 
          echo $m; 
          echo "</font></td>   </tr>   </table>  <BR>"; 
          $this->error = true; 
  }
   
}    // end of class DB_Rep 
 
// Header of all Pages 
 
function page_header() 
{   // page header for all pages of the application 
 echo "<head><title>Report Generator REPGEN</title>  <style type=\"text/css\">"; 
 echo "<!-- body { font-family: Arial, Helvetica, sans-serif } td   { font-family: Arial, Helvetica, sans-serif ;width : \"10%\";font-size:\"10pt\"}"; 
 echo "   th   { font-family: Arial, Helvetica, sans-serif }   -->"; 
 echo " </style>  </head> <body bgcolor=\"#E4D6C0\"> <center> "; 
 echo "<h1>Report Generator REPGEN</h1>"; 
 echo VERSION."<br><br> "; 
 
 
} 
 
// Footer of all pages 
 
function page_footer() 
{     // page footer for all pages of the application 
 
} 
?>
