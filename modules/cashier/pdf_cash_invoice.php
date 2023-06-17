<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

if (empty($_SESSION['sess_temp_userid'])) {
	die('Not logged in');
}

global $db;
$db->setFetchMode(ADODB_FETCH_ASSOC);

include_once($root_path."/classes/json/json.php");
include_once($root_path."include/care_api_classes/class_cashier.php");

/**
 * http://www.zend.com//code/codex.php?ozid=1540&single=1
 * Function:   convert_number
 * Arguments:  int
 * Returns:    string
 * Description:
 *   Converts a given integer (in range [0..1T-1], inclusive) into
 *   alphabetical format ("one", "two", etc.).
 */
$ORNo = $_REQUEST['nr'];
$sql1="SELECT or_no, ref_no,account_type,qty,amount_due,service_code,cppm.`artikelname`,cppm.`bestellnum` FROM seg_pay_request spr LEFT JOIN `care_pharma_products_main` cppm ON spr.service_code=cppm.`bestellnum` WHERE spr.or_no=".$db->qstr($ORNo);

$result1 = $db->Execute($sql1);

$sql = "SELECT pay.or_no,pay.create_id, pay.or_date, pay.or_name, pay.or_address, pay.pid, pay.encounter_nr, pay.amount_due,pay.amount_tendered,pay.remarks,pay.discount_tendered,pay.vat_amount,chk.company_name,
			chk.or_no AS `check_or_no`,chk.check_no,chk.check_date,chk.bank_name AS `check_bank_name`,chk.payee AS `check_name`,chk.amount AS `check_amount`,
			crd.or_no AS `card_or_no`,crd.card_no,crd.issuing_bank AS `card_bank_name`,crd.card_brand,crd.cardholder_name AS `card_name`,crd.expiry_date AS `card_expiry_date`,crd.security_code AS `card_security_code`,crd.amount AS `card_amount`
		FROM seg_pay AS pay
			LEFT JOIN seg_pay_checks AS chk ON chk.or_no=pay.or_no
			LEFT JOIN seg_pay_credit_cards AS crd ON crd.or_no=pay.or_no
			WHERE pay.or_no = ".$db->qstr($ORNo);
// var_dump($sql);die();
$result = $db->Execute($sql);
// var_dump($row);die();
$row=$result->FetchRow();
	// var_dump($row);die();
#added by daryl
function if_vatable($total){
$vat_rate = 1.12;
$totalAmount = ($total / $vat_rate );
$totalAmount = $totalAmount;
$sTotalAmount = number_format($totalAmount,2);

return $sTotalAmount;
}


function if_amount($total,$vat){

$totalAmount = ($total - $vat);
$totalAmount = $totalAmount;
$sTotalAmount = number_format($totalAmount,2);

return $sTotalAmount;
}


function intToWords($number) {
    if (($number < 0) || ($number > 999999999)) {
        return "$number";
    }

    $Gn = floor($number / 1000000);  /* Millions (giga) */
    $number -= $Gn * 1000000;
    $kn = floor($number / 1000);     /* Thousands (kilo) */
    $number -= $kn * 1000;
    $Hn = floor($number / 100);      /* Hundreds (hecto) */
    $number -= $Hn * 100;
    $Dn = floor($number / 10);       /* Tens (deca) */
    $n = $number % 10;               /* Ones */

    $res = "";

    if ($Gn) {
        $res .= intToWords($Gn) . " Million";
    }

    if ($kn) {
        $res .= (empty($res) ? "" : " ") .
                intToWords($kn) . " Thousand";
    }

    if ($Hn) {
        $res .= (empty($res) ? "" : " ") .
                intToWords($Hn) . " Hundred";
    }

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
        "Nineteen");
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
        "Seventy", "Eigthy", "Ninety");

    if ($Dn || $n) {
        if (!empty($res)) {
            $res .= " and ";
        }

        if ($Dn < 2) {
            $res .= $ones[$Dn * 10 + $n];
        } else {
            $res .= $tens[$Dn];

            if ($n) {
                $res .= "-" . $ones[$n];
            }
        }
    }

    if (empty($res)) {
        $res = "zero";
    }

    return $res;
}
#added by daryl
// $ip_ = getenv($_SERVER['REMOTE_ADDR']);
// $ip_add =  gethostbyname($ip_) ;

// $printer = $db->GetRow("SELECT printer_port, printer_model FROM seg_print_default WHERE ip_address=".$db->qstr($ip_add));
// if (!$printer) {
// 	die('Unable to retrieve printer settings');
// }

// echo $printer["printer_model"];
// echo  $printer["printer_port"];


/*
for ($i=1;$i<=60;$i++) {
	$jobs[0]['items'][] = array(
		'type' => 'text',
		'x' => 1,
		'y' => $i,
		'content' => str_repeat("1234567890",6) . '123'
	);
}
*/
$cClass = new SegCashier();


