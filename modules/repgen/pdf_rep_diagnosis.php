//cha - discharged diagnosis report

<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_Discharged_Diagnosis extends RepGen 
{	//start of class
	var $colored = TRUE;
	var $fromdate;
	var $todate;

	function RepGen_Top_Discharged_Diagnosis ($fromdate, $todate)
	{	//start of function
		global $db;
		$this->RepGen("TOP DISCHARGED DIAGNOSIS--MEDICINE");
		# 165
		$this->ColumnWidth = array();
		$this->RowHeight = 5.5;
		$this->Alignment = array();
		$this->PageOrientation = "P";
		if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
		if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
	}	//end of function
	
	function Header()
	{	//start of function Header
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		
		if ($row = $objInfo->getAllHospitalInfo()) {			
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else
		{
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";			
		}
	
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 165;
		$this->Cell(17,4);

		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(17,4);

		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
  	$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);

		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);

		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
  	$this->Ln(4);
	  $this->SetFont('Arial','B',12);
		$this->Cell(17,5);
		
		$this->Cell($total_w,4,'REPORT OF DISCHARGES',$border2,1,'C');
	 	$this->SetFont('Arial','B',9);
		$this->Cell(17,5);
		
		if ($this->fromdate==$this->todate)
			$text = "For ".date("F j, Y",strtotime($this->fromdate));
		else
	  		#$text = "Full History";
		$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));
			
  	$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header		
    $this->SetFont('Arial','B',8);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;

		$this->Ln();
		
	}	//end of function Header
	
	function Footer()
	{	//start of function Footer
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}	//end of function Footer
	
	function BeforeRow()
	{	//start of function BeforeRow
		$this->FONTSIZE = 8;
		if ($this->colored)
		{
			if (($this->ROWNUM%2)>0) 
				$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
		}
	}	//end of function BeforeRow
	
	function BeforeData()
	{	//start of function BeforeData
		if ($this->colored)
		{
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
	}	//end of function BeforeData
	
	function BeforeCellRender()
	{	//start of function BeforeCellRender
		$this->FONTSIZE = 8;
		if ($this->colored)
		{
			if (($this->RENDERPAGEROWNUM%2)>0) 
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}	//end of function BeforeCellRender
	
	function AfterData()
	{	//start of function AfterData
		global $db;
		
		if (!$this->_count)
		{
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		$cols = array();
	}	//end of function AfterData
	
	function FetchData()
	{		
		global $db;

		if (($this->fromdate)&&($this->todate))
		{
			#$where[]="DATE(e.discharge_date)='$this->date'";
			#$whereSQL = " AND (e.discharge_date>='".$this->fromdate."' AND e.discharge_date<='".$this->todate."')";
			$where[]="DATE(e.discharge_date) BETWEEN '$this->fromdate' AND '$this->todate'";
		}
		if ($where)
			$whereSQL = "AND (".implode(") AND (",$where).")";
			
		$sql = "";
		
		$sql .= "ORDER BY DATE(discharge_date),patient_name";
		
		#echo "sql = ".$sql;
		$result=$db->Execute($sql);
		if ($result)
		{
			$this->_count = $result->RecordCount();
			$this->Data=array();
			while ($row=$result->FetchRow())
			{
				$this->Data[]=array(
				mb_strtoupper($row['patient_name']),
				$row['pid'],
				$row['encounter_nr'],
				date("m/d/Y",strtotime($row['admission_date'])),
				date("m/d/Y",strtotime($row['discharge_date'])),
				$row['department_name'],
				strtoupper($row['sex']),
				$row['result_desc']);
			}
		}
		else
		{
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}			
	}//end of function FetchData
}//end of class

$fromdate = $_GET['from'];
$todate = $_GET['to'];
$rep = new RepGen_Top_Discharged_Diagnosis($fromdate, $todate);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>