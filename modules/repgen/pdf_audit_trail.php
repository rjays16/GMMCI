<?php
/**
* SegHIS - Hospital Information System
* Enhanced by Segworks Technologies Corporation
* Transmittal Letter
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path."include/care_api_classes/class_hospital_admin.php");
include_once($root_path."include/care_api_classes/class_insurance.php");

require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');

require($root_path.'/modules/repgen/themes/dmc/dmc2.php');



define('DEFAULT_HCAREID', 18);
  //declare ug var...
class RegGen_AUDITTRAILReport extends DMCRepGen {
        var $is_detailed = FALSE;
        var $transmit_no;
        var $transmit_date;
        var $classification;
        var $ColumnBorders;
        var $audit_trail_try;
        var $frmdte;
        var $todte;

        var $from_date;
        var $to_date;
        
        //new
        
 

        var $prev_date;

        var $PgTotals;
        var $GrTotals;
        var $bEndOfReport;
        


        function RegGen_AUDITTRAILReport($insurance_name, $bdetailed = false, $bSummaryRep = false) {
                global $db;
             //output size....
                $pg_size = array('215.9', '330.2');
                $this->DMCRepGen($insurance_name, "L", $pg_size, $db, TRUE);
                $this->Caption = "TRANSMITTAL LETTER";
                $this->is_detailed = $bdetailed;
                if ($this->is_detailed) {
                        $this->ColumnWidth = array(22,40,40,23,23,15,15,15,15,15,15,15,15,15,15,15);
                        $this->Columns = 7;
                        $this->ColumnLabels = array(
                                    'User Personnel',
                                    'Patient\'s Name',
                                    'Field',
                                    'Change Date',
                                    'New Value',
                                    'Old Value',
                                    'Record Pointer',
                        );
                        $this->ColumnBorders = array(
                                //'LBR',
                               // 'LBR',
                              //  'LBR',
                                1,
                                1,
                                1,
                                1,
                                1,

                                'LBR',
                                'LBR'
                        );
                        $this->Alignment = array('C','L','L','C','C','R','R','R','R','R','R','R','R','R','R','R');
                        $this->PgTotals  = array(0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
                        $this->GrTotals  = array(0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
                }
                else {
                    if (!$bSummaryRep) {
                            $this->ColumnWidth = array(41,41,41,41,41,41,41);
                            $this->Columns = 7;
                            $this->ColumnLabels = array(
                                  'User Personnel',
                                  'Patient\'s Name',
                                    'Field',
                                    'Old Value',
                                    'New Value',
                                    'Change Date',
                                    'HRN',
                            );
                            $this->Alignment = array('L','L','L','L','L','L','L');
                    }
                    else {
                            $this->ColumnWidth = array(41,41,41,41,41,41,41);
                            $this->Columns = 7;
                            $this->ColumnLabels = array(
                                    'User Personnel',
                                    'Patient\'s Name',
                                    'Field',
                                    'Old Value',
                                    'New Value',
                                    'Change Date',
                                    'HRN/Case No.',

                            );
                            $this->Alignment = array('L','L','L','L','L','L','L');
                    }
                    $this->PgTotals  = array(0.00, 0.00, 0.00, 0.00);
                    $this->GrTotals  = array(0.00, 0.00, 0.00, 0.00);
                }

                $this->TotalWidth = array_sum($this->ColumnWidth);

        // mao ni ang alignment
        //edited by shand size from 12 to 13 01/28/2013
                $this->RowHeight = 11;
                $this->TextHeight = 5;

               // $this->SetDrawColor(0,0,0);

                $this->PageOrientation = "L";

                $this->NoWrap = FALSE;
        }

         function Footer()
  {
    $this->SetY(-7);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
  }

        

        function BeforeData() {
                $cell_height = $this->RowHeight;
                $indention   = 3;
                //date for prev..
               // $this->prev_date = '0000-00-00';

                $this->SetFontSize(14);
                $this->Cell(0, $cell_height, $this->title, 0, 1, 'C');

                
                    // ... if summary report of transmittals ...
                    $this->Cell(0, $cell_height, "SUMMARY OF AUDIT TRAIL",0,1,'C');
                    $this->Ln(2);
                    $this->SetFontSize($this->DEFAULT_FONTSIZE);

                    $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, 11);
                   /// image add on
                    $this->Image('segworlogo.jpg',260,6,50);
                    $this->SetFontSize(12);
                    $sTmp = "".$this->tmpTitle;
                    //Search Result :
                    $cwidth = ($this->rMargin - $this->lMargin)/2;
                    $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "C");
                    $this->Ln(1);
                

                $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);

                if ($this->is_detailed) {
                        $this->Ln(1.2);
                        $this->SetX($this->GetX());
                        $this->Cell($this->ColumnWidth[0], $this->RowHeight+1, " ", "TLR", 0, "L");
                        $this->Cell($this->ColumnWidth[1], $this->RowHeight+1, " ", "TLR", 0, "C");
                        $this->Cell($this->ColumnWidth[2], $this->RowHeight+1, " ", "TLR", 0, "C");
                        $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight+1, "Confinement Period", 1, 0, "C");                                                                             // Confinement Period
                        $this->Cell($this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9], $this->RowHeight+1, "Hospital Charges", 1, 0, "C");     // Hospital Charges
                        $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13], $this->RowHeight+1, "Professional Fee", 1, 0, "C");                           // Professional Fee
                        $this->Cell($this->ColumnWidth[14], $this->RowHeight+1, " ", "TLR", 0, "C");
                        $this->Cell($this->ColumnWidth[15], $this->RowHeight+1, "Patient's", "TLR", 1, "C");
                }
         ///transmit_no
                if ($this->is_detailed)
                        $this->SetFontSize($this->DEFAULT_FONTSIZE);
                else {
                        $this->SetFontSize(10);
                        if ($this->transmit_no == '') $this->SetFont($this->DEFAULT_FONTFAMILY, "B", 10);
                }

                if ($this->is_detailed) {
                    if ($this->colored) $this->SetFillColor(255);
                    $this->SetTextColor(0);
                    for ($i=0;$i<$this->Columns;$i++) {
                        $this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->ColumnLabels[$i],$this->ColumnBorders[$i],0,'L',1);
                    }
                    $this->Ln();
                }
                else
                    parent::Header();

                $this->bEndOfReport = false;
        }

        function Header() {
                if ($this->PageNo() > 1) {
                        if ($this->is_detailed) {
                                $this->SetX($this->GetX());
                                $this->Cell($this->ColumnWidth[0], $this->RowHeight, " ", "TLR", 0, "L");
                                $this->Cell($this->ColumnWidth[1], $this->RowHeight, " ", "TLR", 0, "C");
                                $this->Cell($this->ColumnWidth[2], $this->RowHeight, " ", "TLR", 0, "C");
                                $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight, "Confinement Period", 1, 0, "C");                                                                             // Confinement Period
                                $this->Cell($this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9], $this->RowHeight, "Hospital Charges", 1, 0, "C");     // Hospital Charges
                                $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13], $this->RowHeight, "Professional Fee", 1, 0, "C");                           // Professional Fee
                                $this->Cell($this->ColumnWidth[14], $this->RowHeight, " ", "TLR", 0, "C");
                                $this->Cell($this->ColumnWidth[15], $this->RowHeight, "Patient's", "TLR", 1, "C");

                                $this->SetFontSize($this->DEFAULT_FONTSIZE);
                        //transmit_no
                        }
                        else {
                                $this->SetFontSize(10);                         
                                if ($this->transmit_no == '') $this->SetFont($this->DEFAULT_FONTFAMILY, "B", 10);
                        }
                                                                                                    
                        if ($this->is_detailed) {
                            if ($this->colored) $this->SetFillColor(255);
                            $this->SetTextColor(0);
                            for ($i=0;$i<$this->Columns;$i++) {
                                $this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->ColumnLabels[$i],$this->ColumnBorders[$i],1,'C',1);
                            }
                            $this->Ln();
                        }
                        else
                            parent::Header();
                }
        }

        function getClassificationDesc() {
                if (($this->classification != '') && ($this->classification != '0')) {
                    $strSQL = "select memcategory_desc from seg_memcategory
                                                where memcategory_id = $this->classification";
                    $sDesc = '';
                    if ($result=$this->Conn->Execute($strSQL)) {
                            $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
                             if ($row = $result->FetchRow()) {
                                    $sDesc = (is_null($row["memcategory_desc"])) ? "" : $row["memcategory_desc"];
                             }
                    }
                }
                else
                    $sDesc = "ALL MEMBER CLASSIFICATIONS";
                return $sDesc;
        }

        
        function BeforeCellRender() {
            if ($this->RENDERCOLNUM == 1) {
                $this->SetXY($this->RENDERROWX - 5, $this->RENDERROWY);
                $this->Cell(4, $this->RowHeight, $this->RENDERROWNUM + 1, 0, 0, "R");
                $this->SetXY($this->RENDERROWX, $this->RENDERROWY);
            }
        }
        
        
      //created by: Francis L.G
    //01-04-13
    //For audit trail
         function getRows($religion,$counter_rel,$values) {
                global $db;
                $counter=0;
                $gRows="";
                while($values[$counter]){
                    if(($religion==1)&&($counter_rel==$counter)){
                        $sql = "SELECT * FROM seg_religion WHERE religion_nr=".$values[$counter];
                        $result = $db->Execute($sql);
                        if($result){
                            $religionRow = $result->FetchRow();
                            $religionName = $religionRow['religion_name'];
                            $gRows .= $religionName."\n";
                        }
                    }
                    else
                        $gRows .= $values[$counter]."\n";
                    $counter++;
                }

                  return $gRows;
              }
              //end


        function FetchData() {

                $selValue = $_GET['sValue'];
                $selDate = $_GET['sDate'];
                $specific = $_GET['spec'];
                $selDate1 = $_GET['sDate1'];
                $selDate2 = $_GET['sDate2'];
                $where = '';
                 
                  if($selValue != '' || $selDate != '' )
               {
                  
               
                if($selValue != ''){
                    if($specific=='name'){
                         if (strpos($selValue, ",") == false){
                            $data[] = "name_last like '".trim($selValue)."%' OR cu.name like '".trim($selValue)."%' OR fn_get_person_last_enc(pk_value) like '".trim($selValue)."%'";
                            $this->tmpTitle = "Patient Name having Family Name \"".trim($selValue)."\" ";
                         }
                         else 
                         {
                            $tmp = explode(",", $selValue);
                            $data[] = "name_last like '".trim($tmp[0])."%' OR fn_get_person_last_enc(pk_value) like '".trim($selValue)."%'";
                            $data[] = "name_first like '".trim($tmp[1])."%'  OR fn_get_person_first_enc(pk_value) like '".trim($selValue)."%'";
                            $this->tmpTitle = "Patient Name having \"".trim($selValue)."\" ";
                           
                         }
                       
                    }
                    else
                    {
                        $data[] = "pk_value = ".$selValue;
                        $this->tmpTitle = "Patient having HRN ".trim($selValue)." ";
                        
                        //$data[]= "pk_value=".$selValue;
                    }
                    
                }
                                          
                
               if($selDate){         
                if($selDate=='between'){
                    $sDate1 = date("Y-m-d",strtotime($selDate1));
                    $sDate2 = date("Y-m-d",strtotime($selDate2));
                     $data[] = "DATE(date_changed) BETWEEN '".$sDate1."' AND '".$sDate2."'";
                     $this->tmpTitle .= "FROM ".date("M d,Y",strtotime($selDate1))." to ".date("M d,Y",strtotime($selDate1));
                }
                else if($selDate=='today'){
                    $data[] = 'DATE(date_changed)=DATE(NOW())';
                    $this->tmpTitle .= " Today";
                }
                else if($selDate=='thisweek'){
                    $data[] = 'YEAR(date_changed)=YEAR(NOW()) AND WEEK(date_changed)=WEEK(NOW())';
                    $this->tmpTitle .= " This Week";
                }
                else if($selDate=='thismonth'){
                    $data[] = 'YEAR(date_changed)=YEAR(NOW()) AND MONTH(date_changed)=MONTH(NOW())';
                    $this->tmpTitle .= " This Month";
                }
                
              //  else //if($selDate=='specificdate')
//                {
//                    $date_s = date("Y-m-d",strtotime($selDate));
//                    $data[] = "DATE(date_change)='$date_s'";
//                }
                
                else //if($selDate=='specificdate') 
                
                {
                    $date_s = date("Y-m-d",strtotime($selDate));
                    $data[] = "DATE(date_changed)='$date_s'";
                    $this->tmpTitle .= " on ".date("M d,Y",strtotime($selDate));
                }
               
               }
               
               
                
                  //   $pol = implode(" AND ", $data);
                  //echo  $pol;      
                $where = implode(" AND ",$data);
                
                //$where = $data;
               // echo $where;           
                $q = "SELECT a.date_changed,
                     a.Action_type,
                     a.login,
                     a.table_name,
                     a.field_c,
                     a.old_value,
                     a.pk_value,
                     a.new_value,
                     c.name,
                     c.login_id,
                     cp.name_first,
                     cp.name_last,
                     cu.name AS name2,
                     fn_get_person_first_enc(pk_value) AS name_first1,
                     fn_get_person_last_enc(pk_value) AS name_last1 
                     FROM seg_audit_trail `a`
                     INNER JOIN care_users `c` 
                     ON a.login = c.login_id
                     LEFT JOIN care_person `cp`
                     ON a.pk_value = cp.pid
                     LEFT JOIN care_users `cu`
                     ON a.pk_value = cu.personell_nr
                     WHERE ($where) AND is_visible = 1 ORDER BY date_changed DESC";// ORDER BY date_changed, Fname, Lname";
                
                }
                
                else
                { 
                    $q = "SELECT a.date_changed,
                     a.Action_type,
                     a.login,
                     a.table_name,
                     a.field_c,
                     a.old_value,
                     a.pk_value,
                     a.new_value,
                     c.name,
                     c.login_id,
                     cp.name_first,
                     cp.name_last,
                     cu.name AS name2,
                     fn_get_person_first_enc(pk_value) AS name_first1,
                     fn_get_person_last_enc(pk_value) AS name_last1 
                     FROM seg_audit_trail `a`
                     INNER JOIN care_users `c` 
                     ON a.login = c.login_id
                     LEFT JOIN care_person `cp`
                     ON a.pk_value = cp.pid
                     LEFT JOIN care_users `cu`
                     ON a.pk_value = cu.personell_nr
                     WHERE is_visible = 1 ORDER BY date_changed DESC";// ORDER BY date_changed, Fname, Lname";
                }
                
                $strSQL = $q; 
                
                $result=$this->Conn->Execute($strSQL);
                $this->_count = $result->RecordCount();
                $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
                if ($result) {
                        $this->Data=array();

                        while ($row=$result->FetchRow()) {


                                    $fields = explode('+',$row['field_c']);
                                    $newValues = explode('+', $row["new_value"]);
                                    $oldValues = explode('+', $row["old_value"]);
                                    
                                    $fields_v = '';
                                    $newValues_v = '';
                                    $oldValues_v = '';
                                    
                                    $counter=0;
                                    $religion=0;
                                    $counter_rel=0;
                                    while($fields[$counter]){
                                        $fields_v .= $fields[$counter]."\n";

                                        $counter++;
                                    }
                                    
                                    $oldValues_v = $this->getRows($religion,$counter_rel,$oldValues);
                                    $newValues_v = $this->getRows($religion,$counter_rel,$newValues);
                                    
                                    /*
                                    
                                    $fieldc = count($fields);
                                    $newValuesc = count($newValues);
                                    $oldValuesc = count($oldValues);
                                    
                                    for($x = 0; $x<=$fieldc; $x++)
                                    {
                                        $fields_v .= "".$fields[$x]."\n";
                                    }
                                    
                                    for($x=0; $x<=$newValuesc; $x++)
                                    {
                                        $newValues_v .= "".$newValues[$x]."\n";
                                    }
                                    

                                    for($x=0; $x<=$oldValuesc; $x++)
                                    {
                                        $oldValues_v .= "".$oldValues[$x]."\n";
                                    }
                                    */

                                    "<div style=\"text-align:left\">" .

                                    $this->Data[]=array
                                                       (                                                               
                                                               $row["name"],
                                                               $row["name_first"].' '.$row["name_last"].' '.$row["name_first1"].' '.$row["name_last1"].' '.$row["name2"],
                                                               $fields_v,
                                                               $oldValues_v,
                                                               $newValues_v,
                                                               date("M d,Y \n h: ia",strtotime($row["date_changed"])),
                                                               $row["pk_value"],

                                                            );

                                                              "</div>";

                        }
                }
                else
                        echo $this->Conn->ErrorMsg();
        }
}

$rep = new RegGen_AUDITTRAILReport('Southern Philippines Medical Center - Hospital Information System', ($_GET['detailed'] == '1'), true);

if (isset($_GET['class']) && $_GET['class'])
    $rep->classification = $_GET['class'];
else
        $rep->classification = "";

if (isset($_GET['trdte']) && $_GET['trdte'])
    $rep->transmit_date = strftime("%B %d, %Y", $_GET['trdte']);
else
    $rep->transmit_date = strftime("%B %d, %Y");

if (isset($_GET['fromdte']) && $_GET['fromdte']) {
    $rep->from_date = strftime("%B %d, %Y", $_GET['fromdte']);
    $rep->frmdte = strftime("%Y-%m-%d", $_GET['fromdte']);
}
else
    $rep->from_date = strftime("%B %d, %Y");

if (isset($_GET['todte']) && $_GET['todte']) {
    $rep->to_date = strftime("%B %d, %Y", $_GET['todte']);
    $rep->todte = strftime("%Y-%m-%d", $_GET['todte']);
}
else
    $rep->to_date = strftime("%B %d, %Y");

//$rep->TransmittalHeader();
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>