<?php
ini_set('memory_limit', '1024M');

require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'/modules/repgen/repgenclass.php';
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path . 'classes/PHPExcel/PHPExcel.php';

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class ReportMonthlyBillsRendered extends RepGen {

    protected $date;
    protected $format;
    protected $formatD;

    /**
     *
     */
    public function __construct($date, $format='CSV') {
        global $db;

        if (!in_array($format, array('CSV', 'Excel5', 'Excel2007'))) {
            die('Invalid report format specified');
        }

        $this->date = strtotime($date);
        if ($this->date === false) {
            $this->date = time();
        }
        $this->format = $format;

        $this->RepGen("Monthly Bills Rendered",'P','Legal');
        $this->PageOrientation = 'L';
        $W = 335.75;


        // $this->ColumnWidth = array_merge(
        //  array($W*0.05, $W*0.09, $W*0.18),
        //  array_fill(0, count($this->typeMap), $widthAccts),
        //  array($W*0.1)
        // );

        // $this->TextPadding=array('T'=>'0.25','B'=>'0.25','L'=>'0.25','R'=>'0.25');
        // $this->TextHeight = 6;

        // $this->Alignment = array_merge(
        //  array('C','C','L'),
        //  array_fill(0, count($this->typeMap),'R'),
        //  array('R')
        // );

        $this->ColumnLabels = array(
            'Bill Ref#',
            'Bill Date',
            'Prepared By',
            'Patient Name',
            'Case No.',
            'Department',
            'Type',
            'Actual Charges',
            'Discount',
            'PHIC Coverage',
            'Deposit',
            'Excess',
            'Classification',
            'Classification Discount',
            'Amount Due',
            'OR#',
            'OR Date',
            'Amount Paid',
            'Clerk',
            'Cancelled',        //added by pol
            'Cancelled By',      //added by pol
            'Cancelled Date',         //added by pol
        );
        $this->Columns = sizeof($this->ColumnLabels);
        $this->RowHeight = 6;
    }

    /**
     *
     */
public function FetchData() {
        global $db;

        $db->SetFetchMode(ADODB_FETCH_ASSOC);

        //edited by VAN 02-13-2013
        // from fn_get_pid_name to fn_get_pid_lastfirstmi
        $query = "SELECT
fb.bill_nr, fb.bill_dte, fu.name `prepared_by`, fn_get_pid_lastfirstmi(e.pid) `patient`,e.encounter_nr, department.id `department`,
CONCAT(
    IF (e.encounter_type IN (3,4), IF(w.accomodation_type=1, 'Service', 'Pay'), et.`type`),
        IF(
            (IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) +
                IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) +
                IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) +
                IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0)) > 0,
            '/PHIC', '')
) `type`,

(fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge +
fb.total_ops_charge + fb.   total_doc_charge + fb.total_msc_charge) `total_charge`,

(IFNULL(dsc.total_acc_discount, 0) + IFNULL(dsc.total_med_discount, 0) +
IFNULL(dsc.total_ops_discount, 0) + IFNULL(dsc.total_srv_discount, 0) + IFNULL(dsc.total_msc_discount, 0) +
    IFNULL(dsc.total_d1_discount, 0) + IFNULL(dsc.total_d2_discount, 0) +
    IFNULL(dsc.total_d3_discount, 0) + IFNULL(dsc.total_d4_discount, 0)) `total_discount`,

(IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) +
IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) +
    IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) +
    IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0)) `total_coverage`,

fb.total_prevpayments `previous_payment`,

(
fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge +
fb.total_ops_charge + fb.   total_doc_charge + fb.total_msc_charge -
IFNULL(cov.total_acc_coverage,0) - IFNULL(cov.total_med_coverage, 0) - IFNULL(cov.total_srv_coverage, 0) -
IFNULL(cov.total_ops_coverage, 0) - IFNULL(cov.total_msc_coverage, 0) -
    IFNULL(cov.total_d1_coverage, 0) - IFNULL(cov.total_d2_coverage, 0) -
    IFNULL(cov.total_d3_coverage, 0) - IFNULL(cov.total_d4_coverage, 0) -
IFNULL(dsc.total_acc_discount, 0) - IFNULL(dsc.total_med_discount, 0) -
IFNULL(dsc.total_ops_discount, 0) - IFNULL(dsc.total_srv_discount, 0) - IFNULL(dsc.total_msc_discount, 0) -
    IFNULL(dsc.total_d1_discount, 0) - IFNULL(dsc.total_d2_discount, 0) -
    IFNULL(dsc.total_d3_discount, 0) - IFNULL(dsc.total_d4_discount, 0) - fb.total_prevpayments

) `excess`,