$ORNo = $_REQUEST['nr'];

#added by daryl
# 1 -- vatable
# 0 -- nonvatable
$get_vatable = $cClass->get_ifvatable($ORNo,"vat");
$get_discount = $cClass->get_ifvatable($ORNo,"discount");

$checkEncounterType = $cClass->checkEncounterType($ORNo);

if($checkEncounterType == 3)
$get_vatable = 0;

$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

if ($get_vatable == 1){

$jobs = array(
	// job #1
	array(
		'printer' => array(
			'type' => 'EPSON-ESCP2',
			// 'port' => '\\\\127.0.0.1\epsonlx'
			'port' => '\\\\127.0.0.1\epsonlx2-'
			
			
			
			 
			// 'type' => $printer["printer_model"],
			// 'port' => $printer["printer_port"]
		),
		'jobProperties' => array(
			'draftQuality' => false,
			'condensed' => true,
			'cols' => 137,
			'rows' => 44,
			'interspacing' => '1/8'
		),
		'textProperties' => array(
			'fontName' => 'SansSerif',
			'bold' => false,
			'condensed' => true,
		),
		'items' => array(
		)
	)
);
}else{

$jobs = array(
	// job #1
	array(
		'printer' => array(
			'type' => 'EPSON-ESCP2',
			// 'port' => '\\\\127.0.0.1\epsonlx'
			'port' => '\\\\127.0.0.1\epsonlx-'
			// 'type' => $printer["printer_model"],
			// 'port' => $printer["printer_port"]
		),
		'jobProperties' => array(
			'draftQuality' => false,
			'condensed' => true,
			'cols' => 137,
			'rows' => 44,
			'interspacing' => '1/8'
		),
		'textProperties' => array(
			'fontName' => 'SansSerif',
			'bold' => false,
			'condensed' => true,
		),
		'items' => array(
		)
	)
);
}




$info = $cClass->GetPayInfo( $ORNo, $showDetails=true );
if ($info == false) {
	die('Error in retrieving payment information...');
}

$printItems = array();

// Date Left
$printItems[] = array(
	'type' => 'text',
	'x' => 41,
	'y' => 8,
	'content' => date("M j, Y g:iA", strtotime($row['or_date']))
);

// Date Right
$printItems[] = array(
	'type' => 'text',
	'x' => 113,
	'y' => 8,
	'content' => date("M j, Y g:iA", strtotime($row['or_date']))
);


// Name Left
$printItems[] = array(
	'type' => 'text',
	'x' => 9,
	'y' => 8,
	'content' => strtoupper($row['or_name'])
);


// Name Right
$printItems[] = array(
	'type' => 'text',
	'x' => 81,
	'y' => 8,
	'content' => strtoupper($row['or_name'])
);

// Items
$rsDetails = $cClass->GetPayDetails( $ORNo );
$details = $rsDetails->GetRows();
$items = array();
while ($row1=$result1->FetchRow()) {
	#added by monmon : exclude non medicine items
	# M = medicine
	$itemClass = $cClass->getItemClassification($row1['bestellnum']);
	if($itemClass != 'M'){
		$row['amount_due'] -= $row1['amount_due'];
		continue;
	}
	$items[] = 	array(
		'itemcode' => $row1['bestellnum'],
		'name' => preg_replace('/\s+/', ' ', addslashes($row1["artikelname"])),
		'price' => ((float) $row1['amount_due']) / ((float) $row1['qty']),
		'quantity' => (int) $row1['qty'],
		'item_amount' => number_format((double)$row1['amount_due']."",2)
	);
}

// foreach ($details as $row) {
// 	$code = explode("|",$row["account_code"]);
// 	$items[] = 	array(
// 		'code' => $code[0],
// 		'name' => preg_replace('/\s+/', ' ', addslashes($row["service"])),
// 		'price' => ((float) $row['amount_due']) / ((float) $row['qty']),
// 		'quantity' => (int) $row['qty']
// 	);
// }

