<?php
/**
* SegHIS - Hospital Information System (BPH Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
include_once($root_path.'include/care_api_classes/inventory/class_request.php');
include_once($root_path.'include/care_api_classes/inventory/class_issuance.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/inventory/class_unit.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_req_iss_slip_internal extends DMCRepGen {

    var $issrefno;
    var $refno;   
    var $left;
    var $right;
    var $top;
    var $date;
    
    function RepGen_req_iss_slip_internal($refno='') {
        global $db;
        
        $this->DMCRepGen("RADIOLOGY", "P", "Letter", $db, TRUE);
        
        //$this->Caption = "Radiology Services";
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(35,20,25,97,20,30,30);
        $this->Columns = 7;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        $this->Alignment = array('L','l','C','L','L','R','R');
        $this->PageOrientation = "L";
        //if ($to) $this->to_date=$to;
        
        if($date) $this->date = $date;
        else $this->date=date("Y-m-d");
        
        $this->issrefno = $refno;
        //$this->issrefno = '987651'; //issrefno ni
        
        $this->date=date("Y-m-d",strtotime($this->date));

        $this->NoWrap = FALSE;
        $this->colored = TRUE;

        
    }
    
    function Header() {
        
        $total_w = 0; 

        $this->SetFont("Arial","B","14");
        $this->Ln(6);
        #$this->Cell(17,4);
        //insert information
        $objInfo = new Hospital_Admin();
    
        if ($row = $objInfo->getAllHospitalInfo()) {      
          $row['hosp_agency'] = strtoupper($row['hosp_agency']);
          $row['hosp_name']   = strtoupper($row['hosp_name']);
        }
        $this->SetFont('Arial','B',12);
        //$this->SetXY(13,32);
        $this->MultiCell($total_w, 3,$row['hosp_name']." OF MALAYBALAY", '', 'C','');
        $this->Ln(1);
        $this->SetFont('Arial','',11);
        $this->MultiCell($total_w, 3,"Malaybalay, Bukidnon",'', 'C','');
        
        $this->Ln(2);
        $this->SetFont('Arial','',14);
        $this->Cell($total_w,7,'REQUISITION & ISSUE SLIP',0,0,'C');
        $this->Ln(2); 
       
        
    }
    
    function BeforeData() {
        $req_obj = new Request();
        $iss_obj =  new Issuance();
        
        $req_served = $iss_obj->getReqServedbyIss($this->issrefno);
        $row = $req_served->FetchRow();
        $this->refno = $row['request_refno'];
        $header = $req_obj->getRequestHeader($row['request_refno']);
        
        $this->FONTSIZE = 8.7;
        if ($this->colored) {
            $this->DrawColor = array(0,0,0);
            #$this->DrawColor = array(255,255,255);
        }
        
        //draw lines
        $this->left = 13;
        $this->right = 270;
        
        $this->SetDrawColor(0,0,0);
        
        if($header){  
            $this->SetFont('Arial','B',11);
            $this->SetXY(232,11);
            $this->Cell(53.5,5,$header['refno'],0,0,'L'); 
            $this->refno = $header['refno'];
            $this->SetFont('Arial','B',11);
            $this->SetXY(230,21);
            $this->Cell(53.5,5,$this->issrefno,0,0,'L');
            $thedate=date("Y-m-d",strtotime($header['request_date']));
            $this->SetXY(228,31);
            $this->Cell(53.5,5,$thedate,0,0,'L');            
        }

        //labels
        $this->SetFont('Arial','',11);
        $this->SetXY(217,12);
        $this->Cell(53.5,5,'Req No: _________________',0,0,'L');
        
        $this->SetXY(217,22);
        $this->Cell(53.5,5,'Iss No: ________________',0,0,'L');
        
        $this->SetXY(217,32);
        $this->Cell(53.5,5,'Date: ________________',0,0,'L');
        
        $this->SetXY(13,35);
        $this->Cell(53.5,5,'The Supply Section In-charge',0,0,'L');
        $this->SetXY(13,39);
        $this->Cell(53.5,5,'BPHM, Maramag, Bukidnon',0,0,'L'); 
        $this->SetXY(13,48);
        $this->Cell(53.5,5,'S i r :',0,0,'L'); 
        $this->SetXY(30,52);
        $this->Cell(53.5,5,'Please furnish the following supplies/materials:',0,0,'L');

        //draw the lines
        $this->Line($this->left,58,$this->right,58);
        $this->Line($this->left,64,$this->right,64);
        $this->Line($this->left,76,$this->right,76); 
        
        $this->Line(13,58,13,76);
        $this->Line(190,58,190,76);
        $this->Line(270,58,270,76);
        
        $this->Line(48,64,48,76);
        $this->Line(68,64,68,76);
        
        $this->Line(93,64,93,76);
        
        $this->Line(210,64,210,76);
        $this->Line(240,64,240,76);
        
        //labels
        $this->SetXY(13,58);
        $this->Cell(177,5,'R E Q U I S I T I O N',0,0,'C');
        $this->SetXY(190,58);
        $this->Cell(80,5,'I S S U E D',0,0,'C');
        $this->SetXY(13,68);
        $this->Cell(35,5,'No',0,0,'C');
        $this->SetXY(48,68);
        $this->Cell(20,5,'QTY',0,0,'C');
        $this->SetXY(68,68);
        $this->Cell(25,5,'UNIT',0,0,'C');
        $this->SetXY(93,68);
        $this->Cell(97,5,'A  R  T  I  C  L  E  /  S',0,0,'C');
        $this->SetXY(190,68);
        $this->Cell(20,5,'QTY',0,0,'C');
        $this->SetXY(210,68);
        $this->Cell(30,5,'UNIT PRICE',0,0,'C');
        $this->SetXY(240,68);
        $this->Cell(30,5,'TOTAL VALUE',0,0,'C');
        
        $this->top = 50;
        $this->SetXY(13,76);
        $this->SetFont('Arial','',9);
    }

    function FetchData() {
        $this->_count = 1;   
        
        $req_obj = new Request();
        $iss_obj = new Issuance();
        $prod_obj = new SegPharmaProduct();
        $unit_obj = new Unit();
        
        $refdetails = $req_obj->getRequestDetails($this->refno);
        $reqserveddetails = $iss_obj->getReqServedbyIss($this->issrefno);
        
        if($reqserveddetails){
            while($row = $reqserveddetails->FetchRow()){
            
                $reqdetails = $req_obj->getRequestDetailInfo($row['request_refno'], $row['item_code']);
                
                if($reqdetails){
                    $comqty = 0;
                    $the_unit = '';
                    $count = $reqdetails->RecordCount();
                    $itemextended = $prod_obj->getExtendedProductInfo($row['item_code']);
                    $iteminfo = $prod_obj->getProductInfo($row['item_code']);
                    
                    $issdetails = $iss_obj->getIssuanceDetailInfo($row['issue_refno'],$row['item_code']);
                    $rowissdet = $issdetails->FetchRow();
                    
                    while($rowreqdet = $reqdetails->FetchRow()){
                        if($count > 1){
                            $the_unit = $itemextended['pc_unit_id'];                            
                            if($rowreqdet['is_unitperpc'] != 1){
                                $comqty += $rowreqdet['item_qty'] * $itemextended['qty_per_pack'];
                            }
                            else{
                                $comqty += $rowreqdet['item_qty'];
                            }
                        } 
                        else{
                            $comqty += $rowreqdet['item_qty'];
                            $the_unit = $rowreqdet['unit_id'];
                        }               
                    }
                    $this->Data[]=array(
                        $row['item_code'],
                        $comqty,
                        $unit_obj->getUnitName($the_unit),
                        $iteminfo['artikelname'],
                        $row['served_qty'],
                        $rowissdet['avg_cost'],
                        $row['served_qty'] * $rowissdet['avg_cost']
                                                      
                    );     
                }
            }
        }     
        
        
    }
    
    function AfterData() {
        global $db;
        
        $person_obj = new Personell();
        $iss_obj = new Issuance();
        $req_obj = new Request();

        $issheader = $iss_obj->getIssuanceHeader($this->issrefno);
        $reqheader = $req_obj->getRequestHeader($this->refno);
        
        $request = $person_obj->get_Person_name($reqheader['requestor_id']);
        $issue = $person_obj->get_Person_name($issheader['issuing_id']);
        $acknowledge = $person_obj->get_Person_name($issheader['acknowledging_id']);      
        $authorize = $person_obj->get_Person_name($issheader['authorizing_id']);      

        if (!$this->CM) {
            if (!$this->_count) {
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(0);
                $this->SetTextColor(0);
                $this->Cell(0, $this->RowHeight, "No meds and med supplies found...", 1, 1, 'L', 1);
            }
            
            $this->Ln(7);
            
            $this->SetFont('Arial','',12);
            $this->SetWidths(array(1,80,99,80,10));
            
            $this->RowNoBorder(array('','REQUESTED BY:                                                                                                 _________________________________________  Name and Signature','','RECEIVED BY:                                                                                                             '.$acknowledge['name_first'].' '.$acknowledge['name_last'].'',''));
            $this->Ln(10);
            $this->SetWidths(array(1,100,59,100,10));
            
            $this->RowNoBorder(array('','APPROVED BY:                                                                    _________________________________________  Hospital Administrator or Authorized Representative','','                 ISSUED BY:                                                                                                                                                    '.$issue['name_first'].' '.$issue['name_last'].'',''));
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }

}

$rep = new RepGen_req_iss_slip_internal($_GET['refno']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>