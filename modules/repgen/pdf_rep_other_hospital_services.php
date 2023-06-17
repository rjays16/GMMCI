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

class RepGen_Lab_Services extends DMCRepGen {

    var $from_date;
    var $to_date;    
    var $dept_nr;
    var $from_time;    
    var $to_time;    
    
    function RepGen_Lab_Services($from, $to) {
        global $db;
        
        $this->DMCRepGen("OTHER HOSPITAL SERVICES", "L", "Letter", $db, TRUE);
        
        $this->Caption = "Other Hospital Services";
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 2;
        $this->ColumnWidth = array(80,9,19,8,8,16,14,18,19,11,7,19,19);
        $this->Columns = 10;

        $this->TotalWidth = array_sum($this->ColumnWidth);        
        
        /*$this->ColumnLabels = array(
        'Procedures',
        'Number of Patients',
        'Total',
        '% of Grand Total');*/
   
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        
        $this->Alignment = array('L','C','C','C','C','C','C','C','C','C','C','R','R');
        $this->PageOrientation = "L";
        
        $this->from=date("Y-m-d",strtotime($from));
        if ($to) $this->to=date("Y-m-d",strtotime($to));

        $this->from_time = '';
        $this->to_time = '';
        $this->dept_nr = '';

        $this->NoWrap = FALSE;
        
    }
    
    function Header() {
          
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("m/d/Y h:iA"),0,0,'R');
        $this->Ln(2);
        $this->SetFont("Arial","B","12");
        $this->Cell(0,4,'OTHER HOSPITAL SERVICES',$border2,1,'C');
        $this->Cell(0,4,date("m/d/Y",strtotime($this->from))." - ".date("m/d/Y",strtotime($this->to)),$border2,1,'C');
        
        //$this->Cell(0,4,'Number of Records : '.$this->_count,$border2,1,'L');
        $from_dt=strtotime($this->from_date);
        $to_dt=strtotime($this->to_date);
        /*if (!empty($this->from_date) && !empty($this->to_date))
            $this->Cell(0,5,
                sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
                $border2,1,'C');*/
                
        $this->Ln(1);
        $this->SetFont('Arial','B',10);
        $this->SetXY(30,16);
        $this->Cell(10,16,'Procedures',0,0,'C');
        $this->SetXY(100,17);
        $this->Cell(60,8,'Number of Patients',0,0,'C');
        $this->SetXY(80,23);
        $this->Cell(30,7,'In-Patient',0,0,'C');
        $this->SetXY(160,23);
        $this->Cell(30,7,'Out-Patient',0,0,'C');
        $this->SetXY(220,16);
        $this->Cell(10,16,'Total',0,0,'C');
        
        $this->SetXY(240,18);
        $this->Cell(20,7,'% of Grand',0,0,'C');
        $this->SetXY(240,22);
        $this->Cell(20,7,'Total',0,0,'C');
        $this->SetXY(30,28);
        
        parent::Header();
        
    }
    
    
    function FetchData() {        
        
        if (empty($this->to)) $end_date="NOW()";
        else $end_date=$this->to;
        if (empty($this->from)) $start_date="NOW()";
        else $start_date=$this->from;

        //laboratory
        
        $sql = "SELECT * FROM seg_lab_service_groups;";

        $result=$this->Conn->Execute($sql);
        $this->_count = $result->RecordCount();
        $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($result) {
            $this->Data=array();
            $i=1;
            $this->Data[]=array("Laboratory Examination",'','','','','','','','','','','','');
            while ($row=$result->FetchRow()) {
                $service_group = $row["name"];
                $group_code = $row["group_code"];
                $this->Data[]=array($i .". ".$service_group,'','','','','','','','','','','','');
                $j=0;
                $sql = "SELECT * FROM seg_lab_services WHERE group_code='".$group_code."';";
                $result2=$this->Conn->Execute($sql);
                $this->_count = $this->_count + $result2->RecordCount(); 
                if($result2)
                {
                    while ($row=$result2->FetchRow()) {
                        $service = $row["name"];
                        //$this->Data[]=array($i.".".$j.". ".$service,'','','','','','','','','','','','');
                        $this->Data[]=array("   ".$i.".".$j.". ".$service,'trial','trialdata','trial','trial','trialdata','trialdata','trialdata','trialdata','trial','trial','trialdata','trialdata');
                        $j++;
                    }
                }
                $i++;
            }
            /*if (trim($row['street_name'])){
                    if (trim($row["brgy_name"])!="NOT PROVIDED")
                        $street_name = trim($row['street_name']).", ";
                    else
                        $street_name = trim($row['street_name']).", ";    
            }else{
                    $street_name = "";    
            }    
                
        
            if ((!(trim($row["brgy_name"]))) || (trim($row["brgy_name"])=="NOT PROVIDED"))
                $brgy_name = "";
            else 
                $brgy_name  = trim($row["brgy_name"]).", ";    
                    
            if ((!(trim($row["mun_name"]))) || (trim($row["mun_name"])=="NOT PROVIDED"))
                $mun_name = "";        
            else{    
                if ($brgy_name)
                    $mun_name = trim($row["mun_name"]);    
                else
                    $mun_name = trim($row["mun_name"]);        
            }
                
            if ((!(trim($row["prov_name"]))) || (trim($row["prov_name"])=="NOT PROVIDED"))
                $prov_name = "";        
            else
                $prov_name = trim($row["prov_name"]);            
                
            if(stristr(trim($row["mun_name"]), 'city') === FALSE){
                if ((!empty($row["mun_name"]))&&(!empty($row["prov_name"]))){
                    if ($prov_name!="NOT PROVIDED")    
                        $prov_name = ", ".trim($prov_name);
                    else
                        $prov_name = trim($prov_name);    
                }else{
                    $prov_name = "";
                }
            }else
                $prov_name = "";    
                
            $addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);
                
            $physician = "";
            
            #$row['consult_date'],
            */

        }
        else
            echo $this->Conn->ErrorMsg();
    }
}

$rep = new RepGen_Lab_Services($_GET['from'],$_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>