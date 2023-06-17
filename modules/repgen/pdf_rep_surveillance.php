<?php
require('./roots.php');
#include_once($root_path."/classes/fpdf/fpdf.php");
include_once($root_path."/classes/fpdf/pdf.class.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
  
class SentinelSurveillance extends FPDF{
  var $from;
  var $to;
  
  function SentinelSurveillance($from, $to){
    global $db;  
    $this->ColumnWidth = array(22,55,25,10,10,80,33,25);
    $this->SetLeftMargin(5);
    $this->SetTopMargin(3);
    $this->Alignment = array('C','C','R','R','R','R','R','R','R','R','R');
    $this->FPDF("L", 'mm', 'Letter'); 
     
    if ($from) $this->from=date("Y-m-d",strtotime($from));
    if ($to) $this->to=date("Y-m-d",strtotime($to));
  }
  
  function Header() {
    global $root_path, $db;
    $this->SetFont('Arial', 'B', 14);
    $this->Cell(0, 6, "SENTINEL SURVEILLANCE WORKSHEET",0,1,'C');
    
    $this->SetFont('Arial', 'B', 12);
    if($this->from == $this->to){
      $this->Cell(0,6, "As of ".date("F j, Y",strtotime($this->from)), 0,1,'C');
    }
    else{
      $this->Cell(0, 4, "From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to)),0,1,'C');
    }
    $this->Ln(5);
    #Table Header
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(18, 4, "Diagnosis : ",0,0,'L');
    $this->Cell(20, 4, "", 0,0,'L');
    $this->Cell(20,4,"Description : ",0,0,'L');
    $this->Cell(0,4,"", 0,1,'L');
    $this->Cell($this->ColumnWidth[0], 4, "", "TLR",0,'L');
    $this->Cell($this->ColumnWidth[1], 4, "", "TLR",0,'C');
    $this->Cell($this->ColumnWidth[2], 4, "", "TLR",0,'C');
    $this->Cell($this->ColumnWidth[3], 4, "", "TLR",0,'C');
    $this->Cell($this->ColumnWidth[4], 4, "", "TLR", 0,'C');
    $this->Cell($this->ColumnWidth[5], 4, "", "TLR", 0,'C');
    $this->Cell($this->ColumnWidth[6], 4, "IF DENGUE", "TLR",0,'C');
    $this->Cell($this->ColumnWidth[7], 4, "", "TLR",1,'C');
    
    $this->Cell($this->ColumnWidth[0], 4, "CASE NO.", "LR",0,'C');
    $this->Cell($this->ColumnWidth[1], 4, "FULL NAME", "LR",0, 'C');
    $this->Cell($this->ColumnWidth[2], 4, "DATE ADMITTED", "LR",0,'C');
    $this->Cell($this->ColumnWidth[3], 4, "AGE", "LR",0, 'C');
    $this->Cell($this->ColumnWidth[4], 4, "SEX", "LR",0,'C');
    $this->Cell($this->ColumnWidth[5], 4, "COMPLETE ADDRESS", "LR",0,'C');
    $this->Cell($this->ColumnWidth[6], 4, "Platelet below 100,000", "LR", 0,'C');
    $this->Cell($this->ColumnWidth[7], 4, "FATALITY","LR",1,'C');
    
    $this->Cell($this->ColumnWidth[0], 4, "", "BLR",0,'C');
    $this->Cell($this->ColumnWidth[1], 4, "", "BLR",0,'C');
    $this->Cell($this->ColumnWidth[2], 4, "", "BLR",0,'C');
    $this->Cell($this->ColumnWidth[3], 4, "", "BLR",0,'C');
    $this->Cell($this->ColumnWidth[4], 4, "", "BLR",0,'C');
    $this->Cell($this->ColumnWidth[5], 4, "", "BLR",0,'C');
    $this->Cell($this->ColumnWidth[6], 4, "or less per mm3", "BLR",0,'C');
    $this->Cell($this->ColumnWidth[7], 4, "", "BLR",0,'C');
    $this->Ln();
    
  }
  
  function GetDiagnosisOccurrence($diag, $desc){
    global $db;
    $sql2 = "SELECT e.encounter_nr AS Case_No, 
            CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ' ,IF(trim(p.name_first) IS NULL ,' ',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,' ',trim(p.name_middle))) AS 'FullName',
            e.admission_dt AS Date_Admitted, IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            p.sex AS Sex, 
            CONCAT(IF (trim(p.street_name) IS NULL,'',trim(p.street_name)),' ',
                  IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
                  IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
                  IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
                  IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
                  IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS Address,
            '' AS If_Dengue, IF(p.death_date!='0000-00-00' AND (p.death_encounter_nr=e.encounter_nr),p.death_date,'') AS Fatality,
            e.discharge_date AS 'Discharged Date'
            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_encounter_diagnosis AS ed ON ed.encounter_nr=e.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
            WHERE DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
            AND e.status NOT IN ('deleted','hidden','inactive','void') 
            AND ed.status NOT IN ('deleted','hidden','inactive','void')
            AND ed.code = '".$diag."'
            ORDER BY DATE(e.admission_dt) ASC, p.name_last, p.name_first, p.name_middle";
      $result2 = $db->Execute($sql2); 
      $row2 = $result2->FetchRow();
      /*#Table Header
   
    
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(21, $RowWidth, "Diagnosis : ", 0,0,'L');
    $this->Cell(12, $RowWidth); // for ICD 10
    $this->Cell(25, $RowWidth, "Desciption : ", 0, 0, 'L');
    $this->Cell(0, $RowWidth, "", 0, 1, 'L'); //for description
    */    
      return $row2;    
  }
  
  function GetDiagnosisData(){
    global $db;
    $prev_diag = "";
    $sql = "SELECT ed.code_parent,ed.code AS ICD, c.description AS ICD_Description, e.encounter_nr AS Case_No, 
            CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ' ,IF(trim(p.name_first) IS NULL ,' ',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,' ',trim(p.name_middle))) AS FullName,
            e.admission_dt AS Date_Admitted, IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            p.sex AS Sex, 
            CONCAT(IF (trim(p.street_name) IS NULL,'',trim(p.street_name)),' ',
                  IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
                  IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
                  IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
                  IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
                  IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS Address,
            '' AS If_Dengue, IF(p.death_date!='0000-00-00' AND (p.death_encounter_nr=e.encounter_nr),p.death_date,'') AS Fatality,
            e.discharge_date AS 'Discharged Date'
            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_encounter_diagnosis AS ed ON ed.encounter_nr=e.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
            WHERE DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
            AND e.status NOT IN ('deleted','hidden','inactive','void') 
            AND ed.status NOT IN ('deleted','hidden','inactive','void')
            ORDER BY ed.code, DATE(e.admission_dt) ASC, p.name_last, p.name_first, p.name_middle";     
  
    $result = $db->Execute($sql);
    while($row = $result->FetchRow()){
      $diag = $row['ICD'];
      $desc = $row['ICD_Description'];
        //Row
        $this->Cell($this->ColumnWidth[0], 4, $row['Case_No'], 0,0,'C');
        $this->Cell($this->ColumnWidth[1], 4, $row['FullName'], 0,0,'C');
        $this->Cell($this->ColumnWidth[2], 4, $row['Date_Admitted'],0,0,'C');
        $this->Cell($this->ColumnWidth[3], 4, $row['Age'],0,0,'C');
        $this->Cell($this->ColumnWidth[4], 4, $row['Sex'],0,0,'C');
        $this->Cell($this->ColumnWidth[5], 4, $row['Address'],0,0,'C');
        $this->Cell($this->ColumnWidth[6], 4, $row['If_Dengue'],0,0,'C');
        $this->Cell($this->ColumnWidth[7], 4, $row['Fatality'],0,0,'C');
        $this->Ln();    
       
      
    }
  }
  
  
  function Footer()
  {
    $this->SetY(-23);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
  }
  
  //-------------------------------------
   function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
  $row = 3;    
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $nb2=$this->NbLines($this->widths[1],$data[1]);
        $nb3=$this->NbLines($this->widths[0],$data[0]);
        if($nb2>$nb3){
          $nbdiff = $nb2 - $nb3;
           $nbdiff = $nbdiff*$row;  
          k == 1;
        }
        else if($nb3>$nb2){
          $nbdiff = $nb3 - $nb2;
           $nbdiff = $nbdiff*$row;  
          k==0;
        }
        else{
          $nbdiff = 0;
        }
         
          
        //$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
        //print_r($nb2, $nb3);
        
        //$nb = $nb*2;
        //print_r($nb);
    $h=$row*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row

    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        //$this->Rect($x,$y,$w,$h);
        //Print the text
        //print_r($data[i]);
        //number_format($objroom->charges,2,'.',',')
        if($i==2){
          $length = $this->GetStringWidth($data[$i]);
            if($length < $this->widths[$i]){
              $this->Cell($w, $h, $data[$i],1,0,'L');
            }
            else{
              $this->MultiCell($w, $row,$data[$i],1,'L');
            }
        }
        else if($i>2){
          //print_r(i);
            if($i==3){
              $this->Cell($w, $h, $data[$i],1,0,'R');
            }
            else{
              $this->Cell($w, $h, number_format($data[$i],2,'.',','),1,0, 'R');  
            }
          
        } 
        else{
          $length = $this->GetStringWidth($data[$i]);
            if($length < $this->widths[$i]){
              $this->Cell($w, $h, $data[$i],1,0,'L');
            }
            else{
              $nbrow = 3;
              // $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
              $this->MultiCell($w, $row,$data[$i],1,'L');
                
              //$this->MultiCell($length, $row,$data[$i],1,'L');
                
            }
          
        } 
          
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
  
}

$from = $_GET['from'];
$to = $_GET['to'];
  
$pdf = new SentinelSurveillance($from, $to);
$pdf->AliasNbPages();
$pdf->GetDiagnosisData();
$pdf->Output(); 
?>