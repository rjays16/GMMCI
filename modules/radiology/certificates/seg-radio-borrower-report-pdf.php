<?php

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');


//$_GET['encounter_nr'] = 2007500006;
/*
if($_GET['id']){
	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['id']))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
	extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}
*/
if (isset($_GET['borrower_id']) && $_GET['borrower_id']){
	$borrower_id = $_GET['borrower_id'];
}

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}


include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

if ($pid){
	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

# Create personnel object
include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;

if ($borrower_id){
	if ($personellInfo = $personell_obj->getPersonellInfo($borrower_id)){
#echo "seg-radio-borrow.php : personell_obj->sql ='".$personell_obj->sql."' <br> \n";
#echo "seg-radio-borrow.php : personellInfo['pid'] ='".$personellInfo['pid']."' <br> \n";
#echo "seg-radio-borrow.php :: personellInfo : <br> \n"; print_r($personellInfo); echo "<br>\n";
		$dept_name = $personellInfo['dept_name'];
		$pid=$personellInfo['pid'];
		$recordBorrowObj = $radio_obj->getBorrowerBorrowedFilms($borrower_id);
	}else{
		echo "<em class='warn'> No informatin of employment found. <br> \n Sorry but the page cannot be displayed!</em>";
		exit();	
	}
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Personnel ID!</em>';
	exit();
}

			# burn added : March 26, 2007
			if($date_birth){
				$segBdate = @formatDate2Local($date_birth,$date_format);
				if (!($age = $person_obj->getAge($segBdate))){
					$age = '';
					$segBdate = 'Not Available';
					$segBdateAge = $segBdate;
				}else{
#					$smarty->assign('sAge','<span class="vi_data">'.$age.' </span> year(s) old');
					$age=$age.' year(s) old';
					$segBdateAge = $segBdate.'   / '.$age;
				}
			}
	if ($sex=='f'){
		$gender = "female";
	}else if($sex=='m'){
		$gender = "male";	
	}
	$sAddress = trim($street_name);
	if (!empty($sAddress) && !empty($brgy_name))
		$sAddress= trim($sAddress.", ".$brgy_name);
	else
		$sAddress = trim($sAddress." ".$brgy_name);
	if (!empty($sAddress) && !empty($mun_name))
		$sAddress= trim($sAddress.", ".$mun_name);
	else
		$sAddress = trim($sAddress." ".$mun_name);
	if (!empty($zipcode))
		$sAddress= trim($sAddress." ".$zipcode);
	if (!empty($sAddress) && !empty($prov_name))
		$sAddress= trim($sAddress.", ".$prov_name);
	else
		$sAddress = trim($sAddress." ".$prov_name);

require_once($root_path.'classes/fpdf/fpdf.php');
class PDF extends FPDF{

	/*
	*	Page header
	*	override the method in FPDF (the implementation in FPDF is empty)
	*/
/*
	function Header(){
			//Logo
		$this->Image('logo_pb.png',10,8,33);
			//Arial bold 15
		$this->SetFont('Arial','B',15);
			//Move to the right
		$this->Cell(80);
			//Title
		$this->Cell(30,10,'Title',1,0,'C');
			//Line break
		$this->Ln(20);
	}
*/
	/*
	*	Page footer
	*	override the method in FPDF (the implementation in FPDF is empty)
	*/
	function Footer(){
			//Position at 1.5 cm from bottom
		$this->SetY(-15);
			//Arial italic 8
		$this->SetFont('Arial','I',8);
			//Page number
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
	}
}# end of class PDF


//set border 
$border_0="0";
$border_1="1";
$spacing =2;
// font setup
$fontSizeLabel = 8+3;
$fontSizeInput = 11;
$fontSizeText = 12;
$fontSizeHeader = 14;
//fontstyle setup
$fontStyle = "Arial";
$fontStyle2 = "Times";
$fontStyleCourier = "Courier";

$my_add_left_margin=10; # additional left margin
 
/*
//instantiate fpdf class
$pdf  = new FPDF();
$pdf->AddPage("P");
*/
//Instanciation of inherited class
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage("P");

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	
// Hospital Logo
$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,25,30);

