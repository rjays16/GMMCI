<?php
/* Include Datei repgen.inc for PHP Report Generator
   Bauer, 7.2.2002
   changed 19.11.2002
   Version 0.44
   Bauer (total, wordwrap)
*/
session_start();
require_once "repgen_def.inc";
require_once "class.ezpdf.php";   // PDF class
// Define constants
define("RH","RH"); // Repport Header konstante
define("PH","PH"); // Page Header konstante
define("GH","GH"); // Group Header konstante
define("DE","DE"); // Detail Header konstante
define("GF","GF"); // Group Foot konstante
define("PF","PF"); // Page Foot konstante
define("RF","RF"); // Report Foot konstante
//      Konstante for Report file
define("SEL","select"); // select konstante
define("INFO","info"); // info  konstante
define("GRUP","group"); // group konstante
define("ITEM","item"); // item konstante
define("STR","String"); // Type of item
define("TERM","Term"); // Type of item
define("TEXT","Textarea"); // Type of item
define("LINE","Line"); // Type of item
define("RECT","Rect"); // Type of item
define("IMAGE","Img"); // Type of item
define("DB","DB"); // Type of item
define("BLOCK","Block"); // Type of item

define("KLASSE","class");  // Type of report(more records on a page)
define("TABLE","classtable");  // Type of report(more records on a page) with Boxes
define("BEAM","classbeam");    // type of report with BEAM
define("GRID","classgrid");    // type of report with BEAM and BOXES
define("SINGLE","single"); //type of report( one record on a page)
define("NEWPAGE","newpage"); // type of group-print(every group begins on a new page
define("NOPAGE","nopage");   // type of group-print(no new page beginning)
define("DISTANZ",5);         // Distanz between fields in Report
define("BLOCKDIST",5);      // Distance of Blocks
define("POS_START",30);      // First Print Position in a page
define ("POS_END",30);      // last Print Position in a page
define ("POS_LEFT",30);      // first Print Position at left of line
define ("POS_RIGHT",30);      // last Print Position at right of line
define ("YDIST",4);           // distance between two lines

$db=0;
global $database,$host, $user, $password;

$p_formate = array("A3"=>"842|1190","A4"=>"595|842","A5"=>"421|595","a3"=>"842|1190","a4"=>"595|842","a5"=>"421|595"); // Paperformat
## my_error($msg):
##
## Display error messages

  function my_error($msg) {
?>
  <table border=0 bgcolor="#eeeeee" align="center" cellspacing=0 cellpadding=4 width=540>
   <tr>
    <td><font color=#FF2020><?php print $msg ?></font></td>
   </tr>
  </table>
  <BR>
<?php
}

## my_msg($msg):
##
## Display success messages
  function my_msg($msg) {
?>
 <table border=0 bgcolor="#eeeeee" align="center" cellspacing=0 cellpadding=4 width=540>
  <tr>
   <td><font color=#008000>O.K.: <?php print $msg ?></font></td>
  </tr>
 </table>
 <br>
<?php
}


/*
 *
 * Class Item stores one Item of the report
 *
 *
 *
*/
class item {
/* this is an Item of the Report
*/
var $type;         // Itemtype: String = S, Term = T or DB-Feld = D
var $art;          // Art: Page Head = PH   Group Head = GH
                   //      Detail = DE      Group Foor = GF
                   //      PageFoot = PF
var $fontname;     //  Fontname
var $fontsize;     // Fontsize
var $fieldlength;  //  Number of Characters of the field
var $xpos;         // x-Position of first character in points or
                   //            column number, if Group
var $ypos;         // y-Position of first character in points or
                   //            line number, if Group
var $string;       // String to print or Term to print or DB-fieldname to print
                   //        depending on $type
var $term;         // Function code if $type = term
var $ord;          // Order of Item in Detail
var $entschl;      // if true -> take it as key to find value
var $von;          // 2. value for substring of text
var $bis;          // 3. value for substring of text
//-----------------     for Total sums -----------
var $total_item;        // true:this item wants total sum
var $o_score;      // line over the sum
var $u_score;      // line under the score
var $bold;         // should be printed bold
//---------------     end of toal sum ----------------
var $filename;     // filename of Jpeg-image
function get_type(){ return $this->type;}
function set_type($t){ $this->type = $t;}
function get_art(){ return $this->art;}
function set_art($a){ $this->art = $a;}
function get_fontname(){return $this->fontname;}
function set_fontname($f){$this->fontname = $f;}
function get_fontsize(){ return $this->fontsize;}
function set_fontsize($s){ $this->fontsize = $s;}
function get_fieldlength(){return $this->fieldlength;}
function set_fieldlength($f){$this->fieldlength = $f;}
function get_xpos(){  return $this->xpos;}
function set_xpos($x){ $this->xpos = $x;settype($this->xpos, "double");}
function get_ypos(){ return $this->ypos;}
function set_ypos($t){ $this->ypos = $t;settype($this->ypos, "double");}
function get_string(){ return $this->string;}
function set_string($t){ $this->string = $t;}
function get_ord(){ return $this->ord;}
function set_ord($t){ $this->ord = $t;}
function get_entschl(){ return $this->entschl;}
function set_entschl($n){$this->entschl= $n;}
function get_von(){ return $this->von;}
function set_von($n){$this->von= $n;}
function get_bis(){ return $this->bis;}
function set_bis($n){$this->bis= $n;}

function get_total_item(){ return $this->total_item;}
function set_total_item($n){$this->total_item= $n;}
function get_o_score(){ return $this->o_score;}
function set_o_score($n){$this->o_score= $n;}
function get_u_score(){ return $this->u_score;}
function set_u_score($n){$this->u_score= $n;}
function get_bold(){ return $this->bold;}
function set_bold($n){$this->bold= $n;}

function get_term(){ return $this->term;}
function set_term($n){$this->term = $n;}

}
/*
 *
 * Class Report reads a report-defintion from the table reports and interprets all read items
 *
 *
 *
*/