IFNULL(dd.discountdesc,'-') `classification`,
IFNULL(bd.discount,0) `discount_pct`,
bd.discount_amnt `discount_fixed`,

#fb.total_prevpayments,

#GROUP_CONCAT(IFNULL(p.or_no,'-') SEPARATOR ', ') `or`,
#GROUP_CONCAT(IFNULL(DATE_FORMAT(p.or_date, '%Y-%m-%d %h:%i %p'),'-') SEPARATOR ', ') `or_dates`,
#IF(SUM(IFNULL(pr.amount_due,0))>0, SUM(IFNULL(pr.amount_due,0)), '-') `or_amount`,
#GROUP_CONCAT(IFNULL(pu.`name`,'-') SEPARATOR ', ') `or_clerk`

p.or_no `or`,
p.or_date `or_date`,
p.cancel_date,
pr.ref_source, pr.service_code,
pr.amount_due `or_amount`,
fb.is_deleted,
mu.name,
(SELECT DATE_FORMAT(fb.modify_dt,'%b %d %Y %h:%i %p')) as modifydate,
pu.`name` `or_clerk`


FROM seg_billing_encounter fb
INNER JOIN care_encounter e ON e.encounter_nr = fb.encounter_nr
INNER JOIN care_department `department` ON department.nr = e.current_dept_nr
#left join care_encounter_location loc on loc.encounter_nr=e.encounter_nr
LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr AND hcare_id='18'
LEFT JOIN care_type_encounter et ON e.encounter_type=et.type_nr
LEFT JOIN care_ward w ON w.nr = e.current_ward_nr
LEFT JOIN seg_billing_coverage cov ON cov.bill_nr = fb.bill_nr
LEFT JOIN seg_billingcomputed_discount dsc ON dsc.bill_nr = fb.bill_nr
LEFT JOIN seg_billing_discount bd ON bd.bill_nr = fb.bill_nr
LEFT JOIN seg_discount dd ON dd.discountid=bd.discountid
LEFT JOIN seg_pay_request pr ON pr.ref_source='FB' AND pr.service_code = fb.bill_nr
LEFT JOIN seg_pay p ON p.or_no=pr.or_no AND p.cancel_date IS NULL
LEFT JOIN care_users pu ON p.create_id = pu.login_id
LEFT JOIN care_users fu ON fb.create_id = fu.login_id
LEFT JOIN care_users mu ON fb.modify_id = mu.login_id 

