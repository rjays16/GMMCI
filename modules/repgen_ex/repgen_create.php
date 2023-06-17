<?php     session_start();
/* Create a new report or alter a stored report
 * repgen_create.php for PHP Report Generator
   Bauer, 22.1.2002
   Version 0.2
*/

/*
 *
 *
 * 1. A section where utility functions are defined.
 * 2. A section that is called only after the submit.
 * 3. And a final section that is called when the script runs first time and
 *    every time after the submit.
 *
 * Scripts organized in this way will allow the user perpetual
 * editing and they will reflect submitted changes immediately
 * after a form submission.
 *
 * We consider this to be the standard organization of table editor
 * scripts.
 *
*/
require_once("repgen_const.inc");
require_once("repgen_def.inc");
require_once("repgen.inc");

global $HTTP_HOST;

session_register("create_allg");

function check_short($short) { // controls, that short-name of reports does not be twice
        global $id_neu, $database,$host,$user,$password;
        if (empty($short)) return false;
        $db = new DB_Repgen;
        $db->set_variables($database,$host,$user,$password);
        $db->connect($database,$host,$user,$password);
        $query = "select attrib,id from reports where typ='info'";
        $db->query($query);
        while ($db->next_record()) {
                $h=explode("|",$db->f("attrib"));
                if (($h[0] == $short) && (trim($db->f("id")) != $id_neu)) return false;
        }
        return true;
}

function m_s($a1,$a2)
{   // sets "selected" in select box when $a1 == $a2
   if ($a1 == $a2)  echo "selected";

}


function store($id, $info, $sql,$group, $group_type)
{   // stores the records info, select and group in the database
    global $database, $host, $user,$password;
   $db = new DB_Repgen;
   $db->connect($database,$host,$user,$password);
   $db->query("BEGIN");
   $query="delete from reports where (id ='".$id."' and typ='info')";
   $db->query($query);
   $sql=strtr($sql,"'","^");    // translate ' into ^
   $query="insert into reports values ('".$id."','info','".$info."')";
   $db->query($query);
   
   $query="delete from reports where (id ='".$id."' and typ='select')";
   $db->query($query);
   $sql=strtr($sql,"'","^");    // translate ' into !
   $query="insert into reports values ('".$id."','select','".$sql."')";
   $db->query($query);
   $query="delete from reports where (id ='".$id."' and typ='group')";
   $db->query($query);
   $g = $group."|".$group_type;
   $query="insert into reports values ('".$id."','group','".$g."')";
   $db->query($query);
   $db->query("COMMIT");
   set_session_data();
}


global $HTTP_HOST;


###
### Submit Handler
###

## Check if there was a submission
if (empty($id_neu)) get_session_data();
while ( is_array($HTTP_POST_VARS)
     && list($key, $val) = each($HTTP_POST_VARS)) {
  switch ($key) {


  case "test_sel":
            // test the sql statement and show the resultset
            set_session_data();
            if (empty($sql)) {
                $error = SQL_ERROR1;
            }  else {
               $sqle = strtr(urlencode(stripslashes($sql)),"^","'");
               $url=REPGENDIR."/repgen_test_sel.php?".SID."&sql=".$sqle."&back=repgen_create.php";
               header("Location: http://$HTTP_HOST".$url);
               exit;
            }
          break;
  case "select":
             // go to the page for selection of an old report without storing the content of this page

 
            $url=REPGENDIR."/repgen_select.php?".SID;
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_del.php
            exit;

          break;
  case "seiten_aufc":
              // go to page for definition of String-items
            $error = "";
            if (!check_short($short) or empty($sql) or trim($sql) == "") {
                       $error = ID_ERROR;
            }
            if (empty($group) && ($group_type == "newpage")) {
                       $error = GROUP_ERROR;
            }
             if (empty($error)) {
                // switches to repgen_seitec.php (Definition of String-items of the report)
//                   get_session_data();
                      // test, if $sql is correct SQL Statement
                   $db = new DB_Rep;
                   $db->set_variables($database,$host,$user,$password);
                   $db->Halt_On_Error = "report";
                   $sql=urldecode(stripslashes($sql));
                   $db->query($sql);
                   $db->Halt_On_Error = "yes";
                   if ($db->Errno == "0") { // no database error
                       $info = $short."|".$datum."|".$author."|".$long."|".$print_format."|".$print_size."|".$report_type ;
                       store($id_neu,$info,$sql,$group,$group_type);
                       $url=REPGENDIR."/repgen_seitec.php?".SID;
                       $url .= "&id_neu=".$id_neu."&long=".urlencode($long)."&report_type=".$report_type;
                       header("Location: http://$HTTP_HOST".$url);  // switches to repgen_seitec.php
                       exit;
                   } else {
		    $error = "SQL-Statement : '".$sql."' ".SQL_ERROR.":<BR> ".$db->Error."<BR>".NOTSTORED;
		   }
           } else {
                 $error .=  "<BR>".NOTSTORED;
           }
          break;

  case "seiten_aufl":
                // go to page for definition of Line-items
            if (!check_short($short) or empty($sql) or trim($sql) == "") {
                    $error = ID_ERROR;
            };
            if (empty($error)) {
                // switches to repgen_seitel.php (Definition of items of the report)
                   set_session_data();
                      // test, if $sql is correct SQL Statement
                   $db = new DB_Rep;
                   $db->set_variables($database,$host,$user,$password);
                   $db->Halt_On_Error = "report";
                   $sql=urldecode(stripslashes($sql));
                   $db->query($sql);
                   $db->Halt_On_Error = "yes";
                   if ($db->Errno == 0) { // no database erro
                       $info = $short."|".$datum."|".$author."|".$long."|".$print_format."|".$print_size."|".$report_type ;
                       store($id_neu,$info,$sql,$group,$group_type);
                       $url=REPGENDIR."/repgen_seitel.php?".SID."&id_neu=".$id_neu;;
                       header("Location: http://$HTTP_HOST".$url);  // switches to repgen_seitel.php
                       exit;
                   } else {
                       $error .= "<BR> Angaben wurden NICHT abgespeichert!";
                   }
           }
                   
          break;

  default:
  break;
 }
}
?>
<html>