class report {
//  class for reports
var $begin = true;   //  switch for beginning
var $with_ord = false; // switch for ord
var $name;           //  reportname
var $long_name;      // long report name
var $datum;           // Creation date of the report
var $author;         // author of the report
var $report_format;  // Format of the report Portrait or Landscape
var $height;         // Height of one page in points
var $width;          // Width of one page in points
var $paperformat;    // Format of paper (a4,a3..)
var $select;         // select-command for the data
var $report_typ;     // Type of report (class or single)
var $pdf;            // PDF-pointer
var $group;          // fieldname of group
var $group_old = "";      // old group for changing group
var $group_new;      // new group for changing group
var $group_type;     // Type of group: newpage -> new page for every group
                     //                nopage  -> group on the same page
var $rh_items=array();       // report header Items array
var $ph_items=array();       // page header Items array
var $gh_items=array();       // group header Items array
var $de_items=array();       // detail header Items array
var $gf_items=array();       // group footer Items array
var $pf_items=array();       // page footer Items array
var $rf_items=array();       // report footer Items array
var $block_items=array();       // block Items array
var $rh_i_items;     // report header index for items;
var $ph_i_items;     // page header index for items;
var $gh_i_items;     // group header index for items;
var $de_i_items;     // detail index for items;
var $gf_i_items;     // group foot index for items;
var $pf_i_items;     // page foot index for items;
var $rf_i_items;     // report foot index for items;
var $a = array();
// variables for drawing the Table in classtables
var $x_tab = array(); // values of vertical line in table
var $y_dist;          // distance between two horizontal lines
var $y_pos;	      // current y position	
var $x_left=POS_START;
var $x_right=POS_END;
var $str_type = array(STR,DB,TERM,TEXT);
var $dark=false;      // $dark = true  -> gray background
var $subtotal = array(); // for subtotals of numeric fields
var $total = array();    // for totals of numeric fields
var $subcount; // counts number of records of groups
var $count;    // counts number of records of the report


function get_name(){ return $this->name; }
function set_name($n){  $this->name= $n;}
function get_long_name(){ return $this->long_name;}
function set_long_name($n){ $this->long_name= $n;}
function get_datum(){  return $this->date;}
function set_datum($d){  $this->date= $d;}
function get_author(){  if ($this->author == "") return "author"; else return $this->author;}
function set_author($n){ $this->author= $n;}
function get_report_format(){ return $this->report_format;}
function set_report_format($f){  $this->report_format= $f;}
function get_paper_format(){ return $this->paper_format;}
function set_paper_format($f){  $this->paper_format= $f;}
function get_height(){return $this->height;}
function set_height($n){  $this->height= $n; settype($this->height, "double");}
function get_width(){return $this->width;}
function set_width($n){ $this->width= $n;settype($this->width, "double");}
function get_select(){return $this->select;}
function set_select($n){  $this->select= $n;}
function get_group(){ return $this->group;}
function set_group($n){$this->group= $n;}
function is_set_group(){ $t=true;
                         if (empty($this->group))
			        $t = false;
			 return $t;
			}
function get_report_typ(){ return $this->report_typ;}
function set_report_typ($n){$this->report_typ= $n;}
function get_group_new(){ return $this->group_new;}
function set_group_new($n){$this->group_new= $n;}
function get_group_old(){ return $this->group_old;}
function set_group_old($n){$this->group_old= $n;}
function get_group_type(){ return $this->group_type;}
function set_group_type($n){$this->group_type= $n;}


function do_decode($art,$begriff) { // decode database-fields with help of table schluess
   global $db;
         $db->query("select bezeichnung from schluessel where (art='$art' and begriff='$begriff')");
         $db->next_record();
         return trim($db->f("bezeichnung"));
}
function sort_de_items() { 
$h_item = array();
$l=0;
foreach ($this->de_items as $a){
    $key= $a->get_ord().$l;  // make key for sort of ordered fields
    $l++;
    $h_item[$key] = $a;
}
ksort($h_item);
$this->de_items = array_values($h_item);
  }   // sort of de_items

function get_first_item($s){
        //  returns the first item of the specified type $s
    switch ($s) {
      case RH: $a = $this->rh_items[0];$this->rh_i_items = 0;break;    
      case PH: $a = $this->ph_items[0];$this->ph_i_items = 0;break;
      case GH: $a = $this->gh_items[0];$this->gh_i_items = 0; break;
      case DE: $a = $this->de_items[0];$this->de_i_items = 0; break;
      case GF: $a = $this->gf_items[0];$this->gf_i_items = 0; break;
      case PF: $a = $this->pf_items[0];$this->pf_i_items = 0; break;
      case RF: $a = $this->rf_items[0];$this->rf_i_items = 0; break;
    }
    return $a;
    }
function get_next_item($s){
        // returns next item of the specified type $s
    switch ($s) {
      case RH: $a = $this->rh_items;$this->rh_i_items ++; $i = $this->rh_i_items;break;    
      case PH: $a = $this->ph_items;$this->ph_i_items ++; $i = $this->ph_i_items;break;
      case GH: $a = $this->gh_items;$this->gh_i_items ++; $i = $this->gh_i_items; break;
      case DE: $a = $this->de_items;$this->de_i_items ++; $i = $this->de_i_items; break;
      case GF: $a = $this->gf_items;$this->gf_i_items ++; $i = $this->gf_i_items; break;
      case PF: $a = $this->pf_items;$this->pf_i_items ++; $i = $this->pf_i_items; break;
      case RF: $a = $this->rf_items;$this->rf_i_items ++; $i = $this->rf_i_items; break;      

    }
    if ($i < $this->get_item_count($s))
         {return $a[$i];}
       else{return NULL;}
}
function get_item_count($s){
         // returns the number of items in the specified typ $s

    switch ($s) {
      case RH: $a = sizeof($this->rh_items);break;    
      case PH: $a = sizeof($this->ph_items);break;
      case GH: $a = sizeof($this->gh_items); break;
      case DE: $a = sizeof($this->de_items); break;
      case GF: $a = sizeof($this->gf_items); break;
      case PF: $a = sizeof($this->pf_items); break;
      case RF: $a = sizeof($this->rf_items);break;      

    }
    return $a;
  }
function set_item($s,$item){
    // $s is type
    // $item is item

    switch ($s) {
      case RH: array_push($this->rh_items,$item);break;    
      case PH: array_push($this->ph_items,$item);break;
      case GH: array_push($this->gh_items,$item); break;
      case DE: array_push($this->de_items,$item);break;
      case GF: array_push($this->gf_items,$item); break;
      case PF: array_push($this->pf_items,$item); break;
      case RF: array_push($this->rf_items,$item); break;
      case BLOCK: array_push($this->block_items,$item); break;
    }
  }
function insert_blocks() {  // read blocks and insert into item arrays
    global $database,$host,$user,$password;
    $blocks = array();
    // read all stored blocks and save id and attrib in $blocks
    $dbn = new DB_Repgen;
    $dbn->set_variables($database, $host, $user, $password);
    $dbn->connect($database,$host, $user, $password);
    $query = "select * from reports where  typ = 'block'";
    $dbn->query($query);
    while($dbn->next_record()) {
       $h = explode("|",$dbn->f("attrib"));
       $blocks[$h[0]] = trim($dbn->f("id"));    // $h[0] is short name of block
    }   

    foreach ($this->block_items as $item) {
    // get  items from block_array 
    // and read their items from the reports table
       $h = $item->get_string();
       $id = $blocks[$h];
       $query = "select * from reports where id = '$id' and typ <> 'block'";
       $dbn->query($query);
       while ($dbn->next_record()) {
         // insert the read items into the appropriate array
                        $h = explode("|",$dbn->f("attrib"));
                        $it = new item;
                        $it->set_type($h[0]);
                        $it->set_art($h[1]);
                        $it->set_fontname($h[2]);
                        $it->set_fontsize($h[3]);
                        $it->set_fieldlength($h[4]);
                        $it->set_xpos($h[5]);
                        $it->set_ypos($h[6]);
                        $it->set_string($h[7]);
                        $it->set_ord($h[8]);    // order of item in DE
                        $it->set_entschl($h[9]);
                        $it->set_von($h[10]);
                        $it->set_bis($h[11]);
                        $it->set_total_item($h[12]);
                        $it->set_o_score($h[13]);
                        $it->set_u_score($h[14]);
                        $it->set_bold($h[15]);									
                        $this->set_item($h[1],$it);  // otherwise add item to the ph, de,... array
       }
   }

}  
function make_terms_block(&$it,&$blocks) {
    for ($i=0; $i<count($it); $i++) {        // seek in block
       if ($it[$i]->get_type() == 'Term') {
            $h = $it[$i]->get_string();
            $it[$i]->set_term($blocks[$h]);  // Code is in $term
       }
    }
}

function make_terms($db) {   // replace $item->string with the functioncode, if set_type = term
global $database, $host, $user, $password;
    $blocks = array();
    // read all stored blocks and save $short and Code in $blocks
    $dbn = new DB_Repgen;
    $dbn->set_variables($database, $host, $user, $password);
    $dbn->connect($database,$host, $user, $password);
    $query = "select * from reports where typ = 'funct'";
    $dbn->query($query);
    while($dbn->next_record()) {
       $h = explode("|",$dbn->f("attrib"));
       $blocks[$h[0]] = $h[4];    // $h[0] is short name of block, $h[4] is function code
			 echo "eval:".stripslashes($blocks[$h[0]])."<br>";
       eval(stripslashes($blocks[$h[0]]));   // declare function,  only once per run
    }
    $this->make_terms_block($this->rh_items,$blocks);    
    $this->make_terms_block($this->ph_items,$blocks);
    $this->make_terms_block($this->gh_items,$blocks);
    $this->make_terms_block($this->de_items,$blocks);
    $this->make_terms_block($this->gf_items,$blocks);
    $this->make_terms_block($this->pf_items,$blocks);
    $this->make_terms_block($this->rf_items,$blocks);
}
function do_report($db,$id)  {
    // reads report from database and store in variables
    global $error,$p_formate;
    $this->count=0;   // number of records = 0
    $query = "select typ, attrib from reports where id = '".$id."'";
    $db->query($query);
    $e = $db->Error;
    if (!empty($e)) {
        // Database Error
        my_error($e."<BR> EXIT1!!!");
        exit;
    }
    if ($db->num_rows() == 0) {
//    if ($db->num_rows($result) == 0) {
        // report is not declared
        my_error($e."<BR> Report does not exist !! id=".$id);
        exit;
    }
    while ( $db->next_record()) {
                                // read the report, store values
    $typ = trim($db->f("typ"));
    $attrib = trim($db->f("attrib"));
    switch ($typ) {
        case SEL :           // select record
                        $this->set_select(strtr($attrib,"^","'"));
                        break;
        case INFO   :        // info record
                        $h = explode("|",$attrib);
                        $this->set_name($h[0]);
                        $this->set_datum($h[1]);
                        $this->set_author($h[2]);
                        $this->set_long_name($h[3]);
                        $this->set_paper_format($h[5]);
                        $h1 = $p_formate[$h[5]];
                        $h2 = explode("|", $h1);
                        $this->set_width($h2[0]);
                        $this->set_height($h2[1]);
                        $this->set_report_format($h[4]);
                        if (strtolower($h[4])== "landscape"){
                                            $a=$this->get_width();
                                            $this->set_width($this->get_height());
                                            $this->set_height($a);
                        }
                        $this->set_report_typ($h[6]);
                        break;
        case GRUP  :         // group record
                        $h = explode("|",$attrib);
                        $this->set_group($h[0]);
                        $this->set_group_type($h[1]);
                        break;
        case ITEM   :        // item record
                        $h = explode("|",$attrib);
                        $it = new item;
                        $it->set_type($h[0]);
                        $it->set_art($h[1]);
                        $it->set_fontname($h[2]);
                        $it->set_fontsize($h[3]);
                        $it->set_fieldlength($h[4]);
                        $it->set_xpos($h[5]);
                        $it->set_ypos($h[6]);
                        $it->set_string($h[7]);
                        $it->set_ord($h[8]);    // order of item in DE
                        if (!empty($h[8])) $this->with_ord = true;
                        $it->set_entschl($h[9]);
                        $it->set_von($h[10]);
                        $it->set_bis($h[11]);
                        $it->set_total_item($h[12]);
                        $it->set_o_score($h[13]);
                        $it->set_u_score($h[14]);
                        $it->set_bold($h[15]);									
                        if ($it->get_total_item() == "true") {
			    $this->subtotal[$it->get_string()] = 0;
			    $this->total[$it->get_string()] =0;
			}
			if ($it->get_type() == BLOCK) {  // add item to block-array 
			   $this->set_item(BLOCK,$it);
			   }
			else {  
                           $this->set_item($h[1],$it);  // otherwise add item to the ph, de,... array
			   }
                        break;

        default       : break;
    }
   }// end of while
$this->insert_blocks();  // inserts blocks into item-array
$this->make_terms($db);
$this->sort_de_items();

  }
/*
 *     methods for printing
 *
 *
*/


function lineOfTextWrap($width,$size,$text,$justification='left',$angle=0,$x=0,$y=0){
   // This is a copy from the function addTextWrap() of class.pdf.php without the printing function addText()
   // for compatibility.
   // The height of an item with all wrapped lines is returned.
   
  // this will display the text, and if it goes beyond the width $width, will backtrack to the 
  // previous space or hyphen, and return the remainder of the text.

  // $justification can be set to 'left','right','center','centre','full'

  // need to store the initial text state, as this will change during the width calculation
  // but will need to be re-set before printing, so that the chars work out right
  $store_currentTextState = $this->pdf->currentTextState;

  if (!$this->pdf->numFonts){$this->pdf->selectFont('./fonts/Helvetica');}
  if ($width<=0){
    // error, pretend it printed ok, otherwise risking a loop
    return '';
  }
  $w=0;
  $break=0;
  $breakWidth=0;
  $len=strlen($text);
  $cf = $this->pdf->currentFont;
  $tw = $width/$size*1000;
  for ($i=0;$i<$len;$i++){
    $f=1;
    $directive = $this->pdf->PRVTcheckTextDirective($text,$i,$f);
    if ($directive){
      if ($f){
        $this->pdf->setCurrentFont();
        $cf = $this->pdf->currentFont;
      }
      $i=$i+$directive-1;
    } else {
      $cOrd = ord($text[$i]);
      if (isset($this->pdf->fonts[$cf]['differences'][$cOrd])){
        // then this character is being replaced by another
        $cOrd2 = $this->pdf->fonts[$cf]['differences'][$cOrd];
      } else {
        $cOrd2 = $cOrd;
      }
  
      if (isset($this->pdf->fonts[$cf]['C'][$cOrd2]['WX'])){
        $w+=$this->pdf->fonts[$cf]['C'][$cOrd2]['WX'];
      }
      if ($w>$tw){
        // then we need to truncate this line
        if ($break>0){
          // then we have somewhere that we can split :)
          if ($text[$break]==' '){
            $tmp = substr($text,0,$break);
          } else {
            $tmp = substr($text,0,$break+1);
          }
          $adjust=0;
          $this->pdf->PRVTadjustWrapText($tmp,$breakWidth,$width,$x,$adjust,$justification);

          // reset the text state
          $this->pdf->currentTextState = $store_currentTextState;
          $this->pdf->setCurrentFont();
          return substr($text,$break+1);
        } else {
          // just split before the current character
          $tmp = substr($text,0,$i);
          $adjust=0;
          $ctmp=ord($text[$i]);
          if (isset($this->pdf->fonts[$cf]['differences'][$ctmp])){
            $ctmp=$this->pdf->fonts[$cf]['differences'][$ctmp];
          }
          $tmpw=($w-$this->pdf->fonts[$cf]['C'][$ctmp]['WX'])*$size/1000;
          $this->pdf->PRVTadjustWrapText($tmp,$tmpw,$width,$x,$adjust,$justification);
          // reset the text state
          $this->pdf->currentTextState = $store_currentTextState;
          $this->pdf->setCurrentFont();
	  return substr($text,$i);
        }
      }
      if ($text[$i]=='-'){
        $break=$i;
        $breakWidth = $w*$size/1000;
      }
      if ($text[$i]==' '){
        $break=$i;
        $ctmp=ord($text[$i]);
        if (isset($this->pdf->fonts[$cf]['differences'][$ctmp])){
          $ctmp=$this->pdf->fonts[$cf]['differences'][$ctmp];
        }
        $breakWidth = ($w-$this->pdf->fonts[$cf]['C'][$ctmp]['WX'])*$size/1000;
      }
    }
  }
  // then there was no need to break this line
  if ($justification=='full'){
    $justification='left';
  }
  $adjust=0;
  $tmpw=$w*$size/1000;
  $this->pdf->PRVTadjustWrapText($text,$tmpw,$width,$x,$adjust,$justification);
  // reset the text state
  $this->pdf->currentTextState = $store_currentTextState;
  $this->pdf->setCurrentFont();
//  $pdf->addText($x,$y,$size,$text,$angle,$adjust,$angle);
  return '';
}

function print_string_wrap($string, $x, $y,$fs,$width,$mode)
{
 // prints a string at the given position
// translates Umlaute into pdf-format
//   $uml = sprintf("%c%c%c%c%c%c%c",138,128,154,133,159,134,167);
//   $str=strtr($string,"äÄöÖüÜß", $uml);

    switch ($mode) {
        case "l": $m = 'left';break;
	case "c": $m='center';
	          break;
	case "r": $m= 'right';
		  break;
	default : $m = 'left';
	          break;
	}
     $r = $string;
     $y1 = $y;
     while ($r) {
            $r=$this->pdf->addTextWrap($x,$y1,$width, $fs,$r,$m);
	    $y1=$y1 - $fs;
	    }
}

function print_string($string, $x, $y,$fs)
{
 // prints a string at the given position
// translates Umlaute into pdf-format
//   $uml = sprintf("%c%c%c%c%c%c%c",138,128,154,133,159,134,167);
//   $str=strtr($string,"äÄöÖüÜß", $uml);

   $this->pdf->addText($x,$y, $fs,$string);
}

function set_font($font)
{   // sets the fontname
        $this->pdf->selectFont('./fonts/'.$font) ;

 }

function print_line($x1,$y1,$x2,$y2, $width)
{
 // draws line from $x1/$y1 to $x2/$y2
    $x1n = $x1; settype($x1n,"double");
    $y1n = $y1; settype($y1n,"double");
    $x2n = $x2; settype($x2n,"double");
    $y2n = $y2; settype($y2n,"double");
    $widthn = $width; settype($widthn,"double");  // width of line in points
    if ($widthn <=0) $widthn=1.0;
    $this->pdf->setLineStyle($widthn);
    $this->pdf->line($x1n, $y1n,$x2n, $y2n);
}

function print_rect($linewidth, $x1,$y1,$width,$height)
{
 // prints Rect from $x1/$y1 with width and height

    $x1n = $x1; settype($x1n,"double");
    $y1n = $y1; settype($y1n,"double");
    $heightn = $height; settype($heightn,"double");
    $widthn  = $width; settype($widthn,"double");
    $linewidthn = $linewidth; settype($linewidthn,"double");
    $this->pdf->rectangle($x1n, $y1n, $widthn, $heightn);

}

function print_table($pos,$h)
{ // draws a grid around the table
    $linewidthn = 1.0;
    $x_size = sizeof($this->x_tab);

    // horizontal lines
    $pos1 = $pos  + $this->y_dist - YDIST +1;
    $pos2 = $pos - $h ;
    $this->print_line($this->x_tab[0], $pos1, $this->x_tab[$x_size-1],  $pos1,$linewidthn);
    $this->print_line($this->x_tab[0], $pos2, $this->x_tab[$x_size-1],  $pos2,$linewidthn);
        // vertical lines
    for ($i=0;$i < $x_size; $i++) {
         $this->print_line( $this->x_tab[$i], $pos1, $this->x_tab[$i], $pos2,$linewidthn);
    }

}

function print_background($pos,$h,&$dark)
{   //prints with underground $pos=linke obere Ecke, $h=Tiefe
    $x_size = sizeof($this->x_tab);
    $width = $this->x_tab[$x_size-1] - $this->x_tab[0];
    if ($dark) {
                $this->pdf->setcolor(0.8,0.8,0.8);
    }else {
                $this->pdf->setcolor(1.0,1.0,1.0);
        }
   $this->pdf->filledRectangle($this->x_tab[0],$pos -$h + $this->y_dist-YDIST -1.0,$width,$h + 2.0);
   $this->pdf->setcolor(0.0,0.0,0.0);
   $dark= !$dark;
}




function do_print_item($item, $db, $xpos, $ypos)
{
// prints an item with the current data
 $f = $item->get_type();
  switch ($f) {
    case STR : $field = $item->get_string(); break; // String item
    case TERM:  // print Terms in String
    case TEXT:  // print Terms in Textarea with wrap
                   $h = stripslashes($item->get_term());
                   $h_a =strtok($h,"(");  // look if the function has a parameter
                   $h_a= strtok(")");     // $h_a is now the parameter
                   if (!empty($h_a)) {    // first parameter is $db
                        if (stristr($h_a,","))  { // parameter includes ,
                          $func = '$field='.$item->get_string().'($db,$this);';// two parameters
                        } else {   // one parameter = $db
                          $func = '$field='.$item->get_string().'($db);';// two parameters
                        }
                   } else {
                          $func = '$field='.$item->get_string().'();';
                   }

                  if (function_exists($item->get_string())){
                      @eval($func);   // execute function
                  } else {
                       $field="Error: Function does not exist!";
                  }
                  break; // function call()

    case DB  :  // Database field name
                  $field = trim($db->f($item->get_string()));
                  if ($item->get_entschl()) { // insert the value as field
                       $field = $this->do_decode($item->get_string(),$field);
                  }
               if ($item->get_total_item() == "true") {     // sum up for total fields
	           $this->subtotal[$item->get_string()] +=  $field;
	           $this->total[$item->get_string()] +=  $field;		   
               }
               break;


    default: break;
    }
    // shorten $field, if needed
    $sv = $item->get_von();
    $sb = $item->get_bis();
    if (!(empty($sv) || empty($sb)))
     {
            $field = substr($field,$sv - 1,$sb - $sv + 1);
    }

    if (in_array($f,$this->str_type)) {     // print strings  String modus
            $fn = $item->get_fontname();
            $fs = $item->get_fontsize();
            $this->set_font($fn);
            $fl = $item->get_fieldlength();
            if ($fl == "") {
                $this->print_string($field,$xpos,$ypos,$fs);  // for compatibility reason included
            } else {
                                     // we should print boxed
                list($fl,$mode) = sscanf($fl,"%d%s");
		if (empty($fl)) $mode="l";    // default is left mode
                if ($f != TEXT) {
                        $fh = str_repeat("w",$fl);
                        $w =$this->pdf->getTextWidth($fs,$fh);
                        if (!empty($fl)) {
                             $fieldh = $field;
                        } else
                           $fieldh = $field;
                        $gh=$this->print_string_wrap($fieldh, $xpos , $ypos,  $item->get_fontsize(),$w, $mode);
                } else {   // Term in Textarea

                   $this->print_string_wrap($field, $xpos, $ypos,$item->get_fontsize(),$fl,$mode);
                }
	   }
    } else { // Line-Modus
         switch ($f) {
             case LINE: $this->print_line($item->get_fontsize(),
                                          $item->get_fieldlength(),
                                          $item->get_xpos(),
                                          $item->get_ypos(),
                                          $item->get_fontname);
                        break;
             case RECT: $this->print_rect($item->get_fontname(),
                                          $item->get_fontsize(),
                                          $item->get_fieldlength(),
                                          $item->get_xpos(),
                                          $item->get_ypos());
                        break;
             case IMAGE:
                        $this->pdf->addJpegFromFile($item->get_string(),
                                          $item->get_xpos(),
                                          $item->get_ypos(),
                                          $item->get_fontsize(),
                                          $item->get_fieldlength()
                                          );
                        break;

             default  : break;
         }
    }
}

function de_space($db) {
// returns the overall heigth of the next detail line
       $it = $this->get_first_item(DE);
       $max = 0;
       do {    // go through all items 
            $zeilen = 0; 
            $r = trim($db->f($it->get_string()));  // get Item values
            $fl = $it->get_fieldlength();
	    $fs = $it->get_fontsize();
            list($fl,$mode) = sscanf($fl,"%d%s");  // part justification		 
            if (strlen($r) > $fl) {   // need wordwrap
              $fh = str_repeat("w",$fl);   // get the width of the item
              $this->set_font($it->get_fontname());          
              $fspix = $this->pdf->getTextWidth($fs, $fh);
              while ($r != "") {    //calculate the number of lines
                $r =$this->lineOfTextWrap($fspix,$fs, $r);
	        $zeilen ++;
	      }
	      $zeilen --;
           } 
	   $zeilen ++;
           $hoehe = $zeilen * $fs; // height = number of lines * fontsize + distance
           $max = max($max,$hoehe);    // $max is greatest height of an item
       } while ($it = $this->get_next_item(DE));
return $max;
}
function paragraph_space($typ)
{ // returns needed space for a paragraph in points without textwrap for DE
  $it = $this->get_first_item($typ);
  $max = 0;
  if ($it == NULL) { return 0;} // no item in array
  do{
       $this->set_font($it->get_fontname());
//       $yp = $it->get_ypos() + $this->pdf->getFontHeight($it->get_fontsize()) ; 
       $yp = $it->get_ypos() + $it->get_fontsize() ; 
       $max = max($max,$yp);
  } while ($it = $this->get_next_item($typ));
  return $max;
}


function print_new_page($db) {
        // prints Page footer, closes page, open new page and prints Page Header
          $this->do_print($db,PF);
          $pagenr++;
          $this->do_print($db,PH);
	  $this->dark = false;
}
function make_x_tab()
{   // calculates the highest fontsize in a line and
    // establish the array x_tab
    $ah = $this->get_first_item(DE);
    $i = 0;
    $x_pos = POS_START-2;
    do {
     if (in_array($ah->get_type(),$this->str_type)) {
          $wh1= $ah->get_xpos();  // Compiler needed  this construction with empty
	  if (empty($wh1)) {      // no xpos for this item -> order number must exist
	     $this->x_tab[$i] = $x_pos ;    // set x_pos for lines in table
             $fh = str_repeat("w",$ah->get_fieldlength());
             $this->set_font($ah->get_fontname());          
             $w =$this->pdf->getTextWidth($ah->get_fontsize(),$fh);
	     $x_pos = $x_pos + $w + DISTANZ;

	  } else {
              $this->x_tab[$i] = $ah->get_xpos() -2;  // vertical line in table
              $fh = str_repeat("w",$ah->get_fieldlength());
              $this->set_font($ah->get_fontname());          
              $w =$this->pdf->getTextWidth($ah->get_fontsize(),$fh); 
	      $x_pos = max($x_pos,$this->x_tab[$i] + $w + DISTANZ);
	  }
          $i++;
     }
    } while ($ah=$this->get_next_item(DE));
    if ($i==1) {
            $this->x_tab[1] = $this->get_width() - POS_RIGHT;
    } else {
      $this->x_tab[$i] = $x_pos;  // most right vertical line
    }
    sort($this->x_tab);    // Sort the array, so that x_tab[0] has the lowest value
    reset($this->x_tab);
}

function print_record($db,$table, $height) { // prints one record in detail block
       if (($this->y_pos - $this->de_space($db))< (POS_END + $this->paragraph_space(PF))) {
             $this->print_new_page($db);
             $this->y_pos -= $this->paragraph_space(DE);
       }
       if (($this->get_report_typ() == BEAM) || ($this->get_report_typ() == GRID)) {// print background
		 $this->print_background($this->y_pos, $height,$this->dark);
		 $this->print_table($this->y_pos, $height);
		 }

       $ah = $this->get_first_item(DE);
       $i=0;
       do {
               // navigate thru all items
         if ($table) {
               if ($this->with_ord) {
                   $this->do_print_item($ah, $db, $this->x_tab[$i],$this->y_pos+$ah->get_ypos());
               }  else {
                   $this->do_print_item($ah, $db, $ah->get_xpos(),$this->y_pos+$ah->get_ypos());
               }
               $i++;
         } else {
               $this->do_print_item($ah, $db, $ah->get_xpos(),$ah->get_ypos());
         }

       }  while ($ah=$this->get_next_item(DE));
       $this->count ++;
       $this->subcount ++;
}

function print_class_detail($db)
{       
        //  DE loops through all data records
       $this->y_dist = $this->paragraph_space(DE); // space for first DE-Line
       $this->make_x_tab();                        // creates $this->x_tab[] too
       $this->y_pos -= $this->y_dist; 
       do {          // get new record
           $height = $this->de_space($db);  // height of DE
           $ah = $this->get_first_item(DE);
           if ($this->is_set_group()) {  // work with group
              $this->set_group_new(trim($db->f($this->get_group())));
              if ($this->get_group_new() != $this->get_group_old())  {  // group is changed
                 $hi =  trim($this->get_group_old());
                 if ($hi != "")  {                    // Not first page of report
                     $this->y_pos -= $this->y_dist ;
                     $this->do_print($db,GF);     // print group footer
                     if ( $this->get_group_type() == NEWPAGE) {
                         $this->print_new_page($db);      // new page opened
                     }
                 }
                 $this->set_group_old($this->get_group_new());
                 $this->do_print($db,GH);     // print group header
                 $this->y_pos -= $this->y_dist; 
              }     // end of work for group change
           }                           // end of group work
           $this->print_record($db,true, $height);
	   $this->y_pos -= $height + YDIST - 1.0;
      } while ($db->next_record());// of do
      
}

function print_single_detail($db)
{   // prints a detail on a page
      //  loops through all data records
       $this->make_x_tab();                        // creates $this->x_tab[] too
       do  {
           // get new record
         $this->print_record($db, false,0);
         $this->pdf->ezNewPage();
         $this->y_pos = $this->get_height() -POS_START - DISTANZ;
      } while ($db->next_record());


}

function print_totals($pos, $tot) {
       // prints all totals in one line. $tot==0: print $this->totals. $tot==1: print $this->subtotals
       if ($tot == 0) {
          $tothelp = $this->total;
	  } else {
	  $tothelp = $this->subtotal;
       }
       while (list($key, $value) = each($tothelp)){
          // seek item where $key == $item->get_string() and get x/y, font, fontsize
           $ah = $this->get_first_item(DE);
           $i =0;
	   while ($key != $ah->get_string()) {
	       $ah = $this->get_next_item(DE);
	       $i++;
	    }   
	   // item is found
	   if ($ah->get_xpos() == "") $xpos = $xtab[$i]; 
	     else $xpos= $ah->get_xpos();
           list($fl,$mode) = sscanf($ah->get_fieldlength(), "%d%s");   
           $fh = str_repeat("w",$fl);
           $w =$this->pdf->getTextWidth($ah->get_fontsize(),$fh);
	   $xpos2 = $xpos + $w;//this->pdf->getTextWidth($ah->get_fontsize(),$this->total[$key]);  


	   if ($ah->get_bold() == "true") {  // total shall be printed bold
	      // set the font to bold
	      if (!strstr($fs, "Bold")) {
	          // Insert 'Bold' in fontname
		  $fpos = strcspn($fs,"-") ;
		  $fs1 = substr($fs,0,$fpos)."-Bold".substr($fs+1,$fpos);
		  $fs = trim($fs1);
              }
	   }
   
	   $this->set_font($ah->get_fontname());
	   if ($ah->get_o_score() == "true") {
	      $ypos = $pos + $ah->get_fontsize() +2;
	      $this->print_line($xpos, $ypos ,$xpos2,$ypos,1);
	   }
	   if ($ah->get_u_score() == "true") {
	      $this->print_line($xpos, $pos -2,$xpos2,$pos -2,1);
	   }
           $this->print_string_wrap(trim($value),$xpos, $pos,$ah->get_fontsize(),$w,$mode);	   
           if ($tot == 1) {     
                 $this->subtotal[$key] = 0;
           }
       }
}

// methods to print a report
function do_print($db,$type){
  // prints paragraph from the given type
    switch ($type) {
      case RH: $a = $this->rh_items; $this->y_pos = $this->y_pos -$this->paragraph_space(RH)-DISTANZ;break;    
      case PH: $a = $this->ph_items;
               if ($this->begin) $this->begin = false; 
	       else {
                  $this->pdf->ezNewPage();
		  }
               $this->y_pos = $this->get_height() -POS_START -$this->paragraph_space(PH)-DISTANZ;
               break;
      case GH: $a = $this->gh_items; $this->y_pos = $this->y_pos -$this->paragraph_space(GH)-DISTANZ;$this->subcount=0;
	       break;
      case DE: $a = $this->de_items;break;
      case GF: $a = $this->gf_items;$this->y_pos = $this->y_pos -$this->paragraph_space(GF)-DISTANZ; 
               break;
      case RF: $a = $this->rf_items;$this->y_pos = $this->get_height() - POS_START - $this->paragraph_space(RF)-DISTANZ; break;
      case PF: $a = $this->pf_items;$this->y_pos = POS_END; break;
      default: break;
  }
   if ($type == DE) {    // only for Detail items
      $h = $this->report_typ;
      if ($h == KLASSE || $h == TABLE || $h == BEAM || $h == GRID) {
            $this->print_class_detail($db); // some records on a page
      } else {
            $this->print_single_detail($db);    //single record on a page
      } // end of Detail-work
   } else
    {   // all item-types <> DE
     if ($this->y_pos < POS_END) {    // begin new page
               $this->print_new_page($db);
     }
     if ($type == RF) {       // one page for report Header
       $this->pdf->ezNewpage();
       // print totals
       $this->print_totals($this->y_pos,0);
       }
   if ($type == GF) {     // prints totals in Group foot
       $this->print_totals($this->y_pos,1);
   
   }       
     for ($i=0;$i < $this->get_item_count($type); $i++){
            $this->do_print_item($a[$i], $db, $a[$i]->get_xpos(), $this->y_pos +$a[$i]->get_ypos());    // print all items of this paragraph
     }
   }
   if ($type == RH) {       // one page for report Header
       $this->pdf->ezNewpage();
       }
  $this->y_pos -= BLOCKDIST + $this->y_dist;
 }



function create_page($db)
{   // prints a complete report, because in the paragraph for Detail(DE) is
    // a loop through all records. Here the first Page Header and the last Page footer ist printed.
    // executes the select for data-source.

    $sql=$this->get_select();
    $db->query($sql);
    $e = $db->Error;
    if (!empty($e)) {
        my_error($db->Error." <BR> EXIT3 !!!");
        exit;
    }
    if ($db->num_rows() == 0) {
        my_error($db->Error." $sql<BR> No Data Records found !!!");
        exit;
    }
    $db->next_record();
    $this->y_pos=$this->get_height() - POS_START;
    if ($this->is_set_group()) {  // work with group
           $this->set_group_new(trim($db->f($this->get_group())));
    }
 // new page and page header
   if (!empty($this->rh_items)) {
       $this->do_print($db,RH);
      };
    $this->do_print($db,PH);
    if (!empty($this->de_items)) {
       $this->do_print($db,DE);          // most work is done here
       };
    if (!empty($this->gf_items)) {
       $this->do_print($db,GF);
      };
    if (!empty($this->pf_items)) {
      $this->do_print($db,PF);
      };
    if (!empty($this->rf_items)) {
       $this->do_print($db,RF);
      };

 }


}  // end of class report