WHERE 
(fb.bill_dte LIKE " . $db->qstr(date('Y-m-d', $this->date).'%')  . ") 
#GROUP BY bill_nr
ORDER BY bill_nr ASC
LIMIT 0,999999";

        $result=$db->Execute($query);
        if ($result) {
            $rows = $result->GetRows();
            foreach ($rows as $row) {

            // 'Bill Ref#',
            // 'Bill Date',
            // 'Prepared By',
            // 'Case No.',
            // 'Department',
            // 'Type',
            // 'Actual Charges',
            // 'Discount',
            // 'PHIC Coverage',
            // 'Deposit',
            // 'Excess',
            // 'Classification',
            // 'Classification Discount',
            // 'Amount Due',
            // 'OR#',
            // 'OR Date',
            // 'Amount Paid',
            // 'Clerk'
                 //added by pol
                if ($row['is_deleted']) {
                    $IsDeleted = 'Is Cancelled';
                    $Deletedby = $row['name'];
                    $deletedtime = $row['modifydate'];
                }
                else{
                    $IsDeleted = '';
                    $Deletedby = '';
                    $deletedtime = '';
                }
                //end pol 
                if (($row['classification'] == 'Infirmary') || ($row['classification'] == 'Senior Citizen') ) {
                    $ClassificationShow = $row['classification'];
                    $totalDiscount = (float) $row['discount_fixed'] ? 
                    $row['discount_fixed'] : 
                    ((float) $row['excess']) * $row['discount_pct'];
                    $AmountDueShow = (float) $row['excess'] - $totalDiscount;
                    $OrNumberShow = $row['or'];
                    $OrDateShow = $row['or_date'] ? date('Y-m-d h:i A', strtotime($row['or_date'])) : '-';
                    $AmountPayableShow = $row['or_amount'];
                    $ClerkShow = $row['or_clerk'];

                }
                else{
                    $ClassificationShow = '';
                    $totalDiscount = '';
                    $AmountDueShow = '';
                    $OrNumberShow = '';
                    $OrDateShow = '';
                    $AmountPayableShow = '';
                    $ClerkShow = '';
                }
                
                $this->Data[] = array(
                    $row['bill_nr'],
                    date('Y-m-d h:i A', strtotime($row['bill_dte'])),
                    $row['prepared_by'],
                    $row['patient'],
                    $row['encounter_nr'],
                    $row['department'],
                    $row['type'],
                    $row['total_charge'],
                    $row['total_discount'],
                    $row['total_coverage'],
                    $row['previous_payment'],
                    $row['excess'],
                    $ClassificationShow,
                    $totalDiscount,
                    $AmountDueShow,
                    $OrNumberShow,
                    $OrDateShow,
                    $AmountPayableShow,
                    $ClerkShow,
                    $IsDeleted,   //added by pol
                    $Deletedby,    //added by pol
                    $deletedtime,    //added by pol
                );
            }
        }
        else {
            echo "<pre>", $query, "</pre>";
            print_r($db->ErrorMsg());
            exit;
        }
    }


    /**
     *
     * @return void
     */
     //added by pol
    public function FetchDataDeleted() {
        global $db;

        $db->SetFetchMode(ADODB_FETCH_ASSOC);

        //edited by VAN 02-13-2013
        // from fn_get_pid_name to fn_get_pid_lastfirstmi
        $query = "SELECT
fb.bill_nr, fb.bill_dte, fu.name `prepared_by`, fn_get_pid_lastfirstmi(e.pid) `patient`,e.encounter_nr, department.id `department`,
CONCAT(
    IF (e.encounter_type IN (3,4), IF(w.accomodation_type=1, 'Service', 'Pay'), et.`type`),
        IF(
            (IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) +
                IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) +
                IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) +
                IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0)) > 0,
            '/PHIC', '')
) `type`,

(fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge +
fb.total_ops_charge + fb.   total_doc_charge + fb.total_msc_charge) `total_charge`,

(IFNULL(dsc.total_acc_discount, 0) + IFNULL(dsc.total_med_discount, 0) +
IFNULL(dsc.total_ops_discount, 0) + IFNULL(dsc.total_srv_discount, 0) + IFNULL(dsc.total_msc_discount, 0) +
    IFNULL(dsc.total_d1_discount, 0) + IFNULL(dsc.total_d2_discount, 0) +
    IFNULL(dsc.total_d3_discount, 0) + IFNULL(dsc.total_d4_discount, 0)) `total_discount`,

(IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) +
IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) +
    IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) +
    IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0)) `total_coverage`,

fb.total_prevpayments `previous_payment`,

(
fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge +
fb.total_ops_charge + fb.   total_doc_charge + fb.total_msc_charge -
IFNULL(cov.total_acc_coverage,0) - IFNULL(cov.total_med_coverage, 0) - IFNULL(cov.total_srv_coverage, 0) -
IFNULL(cov.total_ops_coverage, 0) - IFNULL(cov.total_msc_coverage, 0) -
    IFNULL(cov.total_d1_coverage, 0) - IFNULL(cov.total_d2_coverage, 0) -
    IFNULL(cov.total_d3_coverage, 0) - IFNULL(cov.total_d4_coverage, 0) -
IFNULL(dsc.total_acc_discount, 0) - IFNULL(dsc.total_med_discount, 0) -
IFNULL(dsc.total_ops_discount, 0) - IFNULL(dsc.total_srv_discount, 0) - IFNULL(dsc.total_msc_discount, 0) -
    IFNULL(dsc.total_d1_discount, 0) - IFNULL(dsc.total_d2_discount, 0) -
    IFNULL(dsc.total_d3_discount, 0) - IFNULL(dsc.total_d4_discount, 0) - fb.total_prevpayments

) `excess`,

