<?php
/**
* @package SegHIS_api
*
* Class containing all properties and methods related to an encounter's billing For New PHIC Circular.
*
* Note this class should be instantiated only after a "$db" adodb  connector object
* has been established by an adodb instance.
*
* @author     : Jarel Q. Mamac
* @version    : 1.0
* @Created on : November 10, 2013
*
*
***/

require_once('roots.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');
require_once($root_path.'include/care_api_classes/billing/class_accommodation.php');
require_once($root_path.'include/care_api_classes/billing/class_medicine.php');
require_once($root_path.'include/care_api_classes/billing/class_supply.php');
require_once($root_path.'include/care_api_classes/billing/class_services.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_ops.php');
require_once($root_path.'include/care_api_classes/billing/class_prof_fees.php');
require_once($root_path.'include/care_api_classes/billing/class_payment.php');
require_once($root_path.'include/care_api_classes/billing/class_actual_coverage.php');
require_once($root_path.'include/care_api_classes/billing/class_pf_claim.php');
require_once($root_path.'include/care_api_classes/billing/class_msc_chrg.php');
require_once($root_path.'include/care_api_classes/billing/class_billing_discount.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_helper.php');

define('ER_PATIENT', 1);
define('OUT_PATIENT', 2);
define('DIALYSIS_PATIENT', 5);
define('WELLBABY', 12);
define('DEFAULT_PCF', 40);
define('CHARITY', 'CHARITY');
define('CHARITYWARD', 1);
define('NOBALANCEBILLING','NBB');
define('INFIRMARY', 'PHS');
define('SENIORCITIZEN', 'SENIOR');
define('OBANNEX', 'OB-ANNEX');
define('SERVICEWARD', 'SERVICE');
define('ANNEXWARD', 'ANNEX');
define('ICUWARD','ICU');
define('NEWBORN_A', 24);
define('NEWBORN_B', 27);
define('DEFAULT_NBPKG_RATE', 1750);
define('SKED_EFFECTIVITY','2010-10-07');
define('ISSRVD_EFFECTIVITY', '2012-10-09');
define('PHIC_ID', 18);
define('OBANNEX', 'OB-ANNEX');

class Billing extends Core {

    var $encounter_nr;
    var $prev_encounter = '';
    var $prev_encounter_no = ''; //added by poliam 01/04/2014
    var $old_bill_nr;
    var $bill_dte;
    var $bill_frmdte;
    var $death_date;
    var $is_died;
    var $is_final;
    var $pkgamountlimit = 0;
    var $cutoff_hrs = 0;
    var $is_coveredbypkg;
    var $confinetype_id;
    var $accomm_typ_nr;
    var $accomm_typ_desc;
    var $accomm_ward_name;
    var $charged_date;
    var $accommodation_hist; //added by nick 01/06/2014
    // Added by James 1/6/2014
    var $error_no;
    var $error_msg;
    var $error_sql;
    //var $error_final;
    // End James
    var $memcategory_id;
    var $accomodation_type;
    var $caseTypeHist,$memCatHist; //added by nick 05/06/2014
    var $watchersRoomDays = 0, $watchersRoomRate;
    var $prioritynoroom = 0, $accomodationDateDif = 0;

    function setBillArgs($enc,$bill_dte,$bill_frmdte,$death_date ='',$bill_nr='')
    {

        $this->death_date = $death_date;
        $this->bill_frmdte = $bill_frmdte;
        $this->encounter_nr = $enc;
        $this->current_enr = $enc;
        $this->old_bill_nr = $bill_nr;
        $this->bill_dte = $bill_dte;
        $this->charged_date = strftime("%Y-%m-%d %H:%M:%S");

        $this->getPrevEncounterNr();    // Get parent encounter no., if there is ...

        if($this->prev_encounter != '') {
            $this->bill_frmdte = $this->getEncounterDte();
        }

        $this->is_final = $this->isFinal();

        $this->is_coveredbypkg = 0;
        $n_id = $this->getConfinementType();


        if ($old_billnr != '') {
            $ncutoff = $this->getAppliedHrsCutoff();
            $this->correctBillDates();
        }
        else
            $ncutoff = -1;

        $hosp_obj = new Hospital_Admin();
        $this->cutoff_hrs = ($ncutoff == -1) ? $hosp_obj->getCutoff_Hrs() : $ncutoff;
        $this->pcf = $hosp_obj->getDefinedPCF();
        $this->pcf = ($this->pcf == 0) ? DEFAULT_PCF : $this->pcf;

    }


    function getError()
    {
        return($this->error_msg);
    }


    function setBillInfo()
    {
        global $db;

        $this->sql = "SELECT bill_nr, is_final, bill_dte, bill_frmdte ".
                     "FROM seg_billing_encounter WHERE encounter_nr = ".$db->qstr($this->encounter_nr)." ".
                     "AND is_deleted IS NULL";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }

    }

    function getBillInfo()
    {
        $bill_info = array();
        $bill_info->bill_dte = $this->bill_dte;
        $bill_info->bill_frmdte = $this->bill_dte;

        return $bill_info;
    }


    function getPrevEncounterNr()
    {
        global $db;

        $strSQL = "SELECT parent_encounter_nr
                   FROM care_encounter
                   WHERE encounter_nr = ".$db->qstr($this->encounter_nr);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $prev_encounter = $row['parent_encounter_nr'];
                $this->prev_encounter = $prev_encounter;
            }
        }

        return($prev_encounter);
    }

    function getPrevEncounter($enc)
    {
        global $db;

        $strSQL = "SELECT parent_encounter_nr
                   FROM care_encounter
                   WHERE encounter_nr = ".$db->qstr($enc);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $prev_encounter = $row['parent_encounter_nr'];
                $this->prev_encounter = $prev_encounter;
            }
        }

        return($prev_encounter);
    }

    /*added by mai 07-09-2014*/
    function getDiscounts_Sc_Ar($refno){
        global $db;
        $discount_areas = array();
        $this->sql = "SELECT bill_areas, ar_discount, sc_discount, dr_nr 
                    FROM seg_billing_other_discounts WHERE refno = ".$db->qstr($refno)." ORDER BY bill_areas, dr_nr ASC";
        if($result=$db->Execute($this->sql)){
            if($result->RecordCount()){
                while($row=$result->FetchRow()){
                    array_push($discount_areas,array('bill_area'=>$row['bill_areas'],
                                            'dr_nr'=>$row['dr_nr'],
                                            'sc_discount'=>$row['sc_discount'],
                                            'ar_discount'=>$row['ar_discount']));
                }
            }
            return $discount_areas;
        }else{
            return false;
        }
    }

    function getDiscounts_insurance($refno){
        global $db;
        $discount_areas = array();
        $this->sql = "SELECT sba.bill_area, (SELECT phic.coverage FROM seg_billingcoverage_adjustment phic WHERE hcare_id = 18 AND sba.bill_area = phic.bill_area AND ref_no = ".$db->qstr($refno).") AS phic, 
                        (SELECT SUM(coverage) FROM seg_billingcoverage_adjustment other_ins WHERE hcare_id != 18 AND sba.bill_area = other_ins.bill_area AND ref_no = ".$db->qstr($refno).") AS other_insurance
                        FROM seg_billingcoverage_adjustment sba WHERE sba.ref_no = ".$db->qstr($refno)." GROUP BY bill_area";
        if($result=$db->Execute($this->sql)){
            if($result->RecordCount()){
                while($row=$result->FetchRow()){
                    array_push($discount_areas,array('bill_area'=>$row['bill_area'],
                                            'phic'=>$row['phic'],
                                            'other_insurance'=>$row['other_insurance']));
                }
            }
            return $discount_areas;
        }else{
            return false;
        }
    }

     function getDiscounts_doctor($refno){
        global $db;
        $discount_areas = array();
        $this->sql = "SELECT sba.role_area, sba.dr_nr, (SELECT phic.dr_claim FROM seg_billing_pf phic WHERE hcare_id = 18 
                        AND sba.role_area = phic.role_area AND sba.dr_nr = phic.dr_nr AND bill_nr = ".$db->qstr($refno).") AS phic,
                        (SELECT SUM(dr_claim) FROM seg_billing_pf other_ins WHERE hcare_id != 18 AND 
                                sba.role_area = other_ins.role_area AND sba.dr_nr = other_ins.dr_nr AND bill_nr = ".$db->qstr($refno).") AS other_insurance
                        FROM seg_billing_pf sba WHERE sba.bill_nr = ".$db->qstr($refno)." ORDER BY role_area, dr_nr";
        if($result=$db->Execute($this->sql)){
            if($result->RecordCount()){
                while($row=$result->FetchRow()){
                    array_push($discount_areas,array('bill_area'=>$row['role_area'],
                                            'dr_nr'=>$row['dr_nr'],
                                            'phic'=>$row['phic'],
                                            'other_insurance'=>$row['other_insurance']));
                }
            }
            return $discount_areas;
        }else{
            return false;
        }
    }
    /*end added by mai*/

    function isERPatient($enc)
    {
        global $db;
        $enc_type = 0;
        $strSQL = "SELECT encounter_type FROM care_encounter WHERE encounter_nr = ".$db->qstr($enc);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = ($row['encounter_type']);
            }
        }

        return ($enc_type == ER_PATIENT);
    }

    //added by nick 1/6/2014
    function getAccHist($result){
        return($this->accommodation_hist);
    }
    //enc nick

    function getAccomodationList()
    {
        global $db;

        $filter = array('','');

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        if ($prev_encounter != '') $filter[0] = " or cel.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter[1] = " or sel.encounter_nr = ".$db->qstr($prev_encounter);


        $this->sql = "select cel.encounter_nr, location_nr, cr.type_nr, ce.current_room_nr AS room, ce.current_ward_nr AS ward, cw.accomodation_type, concat(ctr.name,' (',cw.name,')') as name, ".
            "      (case when not (isnull(selr.rate) OR selr.rate=0)  then selr.rate else ctr.room_rate end) as rm_rate, 0 as days_stay, 0 as hrs_stay, ".
            "      date_from, date_to, time_from, time_to, 'AD' as source, mandatory_excess ".
            "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr INNER JOIN care_encounter AS ce ON ce.encounter_nr = cel.encounter_nr) ".
            "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr) ".
            "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) ".
                    "      on cel.location_nr = cr.room_nr and cel.group_nr = cr.ward_nr ".
            "        LEFT JOIN seg_encounter_location_addtl `sela` ON cel.encounter_nr = sela.encounter_nr " .
            "   where (cel.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") ".
            "      and exists (select nr ".
            "                     from care_type_location as ctl ".
            "                        where upper(type) = 'ROOM' and ctl.nr = cel.type_nr) ".
            "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
            "         and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->bill_dte) . ") " .
            "             or ".
            "       (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " . $db->qstr($this->bill_frmdte) . " ".
            "         and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->bill_dte) . ") ".
            "          or ".
            "        str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
            " union ".
            "select sel.encounter_nr, cr.room_nr, cr.type_nr, sel.room_nr, sel.group_nr, cw.accomodation_type, concat(ctr.name,' (',cw.name,')') as name, ".
                    "      (case when not (isnull(sel.rate) OR sel.rate=0)  then sel.rate else ctr.room_rate end) as rm_rate, days_stay, hrs_stay, ".
            "      date(sel.create_dt) as date_from, '0000-00-00' as date_to, time(sel.create_dt) as time_from, '00:00:00' as time_to, 'BL' as source, mandatory_excess ".
            "   from (seg_encounter_location_addtl as sel inner join care_ward as cw on sel.group_nr = cw.nr) ".
                    "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) on sel.room_nr = cr.nr and sel.group_nr = cr.ward_nr ".
            "   where (sel.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[1].") ".
            "      and (str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
            "      and str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->bill_dte) . ") ".
            "   order by source, date_from, time_from";

        //echo $this->sql;
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
            }else{
            return false;
                }

    }


      /*added by mai 07-09-2014*/

    function getInsurances($encounter_nr){
        global $db;

        $this->sql = "SELECT 
                      GROUP_CONCAT(cif.`firm_id`) AS insurances 
                    FROM
                      seg_encounter_insurance sei 
                      LEFT JOIN care_insurance_firm cif 
                        ON cif.`hcare_id` = sei.`hcare_id` 
                    WHERE sei.`hcare_id` <> '18' 
                      AND sei.`encounter_nr` =  ".$db->qstr($encounter_nr);

        if($result=$db->Execute($this->sql)){
            if($result->RecordCount()){
                while($row=$result->FetchRow()){
                    return $row['insurances'];
                }
            }
        }

        return false;
    }

    function getDiscounts_Serv($refno){
        global $db;
        $discount_areas = array();
        $this->sql = "select t.bill_areas AS bill_area, SUM(t.sc_discount) AS sc_discount, SUM(t.phic) AS phic, ".
                            "SUM(t.ar_discount) AS ar_discount, SUM(t.other_insurance) AS other_insurance ".
                       "FROM (SELECT discounts.bill_areas, NULL AS phic, discounts.ar_discount, discounts.sc_discount, NULL AS other_insurance ".
                                "FROM seg_billing_other_discounts discounts WHERE discounts.refno = ".$db->qstr($refno)." AND dr_nr = '0' ".
                            "UNION ".
                            "select insurance.bill_area, (SELECT coverage FROM seg_billingcoverage_adjustment WHERE hcare_id = '18'".
                                                            "AND ref_no = insurance.ref_no AND bill_area = insurance.bill_area) AS phic, ".
                                    "NULL AS ar_discount, NULL AS sc_discount, (SELECT SUM(coverage) FROM seg_billingcoverage_adjustment ".
                                                                                "WHERE hcare_id != '18' AND ref_no = insurance.ref_no ".
                                                                                "AND bill_area = insurance.bill_area) AS other_insurance ".
                            "FROM seg_billingcoverage_adjustment insurance WHERE insurance.ref_no = ".$db->qstr($refno).") t ".
                        "GROUP BY t.bill_areas";

        if($result=$db->Execute($this->sql)){
            if($result->RecordCount()){
                while($row=$result->FetchRow()){
                    array_push($discount_areas,array('bill_area'=>$row['bill_area'],
                                            'sc_discount'=>$row['sc_discount'],
                                            'ar_discount'=>$row['ar_discount'],
                                            'phic'=>$row['phic'],
                                            'other_insurance'=>$row['other_insurance']));
                }
            }
            return $discount_areas;
        }else{
            return false;
        }
    }

     function getDiscounts_Pf($refno){
        global $db;
        $discount_areas = array();
        $refno= $db->qstr($refno);
        $this->sql = "SELECT
                          t.bill_areas AS bill_area,
                          t.dr_nr AS dr_nr,
                          SUM(t.phic) AS phic,
                          SUM(t.ar_discount) AS ar_discount,
                          SUM(t.sc_discount) AS sc_discount,
                          SUM(t.other_insurance) AS other_insurance
                        FROM
                          (SELECT
                            discounts.bill_areas,
                            discounts.dr_nr,
                            NULL AS phic,
                            discounts.ar_discount,
                            discounts.sc_discount,
                            NULL AS other_insurance
                          FROM
                            seg_billing_other_discounts discounts
                          WHERE discounts.refno = $refno
                            AND dr_nr != '0'
                          UNION
                          SELECT
                            insurance.role_area,
                            insurance.dr_nr,
                            (SELECT
                              SUM(dr_claim)
                            FROM
                              seg_billing_pf
                            WHERE hcare_id = '18'
                              AND bill_nr = insurance.bill_nr
                              AND role_area = insurance.role_area
                              AND dr_nr = insurance.dr_nr) AS phic,
                            NULL AS ar_discount,
                            NULL AS sc_discount,
                            (SELECT
                              SUM(dr_claim)
                            FROM
                              seg_billing_pf
                            WHERE hcare_id != '18'
                              AND bill_nr = insurance.bill_nr
                              AND role_area = insurance.role_area
                              AND dr_nr = insurance.dr_nr) AS other_insurance
                          FROM
                            seg_billing_pf insurance
                          WHERE insurance.bill_nr = $refno ) t
                        GROUP BY t.bill_areas,
                          t.dr_nr ";

        if($result=$db->Execute($this->sql)){
            if($result->RecordCount()){
                while($row=$result->FetchRow()){
                    array_push($discount_areas,array('bill_area'=>$row['bill_area'],
                                            'sc_discount'=>$row['sc_discount'],
                                            'ar_discount'=>$row['ar_discount'],
                                            'dr_nr'=>$row['dr_nr'],
                                            'phic'=>$row['phic'],
                                            'other_insurance'=>$row['other_insurance']));
                }
            }
            return $discount_areas;
        }else{
            return false;
        }
    }
    /*end added by mai*/

    function getXLOList()
    {
        global $db;

        $filter = array('','');
        $this->services_list = array();

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        /*if ($this->old_bill_nr != '' && $this->is_final) {
            $cond_lab = " AND lh.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='LB' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_rad = " AND rh.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='RD' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_ph =  " AND ph.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='SU' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_mph = " AND mph.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='MS' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_misc = " AND m.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='OA' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
        }else{*/
            $cond_lab = " AND (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                        " AND str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") " ;
            $cond_rad = " AND (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                        " AND str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") " ;
            $cond_ph =  " AND (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ";
            $cond_mph = " AND (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ";
           $cond_misc = " AND (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ";
        //}

        if ($prev_encounter != '') $filter[0] = " or encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter[1] = " or sos.encounter_nr = ".$db->qstr($prev_encounter);

        $this->sql = "select lh.refno, serv_dt, serv_tm, ld.service_code, ls.name as service_desc, ls.group_code, " .
                    "   lsg.name as group_desc, sum(ld.quantity) as qty, ld.price_charge as serv_charge, 'LB' as source " .
                    "   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
                    "          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
                    "          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
                    "      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
                    "         UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag = 'phic' OR ld.request_flag IN (SELECT hcare_id FROM care_insurance_firm)) ".
                    "         and (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
                    $cond_lab.
                    "   group by  ld.service_code, ls.name, ls.group_code, lsg.name ".
                    " UNION ALL ".

                    "select rh.refno, rh.request_date as serv_dt, rh.request_time as serv_tm, rd.service_code, rs.name as service_desc, rs.group_code, " .
                    "   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
                    "   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
                    "          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
                    "          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
                    "      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
                    "      UPPER(TRIM(rd.STATUS)) <> 'DELETED' AND rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag = 'phic' OR rd.request_flag IN (SELECT hcare_id FROM care_insurance_firm)) ".
                    "         and (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' and upper(trim(rd.status)) <> 'DELETED' " .
                    $cond_rad.
                            "   group by rd.service_code, rs.name, rs.group_code, rsg.name ".
                    " UNION ALL ".

                    "select ph.refno, date(ph.orderdate) as serv_dt, time(ph.orderdate) as serv_tm, pd.bestellnum as service_code, artikelname as service_desc, 'SU' as group_code, ".
                            "      'Supplies' as group_desc, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, pricecharge as serv_charge, 'SU' as source ".
                    "   from ((seg_pharma_orders as ph inner join
                                                 seg_pharma_order_items pd on ph.refno = pd.refno and pd.serve_status <> 'N' and pd.request_flag is null) ".
                    "      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
                    "      left join
                            (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
                                                                    FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                                                                         ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].")
                                                                    WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") AND rd.ref_no = oh.refno)
                                        GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and is_cash = 0 ".
                    $cond_ph.
                            "      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 group by pd.bestellnum, artikelname".

                    " UNION ALL ".

                    "select mph.refno, date(mph.chrge_dte) as serv_dt, time(mph.chrge_dte) as serv_tm, mphd.bestellnum as service_code, artikelname as service_desc, 'MS' as group_code, ".
                            "      'Supplies' as group_desc, sum(quantity) as qty, unit_price as serv_charge, 'MS' as source ".
                    "   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
                    "      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") ".
                    /*$cond_mph.*/
                            "   group by  mphd.bestellnum, artikelname ".
                    " UNION ALL ".

                    "select sos.refno, date(eqh.order_date) as serv_dt, time(eqh.order_date) as serv_tm, eqd.equipment_id, artikelname, '' as group_code,
                         'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as uprice, 'OE' as source
                         from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
                         left join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
                         cppm on cppm.bestellnum = eqd.equipment_id
                         where (sos.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[1].")
                            and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). "
                            and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ")
                                 group by eqd.equipment_id, artikelname ".
                    " UNION ALL ".

                    "select m.refno, date(m.chrge_dte) as serv_dt, time(m.chrge_dte) as serv_tm, md.service_code, ms.name as service_desc, '' as group_code, ".
                    "      'Others' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source ".
                    "   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
                    "      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and md.request_flag is null AND m.is_cash = 0".
                    $cond_misc.
                    "   group by  md.service_code, ms.name, service_desc order by service_desc";

        //echo $this->sql;
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }

    }


    function getMedsList()
    {
        global $db;

        $toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        /*if ($this->old_bill_nr != '' && $this->is_final) {
            $cond_pharma = " AND pd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='PH' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
            $cond_order = " AND mpd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='OR' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
        }else{*/
            $cond_pharma = " AND (ph.orderdate BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
            $cond_order =  "AND (mph.chrge_dte BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
        //}

        if ($prev_encounter != '') $filter1 = " OR ph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter2 = " OR mph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounterr != '') $filter3 = " OR encounter_nr = ".$db->qstr($prev_encounter);

        $this->sql = "SELECT serv_dt, refno, bestellnum, unused_flag, artikelname, MAX(flag) AS flag, SUM(qty) AS qty,\n".
                "(SUM(price * qty)/SUM(qty)) AS price,\n".
                "SUM(itemcharge) AS itemcharge, source\n".
            "FROM (\n".

            "SELECT DATE(pd.serve_dt) serv_dt, pd.refno, 0 AS flag, 'Pharma' AS source, pd.bestellnum,\n".
                "pd.is_unused as unused_flag, pd.unused_qty,\n".
                "CONCAT(artikelname,'----',generic)  AS artikelname,\n".
                "SUM(pd.quantity - IFNULL(spri.quantity, 0)) AS qty,\n".
                "(SUM(pricecharge * (pd.quantity - IFNULL(spri.quantity, 0)))/SUM(pd.quantity - IFNULL(spri.quantity, 0))) AS price,\n".
                "SUM((pd.quantity - IFNULL(spri.quantity, 0)) * pricecharge) AS itemcharge\n".
            "FROM seg_pharma_order_items AS pd\n".
                "INNER JOIN seg_pharma_orders AS ph ON ph.refno = pd.refno\n".
                "INNER JOIN care_pharma_products_main AS p ON pd.bestellnum = p.bestellnum \n".
                "LEFT JOIN (SELECT rd.ref_no, 'Return' AS source, rd.bestellnum, SUM(quantity) AS quantity
            FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter3.")
            WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter3.") AND rd.ref_no = oh.refno)
            GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum\n".
            "WHERE\n".
                "pd.serve_status <> 'N' AND pd.request_flag IS NULL AND !ph.is_cash AND p.prod_class = 'M'\n".
                "AND (ph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter1.")\n".
                $cond_pharma.
                "AND (pd.quantity - IFNULL(spri.quantity, 0)) > 0 GROUP BY pd.`bestellnum`\n".

            "UNION ALL\n".

            "SELECT DATE(mph.chrge_dte) serv_dt, mpd.refno, 1 AS flag, 'Order' AS source, mpd.bestellnum,\n".
                " 0 as unused_flag, 0 as unused_qty,\n".
                "CONCAT(artikelname,'----',generic)  AS artikelname,\n".
                "SUM(quantity) AS qty,\n".
                "(SUM(unit_price * quantity)/SUM(quantity)) AS price,\n".
                "SUM(quantity * unit_price) AS itemcharge\n".
            "FROM seg_more_phorder AS mph\n".
                "INNER JOIN seg_more_phorder_details AS mpd ON mph.refno = mpd.refno\n".
                "INNER JOIN care_pharma_products_main AS p ON mpd.bestellnum = p.bestellnum AND p.prod_class = 'M'\n".
            "WHERE\n".
                "(mph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter2.")\n".


            " GROUP BY mpd.`bestellnum` ) AS t\n".
            "GROUP BY bestellnum, artikelname ORDER BY artikelname\n";


       /* if ($_SESSION['sess_temp_userid']=='medocs')
            echo $this->sql;*/

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function getProfFeesList() {
        global $db;

        $tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));
        $filter = array('','','');

        if ($this->prev_encounter_nr != '') $filter[0] = " or dm1.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[2] = " or encounter_nr = '$this->prev_encounter_nr'";

        $issurgical  = $this->isSurgicalCase();
        //added by jasper 09/03/2013 FOR BUG#305
        if ($this->isWellBaby()) {
            $amountlimit = DEFAULT_NBPKG_RATE;
        } else {
            $amountlimit = $this->pkgamountlimit;
        }
        //added by jasper 09/03/2013 FOR BUG#305

        $hc_pf = $this->getHouseCasePCF();
        $strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
                    "   sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
                    "   '' as opcodes, daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
                    "   0 as is_excluded, role_nr, role_area, 0 as role_type_level, 0 as rvu, 0 as multiplier, tbl_source ".
                    "   from ".
                    "      (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
                    "          subdate((select attend_start ".
                    "                      from seg_encounter_dr_mgt as dm2 ".
                    "                      where dm2.encounter_nr = dm1.encounter_nr and ".
                    "                            dm2.att_hist_no > dm1.att_hist_no ".
                    "                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, fn_getdailyrate('{$this->current_enr}', date('{$this->charged_date}'), tier_nr, {$this->confinetype_id}, attending_dr_nr) as daily_rate, cpa.role_nr, fn_getDailyVisitRoleArea(tier_nr) as role_area, discharge_date, 'others' AS tbl_source".
                    "          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
                    "             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
                    "             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
                    "             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
                    "             on dm1.encounter_nr = ce.encounter_nr ".
                    "          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
                    "             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "') " .
                    "          order by att_hist_no) as t ".
                    "   group by attending_dr_nr, role_area".
                    " union all ".
                    "select distinct spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, spd.days_attended as num_days, 0 as daily_rate, ".
                    "      GROUP_CONCAT(DISTINCT CONCAT(socd.ops_code, '-', IFNULL(socd.rvu,0), '(', socd.ops_entryno, ')') SEPARATOR ';') AS opcodes,".
                    " dr_charge, is_excluded, ".
                    "      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, SUM(ifnull(socd.rvu,0)) as tot_rvu, SUM(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->charged_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * ifnull(socd.rvu,0))/SUM(ifnull(socd.rvu,0)) as avg_multiplier, 'others' AS tbl_source ".
                    "   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
                    "      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
                    "      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
                    "      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
                    "      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
                    "   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
                    "      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "') ".
                    "   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
                    " union all ".
                    "select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role, null as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(sosd.ops_code,'-',IFNULL(sosd.rvu,0)) SEPARATOR ';') AS opcodes, ".
                    "      (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(sosd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->charged_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$this->charged_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$this->charged_date."'))) END) + ops_charge as dr_charge, 0 as is_excluded, ".
                    "      sop.role_type_nr, role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), sum(sosd.rvu) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->charged_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier, 'others' AS tbl_source ".
                    "   from (((seg_ops_personell as sop inner join (care_personell as cpn ".
                    "      inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
                    "      inner join (seg_ops_serv as sos inner join
                            (SELECT sd.refno, ops_code, rvu, multiplier, group_code
                                FROM seg_ops_servdetails AS sd INNER JOIN seg_ops_serv AS sh
                                    ON sd.refno = sh.refno
                                WHERE sh.encounter_nr = '$this->current_enr'
                                    HAVING (rvu = (SELECT MAX(rvu) AS rvumax
                                                    FROM seg_ops_servdetails AS d
                                                    WHERE d.refno = sd.refno AND d.group_code = sd.group_code)
                                         AND sd.group_code <> '') OR sd.group_code = '') as sosd ".
                    "         on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
                    "      inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
                    "      inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
                    "   where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
                    "      and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "         and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "') " .
                    "      and role_area is not null and crp.role not like '%_asst%' " .
                    "      and sosd.ops_code = sop.ops_code ".
                    "   group by dr_nr, role_area, role_type_nr ".
                    "UNION ALL
                                SELECT
                                      sedd.dr_nr,
                                      cp.name_last,
                                      cp.name_first,
                                      cp.name_middle,
                                      'Attending Doctor' AS role,
                                      1 AS num_days,
                                      sedd.dr_charge AS daily_rate,
                                      '' AS opcodes,
                                      sedd.`dr_charge` AS dr_charge,
                                      0 AS is_excluded,
                                      cpa.role_nr,
                                      'D1' AS role_area,
                                      0 AS role_type_level,
                                      0 AS rvu,
                                      0 AS multiplier,
                                      'seda' AS tbl_source   
                                FROM seg_encounter_dr_add sedd
                                 LEFT JOIN care_personell cpl 
                                    ON cpl.`nr` = sedd.`dr_nr` 
                                  LEFT JOIN care_person cp 
                                    ON cpl.pid = cp.pid 
                                  LEFT JOIN care_personell_assignment AS cpa 
                                    ON cpl.nr = cpa.personell_nr
                                WHERE encounter_nr = '".$this->current_enr
                                 ."' AND sedd.is_admitting <> '1' GROUP BY dr_nr";