// ----------------------------------------
// items
// ----------------------------------------
$line = 18;
$totalAmount = 0;
foreach ($items as $i => $item) {
	$y = $line + $i;
	// // Item Code Left
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 15,
	// 	'y' => $y,
	// 	'content' => substr($item['code'],0,10)
	// );

	// // Item Code Right
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 15,
	// 	'y' => $y,
	// 	'content' => substr($item['code'],0,10)
	// );

	// Item Name Left
	$printItems[] = array(
		'type' => 'text',
		'x' => 10,
		'y' => $y,
		'content' => $item['quantity']." ". substr($item['name'],0,32) ."  P".  $item['price']
	);


	// Item Name Right
	$printItems[] = array(
		'type' => 'text',
		'x' => 80,
		'y' => $y,
		'content' => $item['quantity']." ". substr($item['name'],0,32) ."  P".  $item['price']
	);
	
	// Item Amount Left
	$printItems[] = array(
		'type' => 'text',
		'x' => 57,
		'y' => $y,
		'content' => $item['item_amount']//."H"
	);

	// Item Amount Right
	$printItems[] = array(
		'type' => 'text',
		'x' => 127,
		'y' => $y,
		'content' => $item['item_amount']//."H"
	);
	
	
	// if ($item['quantity'] > 1) {
		// Show quantity + unit price
		// $line++;
		// $printItems[] = array(
		// 	'type' => 'text',
		// 	'x' => 0,
		// 	'y' => $line+$i,
		// 	'content' => sprintf('(Qty x%d   @%s)', $item['quantity'], number_format($item['price'],2))
		// );
	// }
	
	// $totalAmount += $amount;
}

// Total Amount Left
	// $totalAmount = $totalAmount - $get_discount;
	// $sTotalAmount = number_format($totalAmount, 2);
	// $get_discount = number_format($get_discount, 2);
//daryl end p1

if ($get_vatable == 1){

	// $get_vat = if_vatable($totalAmount);
	// $get_amount = if_amount($totalAmount, $get_vat);

//arbitrary discount RIGHT
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 30,
	// 	'y' => 27,
	// 	'content' => "Discount:"
	// );
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 100,
	// 	'y' => 27,
	// 	'content' => "Discount:"
	// );
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 21,
	// 	'y' => 30,
	// 	'content' => number_format(((double)$row['amount_due']-(double)$row['vat_amount'])."",2)."G"
	// );
	// //arbitrary discount LEFT
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 91,
	// 	'y' => 30,
	// 	'content' => number_format(((double)$row['amount_due']-(double)$row['vat_amount'])."",2)."G"
	// );



// AMOUNT
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 30,
	// 	'y' => 28,
	// 	'content' => "Amount"
	// );

	// // AMOUNT
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 100,
	// 	'y' => 28,
	// 	'content' => "Amount:"
	// );

	$printItems[] = array(
		'type' => 'text',
		'x' => 52,
		'y' => 33,
		'content' => number_format(((double)$row['amount_due'])."",2)//."F"
	);

	// AMOUNT
	$printItems[] = array(
		'type' => 'text',
		'x' => 124,
		'y' => 33,
		'content' => number_format(((double)$row['amount_due'])."",2)//."F"
	);





		// VAT Left
		// $printItems[] = array(
		// 	'type' => 'text',
		// 	'x' => 30,
		// 	'y' => 29,
		// 	'content' => "VAT:"
		// );

		// //VAT Right
		// $printItems[] = array(
		// 	'type' => 'text',
		// 	'x' => 100,
		// 	'y' => 29,
		// 	'content' => "VAT:"
		// );

		$printItems[] = array(
			'type' => 'text',
			'x' => 52,
			'y' => 32,
			'content' => number_format(((double)$row['vat_amount'])."",2)//."D"
		);

		// VAT Right
		$printItems[] = array(
			'type' => 'text',
			'x' => 124,
			'y' => 32,
			'content' => number_format(((double)$row['vat_amount'])."",2)//."D"
		);

}else{

	// arbitrary discount RIGHT
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 30,
	// 	'y' => 31,
	// 	'content' => "Discount:"
	// );
	// $printItems[] = array(
	// 	'type' => 'text',
	// 	'x' => 100,
	// 	'y' => 31,
	// 	'content' => "Discount:"
	// );

	$printItems[] = array(
		'type' => 'text',
		'x' => 24,
		'y' => 31,
		'content' => number_format(((double)$row['vat_amount'])."",2)//."C"
	);
	//arbitrary discount LEFT
	$printItems[] = array(
		'type' => 'text',
		'x' => 97,
		'y' => 31,
		'content' => number_format(((double)$row['vat_amount'])."",2)//."C"
	);
}

		//grand total
		// Total Amount Left
		// $printItems[] = array(
		// 	'type' => 'text',
		// 	'x' => 30,
		// 	'y' => 30,
		// 	'content' => "Grand Total:"
		// );

		// // Total Amount Right
		// $printItems[] = array(
		// 	'type' => 'text',
		// 	'x' => 100,
		// 	'y' => 30,
		// 	'content' => "Grand Total:"
		// );
		//
		//------------------------------------UP
		// Total Amount LEFT
		$printItems[] = array(
			'type' => 'text',
			'x' => 52,
			'y' => 28,
			'content' =>  number_format(((double)$row['amount_due']-(double)$row['vat_amount'])."",2)//."A"
		);
		// Total Amount Right
		$printItems[] = array(
			'type' => 'text',
			'x' => 124,
			'y' => 28,
			'content' =>  number_format(((double)$row['amount_due']-(double)$row['vat_amount'])."",2)//."A"
		);
		//------------------------------------DOWN
		// Total Amount LEFT
		// $printItems[] = array(
		// 	'type' => 'text',
		// 	'x' => 24,
		// 	'y' => 32,
		// 	'content' =>  number_format(((double)$row['amount_due'])."",2)."B"
		// );
		// // Total Amount Right
		// $printItems[] = array(
		// 	'type' => 'text',
		// 	'x' => 97,
		// 	'y' => 32,
		// 	'content' =>  number_format(((double)$row['amount_due'])."",2)."B"
		// );

