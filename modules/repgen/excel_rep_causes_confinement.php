<?php
  //Created by Cherry 07-27-09
  //Statistics of Causes of Confinement (Excel)
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  
  class Excel_Stat_Confinement extends Spreadsheet_Excel_Writer{
    var $from, $to; 
    
        function Excel_Stat_Confinement($from, $to){
            $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(1);      // Letter
          $this->worksheet->setPortrait();
          $this->worksheet->setMarginTop(2);
          $this->worksheet->setMarginLeft(0.5);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.3);
          $this->Headers = array('RANK', 'OCCURRENCE', 'PHIC', 'NON-PHIC', 'CODE', 'ICD DESCRIPTION');
          $this->ColumnWidth = array(8, 15, 10, 10, 10, 40);
          
          //format for table headers
          $this->format1=& $this->addFormat();
          $this->format1->setSize(9);
          $this->format1->setBold();
          $this->format1->setAlign('center');
          
          //format for data
          $this->format2=& $this->addFormat();
          $this->format2->setSize(8);
          $this->format2->setAlign('left');
          $this->format2->setTextWrap(1);
          
          if($from) $this->from=$from;
          if($to) $this->to=$to;
        }
        
        function ExcelHeader()
      {
          $objInfo = new Hospital_Admin();
    
          if ($row = $objInfo->getAllHospitalInfo()) {      
            $row['hosp_agency'] = strtoupper($row['hosp_agency']);
            $row['hosp_name']   = strtoupper($row['hosp_name']);
          }
          else {
            $row['hosp_country'] = "Republic of the Philippines";
            $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
            $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
            $row['hosp_addr1']   = "Quezon Ave., Digos City";      
          }
          if ($this->from==$this->to)
            $text = "For ".date("F j, Y",strtotime($this->from));
          else
            $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));            
          
          $this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\nCOMMON CAUSES OF CONFINEMENT\n".$text."\n\n".$text2."\n",0.5);

          //print table
          $tablerow = 0;
          $this->worksheet->setColumn($tablerow, 0, $this->ColumnWidth[0]);
          $this->worksheet->write($tablerow, 0, $this->Headers[0], $this->format1);
          $this->worksheet->setColumn($tablerow, 1, $this->ColumnWidth[1]);
          $this->worksheet->write($tablerow, 1, $this->Headers[1], $this->format1);
          $this->worksheet->setColumn($tablerow, 2, $this->ColumnWidth[2]);
          $this->worksheet->write($tablerow, 2, $this->Headers[2], $this->format1);
          $this->worksheet->setColumn($tablerow, 3, $this->ColumnWidth[3]);
          $this->worksheet->write($tablerow, 3, $this->Headers[3], $this->format1);
          $this->worksheet->setColumn($tablerow, 4, $this->ColumnWidth[4]);
          $this->worksheet->write($tablerow, 4, $this->Headers[4], $this->format1);
          $this->worksheet->setColumn($tablerow, 5, $this->ColumnWidth[5]);
          $this->worksheet->write($tablerow, 5, $this->Headers[5], $this->format1);
        
      }
      
      function FetchData()
      {
          global $db;
          
          $sql = "SELECT ed.code AS code, 
               count(ed.code_parent) AS occurrence,
               c.description,
               SUM(CASE WHEN ins.hcare_id=18 then 1 else 0 end) AS phic_occurrence,
               SUM(CASE WHEN ins.hcare_id<>18 OR ins.hcare_id IS NULL then 1 else 0 end) AS nonphic_occurrence
               FROM  care_encounter_diagnosis AS ed
               INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
               INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
               LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr 
               WHERE ed.encounter_type IN (3,4)
               AND e.status NOT IN ('deleted','hidden','inactive','void')
               AND ed.status NOT IN ('deleted','hidden','inactive','void')
               AND DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
               GROUP BY ed.code
               ORDER BY count(ed.code) DESC";
           $result=$db->Execute($sql);
           if($result){
               $i=1;
               $temp=1;
               while($row=$result->FetchRow()){
                    $this->worksheet->write($temp, 0, $i, $this->format2);
                    $this->worksheet->write($temp, 1, $row['occurrence'], $this->format2);
                    $this->worksheet->write($temp, 2, $row['phic_occurrence'], $this->format2);
                    $this->worksheet->write($temp, 3, $row['nonphic_occurrence'], $this->format2);
                    $this->worksheet->write($temp, 4, $row['code'], $this->format2);
                    $this->worksheet->write($temp, 5, $row['description'], $this->format2);
                    $temp++;
                    $i++;
               }
           }
      
                 
                 
      } 
  }
  $rep = new Excel_Stat_Confinement($_GET['from'],$_GET['to']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->send('excel_rep_causes_confinement.xls');
  $rep->close();
?>