#echo $strSQL;
        if ($result = $db->Execute($strSQL)) {
            $this->proffees_list = array();

            if ($result->RecordCount()) {
        $bhasD4 = false;
        $d3indx = -1;
        $indx = 0;
                while ($row = $result->FetchRow()) {
                    $objpf = new ProfFee;

          if ($row['role_area'] == 'D4') $bhasD4 = true;
          if ($row['role_area'] == 'D3' && !$row['is_excluded']) $d3indx = $indx;

                    $objpf->setDrNr($row['dr_nr']);
                    $objpf->setDrLast($row['name_last']);
                    $objpf->setDrFirst($row['name_first']);
                    $objpf->setDrMid((is_null($row['name_middle'])) ? '' : $row['name_middle']);
                    $objpf->setRoleNo($row['role_nr']);
                    $objpf->setRoleDesc($row['role']);
                    $objpf->setRoleBenefit($row['role_area']);
                    $objpf->setRoleLevel($row['role_type_level']);
                    $objpf->setDaysAttended($row['num_days']);
                    $objpf->setDrDailyRate($row['daily_rate']);
                    $objpf->setDrCharge($row['dr_charge']);
                    $objpf->setRVU($row['rvu']);
                    $objpf->setMultiplier($row['multiplier']);
                    $objpf->setChrgForCoverage((($row['is_excluded'] != 0) ? 0 : $row['dr_charge']));
                    $objpf->setIsExcludedFlag(($row['is_excluded'] != 0));
                    $objpf->setOpCodes($row['opcodes']);
                    $objpf->setTblSource($row['tbl_source']); //added by mai 08-26-2014

                    //added by jasper 09/01/2013 - FOR BUG#302 SURGEON'S PF IS NOT DISCOUNTABLE
                    //FOR PATIENTS WITHOUT PHIC IN OBANNEX
                    $opcodes = $row['opcodes'];
                    if ($opcodes != '') {
                        $opcodes = explode(";", $opcodes);
                        if (is_array($opcodes)) {
                        foreach($opcodes as $v) {
                            $i = strpos($v, '-');
                            if (!($i === false)) {
                            $code = substr($v, 0, $i);
                            if ($row['role_area'] == 'D3' && $this->findOPcodeNormalDelivery($code) && !$this->isPHIC() && $this->isOBAnnex()) {
                               $this->nonDiscountablePF += $row['dr_charge'];
                            }
                            }
                        }
                        } else {
                        $i = strpos($opcodes, '-');
                        if (!($i === false)) {
                            $code = substr($opcodes, 0, $i);
                            if ($row['role_area'] == 'D3' && $this->findOPcodeNormalDelivery($code) && !$this->isPHIC() && $this->isOBAnnex()) {
                               $this->nonDiscountablePF += $row['dr_charge'];
                            }
                        }
                        }
                    }
                    //added by jasper 09/01/2013 - FOR BUG#302
                    // Add new Service object in collection (array) of doctors' fees charged in this billing.
                    $this->proffees_list[] = $objpf;

          $indx++;
                }

        //Commented By Jarel set the Actual Charge from UI
        /*if (!$bhasD4 && ($d3indx != -1)) {
          $this->proffees_list[$d3indx]->setDrCharge( $this->proffees_list[$d3indx]->getDrCharge() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
        }*/
            }
        }
    }

    function isOBAnnex() {
        if ($this->accomm_typ_desc == '') {
            $this->getAccommodationType();
        }
        return (!(strpos(strtoupper($this->accomm_ward_name), OBANNEX, 0) === false));
    }

    function findOPcodeNormalDelivery($op_code) {
    global $db;

    $strSQL = "SELECT COUNT(ops_code) AS cnt FROM seg_ops_normaldelivery WHERE ops_code = '" . $op_code . "'";
    if ($result = $db->Execute($strSQL)) {
        if ($result->RecordCount()) {
            $row = $result->FetchRow();
            if ($row['cnt'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
        return false;
        }
    }
    }

    function isPHIC() {
        global $db;

        $ncount = 0;
        $filter .= (($filter != "") ? "," : "(")."'{$this->current_enr}')";
        $strSQL = "SELECT ".
                  "     COUNT(*) isphic ".
                  "   FROM seg_encounter_insurance ".
                  "   WHERE encounter_nr IN {$filter} ".
                  "      AND hcare_id = ".PHIC_ID.
                  "   ORDER BY priority LIMIT 1";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $ncount = $row['isphic'];
            }
        }
        return ($ncount > 0);
    }

    function getProfFeesBenefits() {
        global $db;

        $tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));
        $bill_date = $this->charged_date;
        $filter = array('','','');

        $issurgical  = $this->isSurgicalCase();

        if ($this->isWellBaby()) {
            $amountlimit = DEFAULT_NBPKG_RATE;
        } else {
            $amountlimit = $this->pkgamountlimit;
        }
        //added by jasper 09/03/2013 FOR BUG#305

        $hc_pf = $this->getHouseCasePCF();

        if ($this->prev_encounter_nr != '') $filter[0] = " or dm1.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[2] = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select dr_nr, role_area, role_type_level, opcodes, sum(num_days) as totaldays, sum(rvu) as totalrvu, (sum(multiplier * rvu)/sum(rvu)) as avgmuliplier, sum(dr_charge) as totalcharge, ".
                    "\n     sum(case when is_excluded <> 0 then 0 else dr_charge end) as chrg_for_coverage, tbl_source ".
                    "\n  from ".
                    "\n  (select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
                    "\n   sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
                    "\n   '' as opcodes, daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
                    "\n   0 as is_excluded, role_nr, role_area, 0 as role_type_level, 0 as rvu, 0 as multiplier, tbl_source  ".
                    "\n   from ".
                    "\n      (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
                    "\n          subdate((select attend_start ".
                    "\n                      from seg_encounter_dr_mgt as dm2 ".
                    "\n                      where dm2.encounter_nr = dm1.encounter_nr and ".
                    "\n                            dm2.att_hist_no > dm1.att_hist_no ".
                    "\n                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, fn_getdailyrate('{$this->current_enr}', date('{$this->bill_dte}'), tier_nr, {$this->confinetype_id}, attending_dr_nr) as daily_rate, cpa.role_nr, fn_getDailyVisitRoleArea(tier_nr) as role_area, discharge_date, 'others' AS tbl_source ".
                    "\n          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
                    "\n             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
                    "\n             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
                    "\n             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
                    "\n             on dm1.encounter_nr = ce.encounter_nr ".
                    "\n          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
                    "\n             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "\n                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') " .
                    "\n          order by att_hist_no) as t ".
                    "\n   group by attending_dr_nr, role_area, role_nr ".
                    "\n union ".
                    "\nselect distinct spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, (case when is_excluded <> 0 then 0 else spd.days_attended end) as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(socd.ops_code, '-', IFNULL(socd.rvu,0)) SEPARATOR ';') AS opcodes, ".
                    "\n      (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(socd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(is_excluded OR ".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$bill_date."'))) END) + dr_charge as dr_charge, is_excluded, ".
                    "\n      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier, 'others' as tbl_source ".
                    "\n   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
                    "\n      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
                    "\n      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
                    "\n      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
                    "\n      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
                    "\n   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
                    "\n      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "\n      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
                    "\n   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
                    "\n union ".
                    "\n   select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role, null as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(sosd.ops_code,'-',IFNULL(sosd.rvu,0)) SEPARATOR ';') AS opcodes, ".
                    "\n         (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(sosd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$bill_date."'))) END) + ops_charge as dr_charge, ".
                    "\n         0 as is_excluded, sop.role_type_nr, role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), sum(sosd.rvu) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier, 'others' as tbl_source ".
                    "\n      from (((seg_ops_personell as sop inner join (care_personell as cpn ".
                    "\n         inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
                    "\n         inner join (seg_ops_serv as sos inner join
                                (SELECT sd.refno, ops_code, rvu, multiplier, group_code
                                FROM seg_ops_servdetails AS sd INNER JOIN seg_ops_serv AS sh
                                    ON sd.refno = sh.refno
                                WHERE sh.encounter_nr = '$this->current_enr'
                                    HAVING (rvu = (SELECT MAX(rvu) AS rvumax
                                                    FROM seg_ops_servdetails AS d
                                                    WHERE d.refno = sd.refno AND d.group_code = sd.group_code)
                                         AND sd.group_code <> '') OR sd.group_code = '') as sosd ".
                    "\n            on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
                    "\n         inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
                    "\n         inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
                    "\n      where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
                    "\n         and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "\n            and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') " .
                    "\n         and role_area is not null and crp.role not like '%_asst%' " .
                    "\n               and sosd.ops_code = sop.ops_code ".
                    "\n      group by dr_nr, role_area, role_type_nr) ".
                    "\n as o group by role_area, dr_nr ".
