<?php session_start();
/*
 *  Werner Bauer
 *  5.2.2002
 *  file: repgen_seite.php
*   Changed 19.11.2002 Version 0.44: Report Header and footer
 *
 *  item definition routine for Report generator repgen.
 *
 *  shows all items of a report and enables creation of an item
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


function m_s($a1,$a2)
{   // sets "selected" in select box when $a1 == $a2
   if ($a1 == $a2)  return "selected";

}



/* If this page is called direct, switch to repgen_index.php
*/

if ((empty($database) || empty($host)|| empty($user) || empty($password))) {
                         // not all fields have values
            $url=REPGENDIR."/repgen_index.php?".SID;
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_main.php back
            exit;

}



###
### Submit Handler
###

## Check if there was a submission
## Get a database connection
  $db = new DB_Repgen;
  $db->connect($database,$host,$user,$password);
  get_session_data();

while ( is_array($HTTP_POST_VARS)
     && list($key, $val) = each($HTTP_POST_VARS)) {
  switch ($key) {

      // go back 
  case "back":
            $url=REPGENDIR."/repgen_select.php?".SID;
            header("Location: http://$HTTP_HOST".$url);  // switches to page 'select a report'
            exit;

          break;
  case "delete":
                // deletes item from table reports
           $query= "delete from reports where (id ='$id1' and attrib = '$attrib')";

           $db->query($query);
          break;
  case "insert":
               //  inserts item into table reports
                      // test the input 
            $attrib1 = $sel_typ."|".$sel_art."|".$width."|".$x1."|".$y1."|".$x2."|".$y2;
            if (!(empty($id_neu) or empty($width) or empty($x1) or empty($y1) or empty($x2) or empty($y2)))  {

                  // does item exist already?
                  if ($alternate == "true") {
                       $query = "delete from reports where (id = '".$id_neu."' and attrib ='".$attriba."' and typ='item')";
                       $db->query($query);
                       $alternate = "false";
                  }

                  $query = "select * from reports where (id = '".$id_neu."' and attrib ='".$attrib1."' and typ='item')";
                  $db->query($query);
                  if ($db->num_rows() == 0 ) { // it is new item, store it
                      $query = "insert into reports values('$id_neu','item','$attrib1')";
                      $db->query($query);
                      $error= NULL;
                  };
               }
             else $error= ERROR_LEER_LINE;
          break;
  case "druck":
             //  make a test print
          // The submit button "druck" creates a link to repgen_druck with onClick - Event
          // to the javascript function wopen()
          break;
  case "alter":
               //  alters item into table reports
                  $alternate = "true";
                  $h = explode("|",$attrib);
                  $sel_typ=$h[0];
                  $sel_art = $h[1];
                  $width = $h[2];
                  $x1 = $h[3];
                  $y1 = $h[4];
                  $x2 = $h[5];
                  $y2 = $h[6];
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

if (stristr($HTTP_USER_AGENT,"MSIE")) {
     $url="http://$HTTP_HOST".REPGENDIR."/repgen_druck.php?".SID; // Aufruf für MSIE5
} else  {
     $url="http://$HTTP_HOST".REPGENDIR."/repgen_druck1.php?".SID; // aufruf für andere Browser
}
$url .="&id=".$id_neu;

?>
<script language="javascript"><!--
function num_test(feld) {
if (isNaN(feld.value) == true)
{ alert("Use only Numbers here!");
 feld.focus();
 return (false); }
}
function wopen() {
        window2=open("<?php echo $url; ?>","PDFWindow",   "");
}
//--></script>

<center>
<strong><?php if (!empty($long)) echo ITEM_DEF.$long; else echo ITEM_DEF; ?></strong>
</center>
<H3> <?php echo ITEM_LINE; ?> </H3>
<? if (!empty($error))
    my_error($error);
?>
<br>
<?php
   echo IT_HELP;
?>
  <form action="<?php $h="repgen_seitel.php?".SID; echo $h; ?>" method="POST">
    <table border = 0>
      <TR>
        <TD>
          <B><?php echo IT_ART.":"; ?></B>
<?php if ($report_type == "single") { ?>
          <select name="sel_art" size="1" >
           <option value="DE"  selected >Detail</option>
          </select>
<?php  } else {  ?>

          <select name="sel_art" size="1" >
           <option value="RH" <? echo m_s("RH",$sel_art); ?>>Report Header</option>
           <option value="PH" <? echo m_s("PH",$sel_art); ?>>Page Header</option>
           <option value="GH"  <? echo m_s("GH",$sel_art); ?>>Group Header</option>
           <option value="DE"  <? echo m_s("DE",$sel_art); ?>>Detail</option>
           <option value="GF"  <? echo m_s("GF",$sel_art); ?>>Group Foot</option>
           <option value="PF"  <? echo m_s("PF",$sel_art); ?>>Page Foot</option>
           <option value="RF"  <? echo m_s("RF",$sel_art); ?>>Report Footer</option>
          </select>
<?php   }  ?>
        </TD>
        <TD>
          <B>Type:</B>
          <select name="sel_typ" size="1" >
           <option value="Line" <? echo m_s("Line",$sel_typ); ?>>Line</option>
           <option value="Rect"  <? echo m_s("Rect",$sel_typ); ?>>Rectangle</option>
          </select>
        </TD>

        <TD>
          <B><?php echo IT_X1; ?>:</B>
          <input type="text" name="x1" size="4" maxlength="4" value="<?php if (isset($x1)) { echo $x1; } ?>" onBlur="num_test(this);">
        </td>
        <TD>
          <B><?php echo IT_Y1; ?>:</B>
          <input type="text" name="y1" size="4" maxlength="4" value="<?php if (isset($y1)) { echo $y1; } ?>" onBlur="num_test(this);">
        </td>
        <TD>
          <B><?php echo IT_X2; ?>:</B>
          <input type="text" name="x2" size="4" maxlength="4" value="<?php if (isset($x2)) { echo $x2; } ?>" onBlur="num_test(this);">
        </td>
        <TD>
          <B><?php echo IT_Y2;?>:</B>
          <input type="text" name="y2" size="4" maxlength="4" value="<?php if (isset($y2)) { echo $y2; } ?>" onBlur="num_test(this);">
        </td>
        <TD>
          <B><?php echo IT_WIDTH;?>:</B>
          <input type="text" name="width" size="4" maxlength="4" value="<?php if (isset($width)) { echo $width; } ?>" onBlur="num_test(this);">
        </td>
    </TR>
         <input type="hidden" name="id_neu" value="<? echo $id_neu; ?>" >
</table>
<br>
<br>
<table><tr><td >
<center>
<input name="insert" type="submit" value="<? echo IT_STORE; ?>" >
</center>
</TD>

<td>
<input name="back" type="submit" value="<? echo IT_BACK; ?>"  >
</td>
<td>
<input name="druck" type="submit" value="<? echo IT_DRUCK; ?>" onClick="wopen()" >
</td>
</TR>
</table>
</center>
<HR size=5>
<input type = "hidden" name = "alternate" value = "<?php echo $alternate; ?>" >
<input type = "hidden" name = "attriba" value = "<?php echo $attrib; ?>" >

 
</form>
<!--        End of input item form   -->
<!--------------------------------------------------------------------->
<center>
<strong><?php echo ITEM_HEAD; ?></strong>
</center> <br>

<table border=0 bgcolor="#eeeeee" align="center" cellspacing=2 cellpadding=2 width=540>
 <tr valign=top align=left bgcolor = <? echo BGCOLORH; ?>>
  <th><? echo IT_TYP; ?></th>
  <th><? echo IT_ART; ?></th>
  <th><? echo IT_FONT; ?></th>
  <th><? echo IT_FONT_SIZE; ?></th>
  <th><? echo IT_LEN; ?></th>
  <th><? echo IT_STRING; ?></th>
  <th><? echo IT_X1; ?></th>
  <th><? echo IT_Y1; ?></th>
  <th><? echo IT_X2; ?></th>
  <th><? echo IT_Y2; ?></th>
  <th><? echo IT_WIDTH; ?></th>
  <th cellspan=2>Action</th>
 </tr>
<?php

  ## Traverse the result set

 $query="select  * from reports where (typ = 'item' and id='".$id_neu."') order by attrib";

  $db->query($query);
  $line= 0;   // line-number
  while ($db->next_record()){
    $h = explode("|",$db->f("attrib"));
    $it_typ=$h[0];
    $it_art = $h[1];
    $it_font = $h[2];
    $it_fontsize = $h[3];
    $it_zahl = $h[4];
    $it_x1 = $h[5];
    $it_y1 = $h[6];
    if ($it_typ == "String" or $it_typ == "DB" or $it_typ == "Term")   $it_str = $h[7];
    $line ++;
    $bgcolor = BGCOLOR1;     // define the color of the row
    $line % 2  ? 0: $bgcolor = BGCOLOR2;
    
?>
 <!-- existing items -->
 <form name="edit" method="post" action="<? echo "repgen_seitel.php?".SID; ?>
 <tr valign=middle align=left bgcolor = "<? echo $bgcolor; ?>">
    <td> <?php echo $it_typ; ?> 
           <input type="hidden" name="id1" value="<?php echo $id_neu; ?>">
           <input type="hidden" name="attrib" value="<?php echo $db->f("attrib"); ?>">           
    </TD>
    <td> <?php echo $it_art; ?></TD>
    <?php if (in_array($it_typ , array("String","DB","Term"))) {
     ?>
    <td> <?php echo $it_font; ?></TD>
    <td> <?php echo $it_fontsize; ?></TD>
    <td> <?php echo $it_zahl; ?></TD>
    <td> <?php echo $it_str; ?></TD>
    <td> <?php echo $it_x1; ?></TD>
    <?php if ($it_y1 != "") echo " <td>". $it_y1."</TD>";
          else                echo "<TD>.</TD>";
     ?>

    <?php
    } else {
    ?>
    <td>.</td> <td>.</td><td>.</td> <td>.</td>
    <td> <?php echo $it_fontsize; ?></TD>
    <td> <?php echo $it_zahl; ?></TD>

    <?php
    } ?>
    

    <?php if ( in_array($it_typ, array("String","DB","Term","Block","Textarea"))) {
     ?>

           <td>.</td> <td>.</td><td>.</td>
    <?php
    } else {
    ?>
            <td> <?php echo $it_x1; /* width */?></TD>
            <td> <?php echo $it_y1; /* X2 */?></TD>
            <td> <?php echo $it_font; /* y2 */?></TD>
    <?
    }
    ?>
    <TD>
        <input type="submit" name="delete" value="<?php echo DELETE ?>" align="center">
      </td>
<?php if (in_array($it_typ,array("Line","Rect"))) { ?>
    <TD>
        <input type="submit" name="alter" value="<?php echo CHANGE; ?>" align="center">
      </td>
<?php
}
?>
</tr>
 </form>

<?php

 }  // end of while

?>
</table>
<? page_footer(); ?>

</body>
</html>
