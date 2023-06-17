<?php  
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');                                                        
require($root_path."/classes/fpdf/pdf.class.php");        
//require($root_path.'/modules/repgen/repgenclass.php');     
require($root_path.'/modules/repgen/repgen.inc.php');  
require($root_path.'include/inc_environment_global.php');            
include_once($root_path.'include/inc_date_format_functions.php');               
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';    
	
class NotifiableDiseases extends FPDF{
	var $date_from;
	var $date_to;
	var $count_rows;  
					//$_GET['fromtime'], $_GET['totime'], $_GET['dept_nr'], 
//										$_GET['dept_nr_sub'], $_GET['location'],$_GET['icd'], $_GET['modkey'], $_GET[''], 
//										$_GET[''], $_GET[''], $_GET['orderby'])
	function NotifiableDiseases($date_from, $date_to, $fromtime, $totime, $dept_nr, $dept_nr_sub, $location, $icd, $modkey, $modkey2, $modkey3, $sclass, $orderby){
		global $db;                                                 
		$this->SetTopMargin(3);                                       
		$this->FPDF("L", 'mm', 'Legal');                     
		$this->ColumnWidth = array(10,25,50,15,15,50,25,50,85,45);        
		$this->Alignment = array('R','C','L','C','C','L','C','L','L');
																					 
		$this->total_width = array_sum($this->ColumnWidth);      
		$this->NoWrap=FALSE;

		if ($date_from) $this->date_from = date('Y-m-d',strtotime($date_from));
		if ($date_to)$this->date_to = date('Y-m-d', strtotime($date_to));   
	}
	
	function Header() {
		global $root_path, $db;
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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',110,8,30);
		$this->SetFont("Arial","I","9");
		$total_w = 250;
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);
		$this->SetFont('Arial','B',12);
		$this->Cell(50,5);

		$this->Cell($total_w,4,strtoupper('SUSPECTED ILLNESS REPORT'),0,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Ln(3);
		$this->Cell(50,5);
		
		$dates = "From ".date("F j, Y",strtotime($this->date_from))." To ".date("F j, Y",strtotime($this->date_to));

		$this->Cell($total_w,4,$dates,0,1,'C');

		$this->Ln(5);    
	}
	