"UNION ALL
                        SELECT 
                          sedd.dr_nr,
                          'D1' AS role_area,
                          0 AS role_type_level,
                          '' AS opcodes,
                          1 AS totaldays,
                          0 AS totalrvu,
                          NULL AS avgmultiplier,
                          sedd.dr_charge AS totalcharge,
                          sedd.dr_charge AS chrg_for_coverage,
                          'seda' as tbl_source  
                        FROM seg_encounter_dr_add sedd
                        WHERE sedd.`encounter_nr` = '".$this->current_enr ."'
                        AND sedd.is_admitting <> '1'
                        GROUP BY dr_nr ORDER BY role_area";

        if ($result = $db->Execute($strSQL)) {
            $this->hsp_pfs_benefits = array();
            $this->pfs_confine_coverage = array();

            if ($result->RecordCount()) {
                $bhasD4 = false;
                $d3indx = -1;
                $indx = 0;

                while ($row = $result->FetchRow()) {
                    $objpfc = new ProfFeeCoverage;

                    if ($row['role_area'] == 'D4') $bhasD4 = true;
                    if ($row['role_area'] == 'D3' && ($row['chrg_for_coverage'] > 0)) $d3indx = $indx;

                    $objpfc->setDrNr($row['dr_nr']);
                    $objpfc->setRoleBenefit($row['role_area']);
                    $objpfc->setRoleLevel((is_null($row['role_type_level']) ? 0 : $row['role_type_level']));
                    $objpfc->getRoleDesc();
                    if (is_null($row['totaldays']))
                        $objpfc->setDaysAttended(0);
                    else
                        $objpfc->setDaysAttended($row['totaldays']);

                    $objpfc->setDrCharge($row['totalcharge']);
                    $objpfc->setRVU($row['totalrvu']);
                    $objpfc->setMultiplier($row['avgmuliplier']);
                    $objpfc->setChrgForCoverage($row["chrg_for_coverage"]);
                    $objpfc->setOpCodes($row['opcodes']);
                    $objpfc->setTblSource($row['tbl_source']); //added by mai 08-26-2014

                    // Add new object in collection (array) of doctors' fees charged in this billing.
                    $this->hsp_pfs_benefits[] = $objpfc;

                    $indx++;
                }

                if (!$bhasD4 && ($d3indx != -1)) {
                    $this->hsp_pfs_benefits[$d3indx]->setDrCharge( $this->hsp_pfs_benefits[$d3indx]->getDrCharge() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
                    $this->hsp_pfs_benefits[$d3indx]->setChrgForCoverage( $this->hsp_pfs_benefits[$d3indx]->getChrgForCoverage() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
                }
            }
        }

        // $this->hsp_pfs_benefits = $strSQL;
        // return $strSQL;


    }

    function isWellBaby() {
        global $db;

        $enc_type = 0;
        $strSQL = "select encounter_type ".
                            "   from care_encounter ".
                            "   where encounter_nr = ".$db->qstr($this->current_enr);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == WELLBABY);
    }

    function isSurgicalCase() {
        global $db;

        $flag = 0;
        $strSQL = "select count(*) as is_surgical
                        from
                    (select 1 as tr_id, os.refno
                        from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
                        where (encounter_nr = '". $this->current_enr. "') and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
                     union
                     select 2 as tr_id, mo.refno
                        from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
                        where (encounter_nr = '". $this->current_enr. "')) as t";

        $row = $db->GetRow($strSQL);
        $flag = (is_null($row['is_surgical'])) ? 0 : $row['is_surgical'];

        return ($flag != 0);
    }

    function isCharity() {
        if ($this->accomm_typ_desc == '') {
            $this->getAccommodationType();
        }
        return (!(strpos(strtoupper($this->accomm_typ_desc), CHARITY, 0) === false));
    }

    function getAccommodationType() {
        global $db;

        $ntype = 0;
        $sname = '';
        $filter = array('','');

        if ($this->prev_encounter_nr != '') {
            $filter[0] = " or cel.encounter_nr = '$this->prev_encounter_nr'";
            $filter[1] = " or sel.encounter_nr = '$this->prev_encounter_nr'";
        }

            $strSQL = "select 0 AS entry_no,
                  STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                  cw.accomodation_type, accomodation_name, cw.name AS ward_name ".
                    "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                    "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
                    "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
                    "   where (cel.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
                    "      and exists (select nr ".
                    "                     from care_type_location as ctl ".
                    "                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
                    "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
                    "         or ".
                    "      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
                    "      or ".
                    "      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
          " UNION ALL
            SELECT entry_no, occupy_date, cw.accomodation_type, accomodation_name, cw.name AS ward_name
              FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
                INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
            WHERE (sel.encounter_nr = '". $this->current_enr. "'".$filter[1].")
              AND (
                STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) >= '" . $this->bill_frmdte . "'
                AND STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) < '" . $this->bill_dte . "'
              )
            ORDER BY entry_no DESC LIMIT 1";

        $this->debugSQL = $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $ntype = $row['accomodation_type'];
                    $sname = $row['accomodation_name'];
                    $ward_name = $row['ward_name'];
                }
            }
        }

        $this->accomm_typ_nr = $ntype;
        $this->accomm_typ_desc = $sname;
        $this->accomm_ward_name = $ward_name;

        return($db->ErrorMsg() == '');

    }

    function getPFBenefits() {
        return($this->hsp_pfs_benefits);
    }

    function getIsCoveredByPkg() {
        return($this->is_coveredbypkg);
    }

    function getCurrentEncounterNr() {
        return($this->current_enr);
    }

    function initProfFeesCoverage($pfarea) {
        $this->pfs_confine_coverage[$pfarea] = 0.00;
        $this->pfs_confine_benefits[$pfarea] = array();
    }

    function getTotalPFCharge($pfarea = '') {
        // Compute total doctors' fees ...
        $npf      = 0;
        $ndays    = 0;
        $nrvu     = 0;
        $total_df = 0;

        // .... D1 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D1');
        $total_df += $npf;
        if ($pfarea == 'D1') return $npf;

        // .... D2 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D2');
        $total_df += $npf;
        if ($pfarea == 'D2') return $npf;

        // .... D3 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D3');
        $total_df += $npf;
        if ($pfarea == 'D3') return $npf;

        // .... D4 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D4');
        $total_df += $npf;
        if ($pfarea == 'D4') return $npf;

        $this->total_pf_charge = $total_df;
        return($total_df);
    }

    function delEncDoctors($enc){
        global $db;

        $this->sql_mgt = "DELETE FROM seg_encounter_dr_mgt
                                WHERE encounter_nr = ".$db->qstr($enc);
        $del = $db->Execute($this->sql_mgt);

        $this->sql_prv = "DELETE FROM seg_encounter_privy_dr
                                WHERE encounter_nr = ".$db->qstr($enc);

        $bSuccess = $db->Execute($this->sql_prv);

        return $bSuccess;

    }

    function getMiscList()
    {
        global $db;
        $filter = '';

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        if ($prev_encounterr != '') $filter = " OR encounter_nr = ".$db->qstr($prev_encounter);

        $this->sql = "select mcd.service_code, sos.name, sos.description, mcd.refno, sum(mcd.quantity) as qty, (sum(quantity * chrg_amnt)/sum(mcd.quantity)) as avg_chrg, ".
                    "      sum(quantity * chrg_amnt) as total_chrg ".
                    "   from (seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on ".
                    "      mc.refno = mcd.refno) inner join seg_other_services as sos on ".
                    "      mcd.service_code = sos.service_code ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter.") ".
                    "   group by mcd.service_code, sos.name ".
                    "   order by sos.name";

        //echo $this->sql;
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }

    }

    function hasSavedBill($enc)
    {
        global $db;

        $this->sql = "SELECT * FROM seg_billing_encounter ".
                     "WHERE encounter_nr=".$db->qstr($enc).
                     " AND (is_deleted=0 OR is_deleted IS NULL) ".
                     " ORDER BY bill_dte DESC LIMIT 1 ";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }


    function caseRateInfo($code) {
        global $db;

        $this->sql = "SELECT * FROM seg_case_rate_packages WHERE code=".$db->qstr($code);

        if ($buf=$db->Execute($this->sql)){
                if($buf->RecordCount()) {
                    return $buf;
                }else { return FALSE; }
            }else { return FALSE; }

    }

    function setDeathData($data){
        global $db;
        if($data['enc']=='')
            $data['enc'] = "0";
        if($data['deathdate']=='')
            $data['deathdate'] = "0000-00-00 00:00:00";
        $db->BeginTrans();

        $this->sql = "UPDATE care_person SET
                        death_date = DATE_FORMAT('".$data['deathdate']."', '%Y-%m-%d'),
                        death_time = DATE_FORMAT('".$data['deathdate']."', '%H:%i:%s'),
                        history = CONCAT(history, 'Update: ', NOW(), ' ".$data['userid']."\\n'),
                        modify_id = '".$data['userid']."',
                        modify_time = NOW(),
                        death_encounter_nr = '".$data['enc']."'
                        WHERE pid = '".$data['pid']."'";
        $success1 = $db->Execute($this->sql);

        if($success){
            $fldarray = array('encounter_nr' => $db->qstr($data['enc']),
                        'result_code' => '4',
                        'modify_id' => $db->qstr($data['userid']),
                        'modify_time' => 'NOW()',
                        'create_id' => $db->qstr($data['userid']),
                        'create_time' => 'NOW()');
            $success2 = $db->Replace('seg_encounter_result', $fldarray, array('encounter_nr'));
        }

        if(!$success1 || $success2){
            $db->RollbackTrans();
            $objResponse->alert($db->ErrorMsg());
        }
        else{
            $db->CommitTrans();
        }
    }
    function getPrincipalPIDofHCare($s_pid, $nhcareid) {
        global $db;

        $sprincipal_pid = "";

        $strSQL = "select pid ".
                    "   from care_person_insurance ".
                    "      where pid = '". $s_pid ."' and hcare_id = '". $nhcareid ."' ".
                    "      and is_principal <> 0 and is_void = 0";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $sprincipal_pid = $row['pid'];
            }
        }

        return $sprincipal_pid;
    }
    //added by kenneth 12/13/2013
    function saveMiscServices($data){
        global $db;
        $refno = $this->getMiscSrvcRefNo($data['bill_frmdte'],$data['enc_nr']);
        if($refno == ''){
            $this->sql = "INSERT INTO seg_misc_service (chrge_dte, encounter_nr, modify_id, create_id, create_dt)
                                VALUES (".$db->qstr($data['bill_dt']).", ".$db->qstr($data['enc_nr']).", ".$db->qstr($data['sess_user_name']).", 
                                        ".$db->qstr($data['sess_user_name']).", ".$db->qstr($data['bill_dt']).")";
            if($res=$db->Execute($this->sql))
                return true;
            else
                return false;
        }
        else
            return true;
    }
    function saveMiscServicesDetails($data){
        global $db;
        $refno = $this->getMiscSrvcRefNo($data['bill_frmdte'],$data['enc_nr']);
        $this->sql = "INSERT INTO seg_misc_service_details (refno, service_code, account_type, chrg_amnt, quantity)
                            VALUES (".$db->qstr($refno).", ".$db->qstr($data['code']).",".$db->qstr($data['acct_type']).", ".$db->qstr($data['msc_charge']).",".$db->qstr($data['qty']).")";
        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;
    }
    function savePharmaSupply($data){
        global $db;
        $refno = $this->getPharmaChrgRefNo($data['bill_frmdte'],$data['enc_nr']);
        if($refno == ''){
            $this->sql = "INSERT INTO seg_more_phorder (chrge_dte, encounter_nr, area_code, modify_id, create_id, create_dt)
                                VALUES ('".$data['bill_dt']."', '".$data['enc_nr']."', '".$data['area_code']."', '".$data['sess_user_name']."', '".$data['sess_user_name']."', '".$data['bill_dt']."')";
            if($res=$db->Execute($this->sql))
                return true;
            else
                return false;
        }
        else
            return true;
    }
    function savePharmaSupplyDetails($data){
        global $db;
        $refno = $this->getPharmaChrgRefNo($data['bill_frmdte'],$data['enc_nr']);
        $this->sql = "INSERT INTO seg_more_phorder_details (refno, bestellnum, quantity, unit_price)
                            VALUES ('".$refno."', '".$data['code']."', '".$data['qty']."', '".$data['msc_charge']."')";
        if($res=$db->Execute($this->sql))
            return $refno;
        else
            return false;
    }

    /**
     * Get Entry Number for Pharma Supply Details
     * @param $refno
     * @param $itemCode
     */
    function getPharmaSupplyEntryNo($refno, $itemCode) {
        global $db;
        $this->sql = 'SELECT fn_getnext_more_phorder_entryno(?,?) as entry_no';
        $this->result = $db->Execute($this->sql, array($refno, $itemCode));
        if($this->result) {
            $row = $this->result->FetchRow();
            return intval($row['entry_no']);
        }
        return 0;
    }

    /**
     * edited by Marc Lua 7/22/2014
     * removed mysql lock functionality. Used transaction instead.
     * added inventory stock in for the cancellation of meds/supplies
     * @param $data
     * @return bool
     */
    function deletePharmaSupply($data){
        global $db;
//        $db->debug = true;
        $itemCode = $data['serv_code'];
        $this->sql = "SELECT smpd.refno,
                          smpd.bestellnum,
                          smpd.quantity,
                          smpd.entry_no,
                          smpd.unit_price,
                          smp.encounter_nr,
                          smp.area_code,
                          smp.chrge_dte FROM seg_more_phorder AS smp
                        INNER JOIN seg_more_phorder_details smpd ON smp.refno = smpd.refno
                        WHERE smpd.bestellnum = '".$itemCode."'
                        AND smp.encounter_nr = '".$data['encounter_nr']."' 
                        ORDER BY entry_no desc limit 1";

        $rs = $db->Execute($this->sql);

        if($rs){
            if($row = $rs->FetchRow()){
                $areaCode = $row['area_code'];
                $refno = $row['refno'];
                $entryno = $row['entry_no'];
                $qty = intval($row['quantity']);
                $this->sql = "DELETE FROM seg_more_phorder_details
                                WHERE bestellnum = '".$itemCode."'
                                AND entry_no = '".$entryno."'
                                AND refno = '".$refno."'";
                $db->startTrans();
//                $db->debug = true;
                try {
                $success = $db->Execute($this->sql);
                if($success){
                    $dcount = 0;
                    $this->sql = "SELECT count(*) dcount FROM seg_more_phorder_details
                                WHERE refno = '".$refno."'";
                    $rs = $db->Execute($this->sql);
                    if($rs){
                        $row = $rs->FetchRow();
                        if($row){
                            $dcount = is_null($row['dcount']) ? 0 : $row['dcount'];
                        }
                        if($dcount == 0){
                            $this->sql = "DELETE FROM seg_more_phorder
                                    WHERE refno = '".$refno."'";
                                $db->Execute($this->sql);
                            }
                        }
                        $db->completeTrans();
                    }

                    if(!$success) {
                        $db->FailTrans();
                    }
                } catch(Exception $e) {
                    $db->FailTrans();
                        }
                    }
            return array(
                'refNo' => $refno .'-'.$entryno,
                'itemCode' => $data['serv_code'],
                'qty' => $qty,
                'areaCode' => $areaCode,
            );
        }
            return false;
    }
