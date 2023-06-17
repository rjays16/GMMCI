<?php    session_start();
/*  Sample for call of reports
 *  7.2.2002
 * Bauer
 * Version 0.2
 */

//////////////////////////////////////////////////////////////////////////////////
require "db_pgsql.inc";  // If you use another DB, alter db_mysql.inc to db_***.inc
                         // and don't forget to change repgen_def.inc
// Constants fo database: insert the actual values

$database   = "<database>";
$host       = "<host>";
$user       = "<user>";
$password   = "<password>";

////////////////////////////////////////////////////////////////////////////////////
// Definition of a Database class to test connection

class DB_Repgen extends DB_Sql {
  var $classname = "DB_Repgen";
  var $Host      = "";
  var $Database  = "";
  var $User      = "";
  var $Password  = "";

  function set_variables($d,$h,$u,$p)
  {   // sets the variables for DB-Connection
      $this->Host = $h;
      $this->Database = $d;
      $this->User = $u;
      $this->Password = $p;
  }

  function haltmsg($msg)
  {    // does not stop the work, switches to repgen_main
          global $HTTP_HOST;
          echo "<B>Database Error: </B>".$msg." ".DATABASE." Error: ".$this->Error;
          exit;
  }

}

//  reads all reports from table reports, where typ='info'
  $db = new DB_Repgen;
  $db->set_variables($database,$host,$user,$password);
  $db->connect();
  $query="select * from reports where  typ='info'";
  $db->query($query);
  $ar = array();
  $ah = array();
  while ($db->next_record()) {
        $attrib=$db->f("attrib");    // in $ar[] is id and attrib of report
        $ah[0] = $attrib;
        $ah[1] = $db->f("id");
        array_push($ar,$ah);
        }
?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN"> 
<html>
<head>
       <title>Report Selection</title>
</head>
<body>

<?php
       $url="repgen_druck.php?database=".$database."&host=".$host;
       $url .= "&password=".$password."&user=".$user."&id=";
?>
<script language="javascript"><!--
<?php
for ($i=0;$i<count($ar);$i++) {
 echo 'function wopen'.$i.'(){window1=open("'.$url.$ar[$i][1].'","PDFWindow","");}';
}
?>

//--></script>
<center><H1>Report Selection </H1>
<table border=1>
<TR height=10>
<?php

for ($i=0;$i<count($ar);$i++) {
  $selected = false;
  $id = trim($ar[$i][1]);
  $attr=trim($ar[$i][0]);
  $a_attr = explode("|",$attr);
  $long  = $a_attr[3];
?>
<form name="sample" action="sample.php" method="POST" >
<TD> <?php echo $long; ?> </TD>
<TD>
  <input type = "submit" value="Print" name ="drucken" onClick = "wopen<?php echo $i;?>();" >
</TD>
</form>
</tr>
<?php
}   // end of while-loop
?>
</table>
</body>
</html>