function create_report($datab,$ho,$us,$pass,$id, $file)
  //   $id is the id-number of the report in the database

{
//  $d = true;  // switch for debug
global $db, $database, $host, $user, $password;
  $database   = $datab;
  $host       = $ho;
  $user       = $us;
  $password   = $pass;
  $db = new DB_Repgen;
  $db->set_variables($database, $host, $user, $password);
  $db->connect($database, $host, $user, $password);

  $e = $db->Error;
  if (!empty($e)) {
        my_error("Database Error: ".$e ."<br>Connection to DB failed!");
        exit;
  }

    // create report-instantiation
  $rep = new report();
  // read data for report

  $rep->do_report($db, $id);
  $rep->pdf = new Cezpdf($rep->get_paper_format(),$rep->get_report_format());    // in memory creation of the report
  $rep->pdf->ezSetMargins(POS_START,POS_END,POS_LEFT,POS_RIGHT);
  $rep->pdf->addInfo("Author",$rep->get_author());
  $rep->pdf->addInfo("Creator",$rep->get_author());
  $rep->pdf->addInfo("Title",$rep->get_name());
  $rep->pdf->addInfo("Subject",$rep->get_name());
  $rep->pdf->addInfo("Keywords",$rep->get_name());
  $rep->create_page($db);
if (isset($d) && $d){
  $pdfcode = $rep->pdf->ezOutput(1);          // transmit clear text for debugging
  $pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
  echo '<html><body>';
  echo trim($pdfcode);
  echo '</body></html>';

} else {
  $tmp = $rep->pdf->ezOutput();
  $fh =fopen($file,"w");
  fwrite($fh,$tmp);
  fclose($fh);
/*
  $opt= array ('compress'=>1 );  // compress=>1 : mit Kompression(default), funktioniert aber bei IE5 nicht immer
  $rep->pdf->ezStream($opt);   // transmit to browser
*/
}


}

// only for testing:
//create_report("database","host","user,"password","report_id");
?>