$printItems[] = array(
	'type' => 'text',
	'x' => 24,
	'y' => 13,
	'content' =>  number_format(((double)$row['amount_due'])."",2)//."E"
);


// Total Amount Right
$printItems[] = array(
	'type' => 'text',
	'x' => 97,
	'y' => 13,
	'content' =>  number_format(((double)$row['amount_due'])."",2)//."E"
);









// // Total Amount in Words
// $pesos = floor($totalAmount);
// $centavos = round(($totalAmount-$pesos)*100,0);
// $totalInWords = intToWords($pesos) . " peso/s";
// if ($centavos) {
// 	$totalInWords.= ' and ' . intToWords($centavos) . ' centavo/s';
// }
// $totalInWords.=' only';
// $linesArray = explode("\n",
// 	wordwrap(
// 		strtoupper($totalInWords), 55, "\n"
// 	)
// );
// $line = 46;
// foreach ($linesArray as $i=>$aLine) {
// 	$printItems[] = array(
// 		'type' => 'text',
// 		'x' => 3,
// 		'y' => $line + $i,
// 		'content' => $aLine
// 	);
// }

// Cash/Check/MoneyOrder
// $type = 'CASH';
// if ($info['check_no']) {
// 	$type = 'CHECK';
// }

// $y=20;
// switch(strtoupper($type)) {
// 	case 'CHECK':
// 		$y=52;
// 		break;
// 	case 'MONEY_ORDER':
// 		$y=54;
// 		break;
// }

// $printItems[] = array(
// 	'type' => 'text',
// 	'x' => 3,
// 	'y' => $y,
// 	'content' => 'X'
// );

// if ($type == 'CHECK') {
// 	$printItems[] = array(
// 		'type' => 'text',
// 		'x' => 19,
// 		'y' => $y,
// 		'content' => $info['check_bank_name']
// 	);
// 	$printItems[] = array(
// 		'type' => 'text',
// 		'x' => 37,
// 		'y' => $y,
// 		'content' => $info['check_no']
// 	);
	
// 	$checkDate = strtotime($info['check_date']);
// 	$printItems[] = array(
// 		'type' => 'text',
// 		'x' => 49,
// 		'y' => $y,
// 		'content' => ($checkDate !== false) ? date('m-d-Y', $checkDate) : ''
// 	);
// }

// Collecting Officer
$encoder = $db->GetOne("SELECT `name` FROM care_users WHERE login_id=".$db->qstr($_SESSION['sess_temp_userid']));
if (!$encoder) {
	die('Could not retrieve encoder information');
}
$designated = $db->GetOne("SELECT value FROM care_config_global WHERE type='cashier_or_designated_officer'");
if (!$designated) {
	die('Could not retrieve designated officer information');
}
//officer left
// $officer = strtoupper($encoder.'/'.$designated);
$officer = strtoupper($encoder);

$printItems[] = array(
	'type' => 'text',
	'x' => 9,
	'y' => 37,
	'content' => $officer
);

//officer right
$printItems[] = array(
	'type' => 'text',
	'x' => 79,
	'y' => 37,
	'content' => $officer
);

$jobs[0]['items'] = $printItems;

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache">
	<meta charset="iso-5589-1">
	<link rel="stylesheet" media="all" href="css/draft.css" />
</head>
<script>
function closeWindow() {
	setTimeout("window.parent.cClick()", 1500);
}
</script>
<body>
	<table>
		<tr>
			<td width="50"><img id="icon" name="icon" src="<?= $root_path ?>images/print.png" border="0" title="Printing"></td>
			<td>
				<h1 name="msg" id="print-message">Printing Receipt</h1>
				<div align="center">
					<img name="bar" id="in-progress" src="<?= $root_path ?>images/ajax_bar2.gif" border="0" title="Printing" style="margin-left:10px">
				</div>
			</td>
		</tr>
	</table>
	<applet codebase="applet/" archive="DraftPrintSuite.packed.jar" code="com.segworks.draftprintsuite.PrintSuiteDirectorApplet.class" width="0" height="0" mayscript>
		<param name="name" value="Segworks Draft Printing Suite" />
		<param name="jobs" value="<?= htmlentities($json->encode($jobs)) ?>" />
		<param name="onDone" value="closeWindow" />
	</applet>
</body>
</html>