IFNULL(dd.discountdesc,'-') `classification`,
IFNULL(bd.discount,0) `discount_pct`,
bd.discount_amnt `discount_fixed`,

#fb.total_prevpayments,

#GROUP_CONCAT(IFNULL(p.or_no,'-') SEPARATOR ', ') `or`,
#GROUP_CONCAT(IFNULL(DATE_FORMAT(p.or_date, '%Y-%m-%d %h:%i %p'),'-') SEPARATOR ', ') `or_dates`,
#IF(SUM(IFNULL(pr.amount_due,0))>0, SUM(IFNULL(pr.amount_due,0)), '-') `or_amount`,
#GROUP_CONCAT(IFNULL(pu.`name`,'-') SEPARATOR ', ') `or_clerk`

p.or_no `or`,
p.or_date `or_date`,
p.cancel_date,
pr.ref_source, pr.service_code,
pr.amount_due `or_amount`,
fb.is_deleted,
mu.name,
(SELECT DATE_FORMAT(fb.modify_dt,'%b %d %Y %h:%i %p')) as modifydate,
pu.`name` `or_clerk`


FROM seg_billing_encounter fb
INNER JOIN care_encounter e ON e.encounter_nr = fb.encounter_nr
INNER JOIN care_department `department` ON department.nr = e.current_dept_nr
#left join care_encounter_location loc on loc.encounter_nr=e.encounter_nr
LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr AND hcare_id='18'
LEFT JOIN care_type_encounter et ON e.encounter_type=et.type_nr
LEFT JOIN care_ward w ON w.nr = e.current_ward_nr
LEFT JOIN seg_billing_coverage cov ON cov.bill_nr = fb.bill_nr
LEFT JOIN seg_billingcomputed_discount dsc ON dsc.bill_nr = fb.bill_nr
LEFT JOIN seg_billing_discount bd ON bd.bill_nr = fb.bill_nr
LEFT JOIN seg_discount dd ON dd.discountid=bd.discountid
LEFT JOIN seg_pay_request pr ON pr.ref_source='FB' AND pr.service_code = fb.bill_nr
LEFT JOIN seg_pay p ON p.or_no=pr.or_no AND p.cancel_date IS NULL
LEFT JOIN care_users pu ON p.create_id = pu.login_id
LEFT JOIN care_users fu ON fb.create_id = fu.login_id
LEFT JOIN care_users mu ON fb.modify_id = mu.login_id 

WHERE fb.is_deleted = '1'
AND
(fb.bill_dte LIKE " . $db->qstr(date('Y-m-d', $this->date).'%')  . ") 
#GROUP BY bill_nr
ORDER BY bill_nr ASC
LIMIT 0,999999";

        $result=$db->Execute($query);
        if ($result) {
            $rows = $result->GetRows();
            foreach ($rows as $row) {

            // 'Bill Ref#',
            // 'Bill Date',
            // 'Prepared By',
            // 'Case No.',
            // 'Department',
            // 'Type',
            // 'Actual Charges',
            // 'Discount',
            // 'PHIC Coverage',
            // 'Deposit',
            // 'Excess',
            // 'Classification',
            // 'Classification Discount',
            // 'Amount Due',
            // 'OR#',
            // 'OR Date',
            // 'Amount Paid',
            // 'Clerk'

               if ($row['is_deleted']) {
                    $IsDeleted = 'Is Cancelled';
                    $Deletedby = $row['name'];
                    $deletedtime = $row['modifydate'];
                }
                else{
                    $IsDeleted = '';
                    $Deletedby = '';
                    $deletedtime = '';
                }
                if (($row['classification'] == 'Infirmary') || ($row['classification'] == 'Senior Citizen') ) {
                    $ClassificationShow = $row['classification'];
                    $totalDiscount = (float) $row['discount_fixed'] ? 
                    $row['discount_fixed'] : 
                    ((float) $row['excess']) * $row['discount_pct'];
                    $AmountDueShow = (float) $row['excess'] - $totalDiscount;
                    $OrNumberShow = $row['or'];
                    $OrDateShow = $row['or_date'] ? date('Y-m-d h:i A', strtotime($row['or_date'])) : '-';
                    $AmountPayableShow = $row['or_amount'];
                    $ClerkShow = $row['or_clerk'];

                }
                else{
                    $ClassificationShow = '';
                    $totalDiscount = '';
                    $AmountDueShow = '';
                    $OrNumberShow = '';
                    $OrDateShow = '';
                    $AmountPayableShow = '';
                    $ClerkShow = '';
                }
                
                $this->Data[] = array(
                    $row['bill_nr'],
                    date('Y-m-d h:i A', strtotime($row['bill_dte'])),
                    $row['prepared_by'],
                    $row['patient'],
                    $row['encounter_nr'],
                    $row['department'],
                    $row['type'],
                    $row['total_charge'],
                    $row['total_discount'],
                    $row['total_coverage'],
                    $row['previous_payment'],
                    $row['excess'],
                    $ClassificationShow,
                    $totalDiscount,
                    $AmountDueShow,
                    $OrNumberShow,
                    $OrDateShow,
                    $AmountPayableShow,
                    $ClerkShow,
                    $IsDeleted,
                    $Deletedby,
                    $deletedtime,
                );
            }
        }
        else {
            echo "<pre>", $query, "</pre>";
            print_r($db->ErrorMsg());
            exit;
        }
    }


    /**
     *
     * @return void
     */