	function GetDiagnosisOccurrence($code){
		global $db;
		if (empty($this->date_to)) $end_date="NOW()";
		else $end_date=$this->date_to;
		if (empty($this->date_from)) $start_date="NOW()";
		else
			$start_date=$this->date_from;
		$this->count_rows = 0;
		$sql2 = 'SELECT 
								e.encounter_nr AS "Case No.",
								CONCAT(IF (trim(p.name_last) IS NULL,"",trim(p.name_last)),", ",
								IF(trim(p.name_first) IS NULL ,"",trim(p.name_first))," ",
								IF(trim(p.name_middle) IS NULL,"",trim(p.name_middle))) AS "Full Name",
								IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
								UPPER(p.sex) AS Sex,

								CONCAT(IF (trim(p.street_name) IS NULL,"",trim(p.street_name))," ",
								IF (trim(sb.brgy_name) IS NULL,"",trim(sb.brgy_name))," ",
								IF (trim(sm.mun_name) IS NULL,"",trim(sm.mun_name))," ",
								IF (trim(sm.zipcode) IS NULL,"",trim(sm.zipcode))," ",
								IF (trim(sp.prov_name) IS NULL,"",trim(sp.prov_name))," ",
								IF (trim(sr.region_name) IS NULL,"",trim(sr.region_name))) AS "Complete Address",
																							
								e.admission_dt AS "Date Admitted",
								UPPER(IF (e.current_att_dr_nr,fn_get_personell_name(e.current_att_dr_nr),
								fn_get_personell_name(e.consulting_dr_nr))) AS "Attending Physician", 
								e.er_opd_diagnosis AS "Diagnosis", en.remarks AS "Other Disease"

								FROM seg_encounter_notifiable AS en
								LEFT JOIN care_encounter as e 
								ON e.encounter_nr = en.encounter_nr  
								LEFT JOIN care_person as p 
								ON p.pid = e.pid 
								LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
								LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
								LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
								LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
								WHERE DATE(e.admission_dt) BETWEEN '.$db->qstr($start_date).' AND '.$db->qstr($end_date).'
								AND en.code_illness = '.$db->qstr($code).'
								ORDER BY e.admission_dt';
//								echo $sql2.'<br><br>';
		$result2 = $db->Execute($sql2); 
		$this->count_rows = $result2->RecordCount();
			$i=0;     
		
		while($row2 = $result2->FetchRow()){  							
				$i++; 
			if (stristr($row2['Age'],'years')){
				$age = substr($row2['Age'],0,-5);
				$age = floor($age).' y';
			}elseif (stristr($row2['Age'],'year')){	
				$age = substr($row2['Age'],0,-4);
				$age = floor($age).' y';
			}elseif (stristr($row2['Age'],'months')){	
				$age = substr($row2['Age'],0,-6);
				$age = floor($age).' m';	
			}elseif (stristr($row2['Age'],'month')){	
				$age = substr($row2['Age'],0,-5);
				$age = floor($age).' m';		
			}elseif (stristr($row2['Age'],'days')){	
				$age = substr($row2['Age'],0,-4);   					
				if ($age>30){
					$age = $age/30;
					$label = 'm';
				}else $label = 'd'; 						
				$age = floor($Age).' '.$label;		
			}elseif (stristr($row2['Age'],'day')){	
				$age = substr($row2['Age'],0,-3);
				$age = floor($age).' d';		
			}else{
				$age = floor($row2['Age']).' y';			
			}      
			if($code=='OTHER') {
			$this->Row(array($i.'.',
											$row2['Case No.'],
											ucwords(strtolower($row2['Full Name'])),
											$age,                      
											strtoupper($row2['Sex']),    
											ucwords(strtolower($row2['Complete Address'])),    
											date("m/d/Y",strtotime($row2['Date Admitted'])),  
											ucwords(strtolower($row2['Attending Physician'])),
											ucwords(strtolower($row2['Other Disease'])).' | '.ucwords(strtolower($row2['Diagnosis'])))
											); 			
			}                        
			else{
			$this->Row(array($i.'.',
											$row2['Case No.'],
											ucwords(strtolower($row2['Full Name'])),
											$age,                      
											strtoupper($row2['Sex']),    
											ucwords(strtolower($row2['Complete Address'])),    
											date("m/d/Y",strtotime($row2['Date Admitted'])),  
											ucwords(strtolower($row2['Attending Physician'])),
											ucwords(strtolower($row2['Diagnosis'])))
											); 
			}
					
		}
		$this->SetFont('Arial', 'B', 9);
		$this->Cell($this->total_width, 7, "Total Cases: ".$i, 0,0,'L');    
		$this->ln(5);    
	}
	