<?php

page_header();

### Output key administration forms, including all updated
### information, if we come here after a submission...
?>

<script language="javascript"><!--
function num_test(feld) {
if (isNaN(feld.value) == true) 
{ alert("Use only Numbers here!");
 feld.focus();
 return (false); }

}
//--></script>
<strong>
<?php if (!empty($long)) echo ALTER_HEAD." ".$long; else echo CREATE_HEAD ?></strong>
</center> <br>
<br>
<?PHP
   if (!empty($error)) {
          my_error($error);
          $error = NULL;
   }
?>
<br>


 <form name="edit" method="post" action=<?php echo REPGENDIR."/repgen_create.php?".SID; ?>" >
 <table>

    <TR><TD align = right><?php echo ID.":"; ?> </TD><TD> <?php echo $id_neu; ?>
               <input type=hidden name=id_neu size=10 maxlength=10 value="<?php echo $id_neu;?>" ></td></TR>
    <TR><TD align = right><?php echo SHORT.":"; ?> </TD><TD> <input type=text name=short size=10 maxlength=10 value="<?php echo $short;?>"></td></TR>
    <TR><TD align = right><?php echo LONG.":"; ?> </TD><TD> <input type=text name=long size=40 maxlength=40 value="<?php echo $long;?>"></td></TR>
    <TR><TD align = right><?php echo AUTHOR.":"; ?> </TD><TD> <input type=text name=author size=20 maxlength=20 value="<?php echo $author;?>"></td></TR>
    <TR><TD align = right><?php echo DATUM.":"; ?> </TD><TD>          <? echo date("Y-m-d"); ?></td></TR>
    <TR><TD bgcolor="#eeeeee" align = right><?php echo GROUP.":"; ?> </TD><TD> <input type=text name=group size=20 maxlength=30 value="<?php echo $group;?>"></td></TR>
    <TR bgcolor="#eeeeee"><TD align = right><?php echo GROUP_TYPE.":"; ?> </TD>
    <TD bgcolor="#eeeeee">
        <Select name = group_type size=1 >
            <OPTION value = "nopage" <?php m_s($group_type,"nopage"); ?> > <?php echo KEIN_PAGE; ?>
            <OPTION value = "newpage" <?php m_s($group_type,"newpage"); ?>> <?php echo NEU_PAGE; ?>
         </Select>
    </TD></TR>

    <TR><TD align = right><?php echo SQL.":"; ?> </TD><TD> <input type=text name=sql size=60 maxlength=255 value="<?php echo
    strtr(urldecode(stripslashes($sql)),"^","'");?>"></td></TR>
    <TR><TD></TD><TD>
             <input type="submit" name="test_sel" value="<?php echo TEST_SEL ?>"  >
    </TD></TR>
    <TR><TD>    </TD><TD>   </TD></TR>
    <TR><TD align = right><?php echo PRINT_FORMAT.":"; ?> </TD>
    <TD> <Select name = print_format size=1>
            <OPTION value = "portrait" <?php m_s($print_format,"portrait"); ?>> Portrait
            <OPTION value = "landscape" <?php m_s($print_format,"landscape"); ?>> Landscape
         </Select>
    </TD></TR>

  <TR ><TD align = right><?php echo A4FORMAT1.":"; ?> </TD>
    <TD> <Select name = print_size size=1>
            <OPTION value = "a4" <?php m_s($print_size,"a4"); ?>> A4
            <OPTION value = "a3" <?php m_s($print_size,"a3"); ?>> A3
            <OPTION value = "a5" <?php m_s($print_size,"a5"); ?>> A5
         </Select>
    </TD></TR>

    <TR ><TD align = right><?php echo REPORT_TYPE.":"; ?> </TD><TD>
        <Select name = report_type size=1>
            <OPTION value = "single" <?php m_s($report_type,"single"); ?> > <?php echo PAGE_REC; ?>
            <OPTION value = "class" <?php m_s($report_type,"class"); ?>> <?php echo LINE_REC; ?>
            <OPTION value = "classtable" <?php m_s($report_type,"classtable"); ?>> <?php echo GRID_REC; ?>
            <OPTION value = "classbeam" <?php m_s($report_type,"classbeam"); ?>> <?php echo BEAM_REC; ?>
            <OPTION value = "classgrid" <?php m_s($report_type,"classgrid"); ?>> <?php echo BEAMGRID_REC; ?>	    
         </Select>
    </TD></TR>
    <input type="hidden", name="datum" value = "<? echo date("Y-m-d"); ?>">
    <input type="hidden" name="id" value=$id;> </TD>
    </table> <br>
<br>
<br>

    <table>
        <TR><TD align= right> <input type="submit" name="select" value="<?php echo SELECT_CR ?>" ></TD>
           <TD align = center>  <input type="submit" name="seiten_aufc" value="<?php echo SEITEN_AUFC ?>" > </TD>
           <TD align = left>  <input type="submit" name="seiten_aufl" value="<?php echo SEITEN_AUFL ?>" > </TD>
        </TR>
     </table>
     

 </form>
<?php page_footer(); ?>

</body>
</html>