function deleteMiscServices($data){
        global $db;
        $obj_curl = new Rest_Curl();

        $this->sql = "SELECT * FROM seg_misc_service_details AS smsd
                WHERE service_code = ".$db->qstr($data['serv_code'])."
                AND EXISTS (SELECT * FROM seg_misc_service AS sms
                    WHERE sms.refno = smsd.refno AND !is_cash
                    AND sms.encounter_nr = ".$db->qstr($data['encounter_nr'])."
                    AND sms.chrge_dte >= ".$db->qstr($data['bill_frmdte']).")
                    AND get_lock('sms_lock', 10)
                    ORDER BY entry_no desc limit 1 FOR update";
        $rs = $db->Execute($this->sql);
        if($rs){
            if($row = $rs->FetchRow()){
                $refno = $row['refno'];
                $entryno = $row['entry_no'];
                $this->sql = "DELETE FROM seg_misc_service_details
                                WHERE service_code = ".$db->qstr($data['serv_code'])."
                                AND entry_no = ".$db->qstr($entryno)."
                                AND refno = ".$db->qstr($refno)."";
                $success = $db->Execute($this->sql);
                $sql = "SELECT RELEASE_LOCK('sms_lock')";
                $db->Execute($sql);
                if($success){
                    $dcount = 0;

                    $obj_curl->inpatientMiscRequest($refno); //delete in fis miscellaneous request

                    $this->sql = "SELECT count(*) dcount FROM seg_misc_service_details
                                WHERE refno = ".$db->qstr($refno)."";
                    $rs = $db->Execute($this->sql);
                    if($rs){
                        $row = $rs->FetchRow();
                        if($row){
                            $dcount = is_null($row['dcount']) ? 0 : $row['dcount'];
                        }
                        if($dcount == 0){
                            $this->sql = "DELETE FROM seg_misc_service
                                    WHERE refno = ".$db->qstr($refno)."";
                           return $db->Execute($this->sql);
                        }
                    }
                }else
                   return $msg = $db->ErrorMsg();
            }return true;
        }
        else
            return false;
    }
    function deleteMiscCharge($data){
        global $db;

        $this->sql = "SELECT * FROM seg_misc_chrg_details AS smcd
           WHERE service_code = ".$db->qstr($data['code'])."
                        AND EXISTS (SELECT * FROM seg_misc_chrg AS smc
                        WHERE smc.refno = smcd.refno
                        AND smc.encounter_nr = ".$db->qstr($data['encounter_nr'])."
                        AND smc.chrge_dte >= ".$db->qstr($data['bill_frmdte']).")
                        AND get_lock('smp_lock', 10)
                        ORDER BY entry_no desc limit 1 FOR update";
        $rs = $db->Execute($this->sql);
        if($rs){
            if($row = $rs->FetchRow()){
                $refno = $row['refno'];
                $entryno = $row['entry_no'];
                $this->sql = "DELETE FROM seg_misc_chrg_details
                                WHERE service_code = ".$db->qstr($data['code'])."
                                AND refno = '".$refno."'";
                $success = $db->Execute($this->sql);
                $sql = "SELECT RELEASE_LOCK('sms_lock')";
                $db->Execute($sql);
                if($success){
                    $dcount = 0;
                    $this->sql = "SELECT count(*) dcount FROM seg_misc_chrg_details
                                WHERE refno = '".$refno."'";
                    $rs = $db->Execute($this->sql);
                    if($rs){
                        $row = $rs->FetchRow();
                       if($dcount == 0){
                            $this->sql = "DELETE FROM seg_misc_service
                                    WHERE refno = '".$refno."'";
                           return $db->Execute($this->sql);
                        }
                    }
                }else
                   return $msg = $db->ErrorMsg();
            }return true;
        }
        else
            return false;
    }

    function getMiscSrvcRefNo($bill_frmdte, $enc_nr) {
        global $db;

        $srefno = '';
        # Fix for MS-535 by Bong
        $strSQL = "select refno ".
                            "   from seg_misc_service ".
                            "   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= ".$db->qstr($bill_frmdte)." ".
                            "      and encounter_nr = ".$db->qstr($enc_nr)." ".
                            "      and !is_cash ".
                            "   order by chrge_dte limit 1";

        if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                        while ($row = $result->FetchRow())
                                $srefno = $row['refno'];
                }
        }
        return($srefno);
    }

    function saveMiscCharge($data_misc)
    {
         global $db;
        $refno = $this->getMiscChrgRefNo($data_misc['bill_frmdte'],$data_misc['enc_nr']);
        $this->sql = "INSERT INTO seg_misc_chrg_details (refno, service_code, account_type, quantity, chrg_amnt)
                            VALUES ('".$refno."', ".$db->qstr($data_misc['code']).",".$db->qstr($data_misc['acct_type']).", ".$db->qstr($data_misc['qty']).", ".$db->qstr($data_misc['msc_charge']).")";
        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;
    }

      function CreateMiscCharge($data)
    {
        global $db;
        $refno = $this->getMiscChrgRefNo($data['bill_frmdte'],$data['enc_nr']);

        if($refno == ''){
            $this->sql = "INSERT INTO seg_misc_chrg(chrge_dte, encounter_nr, modify_id, create_id, create_dt)
                                VALUES (".$db->qstr($data['bill_dt']).", ".$db->qstr($data['enc_nr']).", ".$db->qstr($data['sess_user_name']).", ".$db->qstr($data['sess_user_name']).", ".$db->qstr($data['bill_dt']).")";

            if($res=$db->Execute($this->sql))
                return true;
            else
                return false;
        }
        else
            return true;
    }

    function getMiscChrgRefNo($bill_frmdte, $enc_nr) {
        global $db;

        $srefno = '';
        # Fix for MS-535 by Bong
        $strSQL = "select refno ".
                            "   from seg_misc_chrg ".
                            "   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= ".$db->qstr($bill_frmdte)." ".
                            "      and encounter_nr = '".$enc_nr."' ".
                            "      and !is_cash ".
                            "   order by chrge_dte limit 1";

        if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                        while ($row = $result->FetchRow())
                                $srefno = $row['refno'];
                }
        }
        return($srefno);
    }

    function getPharmaChrgRefNo($bill_frmdte, $enc_nr) {
        global $db;

        $srefno = '';
        $strSQL = "select refno ".
                            "   from seg_more_phorder ".
                            "   where encounter_nr = '".$enc_nr."' ".
                            "   order by chrge_dte limit 1";

        if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                        while ($row = $result->FetchRow())
                                $srefno = $row['refno'];
                }
        }

        return($srefno);
}
    function getPharmaAreas(){
        global $db;

        $this->sql = "SELECT sa.* FROM seg_areas AS sa
                        INNER JOIN care_department AS cd ON sa.dept_nr = cd.nr
                        WHERE name_formal REGEXP '.*pharma.*|.*supply.*'
                        ORDER BY name_formal";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }
    //ended by kenneth

    function getHouseCasePCF(){
        global $db;

        $bhousecase = 0;
        $strSQL = "select fn_isHouseCaseAsOfRefDate('".$this->encounter_nr."', '".$this->bill_dte."') as casetype";
        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                     $bhousecase = is_null($row["casetype"]) ? 0 : $row["casetype"];
                }
            }
        }

        if ($bhousecase)
            return DEFAULT_PCF;
        else
            return 0;
    }


    function getAppliedHrsCutoff() {
            global $db;

            $n_cutoff = -1;

            $strSQL = "select applied_hrs_cutoff ".
                                "   from seg_billing_encounter ".
                                "   where bill_nr = ".$db->qstr($this->old_bill_nr)." and is_deleted IS NULL";
            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    $row = $result->FetchRow();
                    $n_cutoff = $row['applied_hrs_cutoff'];
                }
            }

            return($n_cutoff);
    }

    function correctBillDates() {
        global $db;

        if ($this->old_bill_nr != '') {
            $strSQL = "select bill_dte, bill_frmdte from seg_billing_encounter where bill_nr = ".$db->qstr($this->old_bill_nr)." and is_deleted IS NULL";
            if ($result=$db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    if ($row = $result->FetchRow()) {
                        $this->bill_frmdte = is_null($row["bill_frmdte"]) ? $this->bill_frmdte : $row["bill_frmdte"];
                        $this->bill_dte    = is_null($row["bill_dte"]) ? $this->bill_dte : $row["bill_dte"];
                    }
                }
            }
        }
    }



    function getCaseRatePkgLimit($sBillArea, $issurgical) {
        global $db;

        $sfield = "";
        $share = 0.00;
        if ($sBillArea == 'D3')
            $sfield = "dist_pfsurgeon share";
        elseif ($sBillArea == 'D4')
            $sfield = "dist_pfanesth share";
        elseif (in_array($sBillArea, array('D1','D2'))) {
            $sfield = "dist_pfdaily share";
        }
        else
            $sfield = "dist_hosp share";

        $strSQL = "SELECT $sfield
                    FROM seg_caseratepkgdist
                    WHERE effect_date <= DATE('".$this->bill_dte."')
                       AND case_type = '".(($issurgical) ? 'S' : 'M')."'
                    ORDER BY effect_date DESC LIMIT 1";
        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $share = (is_null($row['share'])) ? 0.00 :  $row['share'];
                }
            }
        }

        return $share;
    }

    function getTotalPFParams(&$n_days, &$n_rvu, &$n_pf, $role_area = '', $role_level = 0, $b_noexcluded = false, $drnr = '', $opcode = '') {
        $n_days = 0;
        $n_rvu = 0;
        $n_pf = 0;

        if (!empty($this->hsp_pfs_benefits) && is_array($this->hsp_pfs_benefits))
            foreach ($this->hsp_pfs_benefits as $objpf) {
                if ($objpf->getRoleBenefit() == $role_area) {
                    if ($role_level != 0) {
                        if ($role_level == $objpf->getRoleLevel()) {
                            if ($drnr != '') {
                                if ($drnr == $objpf->getDrNr()) {
                                    $n_days += $objpf->getDaysAttended();
                                    if ($opcode != '') {
                                        $opcodes = $objpf->getOpCodes();
                                        if ($opcodes != '') $opcodes = explode(";", $opcodes);
                                        if (is_array($opcodes)) {
                                            foreach($opcodes as $v) {
                                                $i = strpos($v, '-');
                                                if (!($i === false)) {
                                                    $code = substr($v, 0, $i);
                                                    if ($code == $opcode) {
                                                            $n = strpos($v, '(');
                                                            if (!($n === false))
                                                                 $n_rvu += substr($v, $i+1, $n-($i+1));
                                                            else
                                                                 $n_rvu += substr($v, $i+1);
                                                            break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    else
                                        $n_rvu  += $objpf->getRVU();
                                    $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                                }
                            }
                            else {
                                $n_days += $objpf->getDaysAttended();
                                $n_rvu  += $objpf->getRVU();
                                $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                            }
                        }
                    }
                    else {
                        if ($drnr != '') {
                            if ($drnr == $objpf->getDrNr()) {
                        $n_days += $objpf->getDaysAttended();
                                if ($opcode != '') {
                                    $opcodes = $objpf->getOpCodes();
                                    if ($opcodes != '') $opcodes = explode(";", $opcodes);
                                    if (is_array($opcodes)) {
                                        foreach($opcodes as $v) {
                                            $i = strpos($v, '-');
                                            if (!($i === false)) {
                                                $code = substr($v, 0, $i);
                                                if ($code == $opcode) {
                                                        $n = strpos($v, '(');
                                                        if (!($n === false))
                                                             $n_rvu += substr($v, $i+1, $n-($i+1));
                                                        else
                                                             $n_rvu += substr($v, $i+1);
                                                        break;
//                                                      $n_rvu += substr($v, $i+1);
//                                                      break;
}                                            }
                                        }
                                    }
                                }
                                else
                        $n_rvu  += $objpf->getRVU();
                        $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge() - $this->nonDiscountablePF;
                    }
                }
                        else {
                            $n_days += $objpf->getDaysAttended();
                            $n_rvu  += $objpf->getRVU();
                            $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                        }
//                      $n_days += $objpf->getDaysAttended();
//                      $n_rvu  += $objpf->getRVU();
//                      $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                    }
                }
            }
    }

    function getTotalOpCharge() {
        global $db;
        $ntotal = 0;
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select sum(op_charge) as tot_charge from " .
                    "(select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
                    "      concat('OR-', cast(oad.room_nr as char)) as ops_code, concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
                    "      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu, ".
                    "      (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
                    "   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
                    "      inner join care_room as cr on oad.room_nr = cr.nr ".
                    "   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
                    "      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->charged_date ."')) as t";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if (!is_null($row['tot_charge']))
                        $ntotal += $row['tot_charge'];
                }
            }
        }
        $this->total_op_charge = $ntotal;
        return($ntotal);
    }

    function initOpsConfineCoverage() {
        $this->ops_confine_benefits = array();
        $this->ops_confine_coverage = 0.00;
    }

    function getOpBenefits(){
        global $db;

        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select ops_code, opcode, description, provider, sum(rvu) as sum_rvu,
                                fn_getOPRvuRate('$this->current_enr', date('".$this->charged_date."'), sum(rvu), $this->confinetype_id) as op_multiplier,
        /* (sum(multiplier * rvu)/sum(rvu)) as op_multiplier ,*/ sum(op_charge) as tot_charge ".
                    "   from ".
                    "(select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
                    "      concat('OR-', cast(oad.room_nr as char)) as ops_code,
                                 fn_getopcode(oah.refno, oad.entry_no) AS opcode,
                                 concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
                    "      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu,
                                (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
                    "   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
                    "      inner join care_room as cr on oad.room_nr = cr.nr ".
                    "   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
                    "      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->charged_date ."')) as t ".
                    "group by provider, ops_code, description, opcode, entry_no
                     order by ops_code";    // modified by LST - 11.12.2011 --- Issue (from SOW 10-001)

        if ($result = $db->Execute($strSQL)) {
            $this->hsp_ops_benefits = array();

            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objOp = new PerOpCoverage;

                    $objOp->setBillDte($this->charged_date);
                    $objOp->setCurrentEncounterNr($this->current_enr);
                    $objOp->setPrevEncounterNr($this->prev_encounter_no);
                    $objOp->setOpCode($row['ops_code']);
                    $objOp->setOpCodePerformed($row['opcode']);
                    $objOp->setOpDesc($row['description']);
                    $objOp->setOpRVU($row['sum_rvu']);
                    $objOp->setOpMultiplier($row['op_multiplier']);
                    $objOp->setOpCharge($row['tot_charge']);
                    $objOp->setOpProvider($row['provider']);

                    $objOp->computeTotalCoverage($this->getBillAreaDRate('OR'));

                    // Add new medicine object in collection (array) of the list of medicines in this billing.
                    $this->hsp_ops_benefits[] = $objOp;
                }
            }
        }
    }

    function getBillAreaDRate($sbill_area) {
        global $db;

        $n_rate = 0;
        $n_prevrate = 0;

        $area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
        if (!($this->isCharity() && (in_array($sbill_area, $area_array)))) {
            // Get discount rate applicable to bill area of current encounter ...
            $strSQL = "select fn_get_bill_discount('". $this->current_enr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    $row = $result->FetchRow();
                    if (!is_null($row['discount'])) {
                        $n_rate = $row['discount'];
                    }
                }
            }

            // .... get discount rate applied to bill area of encounter while at ER, if there is one.
            if ($this->prev_encounter_nr != '') {
                $strSQL = "select fn_get_bill_discount('". $this->prev_encounter_nr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        $row = $result->FetchRow();
                        if (!is_null($row['discount'])) {
                            $n_prevrate = $row['discount'];
                        }
                    }
                }
            }

            $n_rate = ($n_rate > $n_prevrate ? $n_rate : $n_prevrate);      // Return the highest discount applied.
        }
        return($n_rate);
    }

    function saveAdditionalAccommodation($data){
        global $db;

        $this->sql = "INSERT INTO seg_encounter_location_addtl
                (encounter_nr,
                     room_nr,
                      group_nr,
                       days_stay,
                        hrs_stay,
                         rate,
                          occupy_date,
                           modify_id,
                            create_id,
                             create_dt) 
                   VALUES 
                   ('".$data['encounter_nr']."',
                        '".$data['room_nr']."',
                         '".$data['ward_nr']."',
                          '".$data['days']."',
                           '0',
                            '".$data['room_rate']."',
                             '".$data['createdate']."',
                              '".$data['sessionID']."',           
                               '".$data['sessionUN']."', 
                               '".$data['createdate']."')";
            if($this->result=$db->Execute($this->sql))
            {
                return $this->result;
            } else {
                return false;
            }
    }

    function deleteAccommodation($data){
        global $db;

        if($data['accom_type'] == 'BL'){
            $this->sql = "DELETE FROM seg_encounter_location_addtl 
                      WHERE encounter_nr = '".$data['encounter_nr']."'
                      AND group_nr = '".$data['room_type']."'
                      AND room_nr = '".$data['ward_type']."'
                    ORDER BY entry_no desc limit 1";
        }
        else{
            $this->sql = "DELETE FROM care_encounter_location 
                           WHERE encounter_nr = '".$data['encounter_nr']."'
                           ORDER BY nr DESC LIMIT 3";
        }

        if($this->result=$db->Execute($this->sql))
            {
                return $this->result;
            } else {
                return false;
            }

    }

