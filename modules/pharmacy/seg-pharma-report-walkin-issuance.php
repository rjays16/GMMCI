<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_order.php');
require_once($root_path.'include/care_api_classes/class_product.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

		class RepGen_Pharma_WalkinIssuance extends RepGen {

		var $pid;
		var $area;
		var $date_from;
		var $date_to;
		var $product_name;
		var $product_code;
		var $mode="all";

		function RepGen_Pharma_WalkinIssuance ($mode, $pid, $area, $product_name, $product_code, $datefrom, $dateto)
		{
				global $db;
				$this->RepGen("Walk-In ISSUANCE REPORT");
				$this->Headers = array(
						'Date Served', 'Walk-in Name',
						'Area', 'Product Name', 'Quantity Served'
				);
				$this->colored = TRUE;
				$this->ColumnWidth = array(36,40,30,60,30);
				$this->RowHeight = 6;
				$this->Alignment = array('L','L','C','L','C');
				$this->PageOrientation = "P";
				if ($datefrom) $this->date_from=date("Y-m-d",strtotime($datefrom));
				//else $this->date_from=date("Y-m-d");
				if ($dateto) $this->date_to=date("Y-m-d",strtotime($dateto));
				//else $this->date_to=date("Y-m-d");
				if($area)
					$this->area = $area;
				if($product_code)
					$this->product_code = $product_code;
				if($product_name)
					$this->product_name = $product_name;
				if($pid)
					$this->pid = $pid;
				if($mode)
					$this->mode = $mode;
				if ($this->colored)    $this->SetDrawColor(0xDD);
		}

		function Header()
		{
				global $root_path, $db;
				$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,6,15);
				$this->SetFont("Arial","I","9");
				$total_w = 165;
				$this->Cell(17,4);
				$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
				$this->Cell(17,4);
				$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
				$this->Ln(2);
				$this->SetFont("Arial","B","10");
				$this->Cell(17,4);
				$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
				$this->SetFont("Arial","","9");
				$this->Cell(17,4);
				$this->Cell($total_w,4,'Quezon Ave., Digos City',$border2,1,'C');
				$this->Ln(6);
				$this->SetFont('Arial','B',12);
				$this->Cell(17,5);
				$this->Cell($total_w,4,'WALK-IN ISSUANCE REPORT',$border2,1,'C');
				$this->SetFont('Arial','B',9);
				$this->Cell(17,4);
				if($this->mode=="all")
				{
					 if($this->area)
					{
							$prod_obj=new Product;
							$prod=$prod_obj->getAllPharmaAreas();
							while($row=$prod->FetchRow())
							{
									if($row['area_code']==$this->area)
									{
											$area_name = $row['area_name'];
									}
							}
					}
					else
					{
							$area_name = "All areas";
					}
					$this->Ln(2);
					$this->Cell(17,4);
					$this->Cell($total_w,4,$area_name,$border2,1,'C');
					$this->Cell(17,4);
					if($this->product_name)
					{
							$this->Cell($total_w,4,$this->product_name,$border2,1,'C');
					}
					else
					{
							$this->Cell($total_w,4,"All products",$border2,1,'C');
					}
					$this->Cell(17,4);
					if($this->date_from==$this->date_to)
					{
						$this->Cell($total_w,4,date("F j, Y"),$border2,1,'C');
					}
					else if($this->date_from==$this->date_to && $this->date_from!="")
					{
						$this->Cell($total_w,4,date("F j, Y",strtotime($this->date_from)),$border2,1,'C');
					}
					else if($this->date_from && $this->date_to)
					{
							$this->Cell($total_w,4,date("F j, Y",strtotime($this->date_from))." to ".date("F j, Y",strtotime($this->date_to)),$border2,1,'C');
					}
				}

				$this->Ln();
				$this->SetTextColor(0);
				$row=5;
				#$this->Cell(25,4);
				$this->SetFont('Arial','B',9);
				$this->Cell($this->ColumnWidth[0],$this->RowHeight,$this->Headers[0],1,0,'C',1);
				$this->Cell($this->ColumnWidth[1],$this->RowHeight,$this->Headers[1],1,0,'C',1);
				$this->Cell($this->ColumnWidth[2],$this->RowHeight,$this->Headers[2],1,0,'C',1);
				$this->Cell($this->ColumnWidth[3],$this->RowHeight,$this->Headers[3],1,0,'C',1);
				$this->Cell($this->ColumnWidth[4],$this->RowHeight,$this->Headers[4],1,0,'C',1);
				$this->Ln();

		}

		function Footer()
		{
				$this->SetY(-23);
				$this->SetFont('Arial','I',8);
				$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
		}

		function BeforeData()
		{
				if ($this->colored) {
						$this->DrawColor = array(0xDD,0xDD,0xDD);
				}
				$this->ColumnFontSize = 9;
		}

		function BeforeCellRender()
		{
				$this->FONTSIZE = 8;
				if ($this->colored) {
						if (($this->RENDERPAGEROWNUM%2)>0)
								$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
						else
								$this->RENDERCELL->FillColor=array(255,255,255);
				}
		}

		function AfterData()
		{
				global $db;

				if (!$this->_count) {
						$this->SetFont('Arial','B',9);
						$this->SetFillColor(255);
						$this->SetTextColor(0);
						#$this->Cell(25,4);
						$this->Cell(156, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
				}

				$cols = array();
		}

		function FetchData()
		{
			global $db;
			$pharmaObj = new SegOrder();
			$result = $pharmaObj->getWalkinIssuance($this->pid,$this->area, $this->product_code,$this->date_from,$this->date_to);
			//echo "query=".$pharmaObj->sql;
			$this->SetFont('Arial','',8);
			if($result)
			{
					$this->_count = $result->RecordCount();
					$this->Data=array();
					while($row=$result->FetchRow())
					 {
							$this->Data[]=array(
									$row['date'], $row['name'],
									$row['area'], $row['artikelname'], $row['quantity']
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

		}

}
$rep =& new RepGen_Pharma_WalkinIssuance($_GET["mode"], $_GET['pid'], $_GET['area'], $_GET['product_name'], $_GET['product_code'], $_GET['datefrom'], $_GET['dateto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>