//Header - Republic of the Philippines / Department of Health
$pdf->SetFont($fontStyle, "I", $fonSizeInput);
$pdf->Cell(0,4,'Republic of the Philippines', $border_0,1,'C');
$pdf->Ln(1);
$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');

//Hospital name- Gonzales Maranan Medical Center Incorporated
$pdf->Ln(1);
$pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border_0, 1, 'C');

//Hospital Address
$pdf->Ln(1);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,'Digos City',$border_0, 1, 'C');

//Department Name
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeInput+1);
$pdf->Cell(0,4,'Department of Radiological & Imaging Sciences',$border_0, 1, 'C');


//Borrower's name
$pdf->Ln(12);
#$pdf->Cell(10, 3 ,'', "",0,''); # left margin
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(25, 3 ,'Account of : ', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(0, 3 ,strtoupper($name_last.', '.$name_first.' '.$name_middle), "",0,'');

//Borrower's Department
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(25, 3 ,'Department : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 ,$dept_name, "",0,'');

//Header
$pdf->Ln(10);
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$w_No = 8;
$pdf->Cell($w_No, 3 ,'No.', "",0,'C');
$w_margin = 3;
$pdf->Cell($w_margin, 3 ,'', "",0,'C');
$w_RID = 25;
$pdf->Cell($w_RID, 3 ,'RID', "",0,'C');
#$w_batch_nr = 25;
#$pdf->Cell($w_batch_nr, 3 ,'Batch No.', "",0,'C');
$w_film = 25;
$pdf->Cell($w_film, 3 ,'Film. No.', "",0,'C');
$w_date = 25;
$pdf->Cell($w_date, 3 ,'Date', "",0,'C');
$pdf->Cell($w_margin, 3 ,'', "",0,'C');
$w_name = 50;
$pdf->Cell($w_name, 3 ,'Patient\'s Name', "",0,'L');
$w_desc = 30;
$pdf->Cell($w_desc, 3 ,'Short Desc', "",0,'L');
$w_price = 25;
$pdf->Cell($w_price, 3 ,'Gross Price', "",0,'C');
$pdf->Ln(3);
$pdf->Cell(0, 1 ,'', "TB",0,'C');

//Print the list of borrowed materials
$pdf->Ln(5);

if (is_object($recordBorrowObj)){
	$myCount=1;
	$totalGrossPrice=0;
	while($recordHistory=$recordBorrowObj->FetchRow()){

			# FORMATTING of RID
		$rid = trim($recordHistory['rid']);
		if ($rid){
			$rid = substr($rid,0,4).'-'.substr($rid,4);
		}
			# FORMATTING of Date Borrowed
		$date_borrowed = $recordHistory['date_borrowed'];
		if (($date_borrowed!='0000-00-00')  && ($date_borrowed!=""))
			$date_borrowed = @formatDate2Local($date_borrowed,$date_format);
		else
			$date_borrowed='';
		$patient_name=strtoupper(trim($recordHistory['name_last'])).', '.trim($recordHistory['name_first']);
		if (!empty($recordHistory['name_middle'])){
			$patient_name .= ' '.trim($recordHistory['name_middle']);
		}
		if (strlen(trim($patient_name))>30)
			$patient_name=substr($patient_name,0,30).'...';

		$service_code = trim($recordHistory['service_code']);
		if (strlen(trim($service_code))>10)
			$service_code=substr($service_code,0,10).'...';

		$pdf->SetFont($fontStyle,"", $fontSizeLabel);
		if (($myCount%2))
			$pdf->SetFillColor(230,230,230);
		else
			$pdf->SetFillColor(255,255,255);
		$pdf->Cell($w_No, 3 ,$myCount++, "",0,'R');
		$pdf->Cell($w_RID, 3 ,$rid, "",0,'R');
		#$pdf->Cell($w_batch_nr, 3 ,$recordHistory['refno'], "",0,'R');
		$pdf->Cell($w_film, 3 ,$recordHistory['batch_nr'], "",0,'R');
		$pdf->Cell($w_date, 3 ,$date_borrowed, "",0,'R');
		$pdf->Cell($w_margin, 3 ,'', "",0,'C');
		$pdf->Cell($w_name, 3 ,$patient_name, "",0,'L');
		$pdf->Cell($w_desc, 3 ,$service_code, "",0,'L');
		$pdf->SetFont($fontStyleCourier,"", $fontSizeLabel);
		$pdf->Cell($w_price, 3 ,number_format($recordHistory['price_gross'], 2, '.', ','), "",0,'R');
		$pdf->Ln(4);

		$totalGrossPrice += $recordHistory['price_gross'];
	}
	$sTotalGrossPrice = 'Php '.number_format($totalGrossPrice, 2, '.', ',');
}# end of if stmt 'if (is_object($recordBorrowObj))'

/*
	$myCount=1;
	$totalGrossPrice=0;
	while($myCount<=60){
			# FORMATTING of Date Borrowed
		$date_borrowed='2007-11-21';
		if (($date_borrowed!='0000-00-00')  && ($date_borrowed!=""))
			$date_borrowed = @formatDate2Local($date_borrowed,$date_format);
		else
			$date_borrowed='';
		$patient_name=strtoupper('Clarito').', Bernard Klinch Sabucdalao';
		if (strlen(trim($patient_name))>30)
			$patient_name=substr($patient_name,0,30).'...';

		$recordHistory['price_gross'] = 12345.67;
		$service_code = 'ABCDEFGHIJKLMNO';
		if (strlen(trim($service_code))>10)
			$service_code=substr($service_code,0,10).'...';

		
		$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
		$pdf->Cell($w_No, 3 ,$myCount++, "",0,'R');
		$pdf->Cell($w_RID, 3 ,'2007-00001', "",0,'R');
		$pdf->Cell($w_date, 3 ,$date_borrowed, "",0,'R');
		$pdf->Cell($w_margin, 3 ,'', "",0,'C');
		$pdf->Cell($w_name, 3 ,$patient_name, "",0,'L');
		$pdf->Cell($w_desc, 3 ,$service_code, "",0,'L');
		$pdf->SetFont($fontStyleCourier,"", $fontSizeLabel-1);
		$pdf->Cell($w_price, 3 ,number_format($recordHistory['price_gross'], 2, '.', ','), "",0,'R');
		$pdf->Ln(4);

		$totalGrossPrice += $recordHistory['price_gross'];
	}
	$sTotalGrossPrice = 'Php '.number_format($totalGrossPrice, 2, '.', ',');

*/

$pdf->Cell($w_No + $w_RID + $w_film + $w_date + $w_margin + $w_name + $w_desc, 0.5 ,'', "",0,'C');
$pdf->Cell($w_price, 0.5 ,'', "TB",0,'C');
$pdf->Ln(3);
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell($w_No + $w_RID, 1 ,'TOTAL', "",0,'C');
$pdf->SetFont($fontStyleCourier,"B", $fontSizeLabel);
$pdf->Cell($w_date + $w_margin + $w_film + $w_name + $w_desc + $w_price, 1 ,$sTotalGrossPrice, "",0,'R');

$pdf->Ln(10);
$pdf->Cell($w_No + $w_RID, 1 ,'PENALTY', "",0,'C');
$pdf->SetFont($fontStyleCourier,"B", $fontSizeLabel);

$Penalty = $totalGrossPrice * 0.30;
$sPenalty = 'Php '.number_format($Penalty, 2, '.', ',');
$pdf->Cell($w_date + $w_margin + $w_film + $w_name + $w_desc + $w_price, 1 ,$sPenalty, "",0,'R');

//print pdf
$pdf->Output();

?>