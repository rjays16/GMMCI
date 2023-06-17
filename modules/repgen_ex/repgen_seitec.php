<?php session_start();
/*
 *  Werner Bauer
 *  5.2.2002
 *  file: repgen_seite.php
 *  Changed 19.11.2002 Version 0.44 Total, reportheader+footer
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

function m_c($a1,$a2)
{   // sets "checked" in radio box when $a1 == $a2
   if ($a1 == $a2)  return "checked";

}

function de_lesen($id_neu) {   // read all item-records and returns all DE-Attributs
        global $database, $host, $user, $password;
        $ar = array();
        $dbn = new DB_Repgen;
        $dbn->connect($database,$host, $user, $password);
        $dbn->query("select attrib from reports where (id = '".$id_neu."' and typ='item')");
        while ($dbn->next_record()) {
                $attr = $dbn->f("attrib");
                $attr_h = explode("|",$attr);
                if ($attr_h[1] == "DE"){   // nur art="DE" - Saetze interessieren
                         $ar[] = $attr;
                }
        }
        return $ar;
}
 function ist_ord($ar) {  // checks, if there is an DE-item with ord<> ""
         for ($i=0;$i<sizeof($ar);$i++) {
            $attr_h = explode("|",$ar[$i]);   //
            if (!empty($attr_h[8])) return true;
    }
    return false;
 }

global $id_neu, $database, $host, $user, $password, $report_type;



###
### Submit Handler
###

## Check if there was a submission
## Get a database connection

  get_session_data();
  $db = new DB_Repgen;
  $db->set_variables($database,$host,$user,$password);
  $db->connect($database,$host,$user,$password);

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
                  $is_ord = !(empty($zahl) || empty($ord)); //reihenfolge angegeben = true
                  $is_y1 = !empty($y1) ||($y1 == "0");
                  $is_von = !empty($von) ||($von == "0");
                  $is_bis = !empty($bis) ||($bis == "0");
                  if ((!$is_y1 || empty($x1)) && ! $is_ord){  // keine Rf und x oder y fehlen
                     $error = ERROR_XY;
                     break;
                 }
                 if ((!empty($x1)&& !empty($y1) && $is_ord)){ // RF und x und y angegeben -> Fehler
                     $error = ERROR_MIX;
                     break;
                 }
                 if (($sel_art != "DE") && $is_ord){  // Rf nur wenn art <> DE
                     $error = ERROR_ORDER;
                     break;
                 }
                 if ($report_type !="single")  
                     if (empty($id_neu) || (empty($zahl) && empty($x1)) || empty($zahl)){ // keine Eingabe
	             $error = ERROR_LEER;
                     break;
	             }
                 if ($is_von || $is_bis) {  // $von or $bis have value
	            $von = 0 +$von;
                    $bis = 0 + $bis;
                    if ($von < 1) $von = 1; // $von must be > 0
                    if ($bis > $zahl) $bis = $zahl; // $bis must not > $zahl
                    if ($bis == 0) $bis = $zahl; // $bis must not > 0
                    if ($von >= $bis)   {
                            $error = ERROR_BIS;
                            break;
                    }
                 }
                 $is_x = !empty($x1);
                 $attr_ar= array();
                 $attr_ar = de_lesen($id_neu);      // lesen der Detail -items und Rückgabe als array
                 $ord_attr = ist_ord($attr_ar);

                 if (($is_x && $ord_attr)||($is_x && $is_ord) ) {
                                      // x und y angegeben und art=DE, obwohl schon Rf gespeichert ist
                         $error = ERROR_MIX;
                         break;
                 }
                 if (($sel_typ == "String") && empty($wert)){
                         $error = ERROR_WERT;
                         break;
                 }
                 if (($sel_typ == "Textarea") && empty($term) && empty($width)){
                         $error = ERROR_WERT;
                         break;
                 }

                 if (($total == "true") && ($sel_art != "DE")) {
		          $error = ERROR_TOTAL;
			  break;
		  }
		  
                 ////////////////// end of input-Test /////////////
                 if ($sel_typ == "Textarea"){
                          $zahl=$width;
                 }

                 $attrib1 = $sel_typ."|".$sel_art."|".$sel_font."|".$sel_fontsize."|".$zahl.$sel_center."|".$x1."|".$y1."|";
                 $db->query("BEGIN");
                 switch ($sel_typ) {
		   case "DB": $attrib1 .= $feld;
		              break;
		   case "Term":
                   case "Textarea": $attrib1 .= $term;
		              break;
		   case "String": $attrib1 .= $wert;
		              break;
		   case "Block": $attrib1 .= $block;
		              break;
		   default:   $attrib1 .= $wert;
		              break;
		  }

                 $attrib1 .= "|".$ord;   // new attrib is ready
                 $attrib1 .= "|".$entschl; // decode
                 $attrib1 .= "|".$von."|".$bis; // begin and end of substring
                 $attrib1 .= "|".$total."|".$o_score."|".$u_score."|".$bold; // total sum for this item 
                 if ($alternate == "true") {    // coming from change
                       $query = "delete from reports where (id = '".$id_neu."' and attrib ='".$attriba."' and typ='item')";
                       $db->query($query);
                       $alternate = "false";
                  }

                  $query = "select * from reports where (id = '".$id_neu."' and attrib ='".$attrib1."' and typ='item')";
                  $db->query($query);
                 if ($db->num_rows() == 0 ) { // it is new item, store it
                  if (empty($ord)) {    // store X/Y -item
                     $query = "insert into reports values('$id_neu','item','$attrib1')";
                     $db->query($query);
                     $error= NULL;
                  } else {   // rearrange all items with correct ord ($h[8]) (there are only items with ord)
                      $attr_h = array();
                      for ($i=0;$i<count($attr_ar);$i++) {  // create array(ord => attrib) and delete records
                              $h = explode("|",$attr_ar[$i]);
                              $attr_h[$h[8]] = $attr_ar[$i];
                              $query = "delete from reports where (id = '".$id_neu."' and attrib ='".$attr_ar[$i]."' and typ='item')";
                              $db->query($query);
                      }
                      ksort($attr_h,SORT_STRING); // sort old items
                      reset($attr_h);
                      $li=1;
                      $eingefuegt=false;
                      while (list($key,$attr) = each($attr_h)) { // write items back and insert the new at the right position
                       $h    = explode("|",$attr);
                       if (($ord <= $h[8])&& !$eingefuegt) {  // insert new item in the hole or before old item
                           $hi = explode("|",$attrib1);
                           $hi[8] = $li;
                           $attrib1 = implode("|",$hi);
                           $query = "insert into reports values('$id_neu','item','$attrib1')";
                           $db->query($query);
                           $eingefuegt = true;
                           $li++;
                       }
                       $h[8] = $li;
                       $attr = implode("|",$h);
                       $query = "insert into reports values('$id_neu','item','$attr')";
                       $db->query($query);
                       $li++;
                      }
                       if ($ord > $h[8]) {   // the new item has the greatest ord-number $ord of all items
                           $hi = explode("|",$attrib1);
                           $hi[8] = $li;
                           $attrib1 = implode("|",$hi);
                           $query = "insert into reports values('$id_neu','item','$attrib1')";
                           $db->query($query);
                      }
                  }
                 };
          $db->query("COMMIT");
          break;
  case "druck":
          // The submit button "druck" creates a link to repgen_druck with onClick - Event
          // to the javascript function wopen()
          break;

  case "alter":
               //  alters item into table reports
                  $alternate = "true";
                  $h = explode("|",$attrib);
                  $sel_typ=$h[0];
                  $sel_art = $h[1];
                  $sel_font = $h[2];
                  $sel_fontsize = $h[3];
                  $zahl = substr($h[4],0,strlen($h[4])-1);
                  
                  $sel_center = substr($h[4],strlen($h[4])-1,1);
                  $x1 = $h[5];
                  $y1 = $h[6];
                  if ($sel_typ == "String" ) $wert = $h[7];
                  if ($sel_typ == "DB")      $feld = $h[7];
                  if ($sel_typ == "Term")   $term = $h[7];
                  if ($sel_typ == "Textarea" ) {  $term = $h[7];
                                                  $width = $h[4]; // width stored in $zahl!
                  }

		  $ord = $h[8];
                  $entschl = $h[9];
                  $von = $h[10];
                  if (trim($h[11]) !="") $bis = $h[11]; else $bis=""; // there could be a trailing space from the database		  		  
		  $total = $h[12];
		  $o_score = $h[13];
		  $u_score = $h[14];
                  if (trim($h[15]) !="") $bold = $h[15]; else $bold=""; // there could be a trailing space from the database		  
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
$url="http://$HTTP_HOST".REPGENDIR."/repgen_druck1.php?".SID; // aufruf für andere Browser
$url .="&id=".$id_neu."&report_type=".$report_type."&long=".$long;
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
</center> <br>
<H2><? echo ITEM_CHAR; ?> </H2>
<? if (!empty($error))
    my_error($error);
?>
<br>
  <form action="<?php $h="repgen_seitec.php?".SID."&report_type=".$report_type."&long=".$long; echo $h; ?>" method="POST">
          <!--   Table 1 -->
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
           <option value="RF" <? echo m_s("RF",$sel_art); ?>>Report Foot</option>
          </select>
<?php   }  ?>
        </TD>
        <TD>
          <B>Font:</B>
          <select name="sel_font" size="1" >
            <option value="Helvetica"  <? echo m_s("Helvetica",$sel_font); ?>>Helvetica</option>
            <option value="Helvetica-Bold" <? echo m_s("Helvetica-Bold",$sel_font); ?>>Helvetica Bold</option>
            <option value="Helvetica-Oblique" <? echo m_s("Helvetica-Oblique",$sel_font); ?>>Helvetica Italic</option>
            <option value="Helvetica-BoldOblique" <? echo m_s("Helvetica-BoldOblique",$sel_font); ?>>Helvetica Bold Italic</option>
            <option value="Courier" <? echo m_s("Courier",$sel_font); ?>>Courier</option>
            <option value="Courier-Bold" <? echo m_s("Courier-Bold",$sel_font); ?>>Courier Bold</option>
            <option value="Courier-Oblique" <? echo m_s("Courier-Oblique",$sel_font); ?>>Courier Italic</option>
            <option value="Courier-BoldOblique" <? echo m_s("Courier-BoldOblique",$sel_font); ?>>Courier Bold Italic</option>
            <option value="Times-Roman" <? echo m_s("Times-Roman",$sel_font); ?>>Times Roman</option>
            <option value="Times-Bold" <? echo m_s("Times-Bold",$sel_font); ?>>Times Bold</option>
            <option value="Times-Italic" <? echo m_s("Times-Italic",$sel_font); ?>>Times Italic</option>
            <option value="Times-BoldItalic" <? echo m_s("Times-BoldItalic",$sel_font); ?>>Times Bold</option>
            <option value="Symbol" <? echo m_s("Symbol",$sel_font); ?>>Symbol</option>
            <option value="ZapfDingbats" <? echo m_s("ZapfDingbats",$sel_font); ?>>ZapfDingBats</option>
          </select>
        </TD>
        <TD>
          <B>Fontsize:</B>
            <select name="sel_fontsize" size="1" >
            <?php
                for ($i=0;$i<7;$i++) {
                 $h=$i+6;
                 echo "<option value= \"".$h."\"   ".m_s($h,$sel_fontsize).">".$h."</option>";     }
                for ($i=0;$i<9;$i++) {
                 $h=2*$i+14;
                 echo '<option value= "'.$h.'"   '.m_s($h,$sel_fontsize).'>'.$h.'</option>';     }
                for ($i=0;$i<4;$i++) {
                 $h=4*$i+32;
                 echo '<option value= "'.$h.'"   '.m_s($h,$sel_fontsize).'>'.$h.'</option>';     }
                for ($i=0;$i<5;$i++) {
                $h=6*$i+48;
                echo '<option value= "'.$h.'"   '.m_s($h,$sel_fontsize).'>'.$h.'</option>';     }

             ?>
            </select>
        </TD>
        <TD bgcolor="#FFB143">
          <!--   Table2 in the table1 -->
          <table border = 1 bgcolor="#ffb143">
	    <TR>
	    <TD>
              <!--   first Table in the table2 -->
	      <table bgcolor = "#ccb140" >
              <!-- 1. line -->
               <TR>
	        <TD>
                 <B>X:</B>
                 <input type="text" name="x1" size="4" maxlength="4" value="<?php if (isset($x1)) { echo $x1; } ?>" onBlur="num_test(this);">
                </td>
                <TD>
                 <B>Y:</B>
                 <input type="text" name="y1" size="4" maxlength="4" value="<?php if (isset($y1)) { echo $y1; } ?>" onBlur="num_test(this);">
                </td>
               </TR>
             </table>
	    </TD>
	    </TR>
            <!-- 2. line  of table2-->
<?php if ($report_type != "single") { // don't show order-element ?>
	  <TR bgcolor="#ccB140">
	     <TD>
              <B>    <?php echo ALTERNATIV." ".ORDER; ?>: </B>
                   <input type="text" name="ord" size="2" maxlength="2" value="<?php if (isset($ord)) { echo $ord; } ?>" onBlur="num_test(this);">
             </TD>

          <!--   3. Line -->
            </tr>
	    <tr>
	     <TD> 
	                 <!--   Third table in Table2 -->
	      <table border = 0 frame = "above" bgcolor = "#ffb143">
                <TR>
	         <TD>
                   <B><?php echo NUMBER.":"; ?></B>
                 </TD>
                 <TD>
                   <input type="text" name="zahl" size="2" maxlength="2" value="<?php if (isset($zahl)) { echo $zahl; } ?>" onBlur="num_test(this);">
                 </td>
                 <TD bgcolor="#FFB143">
                   <B><?php echo ALIGN.":"; ?></B>
                 </TD>
                 <TD>
                   <select name="sel_center" size="1" >
                     <option value="l" <? echo m_s("l",$sel_center); ?>>left</option>
                     <option value="c"  <? echo m_s("c",$sel_center); ?>>center</option>
                     <option value="r"  <? echo m_s("r",$sel_center); ?>>right</option>
                   </select>
                 </td>
                </tr>
<?php  } ?>
                <TR bgcolor="#ffcc00"> <TD>
                     <B><?php echo OPTIONAL." ".SUBSTRING; ?></B>
                     </TD>
                 <TD>.</TD>
                 <TD>
                 <B>
                   <?php echo FROM; ?>:<input types="text" name="von" size="2" maxlength="2" value="<?php if (isset($von)) { echo $von; } ?>" onBlur="num_test(this);">
                   
                    </B>
                 </TD>
                 <TD><B><?php echo TO; ?>:<input type="text" name="bis" size="2" maxlength="2" value="<?php if (isset($bis)) { echo $bis; } ?>" onBlur="num_test(this);">
                     </B>
                 </TD
                </TR>
              </table>           <!--   end of  third Table in the table2 -->
	     </TD>
	    </TR>
            <!--  end of 3. Line -->
	  </table>
        <!--  end of inner table  -->
         </td>
       </TR>
</table>
<table border = 1>
  <tr bgcolor = <? echo BGCOLORH; ?>>
     <th> <?php echo ELEMENT; ?>
     </th>
     <th> <?php echo WERT; ?>
     </th>
  </tr>
  <tr bgcolor = <? echo BGCOLOR1; ?>>
     <td>
        <input type="radio" name="sel_typ" value = "String" <? if (empty($sel_typ)) echo " checked "; else echo m_c("String",$sel_typ); ?>>String
     </td>
     <td>
       <input type="text" name="wert" size="40" maxlength="80" value="<?php if (isset($wert)) { echo $wert; } ?>">
     </td>
  </tr>
  <TR bgcolor = <? echo BGCOLOR1; ?> >
     <td>
        <input type="radio" name="sel_typ" value = "DB" <? echo m_c("DB",$sel_typ); ?>><?php echo DBFELD; ?>
     </td>
     <td>
     <?php
       if (substr($id_neu,0,1) != 'B') { // This is a report
       ?>
       <select name = "feld" size=1>
       <?php
           $db->query($sql);   // was set from get_session_data();
	   $sqlw=$sql;         // look for tablename
	   $sqlw=substr(strstr($sqlw,"from"),5);
	   $sqlw=substr($sqlw,0,strcspn($sqlw," ")); 
           $res = $db->metadata($sqlw,false);
           $num = $db->num_fields();
           for ($i=0; $i<$num; $i++){
               echo "<option value=\"". $res[$i]["name"]."\" ".m_s($res[$i]["name"],$feld)." > ". $res[$i]["name"]. "</option>";
           }
       ?>
       </select>
       <?php
       } else {   // this is a block
        ?>
	  <input type="text" name="feld" size=40 maxlength=40 value = "<?php if (isset($feld)) { echo $feld; } ?>">
       <?php
       }
       ?>

        <input type ="checkbox" name="entschl" value="true" <?php if (!empty($entschl)) echo "checked"; ?>><?php echo DECODE; ?>
        <table bgcolor= #ccb140>
	   <tr><td colspan = 2> <?php echo(TOTAL); ?> <BR>
	             <input type ="checkbox" name="total" value="true" <?php if (!empty($total)) echo "checked"; ?>>Total</td></tr>
	   <tr><td>  <input type ="checkbox" name="o_score" value="true" <?php if (!empty($o_score)) echo "checked"; ?>>Overscore</td>
               <td>  <input type ="checkbox" name="u_score" value="true" <?php if (!empty($u_score)) echo "checked"; ?>>Underscore</td>
	       <td> <input type ="checkbox" name="bold" value="true" <?php if (!empty($bold)) echo "checked"; ?>>Bold</td>
	    </tr>
	</table>       
	 
     </td>
  </tr>
  <tr bgcolor = <? echo BGCOLOR1; ?> >
     <td>
        <input type="radio" name="sel_typ" value = "Term" <? echo m_c("Term",$sel_typ); ?>>Term

     </td>
     <td>
        <select name="term" size="1" >
       <?php    // show functions
           $db->query("select attrib from reports where typ='funct'");
           while ($db->next_record()) {
               $a_h = explode("|",$db->f("attrib"));
               echo "<option value=\"". $a_h[0]."\" ".m_s($a_h[0],$term)." > ". $a_h[0]. "</option>";
           }
       ?>
         </select>
     </td>
  </tr>
  <?php if (($report_type == "single") && (substr($id_neu,0,1) != 'B')) { //  show Textarea-element, if Report and single_typed ?>

  <tr bgcolor = <? echo BGCOLOR1; ?> >
     <td>
        <input type="radio" name="sel_typ" value = "Textarea" <? echo m_c("Textarea",$sel_typ); ?>>Textarea
     </td>
     <td>
        <select name="textarea" size="1" >
       <?php    // show functions
           $db->query("select attrib from reports where typ='funct'");
           while ($db->next_record()) {
               $a_h = explode("|",$db->f("attrib"));
               echo "<option value=\"". $a_h[0]."\" ".m_s($a_h[0],$term)." > ". $a_h[0]. "</option>";
           }
       ?>
         </select><?php echo WIDTH; ?>:
         <input type="text" name="width" size=3 maxlength=3 value="<?php if (isset($width)) { echo $width; } ?>"s onBlur="num_test(this);">
     </td>
  </tr>
<?php }
?>
     <?php
       if (substr($id_neu,0,1) != 'B') { // This is a report -> show block
       ?>

  <TR bgcolor = <? echo BGCOLOR1; ?> >
     <td>
        <input type="radio" name="sel_typ" value = "Block" <? echo m_c("Block",$sel_typ); ?>>Block
     </td>
     <td>
       <select name = "block" size=1>
       <?php
           $query = "select * from reports where typ = 'block'";
           $db->query($query);
	   while ($db->next_record()) {
             $h=explode("|",$db->f("attrib"));
             echo "<option value=\"". $h[0]."\"".m_s($h[0],$block)." > ". $h[0]. "</option>";
           }
       ?>
       </select>
       <?php
       };
        ?>
     </td>
  </tr>

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
<?php
       if (!((substr($id_neu,0,1) == 'B')||(substr($id_neu,0,1 ) == 'F'))){ // Only show test button for reports
       ?>

<input name="druck" type="submit" value="<? echo IT_DRUCK; ?>" onClick = "wopen();" >
<?php } ?>
</td>
</TR>
</table>
</center>
<HR size=5>
<input type = "hidden" name = "alternate" value = "<?php echo $alternate; ?>" >
<input type = "hidden" name = "attriba" value = "<?php echo $attrib; ?>" >
<input type = "hidden" name = "id_neu" value = "<?php echo $id_neu; ?>" >

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
  <th><? echo IT_ORD; ?> </TH>
  <th><? echo IT_LEN; ?></th>
  <th><? echo IT_X1; ?></th>
  <th><? echo IT_Y1; ?></th>
  <th>Total</th>
  <th><? echo IT_STRING; ?></th>
  <th cellspan=2>Action</th>
 </tr>
<?php
  ## Traverse the result set
  $rec_ar = array();
  $sort_ar = array("RH"=>"0","PH"=>"1","GH"=>"2","DE"=>"3","GF"=>"4","PF"=>"5","RF"=>"6");
  $query="select  * from reports where (typ = 'item' and id='".$id_neu."')";
  $db->query($query);
  while ($db->next_record()){
      $in = $db->f("attrib");
      $ine = explode("|",$in);
      $in_index =
      sprintf("%2s%2d%3d%3d%3d%3d%4s%1s%2%d%2d%1s%1s%1s%1s%5s",$sort_ar[$ine[1]],$ine[8],$ine[5],$ine[6],$ine[3],$ine[4],$ine[0],$ine[9],
              $ine[10], $ine[11], $ine[12], $ine[13], $ine[14], $ine[15],$ine[2]);
             // für sort nach 1) Art 2) Ord 3) X1 4) Y1 .... hergerichtet
      $rec_ar[$in_index] = $in;
   }
  ksort($rec_ar,SORT_STRING);
  reset($rec_ar);
  $line= 0;   // line-number
  while (list ($key,$val) = each($rec_ar)){
    $h = explode("|",$val);
    $it_typ=$h[0];
    $it_art = $h[1];
    $it_font = $h[2];
    $it_fontsize = $h[3];
    $it_zahl = $h[4];
    $it_x1 = $h[5];
    $it_y1 = $h[6];
    if (in_array($it_typ, array("String","DB","Term","Block","Textarea")))   $it_str = $h[7];
    $it_ord = $h[8];
    $it_entschl = $h[9];
    $it_von = $h[10];
    $it_bis = $h[11];
    $it_total = $h[12];
    $it_o_score = $h[13];
    $it_u_score = $h[14];
    $it_bold = $h[15];        
    $line ++;
    $bgcolor = BGCOLOR1;     // define the color of the row
    $line % 2  ? 0: $bgcolor = BGCOLOR2;
?>
 <!-- existing items -->
 <form name="edit" method="post" action="<? echo REPGENDIR."/repgen_seitec.php?".SID."&report_type=".$report_type; ?> ">
 <tr valign=middle align=left bgcolor = "<? echo $bgcolor; ?>">
    <td> <?php echo $it_typ;  if (!empty($it_entschl)) echo ENCRYPT; ?>
           <input type="hidden" name="id1" value="<?php echo $id_neu; ?>">
           <input type="hidden" name="attrib" value="<?php echo $val; ?>">
           <input type = "hidden" name = "id_neu" value = "<?php echo $id_neu; ?>" >
    </TD>
    <td> <?php echo $it_art; ?></TD>
    <td> <?php echo $it_font; ?></TD>
    <td> <?php echo $it_fontsize; ?></TD>
    <td> <?php echo $it_ord; ?></TD>
    <td> <?php echo $it_zahl; ?></TD>
    <td> <?php echo $it_x1; ?></TD>
<?php if ($it_y1 != "") echo " <td>". $it_y1."</TD>";
          else                echo "<TD>.</TD>";
     echo "<TD>"; 
     if ($it_total !="") echo $it_total;   // for column Total
     else echo ".";
     echo "</TD>";
     
     if (in_array($it_typ, array("Line","Rectangle"))){
              ?>
         <td>.</td>
<?php
     } else {
?>
    <td> <?php if (!(empty($it_von)||empty($it_bis))) echo $it_str."(".$it_von."-".$it_bis.")";
                else echo $it_str; ?></TD>
<?
   }

?>
    <TD>
        <input type="submit" name="delete" value="<?php echo DELETE ?>" align="center">
      </td>
<?php if (in_array($it_typ,array("String","DB","Term","Block","Textarea"))) { ?>
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
<?php page_footer(); ?>
</body>
</html>
