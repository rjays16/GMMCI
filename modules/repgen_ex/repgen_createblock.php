<?php     session_start();
/* Create a new block or alter a stored block
 * repgen_createblock.php for PHP Report Generator
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

function check_short($short) { // controls, that short-name of blocks does not be twice
        global $id_neu, $database,$host,$user,$password;
        if (empty($short)) return false;
        $db = new DB_Repgen;
        $db->set_variables($database,$host,$user,$password);
        $db->connect($database,$host,$user,$password);
        $query = "select attrib,id from reports where typ='block'";
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


function store($id, $info)
{   // stores the records 'block' in the database
    global $database, $host, $user,$password;
   $db = new DB_Repgen;
   $db->connect($database,$host,$user,$password);
   $db->query("BEGIN");
   $query="delete from reports where (id ='".$id."' and typ='block')";
   $db->query($query);
   $query="insert into reports values ('".$id."','block','".$info."')";
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
   case "select":
             // go to the page for selection of an old block without storing the content of this page
            $url=REPGENDIR."/repgen_select.php?".SID;
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_del.php
            exit;

          break;
   case "seiten_aufc":
              // go to page for definition of String-items
            $error = "";
            if (!check_short($short))  {
                       $error = ID_ERROR;
            }
             else {
                // switches to repgen_seitec.php (Definition of String-items of the block)
                   $db = new DB_Rep;
                   $db->set_variables($database,$host,$user,$password);
                   $db->Halt_On_Error = "no";
                   $info = $short."|".$datum."|".$author."|".$long ;
                   store($id_neu,$info);
                   $url=REPGENDIR."/repgen_seitec.php?".SID;
                   $url .= "&id_neu=".$id_neu."&long=".urlencode($long)."&report_type=".$report_type;
                   header("Location: http://$HTTP_HOST".$url);  // switches to repgen_seitec.php
                   exit;
           }
          break;


  case "seiten_aufl":
                // go to page for definition of Line-items
            if (!check_short($short)) {
                    $error = ID_ERROR;
            } else {
                // switches to repgen_seitel.php (Definition of items of the report)
                   set_session_data();
                   $info = $short."|".$datum."|".$author."|".$long ;
                   store($id_neu,$info);
                   $url=REPGENDIR."/repgen_seitel.php?".SID."&id_neu=".$id_neu;;
                   header("Location: http://$HTTP_HOST".$url);  // switches to repgen_seitel.php
                       exit;
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
<?php if (!empty($long)) echo ALTER_BLOCK." ".$long; else echo CREATE_BLOCK ?></strong>
</center> <br>
<br>
<?PHP
   if (!empty($error)) {
          my_error($error);
          $error = NULL;
   }
?>
<br>


 <form name="edit" method="post" action=<?php echo REPGENDIR."/repgen_createblock.php?".SID; ?>" >
 <table>
    <TR><TD align = right><?php echo ID_BLOCK.":"; ?> </TD><TD> <?php echo $id_neu; ?>
                                                         <input type=hidden name=id_neu size=10 maxlength=10 value="<?php echo $id_neu;?>" ></td></TR>
    <TR><TD align = right><?php echo SHORT.":"; ?> </TD><TD> <input type=text name=short size=10 maxlength=10 value="<?php echo $short;?>"></td></TR>
    <TR><TD align = right><?php echo LONG.":"; ?> </TD><TD> <input type=text name=long size=40 maxlength=40 value="<?php echo $long;?>"></td></TR>
    <TR><TD align = right><?php echo AUTHOR.":"; ?> </TD><TD> <input type=text name=author size=20 maxlength=20 value="<?php echo $author;?>"></td></TR>
    <TR><TD align = right><?php echo DATUM.":"; ?> </TD><TD>          <? echo date("Y-m-d"); ?></td></TR>
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
