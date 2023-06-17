<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_Radio_Services extends DMCRepGen {

    var $from_date;
    var $to_date;    
    var $left;
    var $right;
    var $top;
    
    function RepGen_Radio_Services($from, $to) {
        global $db;
        
        $this->DMCRepGen("RADIOLOGY", "L", "Letter", $db, TRUE);
        
        $this->Caption = "Radiology Services";
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(103,21,21,23,21,21,23,23);
        $this->Columns = 8;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        $this->Alignment = array('L','C','C','C','C','C','C','C');
        $this->PageOrientation = "L";
        $this->from_date=$from;
        if ($to) $this->to_date=$to;

        $this->NoWrap = FALSE;
        
    }
    
    function Header() {
          
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("m/d/Y h:iA"),0,0,'R');
        $this->Ln(2);
        $this->SetFont("Arial","B","12");
        $this->Cell(0,4,'RADIOLOGY SERVICES',$border2,1,'C');
        $this->Cell(0,4,date("m/d/Y",strtotime($this->from_date))." - ".date("m/d/Y",strtotime($this->to_date)),$border2,1,'C');
        $from_dt=strtotime($this->from_date);
        $to_dt=strtotime($this->to_date);
        $this->Ln(1);
        
        //print labels
        $this->SetFont('Arial','B',9);
        $this->SetXY(47,16);
        $this->Cell(10,16,'Procedures',0,0,'C');
        $this->SetXY(130,18);
        $this->Cell(60,8,'Number of Patients',0,0,'C');
        $this->SetXY(118,23);
        $this->Cell(15,6,'IPD',0,0,'C');
        $this->SetXY(140,23);
        $this->Cell(15,6,'OPD',0,0,'C');
        $this->SetXY(161,23);
        $this->Cell(15,6,'Walk-in',0,0,'C');
        $this->SetXY(185,23);
        $this->Cell(15,6,'PHIC',0,0,'C');
        $this->SetXY(205,23);
        $this->Cell(15,6,'ER',0,0,'C');
        $this->SetXY(230,23);
        $this->Cell(11,6,'Total',0,0,'C');
        $this->SetXY(247,23);
        $this->Cell(20,6,'Percentage',0,0,'C');
        
        //draw lines
         /*   $this->left = 13;
        $this->right = 260;
        $this->SetDrawColor(0,0,0);
        $this->Line($this->left,19,$this->right,19);
        $this->Line($this->left,19,$this->left,28);
        $this->Line(92,19,92,28);
        $this->Line(92,24,223,24);
        $this->Line(223,19,223,28);
        $this->Line(241,19,241,28);
        $this->Line($this->right,19,$this->right,28);
        $this->Line($this->left,28,$this->right,28);
            $this->top = 29;*/
        $this->SetXY(13,30);
        
    }
    
    function BeforeData() {
        $this->FONTSIZE = 8.7;
        if ($this->colored) {
            $this->DrawColor = array(0xDD,0xDD,0xDD);
            #$this->DrawColor = array(255,255,255);
        }
    }

    function FetchData() {        
        
        if (empty($this->to)) $end_date="NOW()";
        else $end_date=$this->to;
        if (empty($this->from)) $start_date="NOW()";
        else $start_date=$this->from;

        //laboratory
        $sql = "SELECT * FROM seg_radio_services;";
        $result=$this->Conn->Execute($sql);
        $max = $result->RecordCount();
        $ctr = 0;
        $total = 0;
        $temp_array[$max][8];
        $sql = "SELECT * FROM seg_radio_service_groups;";
        $result=$this->Conn->Execute($sql);
        $this->_count = $result->RecordCount();
        $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($result) {
            $this->Data=array();
            $i=1;
            $this->Data[]=array("Radiology Examination",'','','','','','','');
            $this->_count = $this->_count + 1; 
            while ($row=$result->FetchRow()) {
                $service_group = $row["name"];
                $group_code = $row["group_code"];
                $temp_array[$ctr][0] = $i .". ".$service_group;
                $temp_array[$ctr][3] = "x";
                $ctr++;
                $j=0;
                $sql = "SELECT * FROM seg_radio_services WHERE group_code='".$group_code."';";
                $result2=$this->Conn->Execute($sql);
                $this->_count = $this->_count + $result2->RecordCount(); 
                if($result2)
                {
                    while ($row2=$result2->FetchRow()) {
                        $service = $row2["name"];
                        $service_code = $row2["service_code"];
                        $sql = "SELECT 
                                  SUM(
                                    IF(
                                      ce.encounter_type = 2 
                                      AND (
                                        d.`request_flag` != 'phic' 
                                        OR d.`request_flag` IS NULL
                                      ) 
                                      AND s.`encounter_nr` IS NOT NULL,
                                      1,
                                      0
                                    )
                                  ) AS out_patient,
                                  SUM(
                                    IF(
                                      (
                                        ce.encounter_type = 3 
                                        OR ce.`encounter_type` = 4
                                      ) 
                                      AND (
                                        d.`request_flag` != 'phic' 
                                        OR d.`request_flag` IS NULL
                                      ) 
                                      AND s.`encounter_nr` IS NOT NULL,
                                      1,
                                      0
                                    )
                                  ) AS in_patient,
                                  SUM(
                                    IF(
                                      ce.encounter_type = 1 
                                      AND (
                                        d.`request_flag` != 'phic' 
                                        OR d.`request_flag` IS NULL
                                      ) 
                                      AND s.`encounter_nr` IS NOT NULL,
                                      1,
                                      0
                                    )
                                  ) AS er,
                                  SUM(IF(d.`request_flag` = 'phic', 1, 0)) AS phic,
                                  SUM(IF(s.`encounter_nr` IS NULL, 1, 0)) AS walk_in 
                                FROM
                                  care_test_request_radio AS d 
                                  INNER JOIN seg_radio_serv AS s 
                                    ON s.refno = d.batch_nr
                                  LEFT JOIN care_encounter ce 
                                    ON ce.`encounter_nr` = s.`encounter_nr` 
                                WHERE d.service_code ='".$service_code.
                                "' AND DATE(d.served_date) BETWEEN '".$this->from_date.
                                "' AND '".$this->to_date."';";
                              
                        $result3=$this->Conn->Execute($sql);
                        if($result3)
                        {
                            $row3=$result3->FetchRow();
                            $inpatient = $row3["in_patient"];
                            $outpatient = $row3["out_patient"];
                            $phic = $row3['phic'];
                            $walk_in = $row3['walk_in'];
                            $er = $row3['er'];
                        }
                        else{
                            $inpatient = 0;
                              $outpatient = 0;
                              $phic = 0;
                              $walk_in = 0;
                        }
                    
                        $temp_array[$ctr][0] = "     ".$i.".".$j.". ".$service;
                       
                        if($inpatient==0)
                            $temp_array[$ctr][1] = "z";
                        else
                            $temp_array[$ctr][1] = $inpatient;
                        if($outpatient==0)
                            $temp_array[$ctr][2] = "z";
                        else
                            $temp_array[$ctr][2] = $outpatient;
                        if($phic==0)
                            $temp_array[$ctr][4] = "z";
                        else
                            $temp_array[$ctr][4] = $phic;
                        if($walk_in==0)
                            $temp_array[$ctr][5] = "z";
                        else
                            $temp_array[$ctr][5] = $walk_in;
                        if($er==0)
                            $temp_array[$ctr][6] = "z";
                        else
                            $temp_array[$ctr][6] = $er;
                        if($inpatient==0 && $outpatient==0 && $walk_in== 0 && $phic==0)
                            $temp_array[$ctr][3] = "x";
                        else
                            $temp_array[$ctr][3] = $inpatient + $outpatient + $phic + $walk_in + $er;
                        $in_total += $inpatient;
                        $out_total += $outpatient;
                        $phic_total += $phic;
                        $walk_in_total += $walk_in;
                        $er_total += $er;
                        $ctr++;
                        $j++;
                    }
                }
                $i++;
            }
        }
        else
            echo $this->Conn->ErrorMsg();
        $total = $in_total + $out_total + $walk_in_total + $phic_total+ $er;
        $per_total = 0;
        for($z=0; $z<$ctr; $z++)
        {
            $prnt_total = $temp_array[$z][3];
            $prnt_inpatient = $temp_array[$z][1];
            $prnt_outpatient = $temp_array[$z][2];
            $prnt_phic = $temp_array[$z][4] == 'z' ? '' : $temp_array[$z][4];
            $prnt_er = $temp_array[$z][6] == 'z' ? '' : $temp_array[$z][6];
            $prnt_walk_in = $temp_array[$z][5];
            $per_total += $temp_array[$z][3];
            $prnt_pertotal = number_format(($prnt_total/$total)*100,2)."%";

            if($temp_array[$z][3]=="x")
            {
                $prnt_total='';
                $prnt_pertotal='';
            }
            if($temp_array[$z][2]=="z")    
            {
                $prnt_outpatient="";
            }
            if($temp_array[$z][1]=="z")    
            {
                $prnt_inpatient="";
            }
            if($temp_array[$z][4]=="z")    
            {
                $prnt_phic="";
            }
            if($temp_array[$z][5]=="z")    
            {
                $prnt_walk_in="";
            }
            if($temp_array[$z][3]=="z")    
            {
                $prnt_total="";
                $prnt_pertotal="";
            }
            $this->Data[]=array($temp_array[$z][0],$prnt_inpatient,$prnt_outpatient, $prnt_walk_in,$prnt_phic,$prnt_er,$prnt_total,$prnt_pertotal);
        }
        $this->Data[]=array('Grand Total =>',number_format($in_total),number_format($out_total),number_format($walk_in_total),number_format($phic_total),number_format($er_total),number_format($total),"100%");
    }
}

$rep = new RepGen_Radio_Services($_GET['from'],$_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>