function toggleMGH($enc_nr, $mgh_date, $bsetMGH){
    global $db;

    $this->sql = "UPDATE care_encounter SET
                    is_maygohome = $bsetMGH,
                     mgh_setdte   = $mgh_date
                WHERE encounter_nr = '$enc_nr'";

    if($this->result=$db->Execute($this->sql))
        {
            return $this->result;
        } else {
            return false;
                                }
                            }



function getNewBillingNr() {
        global $db;

        $s_bill_nr = "";

        $strSQL = "SELECT fn_get_new_billing_nr() AS bill_nr";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $s_bill_nr = $row['bill_nr'];
            }
        }

        return $s_bill_nr;
    }

function getConfinementType(){
        global $db;

        $n_id = 0;
        $filter = '';

        if ($this->prev_encounter != '') $filter = " or encounter_nr = '$this->prev_encounter'";

        $strSQL = "select confinetype_id,classify_dte,create_id " .
                    " from seg_encounter_confinement ".
                    "   where (encounter_nr = '". $this->encounter_nr. "'".$filter.") ".
                    "      and str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
                    "   order by create_time desc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $this->caseTypeHist[0] = $row['create_id'];
                    $this->caseTypeHist[1] = $row['classify_dte'];
                    $n_id = $row['confinetype_id'];
                }
            }
                        }

        if ($n_id == 0) {
            $strSQL = "select confinetype_id from seg_type_confinement_icds as stci
                            where exists(select * from care_encounter_diagnosis as ced0
                                            where substring(code, 1, if(instr(code, '.') = 0, length(code), instr(code, '.')-1)) =
                                                substring(stci.diagnosis_code, 1, if(instr(stci.diagnosis_code, '.') = 0, length(stci.diagnosis_code), instr(stci.diagnosis_code, '.')-1))
                            and ((exists(select * from care_encounter_diagnosis as ced where instr(stci.paired_codes, ced.code) > 0 and ced.code <> ced0.code and status <> 'deleted') and stci.paired_codes <> '') or stci.paired_codes = '')
                                             and (encounter_nr = '". $this->encounter_nr. "'".$filter.") and str_to_date(create_time, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "'
                                             and status <> 'deleted')
                            order by confinetype_id desc limit 1";

            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    while ($row = $result->FetchRow()) {
                        $n_id = $row['confinetype_id'];
                    }
                }
            }

            if ($n_id == 0) {
                $strSQL = "select confinetype_id from seg_type_confinement
                                where is_default = 1";
                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        while ($row = $result->FetchRow()) {
                            $n_id = $row['confinetype_id'];
                        }
}                }
            }
        }

        $this->confinetype_id = $n_id;
        return($n_id);
    }

    function getPreviousPayments() {
        global $db;

        if (isset($this->total_prevpayment) && !$this->forceCompute) {
            return $this->total_prevpayment;
        }

        $total_payment = 0;

        $this->prev_payments = array();

        $filter = array('','');

        if ($this->prev_encounter_nr != '') $filter[0] = " or sp.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";

        $strSQL = "select spr.or_no, or_date, sum(spr.amount_due) as or_amnt ".
                    "   from seg_pay as sp inner join ".
                    "      (seg_pay_request as spr left join seg_billing_encounter as sbe ".
                    "         on spr.ref_no = sbe.bill_nr and spr.ref_source = 'PP') ".
                    "      on sp.or_no = spr.or_no " .
                    "   where (sp.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
                    "         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "' " .
                    "      and spr.ref_source = 'PP' and spr.service_code <> 'OBANNEX' and cancel_date is null and sbe.is_deleted IS NULL ".
                    "   group by spr.or_no, or_date ".
                    " union ".
                    "select spd.or_no, or_date, sum(deposit) as or_amnt ".
                    "   from seg_pay as sp1 inner join seg_pay_deposit as spd ".
                    "      on sp1.or_no = spd.or_no " .
                    "   where (spd.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
                    "         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "' " .
                    "      and cancel_date is null ".
                    "   group by spd.or_no, or_date ".
                    "   order by or_date";
        //edited by jasper 08/29/2103 -Fix for OB Annex co-payments BUG#:279
        //echo $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objpay = new Payment;

                    $objpay->setORNo($row['or_no']);
                    $objpay->setORDate($row['or_date']);
                    $objpay->setAmountPaid($row['or_amnt']);

                    $this->prev_payments[] = $objpay;

                    $total_payment += $row['or_amnt'];
                }
            }
        }

        $this->total_prevpayment = $total_payment;

        return $total_payment;
    }


