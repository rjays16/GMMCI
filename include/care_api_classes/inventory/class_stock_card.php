<?php

require_once('./roots.php');

class StockCard {

    public function getPairedArea($tRefNo, $itemCode, $trCode, $isSource, $exclude_mvmnt=false) {
        global $db;
//        $db->debug = true;
        $this->sql = "SELECT a.area_name
           FROM (seg_inventory_ledger il INNER JOIN seg_sku_catalog sku2 ON il.sku_id = sku2.sku_id)
              INNER JOIN seg_areas a ON sku2.area_code = a.area_code
           WHERE il.tref_no = ? ";
              
        if(!$exclude_mvmnt)
            $this->sql .= " AND (CASE WHEN {$isSource} THEN il.mvmnt_qty < 0 ELSE il.mvmnt_qty > 0 END) ";
        
        $this->sql .= " AND sku2.item_code = ?
        AND il.tr_code = ? AND il.mvmnt_qty < 0 LIMIT 1";
        $this->result = $db->Execute($this->sql, array($tRefNo, $itemCode, $trCode));

        if($this->result) {
           $result = $this->result->FetchRow();
           return $result['area_name'];
        }

        if($trCode == 'ISS' || $trCode == 'TRA') {
            return '-';
        }
    }

    public function getPairedAreaTo($tRefNo, $itemCode, $trCode, $isSource, $exclude_mvmnt=false) {
        global $db;
//        $db->debug = true;
        $this->sql = "SELECT a.area_name
           FROM (seg_inventory_ledger il INNER JOIN seg_sku_catalog sku2 ON il.sku_id = sku2.sku_id
                                         INNER JOIN seg_issuance iss ON il.tref_no = iss.refno)
              INNER JOIN seg_areas a ON iss.area_code = a.area_code
           WHERE il.tref_no = ? ";
              
        if(!$exclude_mvmnt)
            $this->sql .= " AND (CASE WHEN {$isSource} THEN il.mvmnt_qty < 0 ELSE il.mvmnt_qty > 0 END) ";
        
        $this->sql .= " AND sku2.item_code = ?
        AND il.tr_code = ? LIMIT 1";
        $this->result = $db->Execute($this->sql, array($tRefNo, $itemCode, $trCode));

        if($this->result) {
           $result = $this->result->FetchRow();
           return $result['area_name'];
        }

        if($trCode == 'ISS' || $trCode == 'TRA') {
            return '-';
        }
    }

    public function getFormattedTransactionCode($tRefNo, $itemCode, $trCode, $isSource, $exclude_mvmnt) {
        switch(strtoupper($trCode)) {
            case 'RCV': return 'Delivery';
            case 'ADJ': return 'Adjustment';
            case 'SLE': return 'Sale';
            case 'CNL': return 'Cancelled';
            case 'TRA':
                return $isSource == 1 ? 'Transferred from ' . $this->getPairedArea($tRefNo, $itemCode, $trCode, $isSource, $exclude_mvmnt) : 'Transferred to ' . $this->getPairedAreaTo($tRefNo, $itemCode, $trCode, $isSource, $exclude_mvmnt);
            case 'ISS':
                return $isSource == 1 ? 'Issued from ' . $this->getPairedArea($tRefNo, $itemCode, $trCode, $isSource, $exclude_mvmnt) : 'Issued to ' . $this->getPairedAreaTo($tRefNo, $itemCode, $trCode, $isSource, $exclude_mvmnt);
            case 'CON': return 'Consumed';
            case 'RET': return 'Returned';
            default:
                return '-';
        }
    }
}