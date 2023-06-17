<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');
require_once($root_path.'include/care_api_classes/class_area.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

		class RepGen_Inventory_dailysupply_release extends RepGen {
		var $area;
		var $date;
		var $date_from;
		var $date_to;
		var $shift_start;
		var $shift_end;
		var $detailed;

		//modified 05-18-2011
		//function RepGen_Inventory_dailysupply_release ($area="", $date=FALSE) {
		function RepGen_Inventory_dailysupply_release ($area="", $date_from=FALSE, $date_to=FALSE) {
				global $db;
				$this->RepGen("INVENTORY",'P','Letter');
				#$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
				$this->ColumnWidth = array(20,30,30,56,15,15,30);
				$this->Alignment = array('C','L','R','R','R','R','L');
				$this->PageOrientation = "P";
				$this->Headers = array(
						'DATE',
						'AREA',
						'ITEM CODE',
						'NAME',
						'QTY',
						'UNIT',
						'RELEASED TO',
						'STATUS',
				);

				//modified 05-18-2011
				//if ($date) $this->date=date("Y-m-d",strtotime($date));
				//else $this->date=date("Y-m-d");
				if ($date_from) $this->date_from=date("Y-m-d",strtotime($date_from));
				else $this->date_from=date("Y-m-d");

				if ($date_to) $this->date_to=date("Y-m-d",strtotime($date_to));
				else $this->date_to=date("Y-m-d");
				//end

				$this->area=$area;
				$this->RowHeight = 6;
				$this->TextHeight = 6;
				$this->colored=FALSE;
				if ($this->colored)    $this->SetDrawColor(0xDD);
		}

		function Header() {
				global $root_path, $db;

				if ($this->area) {
						$sql = "SELECT area_name FROM seg_areas WHERE area_code=".$db->qstr($this->area);
						$this->areaName = $db->GetOne($sql);
				}

		$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "DAVAO MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
		}

				#$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,8,15);
		/*
				$this->SetFont("Arial","I","8");
				$total_w = 0;
				#$this->Cell(19,3);
			$this->Cell($total_w,3,'Republic of the Philippines',$border2,1,'C');
				#$this->Cell(19,3);
			$this->Cell($total_w,3,'DEPARTMENT OF HEALTH',$border2,1,'C');
			$this->Ln(1);
				$this->SetFont("Arial","B","9");
				#$this->Cell(19,4);
			$this->Cell($total_w,4,'DAVAO MEDICAL CENTER',$border2,1,'C');
				$this->SetFont("Arial","B","8");
				#$this->Cell(19,5);*/
		$total_w = 0;
		//this is responsible for the logo in the left side
		$this->Image('../../gui/img/logos/dmc_logo.jpg',20,10,20,20);

		$this->SetFont("Arial","I","9");
		#$this->Cell(17,4);
		$this->Cell($total_w,4, $row['hosp_country'],$border2,1,'C');
		#$this->Cell(17,4);
		$this->Cell($total_w,4, $row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		#$this->Cell(17,4);
		$this->Cell($total_w,4, $row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		#$this->Cell(17,4);
		$this->Cell($total_w,4, $row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);
		$this->SetFont('Arial','B',12);
		#$this->Cell(17,5);

			$this->Cell($total_w,3,'SUPPLY OFFICE',$border2,1,'C');
				$this->Ln(2);

			$this->SetFont('Arial','B',10);
		#$this->Cell(17,5);

			//modified 05-18-2011
			//$this->Cell($total_w,4,'DAILY SUPPLIES RELEASE',$border2,1,'C');
			$this->Cell($total_w,4,'SUPPLIES RELEASE REPORT',$border2,1,'C');

				$this->SetFont('Arial','B',9);
		#$this->Cell(17,5);
				if ($this->area)
					$this->Cell($total_w,4,$this->areaName,$border2,1,'C');
				else
					$this->Cell($total_w,4,"All areas",$border2,1,'C');

			$this->SetFont('Arial','B',9);
		#$this->Cell(17,5);

				//modified 05-18-2011
				//if ($this->date) {
					//$this->Cell($total_w,4,date("F j, Y",strtotime($this->date)),$border2,1,'C');
				//}
				if ($this->date_from && $this->date_to) {
					$this->Cell($total_w,4,"From ".date("F j, Y",strtotime($this->date_from))." to ".date("F j, Y",strtotime($this->date_to)),$border2,1,'C');
				}  //end
				else
					$this->Cell($total_w,4,"All dates",$border2,1,'C');
				$this->Ln(4);

				if (!$this->NoHeader) {
						# Print table header

						$this->SetTextColor(0);
						$row=5;

						$this->SetFont('Arial','B',11);
						$this->Cell(0,$row,'INVENTORY',1,0,'C',1);
						$this->Ln($row);
						$this->SetFont('Arial','B',9);

						$this->Cell($this->ColumnWidth[0],$row,'DATE',1,0,'C',1);
						$this->Cell($this->ColumnWidth[1],$row,'AREA',1,0,'C',1);
						$this->Cell($this->ColumnWidth[2],$row,'ITEM CODE',1,0,'C',1);
						$this->Cell($this->ColumnWidth[3],$row,'NAME',1,0,'C',1);
						$this->Cell($this->ColumnWidth[4],$row,'QTY',1,0,'C',1);
						$this->Cell($this->ColumnWidth[5],$row,'UNIT',1,0,'C',1);
						$this->Cell($this->ColumnWidth[6],$row,'RELEASED TO',1,0,'C',1);
						//$this->Cell($this->ColumnWidth[7],$row,'STATUS',1,0,'C',1);

						$this->Ln();
				}
		}

		function BeforeCell() {
				$this->FONTSIZE=8;
		}

		function BeforeData() {
				if ($this->colored) {
						$this->DrawColor = array(0xDD,0xDD,0xDD);
				}
		}

		function BeforeCellRender() {
				$this->FONTSIZE = 10;
				if ($this->colored) {
						if (($this->RENDERPAGEROWNUM%2)>0)
								$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
						else
								$this->RENDERCELL->FillColor=array(255,255,255);
				}
		}

		function AfterData() {
				global $db;

				if (!$this->CM) {
						if (!$this->_count) {
								$this->SetFont('Arial','B',9);
								$this->SetFillColor(255);
								$this->SetTextColor(0);
								$this->Cell(0, $this->RowHeight, "No supplies found...", 1, 1, 'L', 1);
						}

						$this->SetFont('Arial','B',9);
						if ($this->colored)    $this->SetFillColor(0xEE);
						$this->SetTextColor(0);
						$this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
						$this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, strtoupper("Total No. of Releases : $this->_count"), 1, 0, 'C', 1);
						//$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
						$this->Cell($this->ColumnWidth[3], $this->RowHeight, "", 1, 0, '', 1);
						$this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
						$this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
						$this->Cell($this->ColumnWidth[6], $this->RowHeight, "", 1, 0, '', 1);
						//$this->Cell($this->ColumnWidth[7], $this->RowHeight, "", 1, 0, '', 1);
						/*
						$this->Cell(0,5,'',0,1);

						$this->CM = TRUE;

						$this->Data = $this->DataCM;
						$this->_total = $this->_totalCM;
						$this->_count = $this->_countCM;

						$this->Report();
						*/
				}
		}

		function Footer()    {
				$this->SetY(-18);
				$this->SetFont('Arial','I',8);
				$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
		}

		function FetchData() {
				global $db;

				$invr_obj = new SegInventoryReport();
				$area_obj = new SegArea();
				/*
				$sql = "SELECT b.issue_date,d.area_name,c.bestellnum,c.artikelname,a.item_qty,e.unit_name,a.status,f.area_name as dest_area FROM seg_issuance_details as a
												JOIN seg_issuance as b ON a.refno=b.refno
												JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum
												JOIN seg_areas as d ON b.src_area_code=d.area_code
												JOIN seg_unit as e ON a.unit_id=e.unit_id
												JOIN seg_areas as f ON b.area_code=f.area_code ";

				$where = array();

				if ($this->date) {
						$where[]="str_to_date(issue_date, '%Y-%m-%d')='$this->date'";
				}

				if ($this->area) {
						$where[]="b.src_area_code='$this->area'";
				}


				if ($where)
						$sql .= "WHERE (".implode(") AND (",$where).") ORDER BY d.area_name,b.issue_date ASC";
				else $sql .= "ORDER BY d.area_name,b.issue_date ASC";

				$result=$db->Execute($sql);
				*/
				$areaname = $area_obj->getAreaName($this->area);
				$result = $invr_obj->getMovementinAreaForSC($this->area,NULL,1,$this->date_from,$this->date_to);
				//echo $invr_obj->sql;
				$this->_total = 0;

				if ($result) {
						$this->Data=array();

						while ($row=$result->FetchRow()) {
								$account = array();
								$collection = array();
								$a_types = explode("\n",$row['a_types']);
								foreach ($a_types as $i=>$type) {
										$type_arr = explode('|',$type);
										if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
										if (!in_array($type_arr[0],$account)) $account[] = $type_arr[0];
										if (!in_array($type_arr[1],$collection)) $collection[] = $type_arr[1];
								}
								if($row['status']=='1') $stat = 'cancelled';
								else if (($row['status']=='2')) $stat = 'approved';
								else $stat = 'issued';

								//$this->Data[]=array(
//                    date("m/d h:ia",strtotime($row['issue_date'])),
//                    $row['area_name'],
//                    $row['bestellnum'],
//                    $row['artikelname'],
//                    $row['item_qty'],
//                    $row['unit_name'],
//                    $row['dest_area'],
//                    $stat,
//                );

								if($row['outqty'] <= 0) continue;
								$this->Data[]=array(
										date("m/d/Y",strtotime($row['cutoff_date'])),
										$areaname,
										$row['bestellnum'],
										$row['artikelname'],
										number_format($row['outqty'],0,'.',','),
										$row['unit_name'],
										$row['area'],
										//'',
								);
								$this->_total+=$row['amount_due'];
						}
						$this->_count = count($this->Data);

				}
				else {
						print_r($sql);
						print_r($db->ErrorMsg());
						//exit;
						# Error
				}
		}
}

//modified 05-18-2011
//$rep =& new RepGen_Inventory_dailysupply_release($_GET['area'],$_GET['date']);
$rep =& new RepGen_Inventory_dailysupply_release($_GET['area'],$_GET['from_date'], $_GET['to_date']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>