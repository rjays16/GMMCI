<?php

# Lightweight billing information helper class

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegBillingInfo extends Core {
  
  function SegBillingInfo() {
  }
  
  function getTotalBill($nr) {
    global $db;
    $nr = $db->qstr($nr);
    //$this->sql = "SELECT\n".
//        "(b.total_acc_charge-IFNULL(c.total_acc_coverage,0)+".
//        "b.total_med_charge-IFNULL(c.total_med_coverage,0)+".
//        "b.total_srv_charge-IFNULL(c.total_srv_coverage,0)+".
//        "b.total_ops_charge-IFNULL(c.total_ops_coverage,0)+".
//        "b.total_doc_charge-IFNULL(c.total_d1_coverage,0)-IFNULL(c.total_d2_coverage,0)-IFNULL(c.total_d3_coverage,0)-IFNULL(c.total_d4_coverage,0)+".
//        "b.total_msc_charge-IFNULL(c.total_msc_coverage,0))*(1-IFNULL(d.discount,0))".
//        "-b.total_prevpayments AS total\n".
//      "FROM seg_billing_encounter AS b\n".
//        "LEFT JOIN seg_billing_coverage AS c ON c.bill_nr=b.bill_nr\n".
//        "LEFT JOIN seg_billing_discount AS d ON d.bill_nr=b.bill_nr\n".
//      "WHERE b.bill_nr=$nr\n";
      
    $this->sql = "SELECT\n".
        "(IFNULL(b.total_acc_charge,0)-IFNULL(c.total_acc_coverage,0)) AS acc_net,\n".
        "(IFNULL(b.total_med_charge,0)-IFNULL(c.total_med_coverage,0)) AS med_net,\n".
        "(IFNULL(b.total_srv_charge,0)-IFNULL(c.total_srv_coverage,0)) AS srv_net,\n".
        "(IFNULL(b.total_ops_charge,0)-IFNULL(c.total_ops_coverage,0)) AS ops_net,\n".
        "(IFNULL(b.total_doc_charge,0)-IFNULL(c.total_d1_coverage,0)-IFNULL(c.total_d2_coverage,0)-IFNULL(c.total_d3_coverage,0)-IFNULL(c.total_d4_coverage,0)) AS doc_net,\n".
        "(IFNULL(b.total_msc_charge,0)-IFNULL(c.total_msc_coverage,0)) AS msc_net,\n".
        "IFNULL(d.discount,0) AS discount,\n".
        "IFNULL(b.total_prevpayments,0) AS prevpayments\n".
      "FROM seg_billing_encounter AS b\n".
        "LEFT JOIN seg_billing_coverage AS c ON c.bill_nr=b.bill_nr\n".
        "LEFT JOIN seg_billing_discount AS d ON d.bill_nr=b.bill_nr\n".
      "WHERE b.bill_nr=$nr\n";
    if($this->result=$db->GetOne($this->sql)) {
      $totals = $this->result;
      $this->sql = "SELECT\n".
        "";
      
      
      return $this->result;
    } else { return false; }
  }
  
  function GetBillingDetails($nr) {
    global $db;
    $nr = $db->qstr($nr);
    $this->sql = "SELECT p.pid,e.encounter_nr,\n".
        "CONCAT(p.name_first,' ',IF(name_middle,CONCAT(SUBSTRING(p.name_middle,1,1),'.'),''),' ',p.name_last) AS fullname,\n".
        "e.encounter_nr,b.bill_nr,b.bill_dte,b.bill_frmdte\n,".
        "b.total_acc_charge AS acc,".
        "b.total_med_charge AS med,".
        "b.total_sup_charge AS sup,".
        "b.total_srv_charge AS srv,".
        "b.total_ops_charge AS ops,".
        "b.total_doc_charge AS doc,".
        "b.total_msc_charge AS msc,".
        "b.total_prevpayments AS prev,\n".
        "(SELECT SUM(amount) FROM seg_grant_entries AS g WHERE g.ref_source='FB' AND g.ref_no=b.bill_nr) AS grants\n".
      "FROM seg_billing_encounter AS b\n".
        "INNER JOIN care_encounter AS e ON e.encounter_nr=b.encounter_nr\n".
        "LEFT JOIN care_person AS p ON p.pid=e.pid\n".
      "WHERE b.bill_nr=$nr\n";
    if($this->result=$db->Execute($this->sql)) {
      $row = $this->result->FetchRow();
      return $row;
    } else { return false; }
  }
  
  function GetBillingCoverage($nr) {
    global $db;
    $nr = $db->qstr($nr);
    $this->sql = "SELECT ".
        "(b.total_acc_coverage) AS acc,".
        "(b.total_med_coverage) AS med,".
        "(b.total_sup_coverage) AS sup,".
        "(b.total_srv_coverage) AS srv,".
        "(b.total_ops_coverage) AS ops,".
        "(IFNULL(b.total_d1_coverage,0)+IFNULL(b.total_d2_coverage,0)+IFNULL(b.total_d3_coverage,0)+IFNULL(b.total_d4_coverage,0)) AS doc,".
        "(b.total_msc_coverage) AS msc\n".
        "FROM seg_billing_coverage AS b\n".
      "WHERE b.bill_nr=$nr\n";
    if($this->result=$db->Execute($this->sql)) {
      $row = $this->result->FetchRow();
      return $row;
    } else { return false; }
  }
  
  function GetBillingDiscount($nr) {
    global $db;
    $nr = $db->qstr($nr);
    $this->sql = "SELECT SUM(discount) FROM seg_billing_discount AS b WHERE b.bill_nr=$nr";
    $this->result = $db->GetOne($this->sql);

    $discount = $this->result;
    if ((float)$discount < 0) $discount = 0.0;
    if ((float)$discount > 1.0) $discount = 1.0;
    return $discount;
  }
  
}

?>