function savebill($data, $bill_nr, $final) {
        global $db;
        $sql = "INSERT INTO seg_billing_encounter 
                        (bill_nr,
                        bill_dte,
                        bill_frmdte,
                        encounter_nr,
                        accommodation_type,
                        total_acc_charge,
                        total_med_charge,
                        total_srv_charge,
                        total_ops_charge,
                        total_doc_charge,
                        total_msc_charge,
                        total_prevpayments,
                        is_final,
                        modify_id,
                        create_id,
                        create_dt)
                VALUES 
                        (".$db->qstr($bill_nr).",
                        ".$db->qstr($data['billdate']).",
                        ".$db->qstr($data['billdatefrom']).",
                        ".$db->qstr($data['encounter']).",
                         ".$db->qstr($data['accommodation_type']).",
                        ".$db->qstr($data['save_total_acc_charge']).",
                        ".$db->qstr($data['save_total_med_charge']).",
                        ".$db->qstr($data['save_total_srv_charge']).",
                        ".$db->qstr($data['save_total_ops_charge']).",
                        ".$db->qstr($data['save_total_doc_charge']).",
                        ".$db->qstr($data['save_total_msc_charge']).",
                        ".$db->qstr($data['save_total_prevpayment']).",
                        ".$db->qstr($final).",
                        ".$db->qstr($_SESSION['sess_temp_userid']).",
                        ".$db->qstr($_SESSION['sess_temp_userid']).",
                        NOW())";
        #echo $sql;
        if($this->result=$db->Execute($sql)) {
            if($db->Affected_Rows()){
                $ok = true;
            } else {
                $ok = false;
            }
        } else {
            $ok = false;
                }

        $this->current_enr = $data['encounter'];
        $ok1 = $this->saveBillingDiscounts($data,$bill_nr);
        $ok2 = $this->saveBillingInsurance($data, $bill_nr, $final);
        $ok3 = $this->saveCaseRatePackage($data, $bill_nr);

        if($ok && $ok1 && $ok2 && $ok3){
            return true;
        } else {
            return false;
        }


        // End James
    }


    function saveBillingInsurance($data, $bill_nr, $final){
        global $db;

        $result = $this->getPerHCareCoverage($data['encounter']);
        $ndays = '0';
        $ok = true;
            if ($result->RecordCount()) {
                while($objhcare = $result->FetchRow()){


                    $refno = 'T'.$data['encounter'];
                    if(!$this->hasDoctorCoverage($bill_nr)){
                        $pf_sql = "UPDATE seg_billing_pf SET bill_nr =".$db->qstr($bill_nr)." WHERE bill_nr =".$db->qstr($refno);
                        if($ok = $db->Execute($pf_sql))
                            $ok = true;
                        else
                            $ok =false;
                    }else{
                        $this->clearDoctorCoverage($refno);
                        $ok = true;
                    }

                    if($ok){
                        if(!$this->hasHCICoverage($bill_nr)){
                            $pf_sql = "UPDATE seg_billingcoverage_adjustment SET ref_no =".$db->qstr($bill_nr)." WHERE ref_no =".$db->qstr($refno);
                            if($ok = $db->Execute($pf_sql))
                               $ok = true;
                            else
                               $ok =false;
                        }else{
                            $this->clearHCICoverage($refno);
                            $ok = true;

                        }
                    }


                    if($ok){
                        if ($this->isPersonPrincipal($objhcare['hcare_id'], $data['encounter']))
                            $principal_pid = $this->getPrincipalPIDofHCare($data['pid'], $objhcare['hcare_id']);
                        else
                            $principal_pid = "";

                        $this->conftrack_sql = "INSERT INTO seg_confinement_tracker (pid, current_year, bill_nr, hcare_id, confine_days, principal_pid)
                                                VALUES (".$db->qstr($data['pid']).", ".$db->qstr($data['billdate']).", ".$db->qstr($bill_nr).", ".$db->qstr($objhcare['hcare_id']).",
                                                '".$data['ndays']."', '".$principal_pid."')";

                        if($ok = $db->Execute($this->conftrack_sql))
                            $ok = true;
                        else
                            $ok = false;
                    }

                    if($ok) {
                        $area = array('D1','D2','D3','D4');
                        unset($role_amount);

                        foreach ($area  as $key => $value) {
                           $role_amount[$key] = $db->getOne("SELECT SUM(dr_claim) 
                                            FROM seg_billing_pf 
                                            WHERE hcare_id=".$db->qstr($objhcare['hcare_id'])." 
                                            AND bill_nr=".$db->qstr($bill_nr)."
                                            AND role_area=".$db->qstr($value));

                           $role_amount[$key] = (($role_amount[$key]) ? $role_amount[$key] : '0.00');
                        }

                        $area_hci = array('AC','MS','HS','OR','XC');
                        unset($role_amount_hci);

                        foreach ($area_hci  as $key => $values) {
                           $role_amount_hci[$key] = $db->getOne("SELECT SUM(coverage) 
                                            FROM seg_billingcoverage_adjustment 
                                            WHERE hcare_id=".$db->qstr($objhcare['hcare_id'])." 
                                            AND ref_no=".$db->qstr($bill_nr)."
                                            AND bill_area=".$db->qstr($values));

                           $role_amount_hci[$key] = (($role_amount_hci[$key]) ? $role_amount_hci[$key] : '0.00');
                        }

                        $billcover_sql = "INSERT INTO seg_billing_coverage (bill_nr, 
                                                                            hcare_id, 
                                                                            total_services_coverage, 
                                                                            total_acc_coverage, 
                                                                            total_med_coverage, 
                                                                            total_sup_coverage,
                                                                            total_srv_coverage, 
                                                                            total_ops_coverage, 
                                                                            total_d1_coverage, 
                                                                            total_d2_coverage, 
                                                                            total_d3_coverage, 
                                                                            total_d4_coverage, 
                                                                            total_msc_coverage)
                                                                    VALUES (".$db->qstr($bill_nr).", 
                                                                            ".$db->qstr($objhcare['hcare_id']).", 
                                                                            ".$db->qstr($data['hcicoverage']).",
                                                                            ".$db->qstr($role_amount_hci[0]).", 
                                                                            ".$db->qstr($role_amount_hci[1]).", 
                                                                            '0',
                                                                            ".$db->qstr($role_amount_hci[2]).", 
                                                                            ".$db->qstr($role_amount_hci[3]).", 
                                                                            ".$db->qstr($role_amount[0]).", 
                                                                            ".$db->qstr($role_amount[1]).", 
                                                                            ".$db->qstr($role_amount[2]).", 
                                                                            ".$db->qstr($role_amount[3]).",
                                                                            ".$db->qstr($role_amount_hci[4]).")";
                        if($ok = $db->Execute($billcover_sql))
                                $ok = true;
                            else
                                $ok = false;
                    }

                    if($ok){
                        $ok = true;
                    }else{
                        $ok = false;
                        $this->error_msg =  "ERRROR: ".$pf_sql." == ".$billcover_sql.' == '.$this->conftrack_sql;
                    }

                }
            }

            if($ok){
                return true;
            }else{
                return false;
            }
    }

    function saveBillingDiscounts($data, $bill_nr)
    {
        global $db;

        if(!empty($data['disc_id'])) {
            $sqlDiscount = "INSERT INTO seg_billing_discount (bill_nr, discountid, discount, discount_amnt) 
                            VALUES (".$db->qstr($bill_nr).", ".$db->qstr($data['disc_id']).", ".$db->qstr($data['disc']).", ".$db->qstr($data['disc_amnt']).") ";
            $res = $db->Execute($sqlDiscount);
            if($res)
                $ok=true;
            else{
                $ok =false;
                $this->error_msg .= "ERROR: ".$sqlDiscount;
            }
        }else{
             $ok=true;
        }


        if($ok){

            $refno = 'T'.$data['encounter'];
            if(!$this->hasDoctorDiscount($bill_nr)){
                $pf_sql = "UPDATE seg_billing_other_discounts SET refno =".$db->qstr($bill_nr)." WHERE refno =".$db->qstr($refno);
                if($ok = $db->Execute($pf_sql))
                    $ok = true;
                else
                    $ok = false;
            }else{
                $this->clearOtherDiscount($refno);
                 $ok = true;
                 $this->error_msg .=  "ERROR: ".$pf_sql;

            }
        }

        if($ok){

            $area = array('AC','MS','HS','OR','XC','D1','D2','D3','D4');
            unset($role);

            foreach ($area  as $key => $values) {
               $role[$key] = $db->getOne("SELECT SUM(ar_discount + sc_discount) 
                                FROM seg_billing_other_discounts 
                                WHERE  refno=".$db->qstr($bill_nr)."
                                AND bill_areas=".$db->qstr($values)." GROUP BY bill_areas");

               $role[$key] = (($role[$key]) ? $role[$key] : '0.00');
            }


            $this->sql = "INSERT INTO seg_billingcomputed_discount (bill_nr, 
                                                                    total_acc_discount, 
                                                                    total_med_discount, 
                                                                    total_sup_discount, 
                                                                    total_srv_discount, 
                                                                    total_ops_discount,
                                                                    total_msc_discount, 
                                                                    total_d1_discount, 
                                                                    total_d2_discount, 
                                                                    total_d3_discount, 
                                                                    total_d4_discount, 
                                                                    hospital_income_discount,
                                                                    professional_income_discount) 
                                                            VALUES (".$db->qstr($bill_nr).", 
                                                                    ".$db->qstr($role[0]).", 
                                                                    ".$db->qstr($role[1]).", 
                                                                    '0',
                                                                    ".$db->qstr($role[2]).", 
                                                                    ".$db->qstr($role[3]).",
                                                                    ".$db->qstr($role[4]).",
                                                                    ".$db->qstr($role[5]).", 
                                                                    ".$db->qstr($role[6]).", 
                                                                    ".$db->qstr($role[7]).",
                                                                    ".$db->qstr($role[8]).", 
                                                                    '0',
                                                                    '0')";
    //echo $this->sql;
            $res2 = $db->Execute($this->sql);
            if($res2)
                $ok =true;
            else{
                $ok =false;
                 $this->error_msg .=  "ERROR: ".$this->sql;
            }

        }


        if($ok){
            return true;
        }else{
            return false;

        }

    }

    function checkIfPHS($enc) {
        $objEnc = new Encounter();

        $result = $objEnc->getEncounterInfo($enc);
        return ($result['discountid'] == "PHS");

    }

    function isSponsoredMember() {
        if ($this->memcategory_id == '') {
            $this->getMemCategoryDesc();
        }

        if ($this->isCharity()) {
            if ($this->memcategory_id == '5')
                return true;
            else
                return false;
        } else {
          return false;
       }

    }

    function isHSM() {
        if ($this->memcategory_id == '') {
            $this->getMemCategoryDesc();
        }

        if ($this->isCharity()) {
            if ($this->memcategory_id == '9')
                return true;
            else
                return false;
        } else {
           return false;
        }

    }

    function isPersonPrincipal($n_hcareid,$enc) {
        global $db;
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select is_principal ".
                    "   from care_person_insurance as cpi inner join care_encounter as ce on cpi.pid = ce.pid ".
                    "   where (encounter_nr = '". $enc. "'".$filter.") and is_void = 0 ".
                    "      and hcare_id = ". $n_hcareid;

        if ($result = $db->Execute($strSQL))
            if ($result->RecordCount())
                while ($row = $result->FetchRow()) {
                    if ($row['is_principal'])
                        return true;
                    else
                        return false;
                }
    }

    function getPerHCareCoverage($enc){
        global $db;

        $this->hcare_coverage = array();
        $filter = '';

        if($this->prev_encounter != ''){
            $filter = " OR si.encounter_nr = '$this->prev_encounter'";}

        $this->sql = "SELECT DISTINCT ci.hcare_id, firm_id, name
                        FROM care_insurance_firm AS ci
                        WHERE EXISTS (
                            SELECT * FROM seg_encounter_insurance AS si
                            WHERE (si.encounter_nr = ".$db->qstr($enc)."".$filter.")
                            AND si.hcare_id = ci.hcare_id) ORDER BY proirity DESC";
        if($result = $db->Execute($this->sql))
            return $result;
        else
            return false;
    }

    function clearSaveData($bill_nr){
        global $db;
        $bill_nr = $db->qstr($bill_nr);
        $this->sql = "DELETE FROM seg_confinement_tracker WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_coverage WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_discount WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billingcomputed_discount WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_encounter_details WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_caserate WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        return true;
    }

    function setDeathDate($data, $deathdate = "0000-00-00 00:00:00", $user){
        global $db;
        if($data['encounter'] == '')
            $data['encounter'] = '0';
        $db->BeginTrans();

        $this->sql = "UPDATE care_person SET
                        death_date = DATE_FORMAT('$deathdate', '%Y-%m-%d'),
                        death_time = DATE_FORMAT('$deathdate', '%H:%i:%s'),
                        history = CONCAT(history, 'Update: ', NOW(), ' [$user]\\n'),
                        modify_id = '$user',
                        modify_time = NOW(),
                        death_encounter_nr = ".$db->qstr($data['encounter'])."
                        WHERE pid = ".$db->qstr($data['pid'])."";
        $success1 = $db->Execute($this->sql);

        if($success1){
            $fldarray = array('encounter_nr' => $db->qstr($data['encounter']),
                        'result_code' => '4',
                        'modify_id' => $db->qstr($user),
                        'modify_time' => 'NOW()',
                        'create_id' => $db->qstr($user),
                        'create_time' => 'NOW()');
            $success2 = $db->Replace('seg_encounter_result', $fldarray, array('encounter_nr'));
                }

        if(!$success1 || !$success2){
            $db->RollbackTrans();
            return $db->ErrorMsg();
              }
        else{
            $db->CommitTrans();
            return $success1;
            }
    }


    function updatebill($data, $bill_nr, $final) {
        global $db;
        $sql = "UPDATE seg_billing_encounter 
                    SET bill_dte = ".$db->qstr($data['billdate']).",
                        bill_frmdte = ".$db->qstr($data['billdatefrom']).",
                        encounter_nr = ".$db->qstr($data['encounter']).",
                        accommodation_type = ".$db->qstr($data['accommodation_type']).",
                        total_acc_charge = ".$db->qstr($data['save_total_acc_charge']).", 
                        total_med_charge = ".$db->qstr($data['save_total_med_charge']).",
                        total_srv_charge = ".$db->qstr($data['save_total_srv_charge']).",
                        total_ops_charge = ".$db->qstr($data['save_total_ops_charge']).",
                        total_doc_charge = ".$db->qstr($data['save_total_doc_charge']).",
                        total_msc_charge = ".$db->qstr($data['save_total_msc_charge']).",
                        total_prevpayments = ".$db->qstr($data['save_total_prevpayment']).",
                        is_final = ".$db->qstr($final).",
                        modify_id = ".$db->qstr($_SESSION['sess_temp_userid']).",
                        modify_dt = NOW()
                        WHERE bill_nr = ".$db->qstr($bill_nr);



       if($this->result=$db->Execute($sql)) {
           $ok = true;
        }else{
            $ok = false;
        }

        $this->current_enr = $data['encounter'];
        $ok1 = $this->clearSaveData($bill_nr);
        $ok2 = $this->saveBillingDiscounts($data, $bill_nr);
        $ok3 = $this->saveBillingInsurance($data, $bill_nr,$final);
        $ok4 = $this->saveCaseRatePackage($data, $bill_nr);

        if($ok && $ok1 && $ok2 && $ok3 && $ok4){
            return true;
        }else{
            return false;
        }



    }


   function getbillnr($data) {
        global $db;

        $bill_nr = "";

        $strSQL = "SELECT bill_nr 
                    FROM seg_billing_encounter
                    WHERE ISNULL(is_deleted)
                    AND encounter_nr =". $db->qstr($data['encounter']);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $bill_nr = $row['bill_nr'];
                            }
                        }

        return $bill_nr;
                        }
     function isMedicoLegal($enc) {
        global $db;
        $strSQL = "SELECT is_medico FROM care_encounter WHERE encounter_nr = ".$db->qstr($enc);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $ismedico = ($row['is_medico']);
            }
        }
        if($ismedico==1)
        return true;
        else
        return $ismedico;
    }

    function saveCaseRatePackage($data,$bill_nr){
        global $db;
        $ok = true;
        if($data['first_rate_code']){
            $sql1 = "INSERT INTO seg_billing_caserate(bill_nr, package_id, rate_type, amount, hci_amount, pf_amount) 
                        VALUES('".$bill_nr."','".$data['first_rate_code']."','1','".$data['first_rate']."',
                               '".$data['first_hci']."','".$data['first_pf']."')";
            if($result=$db->Execute($sql1))
            {
                $ok = true;
            } else {
                $ok = false;
                $this->error_msg = "ERROR: ".$sql1;
            }
        }

        if($data['second_rate_code']){
            if($ok){
                $sql2 = "INSERT INTO seg_billing_caserate(bill_nr,package_id,rate_type,amount,hci_amount, pf_amount) 
                            VALUES('".$bill_nr."','".$data['second_rate_code']."','2','".$data['second_rate']."',
                                   '".$data['second_hci']."','".$data['second_pf']."')";

                if($result=$db->Execute($sql2))
                {
                    $ok = true;
                } else {
                    $ok = false;
                    $this->error_msg = "ERROR: ".$sql2;
                }
            }
        }


        if ($ok) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * Updated by Nick, 4/23/2014
     * Join with seg_case_rate_special
     */
    function hasSavedPackage($bill_nr,$rtype){
            global $db;

            $this->sql = "SELECT 
                              sbc.*,
                              scrs.`sp_package_id` 
                            FROM
                              seg_billing_caserate AS sbc 
                              LEFT JOIN seg_case_rate_special AS scrs 
                                ON sbc.`package_id` = scrs.`sp_package_id` 
                            WHERE rate_type = $rtype
                              AND bill_nr = ".$db->qstr($bill_nr);

            if ($buf=$db->Execute($this->sql)){
                if($buf->RecordCount()) {
                    return $buf->FetchRow();
                }else { return FALSE; }
            }else { return FALSE; }

    }

    function delSavedPackage($bill_nr,$rType){
        global $db;

        $this->sql = "DELETE FROM seg_billing_caserate WHERE rate_type=$rType AND bill_nr=".$db->qstr($bill_nr);

        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }

    }


    function saveRefNo($data) // Edited by James 1/7/2014
    {
        global $db;

        $index = "bill_nr, refno, ref_area";
        $this->sql="INSERT INTO seg_billing_encounter_details ($index) VALUES $data";
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows())
                return TRUE;
            else
                return FALSE;
        }else
            return FALSE;

    }


    function getEncounterDte()
    {
        global $db;

        $strSQL = "select encounter_date " .
                            "   from care_encounter " .
                            "   where (encounter_nr = '". $this->prev_encounter ."')
                                order by encounter_date limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $enc_dte = strftime("%Y-%m-%d %H:%M", strtotime($row['encounter_date'])).":00";
            }
        }

        return($enc_dte);
    }


    function isFinal()
    {
        global $db;

        $strSQL = "SELECT is_final FROM seg_billing_encounter WHERE bill_nr = ".$db->qstr($this->old_bill_nr);

        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    if ($row["is_final"]) {
                        return TRUE;
                    }else{return FALSE;}
                }else{return FALSE;}
            }else{return FALSE;}
        }else{return FALSE;}

    }

    //added by ken 1/4/2013
    function checkInsuranceRequest($enc){
        global $db;

        $this->sql = "SELECT hcare_id FROM seg_encounter_insurance WHERE encounter_nr = '".$enc."' AND hcare_id = '18'";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function isPayward($enc){
        global $db;

        $this->sql = "SELECT ce.encounter_nr, ce.`current_ward_nr`,cw.accomodation_type
                        FROM care_encounter AS ce
                        INNER JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
                        WHERE ce.encounter_nr = ".$db->qstr($enc)." AND cw.`accomodation_type` = '2'
                        UNION
                        SELECT sela.encounter_nr, sela.group_nr, cw.accomodation_type
                        FROM seg_encounter_location_addtl AS sela
                        INNER JOIN care_ward AS cw ON sela.group_nr = cw.nr 
                        WHERE sela.encounter_nr = ".$db->qstr($enc)." AND cw.`accomodation_type` = '2'";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function getRoomRate($data){
        global $db;

        $this->sql = "SELECT ctr.room_rate 
                        FROM care_room AS cr 
                        INNER JOIN care_type_room AS ctr ON cr.`type_nr` = ctr.`nr` 
                        WHERE cr.`ward_nr` = '".$data['ward_nr']."' AND cr.`nr` = '".$data['room_nr']."' ";

        if($this->result=$db->Execute($this->sql)) {
            if($row = $this->result->FetchRow())
                return $row['room_rate'];
        } else { return false; }
    }
        //ended by ken


    /**
    * Created By Pol 01/04/2014
    * Update by Jarel 03/05/2014
    * Add Codition for special cases
    * And Get pid if Empty.
    */
    function GetPreviousPackage($encnr,$pid='') {
        global $db;

        if($pid=='')
            $pid = $db->getOne("SELECT pid FROM care_encounter WHERE encounter_nr = ".$db->qstr($encnr));

           $SQLstr = ("SELECT scrp.`description`,
                         scrp.`code`,
                         scrp.`package`,
                         ce.`encounter_nr`,
                         DATE_FORMAT(ce.`encounter_date`,'%M %e %Y %r') AS DateAdmitted,
                         DATE_FORMAT(ce.`mgh_setdte`,'%M %e %Y %r') AS DateDischarged,
                         DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d %T'),DATE_FORMAT(ce.`discharge_date`,'%Y-%m-%d %T')) AS daydifferent
                        FROM care_encounter `ce`
                        INNER JOIN seg_billing_encounter `sbe`
                        ON ce.`encounter_nr` = sbe.`encounter_nr`
                            AND sbe.`is_deleted` IS NULL AND sbe.`is_final` = '1'
                        INNER JOIN seg_billing_caserate `sbc`
                        ON sbe.`bill_nr` = sbc.`bill_nr`
                        INNER JOIN seg_case_rate_packages `scrp`
                    ON scrp.`code` = sbc.`package_id` AND scrp.special_case = '0'
                        INNER JOIN care_person `cp`
                        ON cp.`pid` = ce.`pid`
                        WHERE ce.`pid` ='".$pid."'
                        AND ce.`encounter_nr` <>'".$encnr."'
                        AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d %T'),DATE_FORMAT(ce.`discharge_date`,'%Y-%m-%d %T')) <= '90'");
       //echo $SQLstr;
        if ($result = $db->Execute($SQLstr)) {
            if ($result->RecordCount()) {
                return $result;
            } else {
                return false;
            }
        }
    }
     function IsChartyName($charity) {
        if ($charity == '') {
            $this->AccommodationType();
        }
        return (!(strpos(strtoupper($this->accomm_typ_desc), CHARITY, 0) === false));
    }



    function AccommodationType($enc, $bill_date, $bill_from, $prev_encounter) {
        global $db;
        $sname = '';
        $filter = array('','');
        if ($prev_encounter != '') {
            $filter[0] = " or cel.encounter_nr = '$prev_encounter'";
            $filter[1] = " or sel.encounter_nr = '$prev_encounter'";
        }
            $strSQL = "select 0 AS entry_no,
                  STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                  cw.accomodation_type, accomodation_name, cw.name AS ward_name ".
                    "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                    "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
                    "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
                    "   where (cel.encounter_nr = '". $enc. "'".$filter[0].") ".
                    "      and exists (select nr ".
                    "                     from care_type_location as ctl ".
                    "                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
                    "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $bill_from . "' ".
                    "      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
                    "         or ".
                    "      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $bill_from . "' ".
                    "      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
                    "      or ".
                    "      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
          " UNION ALL
            SELECT entry_no, occupy_date, cw.accomodation_type, accomodation_name, cw.name AS ward_name
              FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
                INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
            WHERE (sel.encounter_nr = '". $enc. "'".$filter[1].")
              AND (
                STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) >= '" . $bill_from . "'
                AND STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) < '" . $bill_date . "'
              )
            ORDER BY entry_no DESC LIMIT 1";

        $this->debugSQL = $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $sname = $row['accomodation_name'];
                }
            }
        }

        return ($sname);

    }

    function isDialysisPatient($enc) {
        global $db;

        $enc_type = 0;
        $strSQL = "SELECT encounter_type
                    FROM care_encounter
                     WHERE encounter_nr = '$enc'";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == DIALYSIS_PATIENT);
    }
    //end by pol 01/04/2014

    #-------------------------------------------------
    function getAccomodationDesc(){
        return $this->accomm_typ_desc;
    }

    function getMemCategoryDesc() {
        global $db;
        $s_desc= "";
        $filter = '';
        if ($this->prev_encounter_nr != '') $filter = " or sem.encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "SELECT 
                      memcategory_desc,
                      sm.memcategory_id,
                      sei.modify_id,
                      sei.modify_dt 
                    FROM
                      seg_memcategory AS sm 
                      INNER JOIN seg_encounter_memcategory AS sem 
                        ON sm.memcategory_id = sem.memcategory_id 
                      INNER JOIN seg_encounter_insurance AS sei 
                        ON sem.encounter_nr = sei.encounter_nr 
                    WHERE sem.encounter_nr = " . $db->qstr($this->current_enr) . $filter; //sql updated by Nick 05-12-2014 - Tidy up + modify info

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $s_desc = $row['memcategory_desc'];
                    $this->memcategory_id = $row['memcategory_id'];
                    $this->memCatHist = array(
                                                'modify_id' => $row['modify_id'],
                                                'modify_dt' => $row['modify_dt']
                                             );//added by Nick 05-12-2014
                }
            }
        }
        return $s_desc;
    }


    function getMemCategoryID()
    {
        return $this->memcategory_id;
    }

    function getClassificationDesc($enc, $bill_dte, $charity='', $IsEr='') {
            global $db;

            $s_desc= "";
            $prev = "";

        if ($charity || $IsEr) {
          $filter = '';
          $sql = "SELECT parent_encounter_nr 
                    FROM care_encounter 
                    WHERE encounter_nr = ".$db->qstr($enc);

          if ($result1 = $db->Execute($sql)){
            if ($result1->RecordCount()){
                while($row1 = $result1->FetchRow()){
                    $prev = $row1['parent_encounter_nr'];
                }
            }
          }
          if ($prev != '') $filter = " or scg.encounter_nr = ".$db->qstr($enc);

          $strSQL = "select discountdesc ".
                "   from seg_discount as sd inner join seg_charity_grants as scg on sd.discountid = scg.discountid ".
                "   where (scg.encounter_nr = '". $db->qstr($enc). "'".$filter.") ".
                "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '" . $db->qstr($bill_dte) . "' " .
                "   order by grant_dte desc limit 1";

          if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
              while ($row = $result->FetchRow()) {
                $s_desc = $row['discountdesc'];
              }
            }
          }
        }
            return($s_desc);
        }

    function getCaseTypeID($enc, $bill_date, $prevenc ='') {
        global $db;
        $sdesc = '';
        $filter = '';
        if ($prevenc != '') $filter = " or encounter_nr = ".$db->qstr($prevenc);
        $strSQL = "select sec.casetype_id  ".
                    "   from seg_encounter_case as sec inner join seg_type_case as stc ".
                    "      on sec.casetype_id = stc.casetype_id ".
                    "   where (encounter_nr = ". $db->qstr($enc). "".$filter.") ".
                    "      and str_to_date(sec.modify_dt, '%Y-%m-%d %H:%i:%s') < ".$db->qstr($bill_date)."".
                    "      and !sec.is_deleted ".
                    "   order by sec.create_dt desc limit 1";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $sdesc = $row['casetype_id'];
                }
            }
        }
        return($sdesc);
    }

    function getActualLastBillDte() {
        global $db;

        $lastbill_dte = "0000-00-00 00:00:00";
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select bill_dte " .
                    "   from seg_billing_encounter " .
                    "   where (encounter_nr = '". $this->current_enr ."'".$filter.") " .
                    "      and str_to_date(bill_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' and is_deleted IS NULL ".
                    "   order by bill_dte desc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $lastbill_dte = $row['bill_dte'];
            }
        }

        return($lastbill_dte);
    }

    function getOBAnnexPayment() {
        global $db;
        $this->ob_payments = array();
        $total_payment = 0;
        $strSQL = "SELECT sp.or_no, sp.or_date, spr.amount_due AS ob_amt FROM seg_pay AS sp " .
                   "INNER JOIN seg_pay_request AS spr ON sp.or_no = spr.or_no  " .
                  "WHERE sp.encounter_nr = '" . $this->current_enr . "' " .
                  "AND sp.cancel_date is null AND spr.service_code = 'OBANNEX'";
    // echo $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objpay = new Payment;
                    $objpay->setORNo($row['or_no']);
                    $objpay->setORDate($row['or_date']);
                    $objpay->setAmountPaid($row['ob_amt']);
                    $this->ob_payments[] = $objpay;
                    $total_payment += $row['ob_amt'];
                }
            }
        }
        $this->total_ob_payments = $total_payment;
        return $total_payment;
    }

    //added by art 01/05/2014
    function getPrevConfinement($year){
        global $db;

        $this->sql = "SELECT SUM(confine_days) AS tdays
                        FROM seg_confinement_tracker sct 
                      INNER JOIN care_encounter AS ce
                        ON sct.pid = ce.pid 
                        AND encounter_nr = ".$db->qstr($this->encounter_nr)."
                        AND current_year = ".$db->qstr($year)."
                        AND hcare_id= 18";

        if($result = $db->Execute($this->sql)){
            if ($result->RecordCount()) {
               $row = $result->FetchRow();
                return $days = $row['tdays'];
            }else{ return FALSE; }
        }else { return FALSE; }
    }

    function getAdmissionDate(){
        global $db;

        $this->sql ="SELECT admission_dt FROM care_encounter WHERE encounter_nr = ".$db->qstr($this->encounter_nr)."";

        if($result = $db->Execute($this->sql)){
            if($result->RecordCount()){
               $row = $result->FetchRow();
                return $admission_dt = $row['admission_dt'];
            }else{ return FALSE; }
        }else { return FALSE; }
    }

    function getDaysCount(){
        global $db;
        $bill = date('Y-m-d', strtotime($this->bill_dte));
        $admit = date('Y-m-d', strtotime($this->getAdmissionDate()));

        $this->sql = "SELECT DATEDIFF(".$db->qstr($bill).",".$db->qstr($admit).") as days";
        if ($result = $db->Execute($this->sql)) {
            $row = $result->FetchRow();
            $days = $row['days'];
        }
        return $days;
    }


    function getConDaysFrmAdDteToYearEnd(){
        global $db;
        $year = date('Y',strtotime($this->getAdmissionDate())) . "-12-31";
        $admit = date('Y-m-d',strtotime($this->getAdmissionDate()));
        $strSQL = "SELECT DATEDIFF(".$db->qstr($year).",".$db->qstr($admit).") as days";
        if ($result = $db->Execute($strSQL)) {
            $row = $result->FetchRow();
            $days = $row['days'];
        }
       return $days;
    }

    function getConDaysFrmNwYrToBillDte(){
        global $db;
        $newyr = date('Y',strtotime($this->getAdmissionDate())) . "-12-31";
        $bill  = date('Y-m-d', strtotime($this->bill_dte));
        $strSQL = "SELECT DATEDIFF(".$db->qstr($bill).",".$db->qstr($newyr).") as days";
        if ($result = $db->Execute($strSQL)) {
            $row = $result->FetchRow();
            $days = $row['days'];
        }
        $days = ($days > 1 ? $days : 0);
        return $days;
    }

    function fortyFiveDays(){
        $admit_yr = date('Y',strtotime($this->getAdmissionDate()));
        $bill_yr = date('Y', strtotime($this->bill_dte));
        $limit = 45;
        $ndays = $this->getDaysCount();
        $result = $this->checkInsuranceRequest($this->encounter_nr);
        $isER = $this->isERPatient($this->encounter_nr);

        if($isER == NULL){
            if ($result->RecordCount() != 0) {
                if ($admit_yr == $bill_yr) {
                    $prevdays = $this->getPrevConfinement($admit_yr);

                   $rdays = ($limit > $prevdays ? $limit - $prevdays : 0);
                   $cdays = ($rdays >=  $ndays ? $ndays : $rdays);

                   return array('remaining'=>$rdays , 'covered'=> $cdays ,'save'=> $cdays);
                }else{
                    $day_a = $this->getConDaysFrmAdDteToYearEnd();
                    $day_b = $this->getConDaysFrmNwYrToBillDte();
                    $limit_a = $limit - $this->getPrevConfinement($admit_yr);

                    $covered_a = ($limit_a >= $day_a ? $day_a : $limit_a);
                    $covered_b = ($limit >= $day_b ? $day_b : $limit);
                    $excess_a = ($limit_a >= $day_a ? 0 : $day_a - $limit_a);
                    $excess_b = ($limit >= $day_b ? 0 : $day_b - $limit);

                    $rdays = $limit_a + $limit.'   (last year: ' .$limit_a. ', this year: '.$limit.')';
                    $cdays = $covered_a + $covered_b;

                    return array('remaining'=>'' , 'covered'=>$cdays , 'save'=> $covered_b);
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //end by art
    //added by poliam
    //added new function
    function Classification($enc, $bill_dte, $IsCharity='', $IsEr='', $prevenc) {
         global $db;
        $s_desc= "";
        if ($IsCharity || $IsEr) {
          $filter = '';
            if ($prevenc != '') $filter = " or scg.encounter_nr = '$prevenc'";
                $strSQL = "SELECT discountdesc ".
                    "   FROM seg_discount as sd 
                    inner join seg_charity_grants as scg 
                    on sd.discountid = scg.discountid ".
                    "   where (scg.encounter_nr = '". $enc. "'".$filter.") ".
                    "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte . "' " .
                    "   order by grant_dte desc limit 1";

                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        while ($row = $result->FetchRow()) {
                        $s_desc = $row['discountdesc'];
                    }
                }
            }
        }
        return($s_desc);
    }

    //end by poliam

    //added by nick, 1/6/2014
    function getCaseDate($enc){
        global $db;
        $sql = "SELECT encounter_date FROM care_encounter WHERE encounter_nr = ".$db->qstr($enc);
        $rs = $db->Execute($sql);
        if($rs){
            if($rs->RecordCount()>0){
                $row = $rs->FetchRow();
                return $row['encounter_date'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //end nick

    // Added by James 1/6/2014
    function trapError($bill_nr){
        global $db;

        $sql = "DELETE FROM seg_billing_encounter WHERE bill_nr = ".$db->qstr($bill_nr);
        $rs = $db->Execute($sql);
        if($rs){
            return true;
        }else{
            return false;
        }
    }// End James


    function getTotalAppliedDiscounts($enc){
        global $db;

        $sql = "SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount 
                WHERE encounter_nr = ".$db->qstr($enc);

        $rs = $db->Execute($sql);
             if($rs){
            if($rs->RecordCount()>0){
                $row = $rs->FetchRow();
                return $row['total_discount'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    /**
    * Created By Jarel
    * Created On 02/20/2014
    * Get Case Type Description Returns PRIVATE CASE OR HOUSE CASE
    * @param string enc - Patient Encounter
    * @param string bill_date
    * @param string $prevenc - Parent encounter if any
    * @return string $sdesc
    **/
    function getCaseTypeDesc($enc, $bill_date, $prevenc ='') {
        global $db;
        $sdesc = '';
        $filter = '';
        if ($prevenc != '') $filter = " or encounter_nr = ".$db->qstr($prevenc);
        $strSQL = "select stc.casetype_desc  ".
                    "   from seg_encounter_case as sec inner join seg_type_case as stc ".
                    "      on sec.casetype_id = stc.casetype_id ".
                    "   where (encounter_nr = ". $db->qstr($enc). "".$filter.") ".
                    "      and str_to_date(sec.modify_dt, '%Y-%m-%d %H:%i:%s') < ".$db->qstr($bill_date)."".
                    "      and !sec.is_deleted ".
                    "   order by sec.create_dt desc limit 1";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $sdesc = $row['casetype_desc'];
                }
            }
        }
        return (($sdesc=='') ? "HOUSE CASE" : $sdesc );
    }


    /**
    * Created By Jarel
    * Created On 02/24/2014
    * Get the Name of Insurance Holder
    * @param string pid
    * @param string hcare_id
    * @return string name
    **/
    function getInsuranceMemberName($pid,$hcare_id)
    {
        global $db;

        $strSQL = "SELECT 
                      IF(
                        cpi.is_principal = '1',
                        `fn_get_person_name` (cpi.`pid`),
                        CONCAT(
                          TRIM(member_lname),
                          IF(
                            TRIM(member_fname) <> '',
                            CONCAT(', ', TRIM(member_fname)),
                            ' '
                          ),
                          IF(
                            TRIM(member_mname) <> '',
                            CONCAT(' ', LEFT(TRIM(member_mname), 1), '.'),
                            ''
                          )
                        )
                      ) AS name
                      FROM care_person_insurance cpi
                      INNER JOIN seg_insurance_member_info simi
                      ON simi.`pid` = cpi.`pid` AND simi.`hcare_id` = ".$db->qstr($hcare_id)."\n
                    WHERE  cpi.pid = ".$db->qstr($pid)." AND cpi.`hcare_id` = ".$db->qstr($hcare_id);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
               $row = $result->FetchRow();
                return $row['name'];
            }return false;
        }return false;
    }



   /**
    * Created By Jarel
    * Created On 03/12/2014
    * Get Patient Death date
    * @param string enc
    * @return string death date
    **/
    function getDeathDate($enc)
    {
        global $db;
        $strSQL = $db->Prepare("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate 
                                FROM care_person p
                                WHERE death_encounter_nr = ?");

        if($result=$db->Execute($strSQL,$enc)) {
             $row = $result->FetchRow();
                return $row['deathdate'];
        } else { return false; }
    }

   function getIcd($enc, $encounter_type){
        global $db;
 $diagnosis = $db->GetAll("SELECT
                  d.diagnosis_nr,d.encounter_nr,d.encounter_type,
                  d.type_nr,d.op_nr,d.date,
                  IFNULL(sed.code_alt, d.code) AS code,
                  d.code_parent,d.group_nr,d.code_version,
                  d.localcode,d.category_nr,d.type,
                  d.localization,d.diagnosing_clinician,
                  d.diagnosing_dept_nr, d.status,d.history,
                  d.modify_id,d.modify_time,d.create_id,
                  d.create_time,d.referral_nr,d.diagnosis_description,
                  IFNULL(sed.`code_alt`, TRIM(c.diagnosis_code)) AS CODE,
                  IF(
                    d.diagnosis_description IS NULL,
                    TRIM(c.description),
                    TRIM(d.diagnosis_description)
                  ) AS description,
                  d.type_nr AS TYPE,m.description AS parent_desc,
                  (SELECT 
                    id 
                  FROM
                    care_department AS dept 
                  WHERE dept.nr = d.diagnosing_dept_nr) AS diagnosing_dept_id,
                  (SELECT 
                    name_formal 
                  FROM
                    care_department AS dept 
                  WHERE dept.nr = d.diagnosing_dept_nr) AS diagnosing_dept_name,
                  (SELECT 
                    CONCAT(
                      cp_2.title,
                      ' ',
                      cp_2.name_first,
                      ' ',
                      IF(
                        TRIM(cp_2.name_middle) <> '',
                        CONCAT(LEFT(cp_2.name_middle, 1), '. '),
                        ''
                      ),
                      cp_2.name_last
                    ) AS fullname 
                  FROM
                    care_encounter_diagnosis AS enc_d2,
                    care_personell AS cpl_2,
                    care_person AS cp_2 
                  WHERE enc_d2.encounter_nr = d.encounter_nr 
                    AND cpl_2.nr = enc_d2.diagnosing_clinician 
                    AND cp_2.pid = cpl_2.pid 
                    AND enc_d2.diagnosing_clinician = d.diagnosing_clinician 
                    AND enc_d2.diagnosis_nr = d.diagnosis_nr) AS diagnosing_clinician_name 
                FROM
                  care_encounter_diagnosis AS d 
                  LEFT JOIN care_icd10_en AS c 
                    ON d.code = c.diagnosis_code 
                  LEFT JOIN care_icd10_en AS m 
                    ON d.code_parent = m.diagnosis_code 
                  LEFT JOIN seg_encounter_diagnosis sed 
                    ON (
                      sed.`code` = d.`code` 
                      AND sed.`encounter_nr` = d.`encounter_nr`
                      AND sed.is_deleted <> 1
                    ) 
                WHERE d.encounter_nr IN ('$enc') 
                  AND d.status NOT IN ('deleted','hidden','inactive','void','added')
                  AND encounter_type ='$encounter_type'
                  GROUP BY CODE 
                ORDER BY d.type_nr DESC,
                  d.date ASC");
        if (!empty($diagnosis))
            return $diagnosis;
        else
            return false;
    }


    function getRvs($encs, $enc)
    {
        global $db;
        $encslist = $encs . "','" . $enc;
        $procedures = $db->GetAll("SELECT
                                      smod.ops_code,
                                      IF(smod.description IS NOT NULL,
                                          smod.description,
                                          scrp.description
                                            ) AS description,
                                      smod.laterality,
                                      smod.op_date,
                                      scrp.special_case,
                                      smod.op_date AS special_dates
                                    FROM
                                      seg_misc_ops AS smo
                                      INNER JOIN seg_misc_ops_details AS smod
                                        ON smod.refno = smo.refno
                                        INNER JOIN seg_case_rate_packages AS scrp ON scrp.code = smod.ops_code
                                    WHERE smo.encounter_nr IN ('$encslist')");
        if(!empty($procedures))
            return $procedures;
        else
            return false;
    }

    /**
    * Created By Jarel
    * Created On 04/04/2014
    * set accomodation type
    * @param string type
    **/
    function setAccomodationType($type)
    {
        $this->accomodation_type = $type;
    }


    /**
    * Created By Jarel
    * Created On 04/04/2014
    * get accomodation type
    * @return string type
    **/
    function getAccomodationType()
    {
       return $this->accomodation_type;
    }

    /**
     * @author Nick B. Alcala
     * Created On 04/04/2014
     * Identify if patient is Infirmary of Dependent
     * @param  string  $enc
     * @return boolean/string
     */
    function isInfirmaryOrDependent($enc){
        global $db;
        $output = '';

        if($this->isInfirmary($enc)){
            return 'infirmary';
        }

        $this->sql = $db->Prepare("SELECT 
                                      ce.pid,
                                      parent_pid,
                                      dependent_pid,
                                      relationship 
                                    FROM
                                      seg_dependents AS sd 
                                      INNER JOIN care_encounter AS ce 
                                        ON sd.parent_pid = ce.pid 
                                        OR sd.dependent_pid = ce.pid 
                                    WHERE ce.encounter_nr = ?");
        $rs = $db->Execute($this->sql,$enc);
        if($rs){
            if($rs->RecordCount()){
                while($row = $rs->FetchRow()){
                    if($row['parent_pid'] == $row['pid']){
                        $output = 'infirmary';
                    }else if($row['dependent_pid'] == $row['pid']){
                        $output = 'dependent';
                    }
                }
                return $output;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //added by Nick 05-21-2014
    function isInfirmary($enc){
        global $db;
        $this->sql = "SELECT 
                          a.nr,
                          b.pid 
                        FROM
                          care_personell AS a 
                          INNER JOIN care_encounter AS b 
                            ON a.pid = b.pid 
                        WHERE b.encounter_nr = ?";
        $rs = $db->Execute($this->sql,$enc);
        if($rs){
            if($rs->RecordCount()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @author Nick B. ALcala
     * Get death date by encounter date
     * Created On 4/11/2014
     * @param  string $enc
     * @param  string $curr_enc
     * @return date string / false
     */
    function getDeathDate2($enc,$curr_enc)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate, p.death_encounter_nr
                                FROM care_person p
                                WHERE death_encounter_nr = ?");

        $rs = $db->Execute($this->sql, $enc);
        if($rs){
            if($rs->RecordCount()){
                $row = $rs->FetchRow();
                if($row['death_encounter_nr'] == $curr_enc){
                    return $row['deathdate'];
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @author Nick B. Alcala
     * Identify if patient is new born
     * Created On 4/21/2014
     * @param  String $enc
     * @return boolean
     */
    function isNewBorn($enc){
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      smod.ops_code 
                                    FROM
                                      seg_misc_ops AS smo 
                                      INNER JOIN seg_misc_ops_details AS smod 
                                        ON smod.refno = smo.refno 
                                      INNER JOIN seg_case_rate_special AS scrs 
                                        ON scrs.sp_package_id = smod.ops_code 
                                    WHERE smo.encounter_nr = " . $db->qstr($enc));
        $row = $db->GetRow($this->sql);
        return (count($row)) ? true : false;
    }

    /**
     * @author Nick B. Alcala
     * Identify if patient (new born) availed the hearing test
     * Created On 4/22/2014
     * @param  String $enc
     * @return boolean
     */
    function isHearingTestAvailed($enc,$isWellBaby){
        global $db;
        /* default with hearing test */
        $this->sql = $db->Prepare("SELECT 
                                      scrs.* 
                                    FROM
                                      seg_caserate_hearing_test AS scrs 
                                    WHERE scrs.`encounter_nr` = ?");

        if($isWellBaby){
            $rs = $db->Execute($this->sql,$enc);
            if($rs){
                if($rs->RecordCount() > 0){
                    $row = $rs->FetchRow();
                    return $row['is_availed'];
                }else{
                    $this->sql = $db->Prepare("INSERT INTO seg_caserate_hearing_test (encounter_nr,is_availed) VALUES (?,0)");
                    $rs = $db->Execute($this->sql,$enc);
                    if($rs){
                        return 0;
                    }else{
                        return 2;
                    }
                }
            }else{
                return 2;
            }
        }
    }

    /**
     * @author Nick B. Alcala
     * Add or update new born patient hearing test data
     * Created On 4/22/2014
     * @param  string $enc
     * @param  int    $value
     * @return boolean
     */
    function updateHearingTest($enc,$value){
        global $db;
        $row_count = 0;
        $this->sql = $db->Prepare("SELECT * FROM seg_caserate_hearing_test WHERE encounter_nr = ?");
        $rs = $db->Execute($this->sql,$enc);
        if($rs){
            $row_count = $rs->RecordCount();
        }

        if($row_count){
            $cols = array('encounter_nr' => $enc, 'is_availed' => $value);
            $pk   = array('encounter_nr');
            $rs = $db->Replace('seg_caserate_hearing_test',$cols,$pk);
        }

        return ($rs) ? true : false;
    }

    /**
     * Added by Nick, 4/23/2014
     * Discharge well babies
     * @return Boolean
     */
    function dischargeWellBaby($enc,$isNewBorn){
        if($isNewBorn){
            global $db;
            $this->sql = $db->Prepare("UPDATE 
                                          care_encounter 
                                        SET
                                          is_discharged = 1,
                                          discharge_date = NOW(), discharge_time = NOW() 
                                        WHERE encounter_nr = ?");
            $rs = $db->Execute($this->sql,$enc);
            if($rs){
            return true;
        }else{
            return false;
        }
    }
    }

    /**
    * @author Jarel
    * Created On 04/04/2014
    * get coverage details base on insurance id, area
    * and doctor number if from doctors
    * @param string hcare_id
    * @param string area
    * @return mixed result
    **/
    function getPerHCareCoverageDetails($hcare_id, $area)
    {
       global $db;


        $value = array($hcare_id,$area);


       $this->sql = $db->Prepare("SELECT * 
                                  FROM seg_billingcoverage_adjustment 
                                  WHERE hcare_id = ?
                                  AND bill_area = ?
                                  $docsql");

        if($this->result=$db->Execute($this->sql,$value)) {
            return $this->result;
        } else { return false; }
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * get doctor coverage details
    * @param string refno = bill_nr if final else T+encounter_nr
    * @param string hcare_id
    * @param string dr_nr
    * @param string area
    * @return mixed result
    **/
    function getDoctorCoverageDetails($refno,$hcare_id,$dr_nr,$area)
    {
        global $db;
        $value = array($hcare_id,$refno,$dr_nr,$area);
        $this->sql = $db->Prepare("SELECT dr_claim 
                                  FROM seg_billing_pf 
                                  WHERE hcare_id = ?
                                  AND bill_nr = ?
                                  AND dr_nr = ?
                                  AND role_area = ?");
        if($this->result=$db->Execute($this->sql,$value)) {
            return $this->result;
        } else { return false; }
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * get doctor coverage details
    * @param string refno = bill_nr if final else T+encounter_nr
    * @param string hcare_id
    * @param string dr_nr
    * @param string area
    * @return mixed result
    **/
    function getHCICoverageDetails($refno,$hcare_id,$area)
    {
        global $db;
        $value = array($hcare_id,$refno,$area);
        $this->sql = $db->Prepare("SELECT *
                                  FROM seg_billingcoverage_adjustment 
                                  WHERE hcare_id = ?
                                  AND ref_no = ?
                                  AND bill_area = ?");

        if($this->result=$db->Execute($this->sql,$value)) {
            return $this->result;
        } else { return false; }
    }


    /**
    * @author Genz
    * Created On 04/04/2014
    * get doctor coverage details
    * @param string refno = bill_nr if final else T+encounter_nr
    * @param string dr_nr
    * @return mixed result
    **/
    function getDoctorDetails($refno, $dr_nr)
    {
        global $db;
        $value = array($refno,$dr_nr);
        $this->sql = $db->Prepare("SELECT *
                                  FROM seg_billing_pf 
                                  WHERE bill_nr = ?
                                  AND dr_nr = ?");
        if($this->result=$db->Execute($this->sql,$value)) {
            return $this->result;
        } else { return false; }
    }

    function setPFCoverage($value)
    {
        $this->doctor_coverage += $value;
    }


    function getPFCoverage()
    {
        return($this->doctor_coverage);
    }


    function setPFDiscount($value)
    {
        $this->doctor_discount += $value;
    }


    function getPFDiscount()
    {
        return($this->doctor_discount);
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Save Doctors Coverage
    * @param string value
    * @return bool result
    **/
    function saveDoctorCoverage($value)
    {
        global $db;

        $sql = "INSERT INTO seg_billing_pf 
                (bill_nr, hcare_id, dr_nr, role_area, dr_charge, dr_claim)
                VALUES $value";
        if($result = $db->Execute($sql)){
            return true;
        }else{
            return false;
        }
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Save Doctors Coverage
    * @param string value
    * @return bool result
    **/
    function saveHCICoverage($value)
    {
        global $db;

        $sql = "INSERT INTO seg_billingcoverage_adjustment 
                (ref_no, bill_area, hcare_id, priority, coverage)
                VALUES $value";
        if($result = $db->Execute($sql)){
            return true;
        }else{
            return false;
        }
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Save Doctors Coverage
    * @param string value
    * @return bool result
    **/
    function saveOtherDiscount($value)
    {
        global $db;

        $sql = "INSERT INTO seg_billing_other_discounts 
                (refno, bill_areas, dr_nr, ar_discount, sc_discount)
                VALUES $value";
        if($result = $db->Execute($sql)){
            return true;
        }else{
            return false;
        }
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno
    * @return bool result
    **/
    function hasDoctorCoverage($refno)
    {
        global $db;
        $sql = "SELECT bill_nr FROM seg_billing_pf WHERE bill_nr =".$db->qstr($refno);
        $result = $db->Execute($sql);
        if($result){
            if($result->RecordCount())
                return true;
            else
                return false;
        }else
            return false;
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno
    * @return bool result
    **/
    function hasHCICoverage($refno)
    {
        global $db;
        $sql = "SELECT ref_no FROM seg_billingcoverage_adjustment WHERE ref_no =".$db->qstr($refno);
        $result = $db->Execute($sql);
        if($result){
            if($result->RecordCount())
                return true;
            else
                return false;
        }else
            return false;
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno
    * @return bool result
    **/
    function hasDoctorDiscount($refno)
    {
        global $db;
        $sql = "SELECT refno FROM seg_billing_other_discounts WHERE refno =".$db->qstr($refno);
        $result = $db->Execute($sql);
        if($result){
            if($result->RecordCount())
                return true;
            else
                return false;
        }else
            return false;
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno
    * @return bool result
    **/
    function clearDoctorCoverage($refno)
    {
        global $db;
        $sql = "DELETE FROM seg_billing_pf WHERE bill_nr =".$db->qstr($refno);
        $result = $db->Execute($sql);

        if($result)
            return true;
        else
            return false;
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno
    * @return bool result
    **/
    function clearHCICoverage($refno)
    {
        global $db;
        $sql = "DELETE FROM seg_billingcoverage_adjustment WHERE ref_no =".$db->qstr($refno);
        $result = $db->Execute($sql);

        if($result)
            return true;
        else
            return false;
    }


    /**
    * @author Jarel
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno
    * @return bool result
    **/
    function clearOtherDiscount($refno,$area='')
    {
        global $db;
        $area_cond = '';
        if($area=='doc')
            $area_cond = " bill_areas IN ('D1','D2','D3','D4') AND ";
        elseif($area=='hci')
            $area_cond = " bill_areas NOT IN ('D1','D2','D3','D4') AND ";

        $sql = "DELETE 
                FROM seg_billing_other_discounts 
                WHERE
                $area_cond 
                refno =".$db->qstr($refno);
        $result = $db->Execute($sql);

        if($result)
            return true;
        else
            return false;
    }


    //added by Nick 05/06/2014
    function getCaseTypeHist(){
        return $this->caseTypeHist;
    }

    //added by Nick 05-12-2014
    function updateOpDate($op_date, $refno, $ops_code, $entry_no){
        global $db;
        $this->sql = $db->Prepare("UPDATE 
                                      seg_misc_ops_details 
                                    SET
                                      op_date = DATE_FORMAT(".$db->qstr($op_date).",'%Y-%m-%d') 
                                    WHERE refno = ".$db->qstr($refno)." 
                                      AND ops_code = ".$db->qstr($ops_code)." 
                                      AND entry_no = ".$db->qstr($entry_no));

        $rs = $db->Execute($this->sql);
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    //added by Nick 05-21-2014
    function getMemCatHist(){
        return $this->memCatHist;
    }
    //added by poliam
    function GetWatcherRoom($enc){
        global $db;

        $this->sql ="SELECT days_stay,
                            rate
                    FROM seg_encounter_location_addtl
                    WHERE encounter_nr =".$db->qstr($enc)."
                    AND room_nr ='223'";
        $result = $db->Execute($this->sql);
        if($result){
            while ($row = $result->FetchRow()) {
            $this->watchersRoomDays = $row['days_stay'] +  $this->watchersRoomDays;
            $this->watchersRoomRate = $row['rate'];
            }
            return $this->watchersRoomDays*$this->watchersRoomRate;
        }else{
            return false;
        }
    }

    function GetSCroomdiscount($enc){
        global $db;

        $this->sql ="SELECT discountid 
                    FROM seg_billingapplied_discount
                    WHERE encounter_nr =".$db->qstr($enc)."
                    AND discountid = 'SC' ";
        $result = $db->Execute($this->sql);
        if($result){
            $this->sql2 ="SELECT type_nr, discharge_type_nr, nr
                        FROM care_encounter_location
                        WHERE encounter_nr = ".$db->qstr($enc)."
                        AND type_nr = '5'";
            $result2 = $db->Execute($this->sql2);
            //if naay unod ang care_encounter_location
            if($result2){
                while($row=$result2->FetchRow()){
                    if($row['discharge_type_nr'] == '4' && $row['type_nr'] == '5'){
                        if($this->prioritynoroom =='0'){
                            $this->prioritynoroom = $row['nr'];
                        }else if($this->prioritynoroom > $row['nr']){
                            $this->prioritynoroom = $row['nr'];
                        }
                    }else if($row['type_nr'] =='5'){
                        $this->prioritynoroom = $row['nr'];
                    }
                }
                $this->sql3 = "SELECT DATEDIFF(DATE_FORMAT(NOW(), '%Y-%m-%d'), date_from) AS diff
                                FROM care_encounter_location
                                WHERE encounter_nr = ".$db->qstr($enc)."
                                AND nr = ".$db->qstr($this->prioritynoroom)."";
                $result3 = $db->Execute($this->sql3);
                if($result3){
                    while($row2=$result3->FetchRow()){
                        $this->accomodationDateDiff = $row['diff'];
                    }
                }
                $this->sql4 = "SELECT days_stay 
                                FROM seg_encounter_location_addtl 
                                WHERE encounter_nr = ".$db->qstr($enc)."
                                AND room_nr <> '223'";
                $result4 = $db->Execute($this->sql4);
                if($result4){
                    while($row = $db->Execute($this->sql4)){
                        $this->accomodationDateDiff = $this->accomodationDateDiff + $row['days_stay'];
                    }
                }
                return $this->accomodationDateDiff;
            //if walay unod ang care_encounter_location
            }else{
                return false;
            }

            return $this->sql;
        }else{
            return false;
        }
    }


    function getOtherDiscounts($refno,$area,$dr_nr=0)
    {
        global $db;
        $refno = $db->qstr($refno);
        $area = $db->qstr($area);
        $dr_nr = $db->qstr($dr_nr);

        $sql = "SELECT sc_discount, 
                       ar_discount 
                FROM seg_billing_other_discounts 
                WHERE refno =  $refno
                AND bill_areas = $area
                AND dr_nr = $dr_nr";

        if($result=$db->Execute($sql)) {
            return $result;
        } else { return false; }
    }

    function getDrPf($dr_nr=0, $date_from, $date_to, $senior){
        global $db;

        switch($senior){
            case 'senior':
                $srWhere=" AND fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) >= 60";
            break;
            case 'non-senior':
                $srWhere=" AND fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) < 60";
            break;
            default:
                $srWhere="";
            break;
        }

        $this->sql = "SELECT 
                          UCASE(
                            fn_get_person_lastname_first (ce.`pid`)
                          ) patient,
                           CONCAT(
                            DATE_FORMAT(
                              IFNULL(ce.`admission_dt`,ce.`encounter_date`),
                              '%b %d, %Y'
                            ),
                            ' to ',
                            IFNULL(
                              DATE_FORMAT(
                                DATE(ce.`discharge_date`),
                                '%b %d, %Y'
                              ),
                              'present'
                            )
                          ) confinement,
                          cte.`name` encounter,
                        (SELECT 
                            GROUP_CONCAT(cif.`firm_id`) insurance 
                            FROM
                            seg_billing_coverage sbc 
                            LEFT JOIN care_insurance_firm cif 
                              ON cif.`hcare_id` = sbc.`hcare_id` 
                            WHERE sbc.`hcare_id` <> '18' 
                            AND sbc.`bill_nr` = sbe.`bill_nr`) insurance,
                          (SELECT 
                            SUM(dr_charge) 
                          FROM
                            seg_encounter_privy_dr 
                          WHERE encounter_nr = spp.`encounter_nr` 
                            AND dr_nr = spp.`dr_nr`) dr_charge,
                            (SELECT 
                                SUM(dr_charge) 
                              FROM
                                seg_encounter_dr_add 
                              WHERE encounter_nr = spp.`encounter_nr` 
                                AND dr_nr = spp.`dr_nr`) dr_charge_add,
                          (SELECT 
                            IFNULL(SUM(dr_claim), 0) 
                        FROM
                            seg_billing_pf 
                          WHERE bill_nr = sbe.bill_nr 
                            AND dr_nr = spp.dr_nr 
                            AND hcare_id = 18) phic,
                          (SELECT 
                            IFNULL(SUM(dr_claim), 0) 
                          FROM
                            seg_billing_pf 
                          WHERE bill_nr = sbe.bill_nr 
                            AND dr_nr = spp.dr_nr 
                            AND hcare_id <> 18) hmo,
                          (SELECT 
                            IFNULL(SUM(ar_discount), 0) 
                          FROM
                            seg_billing_other_discounts 
                          WHERE refno = sbe.`bill_nr` 
                            AND dr_nr = spp.`dr_nr`) discount,
                          (SELECT 
                            IFNULL(SUM(sc_discount), 0) 
                          FROM
                            seg_billing_other_discounts 
                          WHERE refno = sbe.`bill_nr` 
                            AND dr_nr = spp.`dr_nr`) senior,
                          spp.`amount`,
                          spp.`or_no` 
                          FROM
                          seg_pf_payments spp 
                          LEFT JOIN seg_billing_encounter sbe 
                            ON sbe.`encounter_nr` = spp.`encounter_nr` 
                            AND (
                              sbe.`is_deleted` <> 1 
                              OR sbe.`is_deleted` IS NULL
                            ) 
                          LEFT JOIN care_encounter ce 
                            ON ce.`encounter_nr` = spp.`encounter_nr` 
                          LEFT JOIN care_type_encounter cte 
                            ON cte.`type_nr` = ce.`encounter_type` 
                          LEFT JOIN care_person cp 
                            ON cp.`pid` = ce.`pid`
                            WHERE spp.dr_nr = ".$db->qstr($dr_nr).
                                " AND DATE(ce.admission_dt) BETWEEN ".$db->qstr($date_from).
                                " AND ".$db->qstr($date_to).
                                " AND ce.discharge_date IS NOT NULL".
                                " $srWhere ORDER BY patient ";

        if($this->result = $db->Execute($this->sql)){
            return $this->result;
        }

        return false;
    }

    public static function getEncounterDate($encounterNr){
        global $db;
        return $db->GetOne("SELECT encounter_date FROM care_encounter WHERE encounter_nr = ?",$encounterNr);
    }

    /***
     *
     */
    public function getFinalDiagnosis($encounterNr) {
        global $db;
        $this->sql = "SELECT 
                        ced.`code`, 
                        (CASE WHEN sed.description IS NOT NULL AND sed.`description` != '' THEN sed.description ELSE cie.description END) description, 
                        ced.`type_nr`  
                      FROM (care_encounter_diagnosis ced INNER JOIN care_icd10_en cie ON ced.code = cie.`diagnosis_code`)
                      LEFT JOIN seg_encounter_diagnosis sed ON ced.`encounter_nr` = sed.`encounter_nr` 
                           AND ced.code = sed.code
                      WHERE ced.encounter_nr = ".$db->qstr($encounterNr)." 
                        AND ced.type_nr = 1 
                        AND ced.`status` != 'deleted'
                      ORDER BY sed.create_time DESC 
                      LIMIT 1";
        if ($result=$db->Execute($this->sql)) {
            return $result->FetchRow();
        } else {
            return false;
        }
    }
    public function getOtherDiagnosis($encounterNr, $code)
    {
        global $db;
        $this->sql = "SELECT 
                ced.`diagnosis_description` as description
              FROM
                seg_encounter_diagnosis sed 
                LEFT JOIN care_encounter_diagnosis ced
                ON ced.`encounter_nr` = sed.`encounter_nr`
                AND sed.`code` = ced.`code`
              WHERE sed.`encounter_nr` = ".$db->qstr($encounterNr)."
                AND sed.`type_nr` != 1
                AND sed.code != ".$db->qstr($code)."
                AND (is_deleted IS NULL OR is_deleted = 0)                                   
                AND ced.`status` != 'deleted'";
        if ($result=$db->Execute($this->sql)) {
            return $result;
        } else {
            return false;
        }
    }
}//end class billing
