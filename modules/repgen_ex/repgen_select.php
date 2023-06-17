<?php session_start();
/* Selection of one of the stored reports
 * repgen_select.php for PHP Report Generator
   Bauer, 22.1.2002
   Version 0.2
*/

/*
 *  Select routine for Report generator repgen.
 *
 *  shows all reports with buttons for change, delete of every report and create
 *  a new report.
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

/* If this page is called direct, switch to repgen_main.php
*/
require_once("repgen_const.inc");
require_once("repgen_def.inc");


if ((empty($database) || empty($host)|| empty($user) || empty($password))) {
                         // not all fields have values
            $url=REPGENDIR."/repgen_index.php?".SID;
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_main.php back
            exit;

}


global $database, $host, $user, $pass;

function get_name($str)
{ // get name out of str
   $h = explode("|",$str);
   return $h[3];
}
function get_author($str)
{ // get author out of str
   $h = explode("|",$str);
   return $h[2];
}
function get_create_date($str)
{ // get datum out of str
   $h = explode("|",$str);
   return $h[1];
}
function get_kurzn($str)
{ // get short out of str
   $h = explode("|",$str);
   return $h[0];
}

function get_print_format($str)
{ // get print_format out of str
   $h = explode("|",$str);
   return $h[4];
}
function get_print_size($str)
{ // get print_size out of str
   $h = explode("|",$str);
   return $h[5];
}
function get_report_type($str)
{ // get report_type out of str
   $h = explode("|",$str);
   return $h[6];
}



###
### Submit Handler
###