	function GetDiagnosisData(){
		global $db;
		$this->ln();
		$this->SetLeftMargin(15);                 
		$prev_code = "";
		$rowheader = 6;               		
		$sql = "SELECT nd.code_illness, nd.illness_name, nd.description   
						FROM seg_notifiable_diseases AS nd 
						WHERE nd.status IS NULL OR nd.status NOT IN ('deleted')
						ORDER by nd.ordering ASC;";     
//		echo $sql;  
		$result = $db->Execute($sql);                                    
		$this->count = $result->RecordCount();
		while($rows = $result->FetchRow()){
																										 
			$code = $rows['code_illness'];
			if($code=='OTHER')    
				$dis_name = 'Other Suspected Diseases';
			else
				$dis_name = $rows['illness_name'];
			$desc = $rows['description'];                 
			
				
			if($prev_code != $code){
				$this->Ln();
				$this->SetFont('Arial', 'B', 9);
				$this->Cell(18, $RowWidth, "Diagnosis: ", 0,0,'L');
				$l_diag = $this->GetStringWidth($dis_name);                   
				$this->Cell($l_diag+2, $RowWidth, $dis_name); // for Suspected Illness
				$this->Cell(20, $RowWidth, "    Desciption: ".$RowWidth, 0, 0, 'L');
				$this->Cell(0, $RowWidth, " ".$desc, 0, 1, 'L'); //for description
				$this->Ln(2);
				$this->SetFont('Arial', 'B', 8);                                              
				$this->Cell($this->ColumnWidth[0],$rowheader,'   ',1,0,'C');
				$this->Cell($this->ColumnWidth[1],$rowheader,'CASE NO.',1,0,'C');
				$this->Cell($this->ColumnWidth[2],$rowheader,'FULL NAME',1,0,'C');
				$this->Cell($this->ColumnWidth[3],$rowheader,'AGE',1,0,'C');
				$this->Cell($this->ColumnWidth[4],$rowheader,'SEX',1,0,'C');
				$this->Cell($this->ColumnWidth[5],$rowheader,'ADDRESS',1,0,'C');
				$this->Cell($this->ColumnWidth[6],$rowheader,'ADM. DATE',1,0,'C');   
				$this->Cell($this->ColumnWidth[7],$rowheader,'ATTENDING PHYSICIAN',1,0,'C'); 	     
				$other_column_width = 0;          
				if($code=='OTHER'){
					$other_column_width=0;
					$this->Cell($this->ColumnWidth[8],$rowheader,'SUSPECTED DISEASE | DIAGNOSIS',1,0,'C');
				}
				else
					$this->Cell($this->ColumnWidth[8]-$other_column_width,$rowheader,'DIAGNOSIS',1,0,'C');           				
				$this->Ln();
				$this->SetFont('Arial', '', 9);
				$row2 = $this->GetDiagnosisOccurrence($code);   
				$prev_code = $code;
				$this->Ln(3);  
			}   
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
						if(($l_data4 >$l_data6) && ($l_data6 > $this->ColumnWidth[6]) && ($nb2 > $nb3)){
							$lgreater = $l_data4;
							$ldiff = $lgreater - $l_data6;
								for($cnt = 0; $cnt<intval($ldiff); $cnt++)
									 $data[6].= " ";
						
						}else if(($l_data6 > $l_data4) && ($l_data4 > $this->ColumnWidth[4]) && ($nb3 > $nb2)){
							
							$lgreater = $l_data6;
							$ldiff = $lgreater - $l_data4;
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
							$data[8].="   ";
					}  
			$h=$row*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidth[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';          
					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border          
							$length = $this->GetStringWidth($data[$i]);
							if($length < $this->ColumnWidth[$i]){
								$this->Cell($w, $h, $data[$i], 1, 0, $this->Alignment[$i]);
							}
							else{
								$nbrow = 3;
								$this->MultiCell($w, $row, $data[$i], 1,$this->Alignment[$i]);
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

//$date_from = $_GET['from'];
//$date_to = $_GET['to']; 

//from=2010-09-01&to=2010-10-04&fromtime=07:00:00&totime=17:00:00&dept_nr=0&dept_nr_sub=0&location=0
//&icd=all&modkey=0&modkey2=0&modkey3=0&sclass=all&orderby=	
$pdf = new NotifiableDiseases($_GET['from'], $_GET['to'], $_GET['fromtime'], $_GET['totime'], $_GET['dept_nr'], 
										$_GET['dept_nr_sub'], $_GET['location'],$_GET['icd'], $_GET['modkey'], $_GET['modkey2'], 
										$_GET['modkey3'], $_GET['sclass'], $_GET['orderby']);
$pdf->Open(); 
$pdf->AliasNbPages();
$pdf->AddPage();  
$pdf->GetDiagnosisData();
$pdf->Output(); 
?>