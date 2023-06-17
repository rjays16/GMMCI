<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

class RepGen_Statistics_ICD_Encoded extends RepGen
{	//start of class
	var $colored = FALSE;
	var $fromdate;
	var $todate;
	var $fontfamily_label = "Arial";
	var $fontfamily_answer = "Arial";
	var $fontstyle_bold = "B";
	var $fontstyle_italize = "I";
	var $fontstyle_normal = '';
	var $fontsize1 = 12;
	var $fontsize2 = 10;
	var $fontsize3 = 9;
	var $fontsize4 = 8;
	var $withoutborder = 0;
	var $withborder = 1;
	var $nextline = 1;
	var $continueline = 0;
	var $alignCenter = "C";
	var $alignLeft = "L";
	var $alignRight = "R";
	var $rowheight = 4;
	var $lineAdjustment = 0.5;

	function RepGen_Statistics_ICD_Encoded ($fromdate, $todate, $ptype, $dept, $encoder)
	{	//start of function
		global $db;
		$this->RepGen("STATISTICS FOR ICD ENCODED");
		# 165
		$this->ColumnWidth = array(100, 48, 48);
		$this->RowHeight = 5.5;
		$this->Alignment = array('L', 'C', 'C');
		$this->ColumnLabel = array('DEPARTMENT', 'NPHIC', 'PHIC');
		$this->PageOrientation = "P";
		if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
		if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
		$this->encoder = $encoder;
		$this->dept = $dept;
		$this->ptype = $ptype;
		#echo "encoder= ".$this->encoder." dept= ".$this->dept." ptype= ".$this->ptype;
	}	//end of function

	function Header()
	{	//start of function Header
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$pers_obj=new Personell;

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else
		{
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
		}

		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_italize,$this->fontsize3);
		$total_w = 165;
		$this->Cell(17,$this->rowheight);

		$this->Cell($total_w,$this->rowheight,$row['hosp_country'],$this->withoutborder,$this->nextline,$this->alignCenter);
		$this->Cell(17,$this->rowheight);

		$this->Cell($total_w,$this->rowheight,$row['hosp_agency'],$this->withoutborder,$this->nextline,$this->alignCenter);
		$this->Ln(2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_bold, $this->fontsize2);
		$this->Cell(17,$this->rowheight);

		$this->Cell($total_w,$this->rowheight,$row['hosp_name'],$this->withoutborder,$this->nextline,$this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_normal, $this->fontsize3);
		$this->Cell(17,$this->rowheight);

		$this->Cell($total_w,$this->rowheight,$row['hosp_addr1'],$this->withoutborder,$this->nextline,$this->alignCenter);
		$this->Ln($this->rowheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_bold, $this->fontsize1);
		$this->Cell(17,$this->rowheight);

		$this->Cell($total_w,$this->rowheight,'STATISTICS FOR ICD ENCODED',$this->withoutborder,$this->nextline,$this->alignCenter);
		$this->SetFont($this->fontfamily_label,$this->fontstyle_bold,$this->fontsize3);
		$this->Cell(17,$this->rowheight);

