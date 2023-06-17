<?php   session_start();
/* Create a new function or alter a stored function
 * repgen_createfunction.php for PHP Report Generator
   Bauer, 5.2.2002
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

function check_short($short) { // controls, that short-name of blocks does not be twice
        global $id_neu, $database,$host,$user,$password;
        if (empty($short)) return false;
        $db = new DB_Repgen;
        $db->set_variables($database,$host,$user,$password);
        $db->connect($database,$host,$user,$password);
        $query = "select attrib,id from reports where typ='funct'";
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
   $query="delete from reports where (id ='".$id."' and typ='funct')";
   $db->query($query);
   $query="insert into reports values ('".$id."','funct','".$info."')";
   $db->query($query);
   $db->query("COMMIT");
   set_session_data();
}



global $HTTP_HOST;
if (!empty($function)) $function = stripslashes($function);  // strip $function

###
### Submit Handler
###

## Check if there was a submission

while ( is_array($HTTP_POST_VARS)
     && list($key, $val) = each($HTTP_POST_VARS)) {
  switch ($key) {
  case "select":
             // go to the page for selection of an old report without storing the content of this page

 
            $url=REPGENDIR."/repgen_select.php?".SID;
	    $url= "http://$HTTP_HOST".$url;
            header("Location: ".$url);  // switches to repgen_select.php
            exit;

          break;
  case "store":
              // go to page for definition of String-items
            if (!check_short($short) ) {
                    $error = ID_ERROR_BLOCK;
            } else if (stristr($function," ".$short."(")) {  // $short == functionname?
                   $info = $short."|".$datum."|".$author."|".$long."|".addslashes($function) ;
                   store($id_neu,$info);
                   $url=REPGENDIR."/repgen_select.php?".SID;
                   $url= "http://$HTTP_HOST/".$url;
                   header("Location: ".$url);  // switches to repgen_seitec.php
           } else  $error = ERROR_FUNC.$short."(){...}";

          break;
  case "test":  if (stristr($function," ".$short."(")) {  // $short == functionname?
                   global $php_errormsg;
                   @eval( $function);       //declare function
                   $h_a =strtok($function,"(");  // look if the function has a parameter
                   $h_a= strtok(")");     // $h_a is now the parameter
                   $dbx = new DB_Repgen;
                   $dbx->connect($database,$host,$user,$password);
                   if (!empty($h_a)) {    // first parameter is $db
                        if (stristr($h_a,","))  { // parameter includes ,
                          $func = '$field='.$short.'($dbx,$this);';// two parameters
                        } else {   // one parameter = $db
                          $func = '$field='.$short.'($dbx);';// two parameters
                        }
                   } else {
                          $func = '$field='.$short.'();';
                   }
                   @eval($func);                       // call function
                   $error=$php_errormsg;
          }
          break;

  default:
  break;
 }
}

?>

<?php
page_header();

### Output key administration forms, including all updated
### information, if we come here after a submission...
?>

<strong>
<?php if (!empty($long)) echo ALTER_FUNCT." ".$long; else echo CREATE_FUNCT ?></strong>
<br>
<br><center>
<?PHP
   if (!empty($error)) {
          my_error(PHP_ERROR.$error);
          $error = NULL;
   } else if (!empty($function)) my_msg(PHP_OK.$field);
if (empty($function)) {
    $dbn = new DB_Repgen;    // get function code from table
    $dbn->connect($database,$host,$user,$password);
    $dbn->query("select attrib from reports where id = '$id_neu'");
    $dbn->next_record();
    $h = explode("|",$dbn->f("attrib"));
    $function = stripslashes($h[4]);
}
?>
<br>


 <form name="edit" method="post" action="<?php echo "repgen_createfunct.php?".SID; ?>" >
 <table>
    <TR><TD align = right><?php echo ID_FUNCT.":"; ?> </TD><TD> <?php echo $id_neu; ?>
            <input type=hidden name=id_neu size=10 maxlength=10 value="<?php echo $id_neu;?>" ></td></TR>
    <TR><TD align = right><?php echo SHORT.":"; ?> </TD><TD> <input type=text name=short size=10 maxlength=10 value="<?php echo $short;?>"></td></TR>
    <TR><TD align = right><?php echo LONG.":"; ?> </TD><TD> <input type=text name=long size=40 maxlength=40 value="<?php echo $long;?>"></td></TR>
    <TR><TD align = right><?php echo AUTHOR.":"; ?> </TD><TD> <input type=text name=author size=20 maxlength=20 value="<?php echo $author;?>"></td></TR>
    <TR><TD align = right><?php echo DATUM.":"; ?> </TD><TD>          <? echo date("Y-m-d"); ?></td></TR>
    <TR><TD align = right><?php echo FUNKTION.":"; ?> </TD><TD><textarea name="function" rows=10 cols=50 wrap=virtual><?php echo $function; ?></textarea></td></TR>
    <input type="hidden", name="datum" value = "<? echo date("Y-m-d"); ?>">
    <input type="hidden" name="id" value=$id;> </TD>
    </table> <br>
<br>
<?php echo FUNC_ERKL; ?>
<br>

    <table>
        <TR><TD align= right> <input type="submit" name="select" value="<?php echo SELECT_CR ?>" ></TD>
           <TD align = center>  <input type="submit" name="store" value="<?php echo SEITEN_STORE ?>" > </TD>
           <TD align = left>  <input type="submit" name="test" value="<?php echo SEITEN_TEST ?>" > </TD>
        </TR>
     </table>

 </form>
<?php page_footer(); ?>

</body>
</html>
