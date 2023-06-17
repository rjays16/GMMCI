<?php
ini_set("memory_limit","-1");
ini_set("max_execution_time",0);
set_time_limit(0);
require './roots.php';
require $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
include_once($root_path.'include/phpjasperxml-master/PHPJasperXML.inc.php');
include $root_path.'include/inc_init_main.php';

$PHPJasperXML = new PHPJasperXML();

global $db;

// list of function

function userNote($db)
{
    $sql = "SELECT ss.`signatory_position`, ss.`signatory_title`, ss.`title`,
            CONCAT(p.`name_last`, ', ', p.name_first, ' ', SUBSTRING(p.`name_middle`, 1, 1), '.') AS `name`
            FROM seg_signatory ss LEFT JOIN `care_personell` cp ON cp.`nr` = ss.`personell_nr` 
            LEFT JOIN care_person p ON p.`pid` = cp.`pid` 
            WHERE ss.`document_code` = 'billing_report' LIMIT 1";
    $result = $db->execute($sql);
    return $result->FetchRow();
}

function personell($db)
{
    $sql = "SELECT cu.login_id AS username, cp.job_function_title AS jobtitle, p.`name_last`, p.`name_first`,
            p.`name_middle` FROM care_person p LEFT JOIN care_personell cp ON cp.pid = p.`pid` 
            LEFT JOIN care_users cu ON cu.personell_nr = cp.nr 
            WHERE cp.`nr` = " . $_SESSION['sess_user_personell_nr'] . "
            LIMIT 1 ";
    $result = $db->execute($sql);
    return $result->FetchRow();
}

$reportSql = "SELECT rep_description, exclusive_opd_er, exclusive_death FROM seg_rep_templates_registry 
            WHERE rep_script=".$db->qstr($_GET['report'])." AND is_active=1";
$report_info = $db->GetRow($reportSql);

$objInfo = new Hospital_Admin();
$hospital = $objInfo->getAllHospitalInfo();

$from_date = sprintf('%02d', $_GET['month_from']);
$to_date = sprintf('%02d', $_GET['month_to']);
$year = $_GET['year'];

//Get Month name format
$formattedMonthFrom = DateTime::createFromFormat('!m', $from_date)->format('F');
$formattedMonthTo = DateTime::createFromFormat('!m', $to_date)->format('F');

$sqlFromDate = DateTime::createFromFormat('m', $from_date)->format('Y-m-d');
$sqlToDate = DateTime::createFromFormat('m', $to_date)->format('Y-m-d');
$sqlYear = DateTime::createFromFormat('Y', $year)->format('Y-m-d');


$userNote = userNote($db);
$person = personell($db);

$today = "DATE GENERATED: ".date("D M j G:i:s T Y")." by Segworks Hospital Information System";
$sql = "SELECT 
          sm.`memcategory_id` AS category_id,
          sm.`memcategory_desc` AS category_name,
          MONTHNAME(t.`transmit_dte`) AS `month_name`,
          MONTH(t.`transmit_dte`) AS `month`,
          COUNT(*) AS tcount 
        FROM
          seg_memcategory sm 
          LEFT JOIN seg_encounter_insurance_memberinfo seim 
            ON seim.`member_type` = sm.`memcategory_code` 
          LEFT JOIN seg_eclaims_claim sec 
            ON sec.`encounter_nr` = seim.`encounter_nr` 
          LEFT JOIN seg_transmittal t 
            ON t.transmit_no = sec.transmit_no 
          LEFT JOIN seg_eclaims_transmittal_ext sete 
            ON sete.transmit_no = sec.transmit_no 
        WHERE sec.`claim_series_lhio` IS NOT NULL 
          AND sete.`is_mapped` = 1 
          AND (MONTH(t.`transmit_dte`) BETWEEN MONTH('$sqlFromDate') AND MONTH('$sqlToDate'))
          AND (YEAR(t.`transmit_dte` ) BETWEEN YEAR('$sqlYear') AND YEAR('$sqlYear'))
        GROUP BY category_name,
          month_name 
        UNION
        SELECT 
          sm1.`memcategory_id`,
          sm1.`memcategory_desc`,
          MONTHNAME(t1.`transmit_dte`),
          MONTH(t1.`transmit_dte`),
          0 AS tcount1 
        FROM
          seg_transmittal t1 
          JOIN seg_memcategory sm1 
        ORDER BY category_name,
          `month`";
//die($sql);
$PHPJasperXML->arrayParameter =
    array(
        "title" => str_replace("\r\n", "", $report_info['rep_description']),
        "hosp_name" => strtoupper($hospital['hosp_name']),
        "date_span" => $formattedMonthFrom . " - " . $formattedMonthTo . " " . $year,
        "address" => strtoupper($hospital['hosp_addr1']),
        "gererate_system" => "[ Billing ]",
        "doh_logo" => $root_path."img/doh_v2.jpg",
        "gch_logo" => $root_path."images/gmmci_logo.png",
        "user_note" => $userNote['name'] . ' ' . $userNote['title'],
        "user_note_position" => $userNote['signatory_position'],
        "user_prepared" => $person['name_last'] . ', ' . $person['name_first'] . ' ' . ($person['name_middle'] ? substr($person['name_middle'], 0, 1) . '.' : ''),
        "user_prepared_position" =>  $person['jobtitle'],
        "today" => $today,
    );


$PHPJasperXML->load_xml_file($root_path."reports/Billing_Transmittal_Based_On_PHIC_Category.jrxml");
$PHPJasperXML->sql = $sql;
$PHPJasperXML->transferDBtoArray($dbhost, $dbusername, $dbpassword, $dbname);
$PHPJasperXML->outpage("I");