public function FetchDataNotDeleted() {
        global $db;

        $db->SetFetchMode(ADODB_FETCH_ASSOC);

        //edited by VAN 02-13-2013
        // from fn_get_pid_name to fn_get_pid_lastfirstmi
        $query = "SELECT
fb.bill_nr, fb.bill_dte, fu.name `prepared_by`, fn_get_pid_lastfirstmi(e.pid) `patient`,e.encounter_nr, department.id `department`,
CONCAT(
    IF (e.encounter_type IN (3,4), IF(w.accomodation_type=1, 'Service', 'Pay'), et.`type`),
        IF(
            (IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) +
                IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) +
                IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) +
                IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0)) > 0,
            '/PHIC', '')
) `type`,

(fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge +
fb.total_ops_charge + fb.   total_doc_charge + fb.total_msc_charge) `total_charge`,

(IFNULL(dsc.total_acc_discount, 0) + IFNULL(dsc.total_med_discount, 0) +
IFNULL(dsc.total_ops_discount, 0) + IFNULL(dsc.total_srv_discount, 0) + IFNULL(dsc.total_msc_discount, 0) +
    IFNULL(dsc.total_d1_discount, 0) + IFNULL(dsc.total_d2_discount, 0) +
    IFNULL(dsc.total_d3_discount, 0) + IFNULL(dsc.total_d4_discount, 0)) `total_discount`,

(IFNULL(cov.total_acc_coverage,0) + IFNULL(cov.total_med_coverage, 0) + IFNULL(cov.total_srv_coverage, 0) +
IFNULL(cov.total_ops_coverage, 0) + IFNULL(cov.total_msc_coverage, 0) +
    IFNULL(cov.total_d1_coverage, 0) + IFNULL(cov.total_d2_coverage, 0) +
    IFNULL(cov.total_d3_coverage, 0) + IFNULL(cov.total_d4_coverage, 0)) `total_coverage`,

fb.total_prevpayments `previous_payment`,

(
fb.total_acc_charge + fb.total_med_charge + fb.total_srv_charge +
fb.total_ops_charge + fb.   total_doc_charge + fb.total_msc_charge -
IFNULL(cov.total_acc_coverage,0) - IFNULL(cov.total_med_coverage, 0) - IFNULL(cov.total_srv_coverage, 0) -
IFNULL(cov.total_ops_coverage, 0) - IFNULL(cov.total_msc_coverage, 0) -
    IFNULL(cov.total_d1_coverage, 0) - IFNULL(cov.total_d2_coverage, 0) -
    IFNULL(cov.total_d3_coverage, 0) - IFNULL(cov.total_d4_coverage, 0) -
IFNULL(dsc.total_acc_discount, 0) - IFNULL(dsc.total_med_discount, 0) -
IFNULL(dsc.total_ops_discount, 0) - IFNULL(dsc.total_srv_discount, 0) - IFNULL(dsc.total_msc_discount, 0) -
    IFNULL(dsc.total_d1_discount, 0) - IFNULL(dsc.total_d2_discount, 0) -
    IFNULL(dsc.total_d3_discount, 0) - IFNULL(dsc.total_d4_discount, 0) - fb.total_prevpayments

) `excess`,