## Check if there was a submission
while ( is_array($HTTP_POST_VARS)
     && list($key, $val) = each($HTTP_POST_VARS)) {
  switch ($key) {

  ## Edit report
  case "change":
            $db = new DB_Repgen;
            $db->connect($database,$host,$user,$password);
            $id = trim($id); $id_neu=trim($id_neu);
            $url=REPGENDIR."/repgen_create.php?".SID;
	    switch (substr($id,0,1)) {  // Change a Block
              case 'B':
                $url=REPGENDIR."/repgen_createblock.php?".SID;
                $url.="&id_neu=$id&short=".urlencode(get_kurzn($attr))."&long=".urlencode(get_name($attr))."&author=".urlencode(get_author($attr));
                $url .= "&id=".$id;
                break;
              case 'F':   // Change Function
                $url=REPGENDIR."/repgen_createfunct.php?".SID;
                $url.="&id_neu=$id&short=".urlencode(get_kurzn($attr))."&long=".urlencode(get_name($attr))."&author=".urlencode(get_author($attr));
                $url .= "&id=".$id;
                break;
              default:   // Change report
                $url=REPGENDIR."/repgen_create.php?".SID;
                $url.="&id_neu=$id&short=".urlencode(get_kurzn($attr))."&long=".urlencode(get_name($attr))."&author=".urlencode(get_author($attr));
                $url .= "&print_format=".get_print_format($attr)."&print_size=".get_print_size($attr);
                $url .= "&report_type=".trim(get_report_type($attr))."&id=".$id;
                $query="select  * from reports where (typ = 'select' and id = '".$id."' )";
                $db->query($query);
                $db->next_record();
                $url .= "&sql=".urlencode(trim($db->f("attrib")));
                $query="select  * from reports where (typ = 'group' and id = '".$id."' )";
                $db->query($query);
                $db->next_record();
                $h = explode("|", $db->f("attrib"));
                $url .= "&group=".trim($h[0])."&group_type=".trim($h[1]);
	    }
//echo $url; exit;
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_create.php
            exit;

          break;
  case "delete":
                // deletes report(all records) with id from table reports
            $url=REPGENDIR."/repgen_del.php?".SID;
            $url .= "&id=".trim($id)."&attr=".urlencode($attr);
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_del.php
            exit;

          break;
  case "copy":
                // copy report(all records) with id from table reports
                // read the records of the selected report
            switch (substr($id,0,1)){
	       case 'B': $type = 'block';
                         break;
               case 'F': $type = 'funct';
                         break;
	        default: $type=  'info';
                         break;
            }
	    $dbn = new DB_Repgen;
            $dbn->connect($database,$host,$user,$password);
	    $query = "select * from reports where id ='$id'";
            $dbn->query($query);
            $typ_ar = array();$attrib_ar = array();
            $l=0;
            while ($dbn->next_record()){
                    $typ_ar[$l] = $dbn->f("typ");
                    $attrib_ar[$l] = $dbn->f("attrib");
                    if (trim($typ_ar[$l]) == $type) {
                            $h = explode("|",$attrib_ar[$l]);  // change short
                            $h[0]= COPYV." ".$h[0];
                            $h[1] = date("Y-m-d"); // correct creation date
                            $attrib_ar[$l] = implode("|",$h);
                    }
                    $l++;
            }
                // change  $id to a new value and $short to "COPY".$short
            $dbn->query("select typ from reports where typ = '$type'");
            $n = $dbn->num_rows()+1;
	    if (substr($id,0,1) == 'B')$n = 'B'.$n;
	    if (substr($id,0,1) == 'F')$n = 'F'.$n;
             // write the records of the new report
            for ($k=0;$k<$l;$k++) {
                $query="insert into reports values('$n','$typ_ar[$k]','$attrib_ar[$k]')";
                $dbn->query($query);
            }

          break;


          
  case "close":
            session_destroy();
            $url=REPGENDIR."/index.php?".SID;
            header("Location: http://$HTTP_HOST".$url);  // switches to index.php
            exit;

          break;
  case "new":
            // create a new report Id
            $dbn = new DB_Repgen;
            $dbn->connect($database,$host,$user,$password);
            $dbn->query("select typ,id from reports where typ = 'info' order by id");
            while($dbn->next_record()){$n = $dbn->f("id");}
            $n = $n +1;
            $url=REPGENDIR."/repgen_create.php?".SID;
            $url.="&id_neu=$n&short=&long=&author=&group=&sql=&print_format=&print_size=&report_type=&id=&group_type=";
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_create.php
            exit;

          break;
  case "newblock":
            // create a new blockId, e.g. B3
            $dbn = new DB_Repgen;
            $dbn->connect($database,$host,$user,$password);
            $dbn->query("select typ from reports where typ = 'block'");
            $n = $dbn->num_rows()+1;
	    $n = 'B'.$n;
            $url=REPGENDIR."/repgen_createblock.php?".SID;
            $url.="&id_neu=$n&short=&long=&author=&group=&sqlf=&print_format=&print_size=&report_type=&id=&group_type=";
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_createblock.php
            exit;

          break;
  case "newfunct":
            // create a new funct e.g. B3
            $dbn = new DB_Repgen;
            $dbn->connect($database,$host,$user,$password);
            $dbn->query("select typ from reports where typ = 'funct'");
            $n = $dbn->num_rows()+1;
	    $n = 'F'.$n;
            $url=REPGENDIR."/repgen_createfunct.php?".SID;
            $url.="&id_neu=$n&short=&long=&author=&group=&sql=&print_format=&print_size=&report_type=&id=&group_type=";
            header("Location: http://$HTTP_HOST".$url);  // switches to repgen_createfunct.php
            exit;

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

<strong><?php echo DESCRIPT ?></strong>
</center> <br>
<form name="navigate" method="post" action="repgen_select?<?=SID ?>">
<table align = center> <TR>
     <TD align=center>
        <input type="submit" name="new" value="<?php echo NEU; ?>" align="center">

    </td>
     <TD align=center>
        <input type="submit" name="newblock" value="<?php echo NEUBLOCK; ?>" align="center">

    </td>
     <TD align=center>
        <input type="submit" name="newfunct" value="<?php echo NEUFUNKT; ?>" align="center">
    </td>
    <td align="right">
        <input type="submit" name="close" value="<?php echo LOGOUT; ?>" align="left" >
    </td>
    </tr>
    </table>
<center><BR>    <?php echo SEL_SELECT; ?><BR> <?php echo SEL_COLOR; ?>   </center>

</form>
<br>

<table border=1 bgcolor="#eeeeee" align="center" cellspacing=2 cellpadding=2 width=540>
 <tr valign=top align=left>
  <th><? echo SHORT; ?></th>
  <th><? echo LONG; ?></th>
  <th><? echo AUTHOR; ?></th>
  <th><? echo CREATIONDATE; ?></th>
  <th>Action</th>
 </tr>
<?php

  ## Traverse the result set
## Get a database connection
  $db = new DB_Repgen;
  $db->set_variables($database,$host,$user,$password);
  $db->connect($database,$host,$user,$password);
  $query="select  * from reports where (typ = 'info' or typ = 'block' or typ = 'funct') order by id";
  $db->query($query);
  while ($db->next_record()){
     $attrib_h = $db->f("attrib");
     switch (substr($db->f("id"),0,1)){
         case 'B':    $bgcolor="cfff8a";
                      break;
         case 'F':    $bgcolor="ffaa49";
                      $h= explode("|",$attrib_h);
                      $h[4]="";       // remove the PHP-statement part because of troubles with ' and "
                      $attrib_h=implode("|",$h);
                      break;
         default:  $bgcolor="dedede";
                   break;
     }

?>
 <!-- existing reports -->
 <form name="edit" method="post" action="repgen_select?<?=SID ?>">
 <tr valign=middle align=left>
    <td <?php echo "bgcolor=$bgcolor"; ?>> <?php echo get_kurzn($attrib_h); ?> <input type="hidden" name="id" value="<?php echo $db->f("id"); ?>"></TD>
    <td <?php echo "bgcolor=$bgcolor"; ?>> <?php echo get_name($attrib_h); ?></TD>
    <td <?php echo "bgcolor=$bgcolor"; ?>> <?php echo get_author($attrib_h); ?></TD>
    <td <?php echo "bgcolor=$bgcolor"; ?>> <?php echo get_create_date($attrib_h); ?></TD>
        <input type="hidden" name="attr" value ="<?php echo ($attrib_h); ?>">
        <input type="hidden" name="id" value ="<?php echo $db->f("id"); ?>">
    <td >
        <input type="submit" name="change" value="<?php echo CHANGE ?>" align="left" >
    </td><td>
        <input type="submit" name="delete" value="<?php echo DELETE ?>" align="center">

    </td>
    <td>
        <input type="submit" name="copy" value="<?php echo COPY ?>" align="center">

    </td>

</tr>
</form>

<?php

 }  // end of while

?>
</table>
<?php page_footer(); ?>

</body>
</html>
