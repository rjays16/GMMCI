<?php
    require('./roots.php');
    require_once($root_path.'include/care_api_classes/class_core.php');

    class FisMapping extends Core {

        var $tb_transaction = 'seg_transaction_code';

        var $tb_lab_service = 'seg_lab_services';

        var $tb_lab_service_group = 'seg_lab_service_groups';

        var $tb_radio_services = 'seg_radio_services';

        var $tb_radio_service_group = 'seg_radio_service_groups';

        var $tb_pharma_product_main = 'care_pharma_products_main';

        var $tb_misc_services = 'seg_other_services';

        var $tb_account_map = 'seg_account_map';

        var $tb_account_chart = 'seg_account_chart';

        var $tb_insurance_firm = 'care_insurance_firm';

        var $tb_company = 'seg_company';

        var $tb_discount = 'seg_fis_discounts';

        var $tb_deposit = 'seg_fis_deposits';

        function GetAccountlist($TransCode=NULL){
            global $db;

            $where =" ";
            if($TransCode){
                $where = "WHERE code_id = ".$db->qstr($TransCode);
            }

            $this->sql = "SELECT SQL_CALC_FOUND_ROWS
                                code_id,
                                code_desc,
                                has_area,
                                code_area,
                                searchable
                        FROM ".$this->tb_transaction." ".
                        $where;

            if($this->result = $db->GetAll($this->sql)){
                return $this->result;
            }else{
                return false;
            }
        }

        function GetAccount($TransCode){
            global $db;

            $this->sql = "SELECT SQL_CALC_FOUND_ROWS
                                code_id,
                                code_desc,
                                code_area
                        FROM ".$this->tb_transaction." 
                        WHERE code_id = ".$db->qstr($TransCode);

            if($this->result = $db->GetRow($this->sql)){
                return $this->result;
            }else{
                return false;
            }
        }

        function GetLabService($name, $offset, $maxRows, $accountTransaction){
            global $db;

            if($name){
                $name = "%".$name."%";
                $where = "WHERE sls.`name` LIKE ".$db->qstr($name)."
                            AND sls.`status` NOT IN ('void', 'deleted')";
            }else{
                $where = "WHERE sls.`status` NOT IN ('void', 'deleted')";
            }
            
            $this->sql = "SELECT SQL_CALC_FOUND_ROWS 
                                sls.`service_code` AS item_code,
                                sls.`group_code`,
                                sls.`name` AS item_name,
                                slsg.`name` AS group_name,
                                IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                                stc.`code_area`
                        FROM ".$this->tb_lab_service." `sls`
                        LEFT JOIN ".$this->tb_lab_service_group." `slsg`
                        ON sls.`group_code` = slsg.`group_code`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON sam.`entry_id` = sls.`service_code`
                        AND sam.`transaction_code` = ".$db->qstr($accountTransaction)."
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = ".$db->qstr($accountTransaction)."
                        ".$where."
                        ORDER BY  sls.`name` ASC 
                        LIMIT $offset, $maxRows";
            if($this->result = $db->Execute($this->sql)){
                return $this->result;
            }else{
                return false;
            }

        }

        function GetRadioService($name, $offset, $maxRows, $accountTransaction){
            global $db;

            if($name){
                $name = "%".$name."%";
                $where = "WHERE srs.`name` LIKE ".$db->qstr($name)."
                            AND srs.`status` NOT IN ('void', 'deleted')";
            }else{
                $where = "WHERE srs.`status` NOT IN ('void', 'deleted')";
            }

            $this->sql = "SELECT SQL_CALC_FOUND_ROWS 
                                srs.`service_code` AS item_code,
                                srsg.`group_code`,
                                srs.`name` AS item_name,
                                srsg.`name` AS group_name,
                                IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                                stc.`code_area`
                        FROM ".$this->tb_radio_services." `srs` 
                        INNER JOIN ".$this->tb_radio_service_group." `srsg` 
                        ON srs.`group_code` = srsg.`group_code`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON sam.`entry_id` = srs.`service_code`
                        AND sam.`transaction_code` = ".$db->qstr($accountTransaction)."
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = ".$db->qstr($accountTransaction)."
                        ".$where."
                        ORDER BY srs.`name` ASC
                        LIMIT $offset, $maxRows";
             if($this->result = $db->Execute($this->sql)){
                return $this->result;
            }else{
                return false;
            }
        }

        function GetPharmaItems($name, $offset, $maxRows, $accountTransaction){
            global $db;

            if($name){
                 $name = "%".$name."%";
                 $where = "WHERE cppm.`artikelname` LIKE ".$db->qstr($name)."
                            AND cppm.`status` NOT IN ('void', 'deleted')";
            }else{
                $where = "WHERE cppm.`status` NOT IN ('void', 'deleted')";
            }

            $this->sql = "SELECT SQL_CALC_FOUND_ROWS
                            cppm.`bestellnum` AS item_code,
                            cppm.`artikelname` AS item_name,
                            IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                            stc.`code_area`
                        FROM ".$this->tb_pharma_product_main." `cppm`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON sam.`entry_id` = cppm.`bestellnum`
                        AND sam.`transaction_code` = ".$db->qstr($accountTransaction)."
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = ".$db->qstr($accountTransaction)."
                        ".$where."
                        ORDER BY cppm.`artikelname` ASC
                        LIMIT $offset, $maxRows";

            if($this->result = $db->Execute($this->sql)){
                return $this->result;
            }else{
                return false;
            }
        }

        function GetMiscItems($name, $offset, $maxRows, $accountTransaction){
            global $db;

            $where = "";
           
            if($name){
                $name = '%'.$name.'%';
                $where = "WHERE sos.`name` LIKE ".$db->qstr($name);
            }

            $this->sql ="SELECT SQL_CALC_FOUND_ROWS
                        sos.`service_code` AS item_code,
                        sos.`name` AS item_name,
                        IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                        stc.`code_area`
                        FROM ".$this->tb_misc_services." `sos`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON sos.`service_code`  = sam.`entry_id`
                        AND sam.`transaction_code` = ".$db->qstr($accountTransaction)."
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = ".$db->qstr($accountTransaction)."
                        ".$where."
                        ORDER BY sos.name ASC
                        LIMIT $offset, $maxRows";

            if($this->result = $db->Execute($this->sql)){
                return $this->result;
            }else{
                return false;
            }
        }

        function GetAccountChart(){
            global $db;

            $this->sql = "SELECT account_code,
                                account_name
                        FROM ".$this->tb_account_chart;

            if($this->result = $db->GetAll($this->sql)){
                return $this->result;
            }else{
                return false;
            }

        }

        function GetItemName($itemcode, $area){
            global $db;

            if($area == "LD"){
                $tb->use = $this->tb_lab_service;
                $where = "WHERE service_code =".$db->qstr($itemcode);
                $select = "name";
            }else if($area == "RD"){
                $tb->use = $this->tb_radio_services;
                $where = "WHERE service_code =".$db->qstr($itemcode);
                $select = "name";
            }else if($area == "PH"){
                $tb->use = $this->tb_pharma_product_main;
                $where = "WHERE bestellnum =".$db->qstr($itemcode);
                $select = "artikelname";
            }else{
                $tb->use =$this->tb_misc_services;
                $where = "WHERE service_code =".$db->qstr($itemcode);
                $select = "name";
            }

            $this->sql = "SELECT ".$select.
                        " FROM ".$tb->use."
                        ".$where;
            if($this->result = $db->Execute($this->sql)){
                while($row = $this->result->fetchRow()){
                    return $row[$select];
                }
            }else{
                return false;
            }

        }


        function checkExisting($map, $hasArea=NULL){
            global $db;
            if($hasArea){
                $where = "AND dept_code = ".$db->qstr($map['area']);
            }else{
                $where = " ";
            }
            $this->sql = "SELECT SQL_CALC_FOUND_ROWS id
                            FROM ".$this->tb_account_map."
                            WHERE transaction_code = ".$db->qstr($map['account_transaction'])."
                            AND entry_id = ".$db->qstr($map['item_code'])."
                            ".$where;

            if($this->result = $db->GetOne($this->sql))
                return $this->result;
            else
                return false;
            

        }

        function GetGroupCode($map){
            global $db, $HTTP_SESSION_VARS;

            if($map['area'] == 'LD'){
                 $this->sql = "SELECT group_code AS code
                        FROM ".$this->tb_lab_service."
                        WHERE service_code =".$db->qstr($map['item_code']);
            }else{
                $this->sql = "SELECT group_code AS code
                        FROM ".$this->tb_radio_services."
                        WHERE service_code =".$db->qstr($map['item_code']);
            }
           
            

            if($this->result = $db->Execute($this->sql)){
                while($row = $this->result->fetchRow()){
                    $group_code = $row['code'];
                    return $group_code;
                }
            }else{
                return false;
            }

            

        }

        function InsertFisMappingLR($map, $group_code){
            global $db, $HTTP_SESSION_VARS;

            $this->sql = "INSERT INTO ".$this->tb_account_map."
                            (transaction_code, 
                            dept_code, 
                            dept_subcode,
                            entry_id, 
                            income_account, 
                            cash_account,
                            tax_account, 
                            inventory_account, 
                            cogs_account,
                            credit_id, 
                            create_dt)
                        VALUE 
                        (".$db->qstr($map['account_transaction']).", 
                            ".$db->qstr($map['area']).", 
                            ".$db->qstr($group_code).",
                        ".$db->qstr($map['item_code']).", 
                        ".$db->qstr($map['account_income']).", 
                        ".$db->qstr($map['account_cash']).",
                        ".$db->qstr($map['account_tax']).", 
                        ".$db->qstr($map['account_Inventory']).", 
                        ".$db->qstr($map['account_COGS']).",
                        ".$db->qstr(date('Y-m-d H:i:s')).", 
                        ".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).")";
            
            if($this->result = $db->Execute($this->sql)){
                return true;
            }else{
                return false;
            }
        }

        function InsertFisMappingPT($map){
             global $db, $HTTP_SESSION_VARS;

             $this->sql = "INSERT INTO ".$this->tb_account_map."
                            (transaction_code, 
                            dept_code, 
                            dept_subcode,
                            entry_id, 
                            income_account, 
                            cash_account,
                            tax_account, 
                            inventory_account, 
                            cogs_account,
                            create_dt, 
                            create_id)
                        VALUE 
                        (".$db->qstr($map['account_transaction']).", 
                        ".$db->qstr($map['area']).", 
                        '',
                        ".$db->qstr($map['item_code']).", 
                        ".$db->qstr($map['account_income']).", 
                        ".$db->qstr($map['account_cash']).",
                        ".$db->qstr($map['account_tax']).", 
                        ".$db->qstr($map['account_Inventory']).", 
                        ".$db->qstr($map['account_COGS']).",
                        ".$db->qstr(date('Y-m-d H:i:s')).", 
                        ".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).")";
            
            if($this->result = $db->Execute($this->sql)){
                return true;
            }else{
                return false;
            }
        }

        function InsertFisMappingWCC($map){
            global $db, $HTTP_SESSION_VARS;

            $this->sql = "INSERT INTO ".$this->tb_account_map."
                            (transaction_code, 
                            dept_code, 
                            dept_subcode,
                            entry_id, 
                            debit_id,
                            credit_id,
                            create_dt, 
                            create_id)
                        VALUE
                            (".$db->qstr($map['account_transaction']).",
                            '',
                            '',
                            ".$db->qstr($map['item_code']).",
                            ".$db->qstr($map['account_debit']).",
                            ".$db->qstr($map['account_credit']).",
                            ".$db->qstr(date('Y-m-d H:i:s')).", 
                            ".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).")";

            if($this->result = $db->Execute($this->sql)){
                return true;
            }else{
                return false;
            }
        }

        function UpdateFisMappingLR($map, $group_code, $id){
            global $db, $HTTP_SESSION_VARS;

            $this->sql = "UPDATE ".$this->tb_account_map."
                            SET transaction_code = ".$db->qstr($map['account_transaction']).",
                            dept_code = ".$db->qstr($map['area']).",
                            dept_subcode = ".$db->qstr($group_code).",
                            entry_id = ".$db->qstr($map['item_code']).",
                            income_account = ".$db->qstr($map['account_income']).",
                            cash_account = ".$db->qstr($map['account_cash']).",
                            tax_account = ".$db->qstr($map['account_tax']).",
                            inventory_account = ".$db->qstr($map['account_Inventory']).",
                            cogs_account = ".$db->qstr($map['account_COGS']).",
                            create_dt = ".$db->qstr(date('Y-m-d H:i:s')).",
                            create_id = ".$db->qstr($HTTP_SESSION_VARS['sess_user_name'])."
                            WHERE id = ".$db->qstr($id);

             if($this->result = $db->Execute($this->sql)){
                return true;
            }else{
                return false;
            }

        }

        function UpdateFisMappingPT($map, $id){
            global $db, $HTTP_SESSION_VARS;

            $this->sql = "UPDATE ".$this->tb_account_map."
                            SET transaction_code = ".$db->qstr($map['account_transaction']).",
                            dept_code = ".$db->qstr($map['area']).",
                            entry_id = ".$db->qstr($map['item_code']).",
                            income_account = ".$db->qstr($map['account_income']).",
                            cash_account = ".$db->qstr($map['account_cash']).",
                            tax_account = ".$db->qstr($map['account_tax']).",
                            inventory_account = ".$db->qstr($map['account_Inventory']).",
                            cogs_account = ".$db->qstr($map['account_COGS']).",
                            create_dt = ".$db->qstr(date('Y-m-d H:i:s')).",
                            create_id = ".$db->qstr($HTTP_SESSION_VARS['sess_user_name'])."
                            WHERE id = ".$db->qstr($id);

            if($this->result = $db->Execute($this->sql)){
                return true;
            }else{
                return false;
            }
        }

        function UpdateFismappingWCC($map, $id){
            global $db, $HTTP_SESSION_VARS;

            $fields = array();
            $set = '';

          
             foreach($map as $key=>$value){
                $title = explode('_', $key);

                if($title[1] == 'account'){
                    $fields[] = $key.' = '.$db->qstr($value);
                }
            }

            if($fields)
                $set = ','.implode(', ', $fields);
            
           
            $this->sql = "UPDATE ".$this->tb_account_map."
                            SET transaction_code = ".$db->qstr($map['account_transaction']).",
                            entry_id = ".$db->qstr($map['item_code']).",
                            debit_id = ".$db->qstr($map['account_debit']).",
                            credit_id = ".$db->qstr($map['account_credit']).' '.$set.",
                            create_dt = ".$db->qstr(date('Y-m-d H:i:s')).",
                            create_id = ".$db->qstr($HTTP_SESSION_VARS['sess_user_name'])."
                            WHERE id = ".$db->qstr($id);

            if($this->result = $db->Execute($this->sql))
                return true;
            else
                return false;
            

        }

        function Getdbaccountid($trans_code, $area, $itemcode){
            global $db;

            $this->sql = "SELECT debit_id,
                                credit_id,
                                income_account,
                                cash_account,
                                tax_account,
                                inventory_account,
                                cogs_account 
                        FROM ".$this->tb_account_map."
                        WHERE entry_id = ".$db->qstr($itemcode)."
                        AND transaction_code =".$db->qstr($trans_code)."
                        AND dept_code =".$db->qstr($area);
            if($this->result = $db->GetRow($this->sql))
                return $this->result;
            else
                return false;
            
        }

        function Getinsurances($name, $offset, $maxRows, $transaction_code){
            global $db;

            $where = "";
            $transaction_code = $db->qstr($transaction_code);
            
            $where = "WHERE cif.`status` <> 'deleted'";

            if($name){
                $name = '%'.$name.'%';
                $where.= " AND cif.`name` LIKE ".$db->qstr($name);
            }

            $this->sql = "SELECT SQL_CALC_FOUND_ROWS
                        cif.`hcare_id` AS id_code,
                        cif.`name` AS name,
                        IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                        stc.`code_area`
                        FROM ".$this->tb_insurance_firm." `cif`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON cif.`hcare_id` = sam.`entry_id`
                        AND sam.`transaction_code` = $transaction_code
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = $transaction_code
                        ".$where."
                        ORDER BY cif.`name` ASC
                        LIMIT $offset, $maxRows";
                        
            if($this->result = $db->Execute($this->sql))
                return $this->result;
            else
                return false;
            
        }

        function GetCompany($name, $offset, $maxRows, $transaction_code){
            global $db;

            $where = "";
            $transaction_code = $db->qstr($transaction_code);

            if($name){
                $name = '%'.$name.'%';
                $where = "WHERE sc.`comp_name` LIKE ".$db->qstr($name);
            }

            $this->sql = "SELECT SQL_CALC_FOUND_ROWS
                                sc.`comp_id` AS id_code,
                                sc.`comp_name` AS name,
                                IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                                stc.`code_area`
                        FROM ".$this->tb_company." `sc`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON sc.`comp_id` = sam.`entry_id`
                        AND sam.`transaction_code` = $transaction_code
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = $transaction_code
                        ".$where." 
                        ORDER BY sc.`comp_name` ASC
                        LIMIT $offset, $maxRows";

            if($this->result = $db->Execute($this->sql))
                return $this->result;
            else
                return false;

        }

        function GetDiscount($name, $offset, $maxRows, $transaction_code){
            global $db;

            $where = "";
            $transaction_code = $db->qstr($transaction_code);

            if($name){
                $name = '%'.$name.'%';
                $where = "WHERE sd.`discountdesc` LIKE ".$db->qstr($name);
            }

            $this->sql ="SELECT SQL_CALC_FOUND_ROWS
                            sd.`discountid` AS id_code,
                            sd.`discountdesc` AS name,
                            IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                            stc.`code_area` 
                        FROM ".$this->tb_discount." `sd`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON sd.`discountid` = sam.`entry_id`
                        AND sam.`transaction_code` = $transaction_code
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = $transaction_code
                        ".$where." 
                        ORDER BY sd.`discountdesc` ASC
                        LIMIT $offset, $maxRows";

            if($this->result = $db->Execute($this->sql))
                return $this->result;
            else
                return false;
        }

        function GetDeposit($name, $offset, $maxRows, $transaction_code){
            global $db;

            $where = "";
            $transaction_code = $db->qstr($transaction_code);

            if($name){
                $name = '%'.$name.'%';
                $where = "WHERE sd.`discountdesc` LIKE ".$db->qstr($name);
            }

            $this->sql ="SELECT SQL_CALC_FOUND_ROWS
                            dp.`depositid` AS id_code,
                            dp.`depositname` AS name,
                            IF(sam.`id`, 'setup', 'not yet setup') AS setup,
                            stc.`code_area` 
                        FROM ".$this->tb_deposit." `dp`
                        LEFT JOIN ".$this->tb_account_map." `sam`
                        ON dp.`depositid` = sam.`entry_id`
                        AND sam.`transaction_code` = $transaction_code
                        LEFT JOIN ".$this->tb_transaction." `stc`
                        ON stc.`code_id` = $transaction_code
                        ".$where." 
                        ORDER BY dp.`depositname` ASC
                        LIMIT $offset, $maxRows";

            if($this->result = $db->Execute($this->sql))
                return $this->result;
            else
                return false;
        }

        function GetCDIName($itemcode, $CDI){
            global $db;
            
            $itemcode = $db->qstr($itemcode);
            
            switch ($CDI) {
                case 'dc':
                   $table = $this->tb_discount;
                   $select = "SELECT discountdesc ";
                   $where = "WHERE discountid = ".$itemcode;
                    break;
                case 'ins':
                    $table = $this->tb_insurance_firm;
                    $select = "SELECT name ";
                    $where = "WHERE hcare_id = ".$itemcode;
                    break;
                case 'com':
                    $table = $this->tb_company;
                    $select = "SELECT comp_name ";
                    $where = "WHERE comp_id = ".$itemcode;
                    break;
                default:
                    $table = "";
                    $select = "";
                    break;
            }

            $this->sql = $select." 
                         FROM ".$table."
                         ".$where;
            if($this->result = $db->GetOne($this->sql))
                return $this->result;
            else
                return false;
            
        }

    }
?>