IFNULL(dd.discountdesc,'-') `classification`,
IFNULL(bd.discount,0) `discount_pct`,
bd.discount_amnt `discount_fixed`,

#fb.total_prevpayments,

#GROUP_CONCAT(IFNULL(p.or_no,'-') SEPARATOR ', ') `or`,
#GROUP_CONCAT(IFNULL(DATE_FORMAT(p.or_date, '%Y-%m-%d %h:%i %p'),'-') SEPARATOR ', ') `or_dates`,
#IF(SUM(IFNULL(pr.amount_due,0))>0, SUM(IFNULL(pr.amount_due,0)), '-') `or_amount`,
#GROUP_CONCAT(IFNULL(pu.`name`,'-') SEPARATOR ', ') `or_clerk`

p.or_no `or`,
p.or_date `or_date`,
p.cancel_date,
pr.ref_source, pr.service_code,
pr.amount_due `or_amount`,
fb.is_deleted,
mu.name,
(SELECT DATE_FORMAT(fb.modify_dt,'%b %d %Y %h:%i %p')) as modifydate,
pu.`name` `or_clerk`


FROM seg_billing_encounter fb
INNER JOIN care_encounter e ON e.encounter_nr = fb.encounter_nr
INNER JOIN care_department `department` ON department.nr = e.current_dept_nr
#left join care_encounter_location loc on loc.encounter_nr=e.encounter_nr
LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr AND hcare_id='18'
LEFT JOIN care_type_encounter et ON e.encounter_type=et.type_nr
LEFT JOIN care_ward w ON w.nr = e.current_ward_nr
LEFT JOIN seg_billing_coverage cov ON cov.bill_nr = fb.bill_nr
LEFT JOIN seg_billingcomputed_discount dsc ON dsc.bill_nr = fb.bill_nr
LEFT JOIN seg_billing_discount bd ON bd.bill_nr = fb.bill_nr
LEFT JOIN seg_discount dd ON dd.discountid=bd.discountid
LEFT JOIN seg_pay_request pr ON pr.ref_source='FB' AND pr.service_code = fb.bill_nr
LEFT JOIN seg_pay p ON p.or_no=pr.or_no AND p.cancel_date IS NULL
LEFT JOIN care_users pu ON p.create_id = pu.login_id
LEFT JOIN care_users fu ON fb.create_id = fu.login_id
LEFT JOIN care_users mu ON fb.modify_id = mu.login_id 

