<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class BloodBankSummary extends FPDF{
	var $from;
	var $to;
	var $count_rows;
	var $code1;
	var $code2;

	function BloodBankSummary($from, $to){
		global $db;
		$this->ColumnWidth = array(85,21,21,21,21,21,21,21,21,21,21,21,21);
		$this->SetTopMargin(3);
		$this->Alignment = array('L','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->FPDF("L", 'mm', 'Legal');
		$this->code1 = trim($code).".-";
		$this->code2 = trim($code)."-";
		//$this->code = $code;

		if($code != 'all')
		//$this->icd_cond = "AND (ed.code_parent = '".$this->code."'";
		$this->icd_cond = "AND (ed.code_parent = '".$this->code1."' OR ed.code_parent = '".$this->code2."')";
	else
		$this->icd_cond = "";
		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));

		$this->year = date("Y", strtotime($from));
		$this->January = "DATE(ls.serv_dt) BETWEEN '".$this->year."-01-01' AND '".$this->year."-01-31'";
		$this->February = "DATE(ls.serv_dt) BETWEEN '".$this->year."-02-01' AND '".$this->year."-02-29'";
		$this->March = "DATE(ls.serv_dt) BETWEEN '".$this->year."-03-01' AND '".$this->year."-03-31'";
		$this->April = "DATE(ls.serv_dt) BETWEEN '".$this->year."-04-01' AND '".$this->year."-04-31'";
		$this->May = "DATE(ls.serv_dt) BETWEEN '".$this->year."-05-01' AND '".$this->year."-05-31'";
		$this->June = "DATE(ls.serv_dt) BETWEEN '".$this->year."-06-01' AND '".$this->year."-06-31'";
		$this->July = "DATE(ls.serv_dt) BETWEEN '".$this->year."-07-01' AND '".$this->year."-07-31'";
		$this->August = "DATE(ls.serv_dt) BETWEEN '".$this->year."-08-01' AND '".$this->year."-08-31'";
		$this->September = "DATE(ls.serv_dt) BETWEEN '".$this->year."-09-01' AND '".$this->year."-09-31'";
		$this->October = "DATE(ls.serv_dt) BETWEEN '".$this->year."-10-01' AND '".$this->year."-10-31'";
		$this->November = "DATE(ls.serv_dt) BETWEEN '".$this->year."-11-01' AND '".$this->year."-11-31'";
		$this->December = "DATE(ls.serv_dt) BETWEEN '".$this->year."-12-01' AND '".$this->year."-12-31'";

		$this->January2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-01-01' AND '".$this->year."-01-31'";
		$this->February2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-02-01' AND '".$this->year."-02-29'";
		$this->March2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-03-01' AND '".$this->year."-03-31'";
		$this->April2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-04-01' AND '".$this->year."-04-31'";
		$this->May2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-05-01' AND '".$this->year."-05-31'";
		$this->June2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-06-01' AND '".$this->year."-06-31'";
		$this->July2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-07-01' AND '".$this->year."-07-31'";
		$this->August2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-08-01' AND '".$this->year."-08-31'";
		$this->September2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-09-01' AND '".$this->year."-09-31'";
		$this->October2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-10-01' AND '".$this->year."-10-31'";
		$this->November2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-11-01' AND '".$this->year."-11-31'";
		$this->December2 = "DATE(spo.serve_dt) BETWEEN '".$this->year."-12-01' AND '".$this->year."-12-31'";

	}

	function Header() {
		global $root_path, $db;
		$rowheight = 5;

		$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
		}

		$this->SetFont('Arial', '', 10);
		$this->Cell(0, $rowheight, $row['hosp_country'], 0, 1, 'C');
		$this->Cell(0, $rowheight, $row['hosp_name'], 0, 1, 'C');
		$this->Ln();

		$this->SetFont('Arial', 'B', 11);
		$this->Cell(0, $rowheight, "SUMMARY BLOOD BANK REPORT", 0, 1, 'C');

		#$this->SetFont('Arial', 'B', 14);
		#$this->Cell(0, 6, "NOTIFIABLE DISEASES",0,1,'C');

		$this->SetFont('Arial', '', 10);
		if($this->from == $this->to){
			$this->Cell(0,6, "As of ".date("F j, Y",strtotime($this->from)), 0,1,'C');
		}
		else{
			$this->Cell(0, 4, "From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to)),0,1,'C');
		}
		$this->Ln(5);
		#Table Header
		$this->SetFont('Arial', 'B', 8);
		#$this->SetLeftMargin(2);
		#$this->Cell(18, 4, "Diagnosis : ",0,0,'L');
		#$this->Cell(20, 4, "", 0,0,'L');
		#$this->Cell(20,4,"Description : ",0,0,'L');
		#$this->Cell(0,4,"", 0,1,'L');



	}

	function FetchDataCrossMatch(){
		global $db;
		$rowheight = 5;

		$this->SetFont('Arial', 'B', 9);
		$this->Cell(0, $rowheight, "BLOOD CROSSMATCHING", 0, 1, 'L');

		$this->SetFont('Arial', 'B', 9);
		$this->Cell($this->ColumnWidth[0], $rowheight, "", 1,0,'C');
		$this->Cell($this->ColumnWidth[1], $rowheight, "JANUARY", 1,0,'C');
		$this->Cell($this->ColumnWidth[2], $rowheight, "FEBRUARY", 1,0,'C');
		$this->Cell($this->ColumnWidth[3], $rowheight, "MARCH", 1,0,'C');
		$this->Cell($this->ColumnWidth[4], $rowheight, "APRIL", 1,0,'C');
		$this->Cell($this->ColumnWidth[5], $rowheight, "MAY", 1,0,'C');
		$this->Cell($this->ColumnWidth[6], $rowheight, "JUNE", 1,0,'C');
		$this->Cell($this->ColumnWidth[7], $rowheight, "JULY", 1,0,'C');
		$this->Cell($this->ColumnWidth[8], $rowheight, "AUGUST", 1,0,'C');
		$this->Cell($this->ColumnWidth[9], $rowheight, "SEPTEMBER", 1,0,'C');
		$this->Cell($this->ColumnWidth[10], $rowheight, "OCTOBER", 1,0,'C');
		$this->Cell($this->ColumnWidth[11], $rowheight, "NOVEMBER", 1,0,'C');
		$this->Cell($this->ColumnWidth[12], $rowheight, "DECEMBER", 1,0, 'C');
		$this->Ln();

		$sql_cross_tot = "SELECT SUM(CASE WHEN $this->January then 1 else 0 end) AS jan_total,
					SUM(CASE WHEN $this->February then 1 else 0 end) AS feb_total,
					SUM(CASE WHEN $this->March then 1 else 0 end) AS mar_total,
					SUM(CASE WHEN $this->April then 1 else 0 end) AS apr_total,
					SUM(CASE WHEN $this->May then 1 else 0 end) AS may_total,
					SUM(CASE WHEN $this->June then 1 else 0 end) AS jun_total,
					SUM(CASE WHEN $this->July then 1 else 0 end) AS jul_total,
					SUM(CASE WHEN $this->August then 1 else 0 end) AS aug_total,
					SUM(CASE WHEN $this->September then 1 else 0 end) AS sep_total,
					SUM(CASE WHEN $this->October then 1 else 0 end) AS oct_total,
					SUM(CASE WHEN $this->November then 1 else 0 end) AS nov_total,
					SUM(CASE WHEN $this->December then 1 else 0 end) AS dec_total
					FROM seg_lab_serv AS ls
					INNER JOIN seg_lab_serv_crossmatched AS slm ON slm.refno = ls.refno
					WHERE ls.ref_source = 'BB'
					AND DATE(ls.serv_dt) BETWEEN '".$this->from."' AND '".$this->to."';";
		 //echo $sql_cross_tot;
		 $result_cross_tot = $db->Execute($sql_cross_tot);
		 $row_cross_tot = $result_cross_tot->FetchRow();
		 //print_r($row_cross_tot);

		 if(!$row_cross_tot['jan_total'])
			$row_cross_tot['jan_total'] = 0;
		 if(!$row_cross_tot['feb_total'])
			$row_cross_tot['feb_total'] = 0;
		 if(!$row_cross_tot['mar_total'])
			$row_cross_tot['mar_total'] = 0;
		 if(!$row_cross_tot['apr_total'])
			$row_cross_tot['apr_total'] = 0;
		 if(!$row_cross_tot['may_total'])
			$row_cross_tot['may_total'] = 0;
		 if(!$row_cross_tot['jun_total'])
			$row_cross_tot['jun_total'] = 0;
		 if(!$row_cross_tot['jul_total'])
			$row_cross_tot['jul_total'] = 0;
		 if(!$row_cross_tot['aug_total'])
			$row_cross_tot['aug_total'] = 0;
		 if(!$row_cross_tot['sep_total'])
			$row_cross_tot['sep_total'] = 0;
		 if(!$row_cross_tot['oct_total'])
			$row_cross_tot['oct_total'] = 0;
		 if(!$row_cross_tot['nov_total'])
			$row_cross_tot['nov_total'] = 0;
		 if(!$row_cross_tot['dec_total'])
			$row_cross_tot['dec_total'] = 0;

		$this->SetFont('Arial', '', 9);
		$this->Cell($this->ColumnWidth[0], $rowheight, "TOTAL # OF PATIENT CROSSMATCHED", 1,0,$this->Alignment[0]);
		$this->Cell($this->ColumnWidth[1], $rowheight, $row_cross_tot['jan_total'], 1,0,$this->Alignment[1]);
		$this->Cell($this->ColumnWidth[2], $rowheight, $row_cross_tot['feb_total'], 1,0,$this->Alignment[2]);
		$this->Cell($this->ColumnWidth[3], $rowheight, $row_cross_tot['mar_total'], 1,0,$this->Alignment[3]);
		$this->Cell($this->ColumnWidth[4], $rowheight, $row_cross_tot['apr_total'], 1,0,$this->Alignment[4]);
		$this->Cell($this->ColumnWidth[5], $rowheight, $row_cross_tot['may_total'], 1,0,$this->Alignment[5]);
		$this->Cell($this->ColumnWidth[6], $rowheight, $row_cross_tot['jun_total'], 1,0,$this->Alignment[6]);
		$this->Cell($this->ColumnWidth[7], $rowheight, $row_cross_tot['jul_total'], 1,0,$this->Alignment[7]);
		$this->Cell($this->ColumnWidth[8], $rowheight, $row_cross_tot['aug_total'], 1,0,$this->Alignment[8]);
		$this->Cell($this->ColumnWidth[9], $rowheight, $row_cross_tot['sep_total'], 1,0,$this->Alignment[9]);
		$this->Cell($this->ColumnWidth[10], $rowheight, $row_cross_tot['oct_total'], 1,0,$this->Alignment[10]);
		$this->Cell($this->ColumnWidth[11], $rowheight, $row_cross_tot['nov_total'], 1,0,$this->Alignment[11]);
		$this->Cell($this->ColumnWidth[12], $rowheight, $row_cross_tot['dec_total'], 1,0, $this->Alignment[12]);
		$this->Ln();
		$this->Cell($this->ColumnWidth[0], $rowheight, "TOTAL # OF UNITS CROSSMATCHED", 1,0,$this->Alignment[0]);
		$this->Cell($this->ColumnWidth[1], $rowheight, "", 1,0,$this->Alignment[1]);
		$this->Cell($this->ColumnWidth[2], $rowheight, "", 1,0,$this->Alignment[2]);
		$this->Cell($this->ColumnWidth[3], $rowheight, "", 1,0,$this->Alignment[3]);
		$this->Cell($this->ColumnWidth[4], $rowheight, "", 1,0,$this->Alignment[4]);
		$this->Cell($this->ColumnWidth[5], $rowheight, "", 1,0,$this->Alignment[5]);
		$this->Cell($this->ColumnWidth[6], $rowheight, "", 1,0,$this->Alignment[6]);
		$this->Cell($this->ColumnWidth[7], $rowheight, "", 1,0,$this->Alignment[7]);
		$this->Cell($this->ColumnWidth[8], $rowheight, "", 1,0,$this->Alignment[8]);
		$this->Cell($this->ColumnWidth[9], $rowheight, "", 1,0,$this->Alignment[9]);
		$this->Cell($this->ColumnWidth[10], $rowheight, "", 1,0,$this->Alignment[10]);
		$this->Cell($this->ColumnWidth[11], $rowheight, "", 1,0,$this->Alignment[11]);
		$this->Cell($this->ColumnWidth[12], $rowheight, "", 1,0, $this->Alignment[12]);
		$this->Ln();


		$sql_cross_breakdown = "select p.artikelname, p.bestellnum,
														SUM(CASE WHEN $this->January then 1 else 0 end) AS jan,
														SUM(CASE WHEN $this->February then 1 else 0 end) AS feb,
														SUM(CASE WHEN $this->March then 1 else 0 end) AS mar,
														SUM(CASE WHEN $this->April then 1 else 0 end) AS apr,
														SUM(CASE WHEN $this->May then 1 else 0 end) AS may,
														SUM(CASE WHEN $this->June then 1 else 0 end) AS jun,
														SUM(CASE WHEN $this->July then 1 else 0 end) AS jul,
														SUM(CASE WHEN $this->August then 1 else 0 end) AS aug,
														SUM(CASE WHEN $this->September then 1 else 0 end) AS sep,
														SUM(CASE WHEN $this->October then 1 else 0 end) AS october,
														SUM(CASE WHEN $this->November then 1 else 0 end) AS nov,
														SUM(CASE WHEN $this->December then 1 else 0 end) AS december
														from care_pharma_products_main as p
														inner join seg_pharma_products_availability AS av ON av.bestellnum=p.bestellnum
														inner join seg_pharma_areas AS a ON a.area_code=av.area_code
														inner join seg_lab_serv_crossmatched AS slm ON slm.item_id = p.bestellnum
														inner join seg_lab_serv AS ls ON ls.refno = slm.refno
														where p.artikelname like '%blood%'
														AND a.area_code='BB'
														AND DATE(ls.serv_dt) BETWEEN '".$this->from."' AND '".$this->to."'
														GROUP BY artikelname;";
		 $result_cross = $db->Execute($sql_cross_breakdown);
		 #echo $sql_cross_breakdown;

		 while($row_cross = $result_cross->FetchRow()){
				$this->Cell($this->ColumnWidth[0], $rowheight, "   * ".strtoupper($row_cross['artikelname']), 1,0,$this->Alignment[0]);
				$this->Cell($this->ColumnWidth[1], $rowheight, $row_cross['jan'], 1,0,$this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, $row_cross['feb'], 1,0,$this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, $row_cross['mar'], 1,0,$this->Alignment[3]);
				$this->Cell($this->ColumnWidth[4], $rowheight, $row_cross['apr'], 1,0,$this->Alignment[4]);
				$this->Cell($this->ColumnWidth[5], $rowheight, $row_cross['may'], 1,0,$this->Alignment[5]);
				$this->Cell($this->ColumnWidth[6], $rowheight, $row_cross['jun'], 1,0,$this->Alignment[6]);
				$this->Cell($this->ColumnWidth[7], $rowheight, $row_cross['jul'], 1,0,$this->Alignment[7]);
				$this->Cell($this->ColumnWidth[8], $rowheight, $row_cross['aug'], 1,0,$this->Alignment[8]);
				$this->Cell($this->ColumnWidth[9], $rowheight, $row_cross['sep'], 1,0,$this->Alignment[9]);
				$this->Cell($this->ColumnWidth[10], $rowheight, $row_cross['october'], 1,0,$this->Alignment[10]);
				$this->Cell($this->ColumnWidth[11], $rowheight, $row_cross['nov'], 1,0,$this->Alignment[11]);
				$this->Cell($this->ColumnWidth[12], $rowheight, $row_cross['december'], 1,0, $this->Alignment[12]);
				$this->Ln();
		 }

		 $this->Ln();
	}

	function FetchDataTransfusion(){
		global $db;
		$rowheight = 5;

		$this->SetFont('Arial', 'B', 9);
		$this->Cell(0, $rowheight, "BLOOD TRANSFUSION", 0, 1, 'L');

		$this->SetFont('Arial', 'B', 9);
		$this->Cell($this->ColumnWidth[0], $rowheight, "", 1,0,'C');
		$this->Cell($this->ColumnWidth[1], $rowheight, "JANUARY", 1,0,'C');
		$this->Cell($this->ColumnWidth[2], $rowheight, "FEBRUARY", 1,0,'C');
		$this->Cell($this->ColumnWidth[3], $rowheight, "MARCH", 1,0,'C');
		$this->Cell($this->ColumnWidth[4], $rowheight, "APRIL", 1,0,'C');
		$this->Cell($this->ColumnWidth[5], $rowheight, "MAY", 1,0,'C');
		$this->Cell($this->ColumnWidth[6], $rowheight, "JUNE", 1,0,'C');
		$this->Cell($this->ColumnWidth[7], $rowheight, "JULY", 1,0,'C');
		$this->Cell($this->ColumnWidth[8], $rowheight, "AUGUST", 1,0,'C');
		$this->Cell($this->ColumnWidth[9], $rowheight, "SEPTEMBER", 1,0,'C');
		$this->Cell($this->ColumnWidth[10], $rowheight, "OCTOBER", 1,0,'C');
		$this->Cell($this->ColumnWidth[11], $rowheight, "NOVEMBER", 1,0,'C');
		$this->Cell($this->ColumnWidth[12], $rowheight, "DECEMBER", 1,0, 'C');
		$this->Ln();

		$sql_trans_tot = "SELECT IF($this->January2, COUNT(DISTINCT spo.refno), 0) AS january_total,
											IF($this->February2, COUNT(DISTINCT spo.refno), 0) AS february_total,
											IF($this->March2, COUNT(DISTINCT spo.refno), 0) AS march_total,
											IF($this->April2, COUNT(DISTINCT spo.refno), 0) AS april_total,
											IF($this->May2, COUNT(DISTINCT spo.refno), 0) AS may_total,
											IF($this->June2, COUNT(DISTINCT spo.refno), 0) AS june_total,
											IF($this->July2, COUNT(DISTINCT spo.refno), 0) AS july_total,
											IF($this->August2, COUNT(DISTINCT spo.refno), 0) AS august_total,
											IF($this->September2, COUNT(DISTINCT spo.refno), 0) AS september_total,
											IF($this->October2, COUNT(DISTINCT spo.refno), 0) AS october_total,
											IF($this->November2, COUNT(DISTINCT spo.refno), 0) AS november_total,
											IF($this->December2, COUNT(DISTINCT spo.refno), 0) AS december_total
											FROM seg_pharma_order_items AS spo
											INNER JOIN seg_pharma_orders AS sp ON sp.refno = spo.refno
											WHERE sp.pharma_area = 'BB'
											AND DATE(spo.serve_dt) BETWEEN '".$this->from."' AND '".$this->to."'
											GROUP BY spo.refno;";
		$result_trans_tot = $db->Execute($sql_trans_tot);
		$row_trans_tot = $result_trans_tot->FetchRow();

		$this->SetFont('Arial', '', 9);
		$this->Cell($this->ColumnWidth[0], $rowheight, "TOTAL # OF PATIENT CROSSMATCHED", 1,0,$this->Alignment[0]);
		$this->Cell($this->ColumnWidth[1], $rowheight, $row_trans_tot['january_total'], 1,0,$this->Alignment[1]);
		$this->Cell($this->ColumnWidth[2], $rowheight, $row_trans_tot['february_total'], 1,0,$this->Alignment[2]);
		$this->Cell($this->ColumnWidth[3], $rowheight, $row_trans_tot['march_total'], 1,0,$this->Alignment[3]);
		$this->Cell($this->ColumnWidth[4], $rowheight, $row_trans_tot['april_total'], 1,0,$this->Alignment[4]);
		$this->Cell($this->ColumnWidth[5], $rowheight, $row_trans_tot['may_total'], 1,0,$this->Alignment[5]);
		$this->Cell($this->ColumnWidth[6], $rowheight, $row_trans_tot['june_total'], 1,0,$this->Alignment[6]);
		$this->Cell($this->ColumnWidth[7], $rowheight, $row_trans_tot['july_total'], 1,0,$this->Alignment[7]);
		$this->Cell($this->ColumnWidth[8], $rowheight, $row_trans_tot['august_total'], 1,0,$this->Alignment[8]);
		$this->Cell($this->ColumnWidth[9], $rowheight, $row_trans_tot['september_total'], 1,0,$this->Alignment[9]);
		$this->Cell($this->ColumnWidth[10], $rowheight, $row_trans_tot['october_total'], 1,0,$this->Alignment[10]);
		$this->Cell($this->ColumnWidth[11], $rowheight, $row_trans_tot['november_total'], 1,0,$this->Alignment[11]);
		$this->Cell($this->ColumnWidth[12], $rowheight, $row_trans_tot['december_total'], 1,0, $this->Alignment[12]);
		$this->Ln();
		$this->Cell($this->ColumnWidth[0], $rowheight, "TOTAL # OF UNITS TRANSFUSED", 1,0,$this->Alignment[0]);
		$this->Cell($this->ColumnWidth[1], $rowheight, "", 1,0,$this->Alignment[1]);
		$this->Cell($this->ColumnWidth[2], $rowheight, "", 1,0,$this->Alignment[2]);
		$this->Cell($this->ColumnWidth[3], $rowheight, "", 1,0,$this->Alignment[3]);
		$this->Cell($this->ColumnWidth[4], $rowheight, "", 1,0,$this->Alignment[4]);
		$this->Cell($this->ColumnWidth[5], $rowheight, "", 1,0,$this->Alignment[5]);
		$this->Cell($this->ColumnWidth[6], $rowheight, "", 1,0,$this->Alignment[6]);
		$this->Cell($this->ColumnWidth[7], $rowheight, "", 1,0,$this->Alignment[7]);
		$this->Cell($this->ColumnWidth[8], $rowheight, "", 1,0,$this->Alignment[8]);
		$this->Cell($this->ColumnWidth[9], $rowheight, "", 1,0,$this->Alignment[9]);
		$this->Cell($this->ColumnWidth[10], $rowheight, "", 1,0,$this->Alignment[10]);
		$this->Cell($this->ColumnWidth[11], $rowheight, "", 1,0,$this->Alignment[11]);
		$this->Cell($this->ColumnWidth[12], $rowheight, "", 1,0, $this->Alignment[12]);
		$this->Ln();

		$sql_trans_breakdown = "SELECT p.artikelname, p.bestellnum,
												SUM(CASE WHEN $this->January2 then 1 else 0 end) AS january,
												SUM(CASE WHEN $this->February2 then 1 else 0 end) AS february,
												SUM(CASE WHEN $this->March2 then 1 else 0 end) AS march,
												SUM(CASE WHEN $this->April2 then 1 else 0 end) AS april,
												SUM(CASE WHEN $this->May2 then 1 else 0 end) AS may,
												SUM(CASE WHEN $this->June2 then 1 else 0 end) AS june,
												SUM(CASE WHEN $this->July2 then 1 else 0 end) AS july,
												SUM(CASE WHEN $this->August2 then 1 else 0 end) AS august,
												SUM(CASE WHEN $this->September2 then 1 else 0 end) AS september,
												SUM(CASE WHEN $this->October2 then 1 else 0 end) AS october,
												SUM(CASE WHEN $this->November2 then 1 else 0 end) AS november,
												SUM(CASE WHEN $this->December2 then 1 else 0 end) AS december
												FROM care_pharma_products_main as p
												INNER JOIN seg_pharma_products_availability AS av ON av.bestellnum=p.bestellnum
												INNER JOIN seg_pharma_areas AS a ON a.area_code=av.area_code
												INNER JOIN seg_pharma_order_items AS spo ON spo.bestellnum = p.bestellnum
												INNER JOIN seg_pharma_orders AS sp ON sp.refno = spo.refno
												WHERE p.artikelname LIKE '%blood%'
												AND a.area_code='BB'
												AND DATE(spo.serve_dt) BETWEEN '".$this->from."' AND '".$this->to."'
												GROUP BY artikelname;";
		$result_trans = $db->Execute($sql_trans_breakdown);
		//$row_trans = $result_trans->FetchRow();

		 while($row_trans = $result_trans->FetchRow()){
				$this->Cell($this->ColumnWidth[0], $rowheight, "   * ".strtoupper($row_trans['artikelname']), 1,0,$this->Alignment[0]);
				$this->Cell($this->ColumnWidth[1], $rowheight, $row_trans['january'], 1,0,$this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, $row_trans['february'], 1,0,$this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, $row_trans['march'], 1,0,$this->Alignment[3]);
				$this->Cell($this->ColumnWidth[4], $rowheight, $row_trans['april'], 1,0,$this->Alignment[4]);
				$this->Cell($this->ColumnWidth[5], $rowheight, $row_trans['may'], 1,0,$this->Alignment[5]);
				$this->Cell($this->ColumnWidth[6], $rowheight, $row_trans['june'], 1,0,$this->Alignment[6]);
				$this->Cell($this->ColumnWidth[7], $rowheight, $row_trans['july'], 1,0,$this->Alignment[7]);
				$this->Cell($this->ColumnWidth[8], $rowheight, $row_trans['august'], 1,0,$this->Alignment[8]);
				$this->Cell($this->ColumnWidth[9], $rowheight, $row_trans['september'], 1,0,$this->Alignment[9]);
				$this->Cell($this->ColumnWidth[10], $rowheight, $row_trans['october'], 1,0,$this->Alignment[10]);
				$this->Cell($this->ColumnWidth[11], $rowheight, $row_trans['november'], 1,0,$this->Alignment[11]);
				$this->Cell($this->ColumnWidth[12], $rowheight, $row_trans['december'], 1,0, $this->Alignment[12]);
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
		$row = 4;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidth[$i],$data[$i]));
					$nb2=$this->NbLines($this->ColumnWidth[4],$data[4]);
					$nb3=$this->NbLines($this->ColumnWidth[6],$data[6]);
					#echo "(nb_2): ".$nb2." (nb_3): ".$nb3;
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

				 $l_data4 = $this->GetStringWidth($data[4]);
				 $l_data6 = $this->GetStringWidth($data[6]);
				 #echo "data4: ".$l_data4." data6:".$l_data6;
						if(($l_data4 >$l_data6) && ($l_data6 > $this->ColumnWidth[6]) && ($nb2 > $nb3)){
							$lgreater = $l_data4;
							$ldiff = $lgreater - $l_data6;
							#echo intval($l);
							#echo "l_data4: ".$l_data4." l_data6: ".$l_data6." ldiff: ".$ldiff;
								for($cnt = 0; $cnt<intval($ldiff); $cnt++)
									 $data[6].= " ";

						}else if(($l_data6 > $l_data4) && ($l_data4 > $this->ColumnWidth[4]) && ($nb3 > $nb2)){

							$lgreater = $l_data6;
							$ldiff = $lgreater - $l_data4;
							#echo "l_data6: ".$l_data6." l_data4: ".$l_data4." ldiff: ".$ldiff;
								for($cnt = 0; $cnt<intval($ldiff); $cnt++)
									$data[4].=" ";
						}
				 $l_data0 = $this->GetStringWidth($data[0]);
				 $l_data8 = $this->GetStringWidth($data[8]);

					if($l_data0 > $this->ColumnWidth[0]){
						$ldiff2 = $lgreater - $l_data0;
						for($cnt1 = 0; $cnt1<intval($ldiff2); $cnt1++)
							$data[0].=" ";
					}

					if($l_data8 > $this->ColumnWidth[8]){
						$ldiff3 = $lgreater - $l_data8;
						for($cnt2 = 0; $cnt2<intval($ldiff3); $cnt2++)
							$data[8].=" ";
					}
								#echo $data[6];
					//$nb = $nb*2;
					//print_r($nb);
			$h=$row*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row

			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidth[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border

							$length = $this->GetStringWidth($data[$i]);
							if($length < $this->ColumnWidth[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								$this->Cell($w, $h, $data[$i], 1, 0, $this->Alignment[$i]);
							}
							else{
								$nbrow = 3;

								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								$this->MultiCell($w, $row, $data[$i], 1,$this->Alignment[$i]);

								//$this->MultiCell($length, $row,$data[$i],1,'L');

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

$from = $_GET['fromdate'];
$to = $_GET['todate'];

$pdf = new BloodBankSummary($from, $to);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->FetchDataCrossMatch();
$pdf->FetchDataTransfusion();
$pdf->Output();
?>