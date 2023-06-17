<?php session_start();
/* Test the SQL Statement
 * repgen_test_sel.php for PHP Report Generator
   Bauer, 22.5.2001
   Version 0.1
*/


require_once("repgen_const.inc");
require_once("repgen_def.inc");

/* If this page is called directly, switch to repgen_main.php
*/

if ((empty($database) || empty($host)|| empty($user) || empty($password))) {
                         // not all fields have values
            $url=REPGENDIR."/repgen_main.php?".SID;
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_main.php back
            exit;

}
?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN"> 
<html>

<?php page_header(); ?>

<strong><? echo SQL_STATEMENT ?></strong> <br><br>


<?php
   $sql = stripslashes($sql);
   $sqle = urldecode($sql);
   //   print the SQL-Command
?>
  <table border=0 bgcolor="#eeeeee" align="center" cellspacing=0 cellpadding=4 width=540>
  <tr>
   <td><font color=#008000> <?php print $sqle ?></font></td>
  </tr>
 </table>
 <br> 
 <?php

  $db = new DB_Rep;
  $db->set_variables($database,$host,$user,$password);
  $db->Halt_On_Error = "report";
  $db->query($sql);      // test, if SQL-statement is correct
  $db->Halt_On_Error = "yes";

  if (!$db->get_error()) { // no database error
    $sqlw=$sql;         // look for tablename
    $sqlw=substr(strstr($sqlw,"from"),5);
    $sqlw=substr($sqlw,0,strcspn($sqlw," ")); 
    $res = $db->metadata($sqlw,false);
    $num = $db->num_fields();
/*
 *
 * show 10 records of this resultset
 *
 *
*/
?>
<br> <br>
<br>
<strong><? echo SQL_ERG ?></strong> <br><br>

<TABLE border rules="all" bgcolor="#eeeeee">
<?php
     echo "<TR>";
     for ($i=0;$i<$num; $i++)     // write column names
        echo "<TH>".$res[$i]["name"]."</TH>";
     echo "</TR>";
     for ($k=0; $k<9; $k++) {
         $db->next_record();
         for ($i=0;$i<$num; $i++)     // write column names
            echo "<TD>".$db->f($i)."</TD>";
         echo "</TR>";
    }
         
?>
</table>
 <br>
<?php
  } // end of no database error
  $url = $back."?".SID;
?>
<form action="<?php echo $url;?>" method ="POST" ?>

 <input type="submit" value="<? echo BACK;?>" name="back">
 </form>
 </center>
<? page_footer(); ?>

</body>
</html>