WHERE fb.is_deleted is NULL
AND
(fb.bill_dte LIKE " . $db->qstr(date('Y-m-d', $this->date).'%')  . ") 
#GROUP BY bill_nr
ORDER BY bill_nr ASC
LIMIT 0,999999";

        $result=$db->Execute($query);
        if ($result) {
            $rows = $result->GetRows();
            foreach ($rows as $row) {

            // 'Bill Ref#',
            // 'Bill Date',
            // 'Prepared By',
            // 'Case No.',
            // 'Department',
            // 'Type',
            // 'Actual Charges',
            // 'Discount',
            // 'PHIC Coverage',
            // 'Deposit',
            // 'Excess',
            // 'Classification',
            // 'Classification Discount',
            // 'Amount Due',
            // 'OR#',
            // 'OR Date',
            // 'Amount Paid',
            // 'Clerk'

                if ($row['is_deleted']) {
                    $IsDeleted = 'Is Cancelled';
                    $Deletedby = $row['name'];
                    $deletedtime = $row['modifydate'];
                }
                else{
                    $IsDeleted = '';
                    $Deletedby = '';
                    $deletedtime = '';
                }
                if (($row['classification'] == 'Infirmary') || ($row['classification'] == 'Senior Citizen') ) {
                    $ClassificationShow = $row['classification'];
                    $totalDiscount = (float) $row['discount_fixed'] ? 
                    $row['discount_fixed'] : 
                    ((float) $row['excess']) * $row['discount_pct'];
                    $AmountDueShow = (float) $row['excess'] - $totalDiscount;
                    $OrNumberShow = $row['or'];
                    $OrDateShow = $row['or_date'] ? date('Y-m-d h:i A', strtotime($row['or_date'])) : '-';
                    $AmountPayableShow = $row['or_amount'];
                    $ClerkShow = $row['or_clerk'];

                }
                else{
                    $ClassificationShow = '';
                    $totalDiscount = '';
                    $AmountDueShow = '';
                    $OrNumberShow = '';
                    $OrDateShow = '';
                    $AmountPayableShow = '';
                    $ClerkShow = '';
                }
                
                $this->Data[] = array(
                    $row['bill_nr'],
                    date('Y-m-d h:i A', strtotime($row['bill_dte'])),
                    $row['prepared_by'],
                    $row['patient'],
                    $row['encounter_nr'],
                    $row['department'],
                    $row['type'],
                    $row['total_charge'],
                    $row['total_discount'],
                    $row['total_coverage'],
                    $row['previous_payment'],
                    $row['excess'],
                    $ClassificationShow,
                    $totalDiscount,
                    $AmountDueShow,
                    $OrNumberShow,
                    $OrDateShow,
                    $AmountPayableShow,
                    $ClerkShow,
                    $IsDeleted,
                    $Deletedby,
                    $deletedtime,

                );
            }
        }
        else {
            echo "<pre>", $query, "</pre>";
            print_r($db->ErrorMsg());
            exit;
        }
    }

  //end by pol
    /**
     *
     * @return void
     */

    public function ReportXls() {
        $excel = new PHPExcel();
        $sheet = $excel->getActiveSheet();

        $sheet->setCellValue('A1',
            sprintf('REPORT OF BILLS RENDERED (%s)', date('M Y', $this->date)));
        $sheet->getStyle('A1:R3')->applyFromArray(array(
            'font' => array(
                'bold' => true
            )
        ));
        $sheet->getStyle('A1:R3')->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


        foreach ($this->ColumnLabels as $i=>$label) {
            $sheet->setCellValue(chr(ord('A') + $i) . '3', $label);
        }

        foreach ($this->Data as $i=>$item) {
            foreach ($item as $j=>$value) {
                $sheet->setCellValue(chr(ord('A') + $j) . ($i+4), $value);
            }
        }

        $writer = PHPExcel_IOFactory::createWriter($excel, $this->format);

        switch ($this->format) {
            case 'Excel5':
            case 'Excel2007':
                header('Content-type: application/xls');
                header('Content-Disposition: attachment; filename="report.xls"');
                break;

            default:
                die('Invalid report format!');

        }
        die('Done');
        $writer->save('php://output');
        exit;

    }

    /**
    * Generatting CSV formatted string from an array.
    * By Sergey Gurevich.
    */
    private function arrayToCSV($array, $header_row = true, $col_sep = ",", $row_sep = "\n", $qut = '"')
    {
        if (!is_array($array) or !is_array($array[0])) return false;

        //Header row.
        if ($header_row)
        {
            foreach ($array[0] as $key => $val)
            {
                //Escaping quotes.
                $key = str_replace($qut, "$qut$qut", $key);
                $output .= "$col_sep$qut$key$qut";
            }
            $output = substr($output, 1)."\n";
        }
        //Data rows.
        foreach ($array as $key => $val)
        {
            $tmp = '';
            foreach ($val as $cell_key => $cell_val)
            {
                //Escaping quotes.
                $cell_val = str_replace($qut, "$qut$qut", $cell_val);
                $tmp .= "$col_sep$qut$cell_val$qut";
            }
            $output .= substr($tmp, 1).$row_sep;
        }

        return $output;
    }

    /**
     * Description
     * @return type
     */
    public function ReportCSV() {
        $array = array();
        foreach ($this->Data as $item) {
            $row = array();
            foreach ($item as $i => $value) {
                $row[$this->ColumnLabels[$i]] = $value;
            }
            $array[] = $row;
        }



        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename="report.csv"');
        echo $this->arrayToCsv($array, true);
        exit;
    }

}
$rep = new ReportMonthlyBillsRendered($_GET['date'], $_GET['format'], $_GET['formatD']);
//$rep->FetchDataNotDeleted();
//$rep->FetchDataDeleted();
//added by pol
if ($formatD == 'SA'){   
    echo $rep->FetchData();
} elseif ($formatD == 'DB'){
    $rep->FetchDataDeleted();
} elseif ($formatD == 'FB'){
    $rep->FetchDataNotDeleted();
}
//end pol
if (stripos($format, 'Excel') !== false) {
    $rep->ReportXls();
} elseif ($format == 'CSV') {
    $rep->ReportCSV();
}