		if ($this->fromdate==$this->todate)
			$text = "For ".date("F j, Y",strtotime($this->fromdate));
		else
				#$text = "Full History";
		$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));

		$this->Cell($total_w,$this->rowheight,$text,$this->withoutborder,$this->nextline,$this->alignCenter);
		$this->Ln($this->rowheight);

		$this->Cell(17, $this->rowheight, "Encoder", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(3, $this->rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);

		if ($this->encoder!='all'){
			$row = $pers_obj->getPersonellInfo($this->encoder);
			$string = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
		}else{
			//$this->Cell($total_w,4,'ALL SOCIAL WORKERS',$border2,1,'C');
			$string = "ALL MEDICAL RECORDS PERSONNEL";
		}

		//$string = strtoupper($this->encoder);
		$length_string = $this->GetStringWidth($string);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$length_string, $y+($this->rowheight - $this->lineAdjustment));
		//put encoder here...
		$this->Cell($length_string, $this->rowheight, $string, $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell(20, $this->rowheight, "# of Records", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(3, $this->rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		//put npumber of records here...
		$this->Cell(10, $this->rowheight, $this->_count, $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+10, $y+($this->rowheight - $this->lineAdjustment));

		$this->Ln();

		# Print table header
		$this->SetFont('Arial','B',9);

		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;

		$this->Cell($this->ColumnWidth[0], $this->rowheight, $this->ColumnLabel[0], $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[1], $this->rowheight, $this->ColumnLabel[1], $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[2], $this->rowheight, $this->ColumnLabel[2], $this->withborder, $this->nextline, $this->alignCenter);

		//$this->Ln();

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
		}else{
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell($this->ColumnWidth[0], $this->rowheight, "GRAND TOTAL", $this->withborder, $this->continueline, $this->alignRight);
			$this->Cell($this->ColumnWidth[1], $this->rowheight, $this->total_nphic, $this->withborder, $this->continueline, $this->alignCenter);
			$this->Cell($this->ColumnWidth[1], $this->rowheight, $this->total_phic, $this->withborder, $this->nextline, $this->alignCenter);

		}
		$cols = array();
	}	//end of function AfterData

	function FetchData()
	{
		global $db;
		$pers_obj=new Personell;
		$this->total_nphic = 0;
		$this->total_phic = 0;
		/*
		if (($this->fromdate)&&($this->todate))
		{
			#$where[]="DATE(e.discharge_date)='$this->date'";
			#$whereSQL = " AND (e.discharge_date>='".$this->fromdate."' AND e.discharge_date<='".$this->todate."')";
			#$where[]="DATE(e.discharge_date) BETWEEN '$this->fromdate' AND '$this->todate'";
		}
		if ($where)
			$whereSQL = "AND (".implode(") AND (",$where).")";
		 */

		if ($this->encoder!='all'){
			$enc = $pers_obj->getPersonellInfo($this->encoder);
			$encoder_fullname = ucwords(strtolower($enc["name_first"]))." ".ucwords(strtolower($enc["name_last"]));
			$this->encoder_fullname = "AND ced.create_id = '".$encoder_fullname."'";
			//$this->Cell($total_w,4,'SOCIAL WORKER : '.$this->encoder_fullname,$border2,1,'C');
		}else{
			//$this->Cell($total_w,4,'ALL SOCIAL WORKERS',$border2,1,'C');
			$this->encoder_fullname = "";
		}

		 /*if ($this->dept){
			if ($this->dept==1)
					#Gynecology
					$sql_dept = " AND (e.current_dept_nr='124' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='124'))  ";
			elseif ($this->dept==2)
					#Medicines
					$sql_dept = " AND (e.current_dept_nr IN (133,154,104) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='133')OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='154') OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='104')) ";
			elseif ($this->dept==3)
					#Obstetrics
					$sql_dept = " AND (e.current_dept_nr='139' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='139')) ";
			elseif ($this->dept==4)
					#Pediatrics
					$sql_dept = " AND (e.current_dept_nr IN (125) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='125')) ";
			elseif ($this->dept==5)
					#Surgery
					$sql_dept = " AND (e.current_dept_nr IN (117,141,136,131,122) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='117')) ";
		} */

		if($this->dept){
			$sql_dept = " AND ce.current_dept_nr=".$this->dept_nr;
		}

		if($this->ptype == 'all'){
			$include_ptype = "";
		}elseif($this->ptype == '1' || $this->ptype == '2'){
			$include_ptype = "AND e.encounter_type = '".$this->ptype."'";
		}else{
			$include_ptype = "AND e.encounter_type IN ('".$this->ptype."')";
		}
		$sql = "SELECT d.name_formal AS department ,SUM(CASE WHEN cpi.hcare_id=18 then 1 else 0 end) AS PHIC,
						SUM(CASE WHEN cpi.hcare_id!=18 then 1 else 0 end) AS NPHIC
						FROM care_encounter_diagnosis AS ced
						INNER JOIN care_encounter AS e ON e.encounter_nr = ced.encounter_nr
						LEFT JOIN care_person_insurance AS cpi ON cpi.pid = e.pid
						INNER JOIN care_department AS d ON d.nr = e.current_dept_nr
						WHERE ced.code IS NOT NULL
						AND ced.status NOT IN('deleted', 'void', 'hidden', 'cancelled')
						AND e.status NOT IN ('deleted', 'void', 'hidden', 'cancelled')
						$include_ptype
						$sql_dept
						$this->encoder_fullname
						AND DATE(ced.create_time) BETWEEN '".$this->fromdate."' AND '".$this->todate."'
						GROUP BY d.name_formal;";

		#$sql .= "ORDER BY DATE(discharge_date),patient_name";

		#echo "sql = ".$sql;
		$result=$db->Execute($sql);
		if ($result)
		{
			$this->_count = $result->RecordCount();
			$this->Data=array();
			while ($row=$result->FetchRow())
			{
				$this->total_nphic += $row['NPHIC'];
				$this->total_phic += $row['PHIC'];
				$this->Data[]=array(
				mb_strtoupper($row['department']),
				$row['NPHIC'],
				$row['PHIC'],
				);
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

$fromdate = $_GET['fromdate'];
$todate = $_GET['todate'];
$ptype = $_GET['ptype'];
$dept = $_GET['dept_nr_sub'];
$encoder = $_GET['encoder'];
$rep = new RepGen_Statistics_ICD_Encoded($fromdate, $todate, $ptype, $dept, $encoder);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>