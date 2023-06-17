<?php

/* Created by: Kenneth Jan Malubay
   	For passing data in FIS from HIS.,*/

require "./roots.php";
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

define('FIS_TRUE',200); #Added By Jarel 10/16/2014
define('CHARGE_ITEM',9);
define('CASH_ITEM',8);
define('BILL_PAYMENT',10);
define('DEPOSIT',11);
define('REVERSED_DEPOSIT',15);
define('DEBIT_ADJUSTMENT',16);
define('PF',12);
define('ACC',7);
define('ARD',1);
define('ARA',2);
define('OR_CODE',13);
define('DISCOUNT_PAY',5);
define('DISCOUNT_CHARGE',6);
define('COMPANY_PAY', 3);
define('COMPANY_CHARGE', 4);
define('PF_PAYMENT', 14);

//discounts
define('senior_discount', 1);
define('arbitrary_discount', 2);

//vat and taxes
define('default_vat', 1.12);
define('default_output_tax', 0.12);

class Rest_Curl{

	function inpatientRadioItem($refno, $frombill=false, $bill_nr=null, $bill_date=null){

		$data = $this->getServiceAmount($refno, $frombill); // if frombill is true, $refno will contain the encounter number

		$debit = array();
		$credit = array();
		$debit_amount = array();
		$credit_amount = array();
		
		if($data){
		foreach ($data as $key => $value) {
			if($value['is_cash'] != '1'){
				$account_codes = $this->getAccountCodes($value['service_code'], 'RD', CHARGE_ITEM);
		
					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$this->getDefaultAccountCode('RD', CHARGE_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$this->getDefaultAccountCode('RD', CHARGE_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
				}
					}

					$credit_amount[$code['income_account']] += $value['price_charge'];
					$debit_amount[$code['cash_account']] += $value['price_charge'];

				$pid = $value['pid'];
					$refno = ($bill_nr == null ? $value['refno'] : $bill_nr);
				$date = $value['request_date'];
				$time = $value['request_time'];
				$enc = $value['encounter_nr'];
			}
		}

		foreach($debit_amount AS $key => $value){
			$debit[] = array('debitid'=>$key, 'amount'=>$value);
		}

		foreach($credit_amount AS $key => $value){
			$credit[] = array('creditid'=>$key, 'amount'=>$value);
		}

		$this->checkCustomer('', $pid);

		$accounts = array('debits'=>$debit, 'credits'=>$credit);
			$accounts = base64_encode(json_encode($accounts));
					
			$data_fis = array(
					'refno' => $refno,
					'oldrefno' => $refno,
					'pid'=> $pid,
					'src'=> 'RAD',
						'trdate' => strtotime((!$frombill ? $date.' '.$time : $bill_date)),
						'time' => date('H:i:s', strtotime((!$frombill ? $time : $bill_date))),
					'accounts' => $accounts,
					'encounter_nr' => $enc,
					'person_type' => '2'
					);

		
			// end of sample data
		
			// target web service
		
			$url = "/modules/api/customer/charge";
		
			// POST request to URL with the data
			$result = $this->rest_post($url, $data_fis);
		
			return $result;
		}
		else
			return true;
	}

	// updated by Margaret Manalo 06/25/14
	function walkinRadioItem($data){
		extract($data);

		$data = array(
					'refno' => $or,
					'oldrefno' => $or,
					'pid' => $pid,
					'src' => 'RAD',
					'trdate' => date('m/d/Y', strtotime($date)),
					'time' => date('H:i:s', strtotime($date)),
					'encounter_nr' => $enc,
					'person_type' => '2',
					'accounts' => $accounts
			);

		$url = "/modules/api/customer/payrequest";

		$result = $this->rest_post($url, $data);

		return $result;
	}
	// end Margaret

	function getUnitName($unit_code){
		global $db;

		return $db->GetOne("SELECT 
							  unit_name 
							FROM
							  seg_unit 
							WHERE unit_id = ".$db->qstr($unit_code));
	}

	//added by maimai 03-05-2015
	function insertItem($data){

		$data = array(
				"id"=>$data['id'],
				"desc"=>$data['item_desc'],
				"category"=>$data['item_category'],
				"unit"=>$this->getUnitName($data['unit'])
			);

		$url = "/modules/api/item/add";
		$result = $this->rest_post($url, $data);

		return $result;
	}

	function updateItem($data){

		$data = array(
				"id"=>$data['id'],
				"old_id"=>$data['id'],
				"desc"=>$data['item_desc'],
				"category"=>$data['item_category'],
				"unit"=>$this->getUnitName($data['unit']) 
			);

		$url = "/modules/api/item/update";
		$result = $this->rest_post($url, $data);

		return $result;
	}

	function deleteItem($data){

		$data = array(
				"id"=>$data['id']
			);

		$url = "/modules/api/item/delete";
		$result = $this->rest_post($url, $data);

		return $result;
	}
	//end maimai

function inpatientLabItem($refno, $frombill=false, $bill_nr=null, $bill_date=null){

		$data = $this->getLabServices($refno, $frombill); // if frombill is true, $refno will contain the encounter number

		if($data){
			if(!$frombill)
		$this->checkCustomer('', $pid);

		$debit_amount = array();
		$credit_amount = array();
		$debit = array();
		$credit = array();
		
		foreach ($data as $key => $value){
			if($value['is_cash'] != '1'){
				$account_codes = $this->getAccountCodes($value['service_code'], 'LD', CHARGE_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$this->getDefaultAccountCode('LD', CHARGE_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$this->getDefaultAccountCode('LD', CHARGE_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
				}
					}

					if($is_ok){
						$credit_amount[$code['income_account']] += floatval($value['price_charge']);
						$debit_amount[$code['cash_account']] += floatval($value['price_charge']);
					}

				$pid = $value['pid'];
					$refno = ($bill_nr == null ? $value['refno'] : $bill_nr);
				$date = $value['serv_dt'];
				$time = $value['serv_tm'];
				$enc = $value['encounter_nr'];
			}
			}

			if($debit_amount && $credit_amount){
		foreach($debit_amount AS $key => $value){
			$debit[] = array('debitid'=>$key, 'amount'=>$value);
		}

		foreach($credit_amount AS $key => $value){
			$credit[] = array('creditid'=>$key, 'amount'=>$value);
		}

		$accounts = array('debits'=>$debit, 'credits'=>$credit);
		$accounts = base64_encode(json_encode($accounts));
		
		
		$data = array(
					'refno' => $refno,
					'oldrefno' => $refno,
					'pid'=> $pid,
					'src'=>'LAB',
						'trdate' => strtotime((!$frombill ? $date.' '.$time : $bill_date)),
						'time' => date('H:i:s', strtotime((!$frombill ? $time : $bill_date))),
					'accounts' => $accounts,
					'encounter_nr' => $enc,
					'person_type' => '2'
				);
		// end of sample data

		// target web service

		$url = "/modules/api/customer/charge";
		
		// POST request to URL with the data

		$result = $this->rest_post($url, $data);

		return $result;
	}
		else
						return true;
	}
		else
			return true;
	}

	// updated by Margaret Manalo 06/25/14
	function walkinLabItem($data){
		extract($data);
		
		$data = array(
					'refno' => $or,
					'oldrefno' => $or,
					'pid' => $pid,
					'src' => 'LAB',
					'trdate' => date('m/d/Y', strtotime($date)),
					'time' => date('H:i:s', strtotime($date)),
					'encounter_nr' => $enc,
					'person_type' => '2',
					'accounts' => $accounts
		);

		$url = "/modules/api/customer/payrequest";

		$result = $this->rest_post($url, $data);
	}
	// end Margaret

	function inpatientPharmaItem($refno, $frombill=false, $bill_nr=null, $bill_date=null){
		$inv_obj = new SKUInventory;

		$items = $this->getPharmaItems($refno, $frombill); // if frombill is true, $refno will contain the encounter number

		$debit_amount = array();
		$credit_amount = array();
		$debit = array();
		$credit = array();

		if($items){
			foreach($items AS $key => $value){
				if($value['serve_status'] == 'S'){

					$avg_cost = $inv_obj->getItemAvgCost($value['bestellnum']);

					// if($avg_cost){
					$account_codes = $this->getAccountCodes($value['bestellnum'], 'PH', CHARGE_ITEM);

						$code = array();
						$is_ok = true;
						if($account_codes){
					foreach($account_codes AS $codes_key => $codes_value){
								$code['income_account'] = $codes_value['income_account'];
								$code['cash_account'] = $codes_value['cash_account'];
								$code['cogs_account'] = $codes_value['cogs_account'];
								$code['inventory_account'] = $codes_value['inventory_account'];
								$this->getDefaultAccountCode('PH', CHARGE_ITEM, $code);
							}
							/**
							 * Check if everything contains an account, if not, continue to the next data
							 */
							foreach ($code as $code_key => $code_value) {
								if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
									$is_ok = false;
							}
						}
						else{
							$code['income_account'] = 0;
							$code['cash_account'] = 0;
							$code['cogs_account'] = 0;
							$code['inventory_account'] = 0;
							$this->getDefaultAccountCode('PH', CHARGE_ITEM, $code);
							/**
							 * Check if everything contains an account, if not, continue to the next data
							 */
							foreach ($code as $code_key => $code_value) {
								if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
									$is_ok = false;
					}
				}
						
						if($is_ok){
							$credit_amount[$code['income_account']] += $value['pricecharge'] * $value['quantity'];
							$debit_amount[$code['cash_account']] += $value['pricecharge'] * $value['quantity'];
							$debit_amount[$code['cogs_account']] += $avg_cost * $value['quantity'];
							$credit_amount[$code['inventory_account']] += $avg_cost * $value['quantity'];
						}
						
					// }
					// else
						// return true;
				}

				$pid = $value['pid'];
				$orderdate = $value['orderdate'];
				$enc = $value['encounter_nr'];
			}

			if($debit_amount){
		foreach($debit_amount AS $key => $value){
			$debit[] = array('debitid'=>$key, 'amount'=>$value);
		}
			}

			if($credit_amount){
		foreach($credit_amount AS $key => $value){
			$credit[] = array('creditid'=>$key, 'amount'=>$value);
		}
			}

			if($debit_amount && $credit_amount){
		$accounts = array('debits'=>$debit, 'credits'=>$credit);
		$accounts = base64_encode(json_encode($accounts));
		
		$data = array(
						'refno' => ($bill_nr == null ? $refno : $bill_nr),
						'oldrefno' => ($bill_nr == null ? $refno : $bill_nr),
					'pid'=> $pid,
					'src'=>'PHA',
						'trdate' => strtotime((!$frombill ? $orderdate : $bill_date)),
						'time' => date('H:i:s', strtotime((!$frombill ? $orderdate : $bill_date))),
					'accounts' => $accounts,
					'encounter_nr' => $enc,
					'person_type' => '2'
				);

		$url = "/modules/api/customer/charge";

		$result = $this->rest_post($url, $data);

			return $result;
		}
		else
			return true;
	}
		else
			return true;
	}

	function walkinPharmaItem($data){
		extract($data);
		
		$data = array(
					'refno' => $or,
					'oldrefno' => $or,
					'pid' => $pid,
					'src' => 'PHA',
					'trdate' => date('m/d/Y', strtotime($date)),
					'time' => date('H:i:s', strtotime($date)),
					'encounter_nr' => $enc,
					'person_type' => '2',
					'accounts' => $accounts
		);

		$url = "/modules/api/customer/payrequest";

		$result = $this->rest_post($url, $data);
	}

	function walkinPharmaServe($refno){
		$inv_obj = new SKUInventory;

		$items = $this->getPharmaItems($refno);

		$debit_amount = array();
		$credit_amount = array();
		$debit = array();
		$credit = array();

		if($items){
			foreach($items AS $key => $value){
				if($value['serve_status'] == 'S'){

					$avg_cost = $inv_obj->getItemAvgCost($value['bestellnum']);

					// if($avg_cost){
					$account_codes = $this->getAccountCodes($value['bestellnum'], 'PH', CASH_ITEM);

						$code = array();
						$is_ok = true;
						if($account_codes){
					foreach($account_codes AS $codes_key => $codes_value){
								// $code['income_account'] = $codes_value['income_account'];
								// $code['cash_account'] = $codes_value['cash_account'];
								$code['cogs_account'] = $codes_value['cogs_account'];
								$code['inventory_account'] = $codes_value['inventory_account'];
								$this->getDefaultAccountCode('PH', CASH_ITEM, $code);
							}
							/**
							 * Check if everything contains an account, if not, continue to the next data
							 */
							foreach ($code as $code_key => $code_value) {
								if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
									$is_ok = false;
							}
						}
						else{
							// $code['income_account'] = 0;
							// $code['cash_account'] = 0;
							$code['cogs_account'] = 0;
							$code['inventory_account'] = 0;
							$this->getDefaultAccountCode('PH', CASH_ITEM, $code);
							/**
							 * Check if everything contains an account, if not, continue to the next data
							 */
							foreach ($code as $code_key => $code_value) {
								if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
									$is_ok = false;
							}
						}

						if($is_ok){
						// $credit_amount[$codes_value['income_account']] += $value['pricecharge'] * $value['quantity'];
						// $debit_amount[$codes_value['cash_account']] += $value['pricecharge'] * $value['quantity'];
							$debit_amount[$code['cogs_account']] += $avg_cost * $value['quantity'];
							$credit_amount[$code['inventory_account']] += $avg_cost * $value['quantity'];
					}
						
					// }
					// else
						// return true;
				}

				$pid = $value['pid'];
				$orderdate = $value['orderdate'];
				$enc = $value['encounter_nr'];
			}
		

		$this->checkCustomer('', $pid);

			if($debit_amount){
		foreach($debit_amount AS $key => $value){
				// if($key == '4212000')
				$debit[] = array('debitid'=>$key, 'amount'=>$value);
		}
			}

			if($credit_amount){
		foreach($credit_amount AS $key => $value){
				// if($key == '1511000')
				$credit[] = array('creditid'=>$key, 'amount'=>$value);
		}
			}

			if($debit_amount && $credit_amount){
		$accounts = array('debits'=>$debit, 'credits'=>$credit);
		$accounts = base64_encode(json_encode($accounts));
		
		$data = array(
					'refno' => $refno,
					'oldrefno' => $refno,
					'pid'=> $pid,
					'src'=>'PHA',
					'trdate' => strtotime($orderdate),
					'time' => date('H:i:s', strtotime($orderdate)),
					'accounts' => $accounts,
					'encounter_nr' => $enc,
					'person_type' => '2'
				);

		$url = "/modules/api/customer/servepharmarequest";

		$result = $this->rest_post($url, $data);

			return $result;
		}
		else
			return true;
	}
		else
			return true;
	}

	function servicePayment($data){
		extract($data);

		$data = array(
					'refno' => $or,
					'oldrefno' => $or,
					'pid' => $pid,
					'src' => 'SRV',
					'trdate' => date('m/d/Y', strtotime($date)),
					'time' => date('H:i:s', strtotime($date)),
					'encounter_nr' => $enc,
					'person_type' => '2',
					'accounts' => $accounts
		);

		$url = "/modules/api/customer/paybill";

		$result = $this->rest_post($url, $data);
	}

	function comPayment($data){
		extract($data);

		$account_codes = $this->getAccountCodes($comp_id, '', COMPANY_PAY);

		$code = array();
		$is_ok = true;
		if($account_codes){
			foreach($account_codes AS $codes_key => $codes_value){
				$code['credit_id'] = $codes_value['credit_id'];
				$code['debit_id'] = $codes_value['debit_id'];
				$this->getDefaultAccountCode('', COMPANY_PAY, $code);
			}
			/**
			 * Check if everything contains an account, if not, continue to the next data
			 */
			foreach ($code as $code_key => $code_value) {
				if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
					$is_ok = false;
			}
		}
		else{
			$code['credit_id'] = 0;
			$code['debit_id'] = 0;
			$this->getDefaultAccountCode('', COMPANY_PAY, $code);
			/**
			 * Check if everything contains an account, if not, continue to the next data
			 */
			foreach ($code as $code_key => $code_value) {
				if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
					$is_ok = false;
			}
		}

		if($is_ok){
			$credit[] = array('creditid'=>$code['credit_id'], 'amount'=>floatval($amount));
			$debit[] = array('debitid'=>$code['debit_id'], 'amount'=>floatval($amount));
			
		$items = array('debits'=>$debit, 'credits'=>$credit);
		$items = base64_encode(json_encode($items));

		$data = array(
				'refno' => $or,
				'oldrefno' => $or,
				'pid' => $pid,
				'trdate' => date('m/d/Y', strtotime($date)),
				'time' => date('H:i:s', strtotime($date)),
				'src' => 'COM',
				'accounts' => $items,
				'encounter_nr' => $enc,
				'person_type' => '2');

		$url = "/modules/api/customer/paybill"; 

		$result = $this->rest_post($url, $data);

			return $result;
		}
		else
			return true;
		
	}


	function walkinMiscItem($data){
		extract($data);

		$data = array(
					'refno' => $or,
					'oldrefno' => $or,
					'pid' => $pid,
					'src' => 'MSC',
					'trdate' => date('m/d/Y', strtotime($date)),
					'time' => date('H:i:s', strtotime($date)),
					'encounter_nr' => $enc,
					'person_type' => '2',
					'accounts' => $accounts
			);

		$url = "/modules/api/customer/payrequest";

		$result = $this->rest_post($url, $data);
	}

	function inpatientMiscRequest($refno, $frombill=false, $bill_nr=null, $bill_date=null, $encounter=null, $from_date=null, $pid=null){
		$debit_amount = array();
		$credit_amount = array();
		$debit = array();
		$credit = array();

		$items_msc = $this->getMiscFromBilling($encounter,$bill_date,$from_date);
		if($items_msc){
			foreach($items_msc AS $row){
				$account_codes = $this->getAccountCodes($row['service_code'], 'OT', CHARGE_ITEM);

				$code = array();
				$is_ok = true;
				if($account_codes){
					foreach($account_codes AS $codes_key => $codes_value){
						$code['income_account'] = $codes_value['income_account'];
						$code['cash_account'] = $codes_value['cash_account'];
						$this->getDefaultAccountCode('OT', CHARGE_ITEM, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['income_account'] = 0;
					$code['cash_account'] = 0;
					$this->getDefaultAccountCode('OT', CHARGE_ITEM, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				if($is_ok){
				// foreach($account_codes AS $codes_key => $codes_value){
					$credit_amount[$code['income_account']] += $row['total_chrg'];
					$debit_amount[$code['cash_account']] += $row['total_chrg'];
				// }
				}
			}//End Foreach
		}//End If

		$data = $this->getMiscServiceAmount($refno, $frombill);

		if($data){
			foreach($data AS $key => $value){
				$account_codes = $this->getAccountCodes($value['service_code'], 'OT', CHARGE_ITEM);

				$code = array();
				$is_ok = true;
				if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
						$code['income_account'] = $codes_value['income_account'];
						$code['cash_account'] = $codes_value['cash_account'];
						$this->getDefaultAccountCode('OT', CHARGE_ITEM, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['income_account'] = 0;
					$code['cash_account'] = 0;
					$this->getDefaultAccountCode('OT', CHARGE_ITEM, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
				}
				}
				if($is_ok){
				// foreach($account_codes AS $codes_key => $codes_value){
					$credit_amount[$code['income_account']] += $value['chrg_amnt'] * $value['quantity'];
					$debit_amount[$code['cash_account']] += $value['chrg_amnt'] * $value['quantity'];
				// }
				}
				$pid = $value['pid']; // reinitialize pid here if ever this function would be used by other module (aside from the parameter)
				$orderdate = $value['chrge_dte'];
				$encounter = $value['encounter_nr']; // reinitialize pid here if ever this function would be used by other module (aside from the parameter)
			}
		}

		if($data || $items_msc){
		foreach($debit_amount AS $key => $value){
			$debit[] = array('debitid'=>$key, 'amount'=>$value);
		}

		foreach($credit_amount AS $key => $value){
			$credit[] = array('creditid'=>$key, 'amount'=>$value);
		}

		$accounts = array('debits'=>$debit, 'credits'=>$credit);
		$accounts = base64_encode(json_encode($accounts));
			
		$data_fis = array(
					'refno' => ($bill_nr == null ? $refno : $bill_nr),
					'oldrefno' => ($bill_nr == null ? $refno : $bill_nr),
					'pid'=> $pid,
					'src'=> 'MSC',
					'trdate' => strtotime((!$frombill ? $orderdate : $bill_date)),
					'time' => date('H:i:s', strtotime((!$frombill ? $orderdate : $bill_date))),
					'accounts' => $accounts,
					'encounter_nr' => $encounter,
					'person_type' => '2'
				);
		// end of sample data

		// target web service

		$url = "/modules/api/customer/charge";

		// POST request to URL with the data
		$result = $this->rest_post($url, $data_fis);

			return $result;
		}
		else
			return true;
	}

	function inpatientDeposits($encounter_nr, $frombill=false, $bill_nr=null, $bill_date=null){
		$debit = array();
		$credit = array();

		$or_no = 0;
		$orderdate = null;
		$enc = null;
		$pid = null;

		$data = $this->getBillingDeposits($encounter_nr);

		if($data){
			foreach($data AS $key => $value){
				// $account_codes = $this->getAccountCodes(DEPOSIT, '', DEPOSIT);

				// foreach ($account_codes as $key => $value) {
				// 	$credit[] = array('creditid'=>$value['credit_id'], 'amount'=>floatval($amount));
				// 	$debit[] = array('debitid'=>$value['debit_id'], 'amount'=>floatval($amount));
				// }

				$account_codes = $this->getAccountCodes(REVERSED_DEPOSIT, '', DEPOSIT);

				if($account_codes){
					foreach($account_codes AS $codes_key => $codes_value){
						// $credit[] = array('creditid'=>$codes_value['credit_id'], 'amount'=>floatval($value['amount_due']));
						// $debit[] = array('debitid'=>$codes_value['debit_id'], 'amount'=>floatval($value['amount_due']));
						$credit_amount[$codes_value['credit_id']] += $value['amount_due'];
						$debit_amount[$codes_value['debit_id']] += $value['amount_due'];
					}
				}
				else
					return true;

				$pid = $value['pid'];
				$or_no = ($or_no != 0 ? $value['or_no'] : 0); // THIS IS JUST IN CASE
				$orderdate = $value['or_date'];
				$enc = $value['encounter_nr'];
			}
		
			if($debit_amount && $credit_amount){
			foreach($debit_amount AS $key => $value){
				$debit[] = array('debitid'=>$key, 'amount'=>floatval($value));
			}

			foreach($credit_amount AS $key => $value){
				$credit[] = array('creditid'=>$key, 'amount'=>floatval($value));
			}

			$this->checkCustomer('', $pid);

			$accounts = array('debits'=>$debit, 'credits'=>$credit);
			$accounts = base64_encode(json_encode($accounts));
				
			$data_fis = array(
						'refno' => ($bill_nr == null ? $or_no : $bill_nr),
						'oldrefno' => ($bill_nr == null ? $or_no : $bill_nr),
						'pid'=> $pid,
						'src'=> 'DEP',
						'trdate' => strtotime((!$frombill ? $orderdate : $bill_date)),
						'time' => date('H:i:s', strtotime((!$frombill ? $orderdate : $bill_date))),
						'accounts' => $accounts,
						'encounter_nr' => $encounter_nr,
						'person_type' => '2'
					);
			// end of sample data

			// target web service

			$url = "/modules/api/customer/reverse_deposit";

			// POST request to URL with the data
			$result = $this->rest_post($url, $data_fis);

			return $result;	
		}
		else
			return true;
	}
		else
			return true;
	}

	
	/* 
	* Modified by Jarel 10/15/2014
	* Change the first arguments to amount from list
	* Add return for validation
	*/
	function inpatientAccItem($amount, $bill_date, $pid, $bill_nr, $enc){
		$items = array();
		$debit = array();
		$credit = array();

		if ($amount>0) {
			$account_codes = $this->getAccountCodes(ACC, '', ACC);

			if($account_codes){
			foreach ($account_codes as $key => $value) {
				$credit[] = array('creditid'=>$value['credit_id'], 'amount'=>floatval($amount));
				$debit[] = array('debitid'=>$value['debit_id'], 'amount'=>floatval($amount));
			}
			}
			else
				return true;

			if($debit && $credit){
			$items = array('debits'=>$debit, 'credits'=>$credit);
		$items = base64_encode(json_encode($items));

			$data = array(
					'refno' => $bill_nr,
					'oldrefno' => $bill_nr,
					'pid' => $pid,
					'trdate' => strtotime($bill_date),
					'time' => date('H:i:s', strtotime($bill_date)),
					'src' => 'ACC',
					'accounts' => $items,
					'encounter_nr' => $enc,
					'person_type' => '2');

			$url = "/modules/api/customer/charge"; 

			$result = $this->rest_post($url, $data);
			}
			else
				return true;

		}#End IF

		if($result['code']==FIS_TRUE || $amount<=0){
			return true;
		}else{
			return false;
		}
	}


	/* 
	* Modified by Jarel 10/15/2014
	* Add return for validation
	*/
	function inpatientChargeItems($encounter, $bill_date, $pid, $bill_nr, $from_date, $or_amount){
		$inv_obj = new SKUInventory;

		$debits_pha = array();
		$credits_pha = array();
		$debits_msc = array();
		$credits_msc = array();
		$debits_or = array();
		$credits_or = array();


		$items = $this->getPharmaFromBilling($encounter,$bill_date,$from_date);
		if($items){
			foreach($items AS $row){
				$avg_cost = $inv_obj->getItemAvgCost($row['bestellnum']);

				$account_codes = $this->getAccountCodes($row['bestellnum'], 'PH', CHARGE_ITEM);

				$code = array();
				$is_ok = true;
				if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
						$code['income_account'] = $codes_value['income_account'];
						$code['cash_account'] = $codes_value['cash_account'];
						$code['cogs_account'] = $codes_value['cogs_account'];
						$code['inventory_account'] = $codes_value['inventory_account'];
						$this->getDefaultAccountCode('PH', CHARGE_ITEM, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
				}
				}
				else{
					$code['income_account'] = 0;
					$code['cash_account'] = 0;
					$code['cogs_account'] = 0;
					$code['inventory_account'] = 0;
					$this->getDefaultAccountCode('PH', CHARGE_ITEM, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($is_ok){
					$credit_pha[$code['income_account']] += $row['itemcharge'];
					$debit_pha[$code['cash_account']] += $row['itemcharge'];
					$debit_pha[$code['cogs_account']] += $avg_cost * $row['qty'];
					$credit_pha[$code['inventory_account']] += $avg_cost * $row['qty'];
				}
			}//End Foreach
		}//End If

		// $items_msc = $this->getMiscFromBilling($encounter,$bill_date,$from_date);
		// if($items_msc){
		// 	foreach($items_msc AS $row){
		// 		$account_codes = $this->getAccountCodes($row['service_code'], 'OT', CHARGE_ITEM);

		// 		foreach($account_codes AS $codes_key => $codes_value){
		// 			$credit_msc[$codes_value['income_account']] += $row['total_chrg'];
		// 			$debit_msc[$codes_value['cash_account']] += $row['total_chrg'];
		// 		}
		// 	}//End Foreach
		// }//End If

		$account_codes = $this->getAccountCodes(OR_CODE, '', OR_CODE);
		
		if($account_codes){
		foreach($account_codes AS $codes_value){
			$credit_or[$codes_value['credit_id']] += $or_amount;
			$debit_or[$codes_value['debit_id']] += $or_amount;
		}

		$url = "/modules/api/customer/charge";

		if(!empty($debit_pha) && !empty($credit_pha)){
			foreach($debit_pha AS $key => $value){
				$debits_pha[] = array('debitid'=>$key, 'amount'=>$value);
			}

			foreach($credit_pha AS $key => $value){
				$credits_pha[] = array('creditid'=>$key, 'amount'=>$value);
			}

			$accounts_pha = array('debits'=>$debits_pha, 'credits'=>$credits_pha);
			$accounts_pha = base64_encode(json_encode($accounts_pha));

			$data = array(
				'refno' => $bill_nr,
				'oldrefno' => $bill_nr,
				'pid' => $pid,
				'trdate' => strtotime($bill_date),
				'time' => date('H:i:s', strtotime($bill_date)),
				'src' => 'PHA',
				'accounts' => $accounts_pha,
				'encounter_nr' => $encounter,
				'person_type' => '2');

			$result_ph = $this->rest_post($url, $data);
		}#End If

			// if(!empty($debit_msc) && !empty($credit_msc)){
			// 	foreach($debit_msc AS $key => $value){
			// 		$debits_msc[] = array('debitid'=>$key, 'amount'=>$value);
			// 	}

			// 	foreach($credit_msc AS $key => $value){
			// 		$credits_msc[] = array('creditid'=>$key, 'amount'=>$value);
			// 	}

			// 	$accounts_msc = array('debits'=>$debits_msc, 'credits'=>$credits_msc);
			// 	$accounts_msc = base64_encode(json_encode($accounts_msc));

			// 	$data = array(
			// 		'refno' => $bill_nr,
			// 		'oldrefno' => $bill_nr,
			// 		'pid' => $pid,
			// 		'trdate' => strtotime($bill_date),
			// 		'time' => date('H:i:s', strtotime($bill_date)),
			// 		'src' => 'MSC',
			// 		'accounts' => $accounts_msc, 
			// 		'encounter_nr' => $encounter,
			// 		'person_type' => '2');

			// 	$result_msc = $this->rest_post($url, $data);
			// }#End If

		if(!empty($debit_or) && !empty($credit_or)){
			foreach($debit_or AS $key => $value){
				$debits_or[] = array('debitid'=>$key, 'amount'=>$value);
			}

			foreach($credit_or AS $key => $value){
				$credits_or[] = array('creditid'=>$key, 'amount'=>$value);
			}

			$accounts_or = array('debits'=>$debits_or, 'credits'=>$credits_or);
			$accounts_or = base64_encode(json_encode($accounts_or));

			$data = array(
				'refno' => $bill_nr,
				'oldrefno' => $bill_nr,
				'pid' => $pid,
				'trdate' => strtotime($bill_date),
				'time' => date('H:i:s', strtotime($bill_date)),
				'src' => 'OPR',
				'accounts' => $accounts_or, 
				'encounter_nr' => $encounter,
				'person_type' => '2');

			$result_or = $this->rest_post($url, $data);
		}#End If

			if(((!empty($debit_pha) && !empty($credit_pha)) || $result_ph['code']==FIS_TRUE) || /*((!empty($debit_msc) && !empty($credit_msc)) || $result_msc['code']==FIS_TRUE) || */((!empty($debit_or) && !empty($credit_or)) || $result_or['code']==FIS_TRUE)){
			return true;
			}else if((empty($debit_pha) && empty($credit_pha)) && /*(empty($debit_msc) && empty($credit_msc)) &&*/ (empty($debit_or) && empty($credit_or))){
			return true;
			}else{
			return false;
		}
		}
		else
			return true;

	}


	/**
	* Modified By Jarel 10/16/2014
	* Add Return for Validation
	*/
	function inpatientCoverageItems($bill_date, $pid, $bill_nr, $encounter){
		$items = array();
		$insurance = array();
		$debits = array();
		$credits = array();
		$cov_total = 0;
		$ins_total = 0;
		// $hcare_id = "";

		// $firm_arr = array();
		
		$coverage = $this->getCoverageData($bill_nr);
		if($coverage){
			foreach ($coverage as $row) {
				$this->checkInsurance($row['firm_id'], $row['name'], $row['addr_mail']);

				$cov_total = /*floatval($row['doctor_coverage']) +*/ floatval($row['hospital_coverage']);
				$ins_total += $cov_total;
				$insurance[] = array('id'=>$row['hcare_id'], 'name'=>$row['name'], 'firm_id'=>$row['firm_id'],
													'address'=>$row['addr_mail'], 'coverage'=>$cov_total);
				
				$account_codes = $this->getAccountCodes($row['hcare_id'], '', ARA);

				$code = array();
				$is_ok = true;
				if($account_codes){
					foreach($account_codes AS $codes_value){
						$code['credit_id'] = $codes_value['credit_id'];
						$code['debit_id'] = $codes_value['debit_id'];
						$this->getDefaultAccountCode('', ARA, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['credit_id'] = 0;
					$code['debit_id'] = 0;
					$this->getDefaultAccountCode('', ARA, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
				}
				}

				if($is_ok){
					// foreach($account_codes AS $value){
						$credits[$row['firm_id']] = array('creditid' => $code['credit_id']);
						$debits[$row['firm_id']] = array('debitid' => $code['debit_id']);
					// }
				}
				
				/*commented by janjan
			$hcare_id = $row['hcare_id'];
			$firm_arr['hcare_id'] = $hcare_id;
			$firm_arr['name'] = $row['name'];
			$firm_arr['ref'] = $row['firm_id'];
			$firm_arr['addr'] = $row['addr_mail'];*/
			}#End Foreach
		

			// $this->checkCustomer('', '', 'I', $firm_arr); # Check an Insurance customer tho

			if($debits && $credits){
		$items = array('debits'=>$debits, 'credits'=>$credits);
		$items = base64_encode(json_encode($items)); 
		$insurance = base64_encode(json_encode($insurance));

		if($ins_total>0){
			$data = array(
				'refno' => $bill_nr,
				'oldrefno' => $bill_nr,
				'pid' => $pid,
				'trdate' => strtotime($bill_date),
				'time' => date('H:i:s', strtotime($bill_date)),
				'src' => 'ARD',
				'accounts' => $items,
				'insurances' => $insurance, 
						// 'hcare_id' => $hcare_id,
				'encounter_nr' => $encounter,
				'person_type' => '2');

			$url = "/modules/api/customer/assignar";

			$result = $this->rest_post($url, $data);
		}#End If
			}
			else
				return true;

		if( ($cov_total<=0 || $result['code']==FIS_TRUE) ){
			return true;
		}else{
			return false;
		}
		}
		else
			return true;
	}

	function inpatientDebitAdjustment($bill_date, $pid, $bill_nr, $encounter){
		$items = array();
		$insurance = array();
		$debits = array();
		$credits = array();
		$cov_total = 0;
		$grand_total = 0;
		$ins_total = 0;
		$adjustment = 0;
		$hcare_id = "";

		// $firm_arr = array();

		$coverage = $this->getCoverageData($bill_nr, true);

		if($coverage){
			foreach ($coverage as $row) {
				// $this->checkInsurance($row['firm_id'], $row['name'], $row['addr_mail']);

				$grand_total = floatval($row['total_hci']);
				$cov_total = /*floatval($row['doctor_coverage']) + */floatval($row['hospital_coverage']);
				$adjustment += $grand_total - $cov_total;
				// $ins_total += $cov_total;
				// $insurance[] = array('id'=>$row['hcare_id'], 'name'=>$row['name'], 'firm_id'=>$row['firm_id'],
													// 'address'=>$row['addr_mail'], 'coverage'=>$cov_total);
			}
				$account_codes = $this->getAccountCodes(DEBIT_ADJUSTMENT, '', DEBIT_ADJUSTMENT);

				if($account_codes){
					foreach($account_codes AS $value){
						$credits[] = array('creditid' => $value['credit_id'], 'amount' => $adjustment);
						$debits[] = array('debitid' => $value['debit_id'], 'amount' => $adjustment);
					}
				}
				else
					return true;
			// $hcare_id = $row['hcare_id'];
			// $firm_arr['hcare_id'] = $hcare_id;
			// $firm_arr['name'] = $row['name'];
			// $firm_arr['ref'] = $row['firm_id'];
			// $firm_arr['addr'] = $row['addr_mail'];
			#}#End Foreach

			// $this->checkCustomer('', '', 'I', $firm_arr); # Check an Insurance customer tho

			if($debits && $credits){
			$items = array('debits'=>$debits, 'credits'=>$credits);
			$items = base64_encode(json_encode($items)); 
			#$insurance = base64_encode(json_encode($insurance));

			if($adjustment>0){
				$data = array(
					'refno' => $bill_nr,
					'oldrefno' => $bill_nr,
					'pid' => $pid,
					'trdate' => strtotime($bill_date),
					'time' => date('H:i:s', strtotime($bill_date)),
					'src' => 'DEA',
					'accounts' => $items,
					'encounter_nr' => $encounter,
					'person_type' => '2');

				$url = "/modules/api/customer/postdebitadjustment";
// echo '<pre>';
// var_dump($data); die;
				$result = $this->rest_post($url, $data);
			}#End If
			else
				return true;
			}
			else
				return true;

			if( ($cov_total<=0 || $result['code']==FIS_TRUE) ){
				return true;
			}else{
				return false;
			}
		}
		else
			return true;
	}

	function getHmoHCI($encounter_nr, $ref_no){
		global $db;

		$sql = "SELECT 
				  (
				    h.`acc_pay` + h.`med_pay` + h.`sup_pay` + h.`srv_pay` + h.`msc_pay` + h.`ops_pay`
				  ) payment,
				  h.`tax_wheld`,
				  h.`encounter_nr`,
				  e.`pid`,
				  f.`firm_id`,
				  f.`hcare_id`,
				  f.`name`,
				  f.`addr_mail`,
				  p.`modify_dt`,
				  (SELECT 
					  SUM(
					    sbc.`total_acc_coverage` + sbc.`total_med_coverage` + sbc.`total_sup_coverage` + sbc.`total_srv_coverage` + sbc.`total_ops_coverage` + sbc.total_msc_coverage
					  ) 
					FROM
					  seg_billing_coverage sbc 
					  LEFT JOIN seg_billing_encounter sbe 
    					ON sbc.`bill_nr` = sbe.`bill_nr` 
    				WHERE sbc.hcare_id = p.hcare_id 
    					AND sbe.encounter_nr = h.encounter_nr 
    					AND sbe.is_deleted IS NULL 
    					AND sbe.is_final = 1) total_claim,
				  (SELECT 
				    SUM(hci_amount) 
				  FROM
				    seg_billing_caserate sbcr 
				    LEFT JOIN seg_billing_encounter sbe 
				      ON sbcr.`bill_nr` = sbe.`bill_nr` 
				    WHERE
				      sbe.encounter_nr = h.encounter_nr 
				      AND sbe.is_deleted IS NULL 
				      AND sbe.is_final = 1) total_hci 
				FROM
				  seg_claim_pay_hosp h 
				  LEFT JOIN care_encounter e 
				    ON e.`encounter_nr` = h.`encounter_nr` 
				  LEFT JOIN seg_claim_posting p 
				    ON p.`ref_no` = h.`ref_no` 
				  LEFT JOIN care_insurance_firm f 
				    ON f.`hcare_id` = p.`hcare_id` 
				WHERE h.`ref_no` = ".$db->qstr($ref_no).
						" AND h.`encounter_nr` = ".$db->qstr($encounter_nr);
			
		if($row = $db->Execute($sql)){
			return $row;
		}

		return false;
	}

	function postHmoHCI($encounter_nr, $ref_no){
		$items = array();
		$insurance = array();
		$debits = array();
		$credits = array();
		$cov_total = 0;
		$ins_total = 0;
		$pid = "";
		$post_dte = "";
		$firm_id = "";

		$firm_arr = array();

		$coverage = $this->getHmoHCI($encounter_nr, $ref_no);
		if($coverage){
			foreach ($coverage as $row) {
				$this->checkInsurance($row['firm_id'], $row['name'], $row['addr_mail']);

				$cov_total = floatval($row['payment']);
				$ins_total += $cov_total;
				
				$account_codes = $this->getAccountCodes($row['hcare_id'], '', ARD);

				$code = array();
				$is_ok = true;
				if($account_codes){
					foreach($account_codes AS $codes_value){
						$code['debit_id'] = $codes_value['debit_id'];
						$code['tax_account'] = $codes_value['tax_account'];
						$code['credit_id'] = $codes_value['credit_id'];
						$code['income_account'] = $codes_value['income_account'];
						$code['cash_account'] = $codes_value['cash_account'];
						$this->getDefaultAccountCode('', ARD, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['debit_id'] = 0;
					$code['tax_account'] = 0;
					$code['credit_id'] = 0;
					$code['income_account'] = 0;
					$code['cash_account'] = 0;
					$this->getDefaultAccountCode('', ARD, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($is_ok){
					// foreach($account_codes AS $values){
						$debit_amount[$code['debit_id']] += ($row['payment'] - $row['tax_wheld']);
						$debit_amount[$code['tax_account']] += $row['tax_wheld'];
						$credit_amount[$code['credit_id']] += $row['total_claim'];

						if($row['total_hci'] < $row['payment']){ //loss
							$credit_amount[$code['income_account']] += ($row['payment'] - $row['total_hci']);
							$debit_amount[$code['cash_account']] += ($row['payment'] - $row['total_hci']);
						}else if($row['total_hci'] > $row['payment']){ //income
							$credit_amount[$code['income_account']] += ($row['total_hci'] - $row['payment']);
							$debit_amount[$code['cash_account']] += ($row['total_hci'] - $row['payment']);
					}
					// }
				}
				// else
				// 	return true;

				$pid = $row['pid'];
				$post_dte = $row['modify_dt'];
				$firm_id = $row['firm_id'];
				$hcare_id = $row['hcare_id'];

				$firm_arr['hcare_id'] = $hcare_id;
				$firm_arr['name'] = $row['name'];
				$firm_arr['ref'] = $row['firm_id'];
				$firm_arr['addr'] = $row['addr_mail'];
			}#End Foreach
		

			// $this->checkCustomer('', '', 'I', $firm_arr); # Check an Insurance Customer tho

			if($debit_amount && $credit_amount){
		foreach($debit_amount AS $key => $value){
			$debits[] = array('debitid'=>$key, 'amount'=>$value);
		}

		foreach($credit_amount AS $key => $value){
			$credits[] = array('creditid'=>$key, 'amount'=>$value);
		}

		$items = array('debits'=>$debits, 'credits'=>$credits);
		$items = base64_encode(json_encode($items)); 
		$insurance = base64_encode(json_encode($insurance));

		if($ins_total>0){
			$data = array(
				'refno' => $ref_no,
				'oldrefno' => $ref_no,
				'pid' => $pid,
				'trdate' => strtotime($post_dte),
				'time' => date('H:i:s', strtotime($post_dte)),
				'src' => 'ARM',
				'accounts' => $items,
				'encounter_nr' => $encounter_nr,
				'firm_id'=> $firm_id,
					'hcare_id' => $hcare_id,
				'person_type' => '2');

			$url = "/modules/api/customer/hmoclaim";
			
			$result = $this->rest_post($url, $data);
		}#End If
			}
			else
				return true;

		if( ($cov_total<=0 || $result['code']==FIS_TRUE) ){
			return true;
		}else{
			return false;
		}
		}
		else
			return true;
	}

	function getHmoPf($ref_no, $encounter_nr, $dr_nr){
		global $db;

		$sql = "SELECT 
				  cpf.`ref_no`,
				  cpf.`tax_wheld`,
				  cpf.`dr_pay`,
				  cpf.`dr_nr`,
				  ce.`pid`,
				  scp.`modify_dt` 
				FROM
				  seg_claim_pay_pf cpf 
				  LEFT JOIN seg_claim_posting scp 
				    ON scp.`ref_no` = cpf.`ref_no` 
				  LEFT JOIN care_encounter ce 
				    ON ce.`encounter_nr` = cpf.`encounter_nr` 
						WHERE cpf.`ref_no` = ".$db->qstr($ref_no).
							' AND cpf.`encounter_nr` = '.$db->qstr($encounter_nr).
							' AND cpf.`dr_nr` = '.$db->qstr($dr_nr);
				
		$result = $db->Execute($sql);

		if($result)
			return $result;
		else
			return false;
	}

	function postHmoPf($encounter_nr, $ref_no, $hcare_id, $dr_nr){
		$items = array();
		$debits = array();
		$credits = array();
		$pf = array();
		$flag = 0;

		$doc_data = $this->getHmoPf($ref_no, $encounter_nr, $dr_nr);

		if($doc_data){
			foreach($doc_data AS $value){
				
				$this->checkDoctor($value['dr_nr']);
                                $this->checkCustomer('', $value['pid']);

					$pf[$value['dr_nr'].'_'.$value['role_area'].'_'.$value['pid']] = ($value['dr_pay'] - $value['tax_wheld']);
				
				$account_codes = $this->getAccountCodes($hcare_id, '', PF_PAYMENT);
					
				$code = array();
				$is_ok = true;
				if($account_codes){
					foreach($account_codes AS $codes_value){
						$code['debit_id'] = $codes_value['debit_id'];
						$code['credit_id'] = $codes_value['credit_id'];
						$this->getDefaultAccountCode('', PF_PAYMENT, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['debit_id'] = 0;
					$code['credit_id'] = 0;
					$this->getDefaultAccountCode('', PF_PAYMENT, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($is_ok){
					// foreach($account_codes AS $values){
						$debit_amount[$code['debit_id']] += ($value['dr_pay'] - $value['tax_wheld']);
						//$debit_amount[$values['tax_account']] += $value['tax_wheld'];
						$credit_amount[$code['credit_id']] += $value['dr_pay'] - $value['tax_wheld'];
					// }
				}

				//$pid = $value['pid'];
				$post_date = $value['modify_dt'];

				$flag = 1;
			}	
		
			if($debit_amount && $credit_amount){
		foreach($debit_amount AS $key => $value){
			$debits[] = array('debitid'=>$key, 'amount'=>$value);
		}

		foreach($credit_amount AS $key => $value){
			$credits[] = array('creditid'=>$key, 'amount'=>$value);
		}

		$items = array('debits'=>$debits, 'credits'=>$credits);
		$items = base64_encode(json_encode($items)); 
		$pf = base64_encode(json_encode($pf)); 
		
		if($flag){

			$data = array(
					'refno' => $ref_no,
					'oldrefno' => $ref_no,
					'pid' => $pid,
					'trdate' => strtotime($post_date),
					'time' => date('H:i:s', strtotime($post_date)),
					'src' => 'PRF',
					'accounts' => $items,
					'proffees' => $pf, 
					'encounter_nr' => $encounter_nr,
					'person_type' => '2');

			$url = "/modules/api/customer/chargepf"; 

			$result = $this->rest_post($url, $data);
		}#End IF
			}
			else
				return true;
		
		if( !($flag) || ($result['code']==FIS_TRUE)){
			return true;
		}else{
			return false;
		}
		}
		else
			return true;
	}

	function getHmoPfExcess($ref_no, $encounter_nr){	
		global $db;

		$sql = "SELECT 
				  ce.`pid`,
				  f.`firm_id`,
				  f.`hcare_id`,
				  f.`name`,
				  f.`addr_mail`,
				  scp.`modify_dt`,
				  SUM(dr_pay) total_pfclaim,
				  (SELECT 
				    SUM(pf_amount) 
				  FROM
				    seg_billing_caserate sbcr 
				    LEFT JOIN seg_billing_encounter sbe 
				      ON sbcr.`bill_nr` = sbe.`bill_nr` 
				    WHERE
				      sbe.encounter_nr = cpf.encounter_nr 
				      AND sbe.is_deleted IS NULL 
				      AND sbe.is_final = 1) total_pf 
				FROM
				  seg_claim_pay_pf cpf 
				  LEFT JOIN seg_claim_posting scp 
				    ON scp.`ref_no` = cpf.`ref_no` 
				  LEFT JOIN care_encounter ce 
				    ON ce.`encounter_nr` = cpf.`encounter_nr` 
				  LEFT JOIN care_insurance_firm f 
				    ON f.`hcare_id` = scp.`hcare_id` 
						WHERE cpf.`ref_no` = ".$db->qstr($ref_no).
							' AND cpf.`encounter_nr` = '.$db->qstr($encounter_nr);
				
		$result = $db->Execute($sql);

		if($result)
			return $result;
		else
			return false;
	}

	function postHmoPfExcess($encounter_nr, $ref_no){
		$items = array();
		$debits = array();
		$credits = array();
		$cov_excess = 0;
		$ins_total = 0;
		$pid = "";
		$post_dte = "";
		$firm_id = "";

		$firm_arr = array();

		$pfexcess = $this->getHmoPfExcess($ref_no, $encounter_nr);
		if($pfexcess){
			foreach ($pfexcess as $row) {
				$this->checkInsurance($row['firm_id'], $row['name'], $row['addr_mail']);

				$cov_excess = floatval($row['total_pf'] - $row['total_pfclaim']);
				$ins_total += $cov_excess;
				
				$account_codes = $this->getAccountCodes($row['hcare_id'], '', PF_PAYMENT);

				$code = array();
				$is_ok = true;
				if($account_codes){
					foreach($account_codes AS $codes_value){
						// $code['debit_id'] = $codes_value['debit_id'];
						// $code['tax_account'] = $codes_value['tax_account'];
						// $code['credit_id'] = $codes_value['credit_id'];
						$code['income_account'] = $codes_value['income_account'];
						$code['cash_account'] = $codes_value['cash_account'];
						$this->getDefaultAccountCode('', PF_PAYMENT, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					// $code['debit_id'] = 0;
					// $code['tax_account'] = 0;
					// $code['credit_id'] = 0;
					$code['income_account'] = 0;
					$code['cash_account'] = 0;
					$this->getDefaultAccountCode('', PF_PAYMENT, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($is_ok){
					// foreach($account_codes AS $values){
						// $debit_amount[$code['debit_id']] += ($row['payment'] - $row['tax_wheld']);
						// $debit_amount[$code['tax_account']] += $row['tax_wheld'];
						// $credit_amount[$code['credit_id']] += $row['total_claim'];

						if($row['total_pf'] < $row['total_pfclaim']){ //loss
							$credit_amount[$code['income_account']] += ($row['total_pfclaim'] - $row['total_pf']);
							$debit_amount[$code['cash_account']] += ($row['total_pfclaim'] - $row['total_pf']);
						}else if($row['total_pf'] > $row['total_pfclaim']){ //income
							$credit_amount[$code['income_account']] += ($row['total_pf'] - $row['total_pfclaim']);
							$debit_amount[$code['cash_account']] += ($row['total_pf'] - $row['total_pfclaim']);
						}
					// }
				}
				// else
				// 	return true;

				$pid = $row['pid'];
				$post_dte = $row['modify_dt'];
				$firm_id = $row['firm_id'];
				$hcare_id = $row['hcare_id'];

				$firm_arr['hcare_id'] = $hcare_id;
				$firm_arr['name'] = $row['name'];
				$firm_arr['ref'] = $row['firm_id'];
				$firm_arr['addr'] = $row['addr_mail'];
			}#End Foreach
		

			// $this->checkCustomer('', '', 'I', $firm_arr); # Check an Insurance Customer tho

			if($debit_amount && $credit_amount){
				foreach($debit_amount AS $key => $value){
					$debits[] = array('debitid'=>$key, 'amount'=>$value);
				}

				foreach($credit_amount AS $key => $value){
					$credits[] = array('creditid'=>$key, 'amount'=>$value);
				}

				$items = array('debits'=>$debits, 'credits'=>$credits);
				$items = base64_encode(json_encode($items)); 

				if($ins_total>0){
					$data = array(
						'refno' => $ref_no,
						'oldrefno' => $ref_no,
						'pid' => $pid,
						'trdate' => strtotime($post_dte),
						'time' => date('H:i:s', strtotime($post_dte)),
						'src' => 'ARM',
						'accounts' => $items,
						'encounter_nr' => $encounter_nr,
						'firm_id'=> $firm_id,
						'hcare_id' => $hcare_id,
						'person_type' => '2');

					$url = "/modules/api/customer/hmoclaim";
					
					$result = $this->rest_post($url, $data);
				}#End If
				else
					return true;
			}
			else
				return true;

			if( ($cov_total<=0 || $result['code']==FIS_TRUE) ){
				return true;
			}else{
				return false;
			}
		}
		else
			return true;
	}

	function inpatientPF($bill_date, $pid, $bill_nr, $data){
		$items = array();
		$debits = array();
		$credits = array();
		$pf = array();
		$flag = 0;

		if($data){
		foreach($data AS $value){
			$pf[$value['dr_nr'].'_'.$value['role_area']] = $value['dr_charge'];
			
			$account_codes = $this->getAccountCodes(PF, '', PF);
	
				$code = array();
				$is_ok = true;
				if($account_codes){
					foreach($account_codes AS $codes_value){
						$code['debit_id'] = $codes_value['debit_id'];
						$code['credit_id'] = $codes_value['credit_id'];
						$this->getDefaultAccountCode('', PF, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['debit_id'] = 0;
					$code['credit_id'] = 0;
					$this->getDefaultAccountCode('', PF, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
				}
				}

				if($is_ok){
					// foreach($account_codes AS $value_codes){
						$debits[] = array('debitid'=>$code['debit_id'], 'amount'=>$value['dr_charge']);
						$credits[] = array('creditid'=>$code['credit_id']);
					// }

			$flag = 1;
		}	
				else
					return true;
			}	

	
		$items = array('debits'=>$debits, 'credits'=>$credits);
		$items = base64_encode(json_encode($items)); 
		$pf = base64_encode(json_encode($pf)); 
		
		if($flag){
			$data = array(
					'refno' => $bill_nr,
					'oldrefno' => $bill_nr,
					'pid' => $pid,
					'trdate' => strtotime($bill_date),
					'time' => date('H:i:s', strtotime($bill_date)),
					'src' => 'PRF',
					'accounts' => $items,
					'proffees' => $pf, 
					'encounter_nr' => $encounter,
					'person_type' => '2');

			$url = "/modules/api/customer/chargepf"; 

			$result = $this->rest_post($url, $data);
		}#End IF

		if( !($flag) || ($result['code']==FIS_TRUE)){
			return true;
		}else{
			return false;
		}
		}
	}


	/**
	* Modified By Jarel 10/16/2014
	*/
	function inpatientDiscount($bill_date, $pid, $bill_nr,$encounter_nr){
		$items = array();
		$debits = array();
		$credits = array();

		$discounts = $this->getBillingDiscounts($encounter_nr, $bill_nr);
		
		if($discounts){
			if($discounts['sc'] > 0){
				$values_sc = $this->getAccountCodes(senior_discount, '', DISCOUNT_CHARGE);

				$code = array();
				$is_ok = true;
				if($values_sc){
					foreach($values_sc AS $codes_value){
						$code['debit_id'] = $codes_value['debit_id'];
						$code['credit_id'] = $codes_value['credit_id'];
						$this->getDefaultAccountCode('', DISCOUNT_CHARGE, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['debit_id'] = 0;
					$code['credit_id'] = 0;
					$this->getDefaultAccountCode('', DISCOUNT_CHARGE, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($is_ok){
					// foreach($values_ as $values){
						$debit_amount[$code['debit_id']] += $discounts['sc'];
						$credit_amount[$code['credit_id']] += $discounts['sc'];
					// }
				}

				// if($values_sc){
				// 	foreach($values_sc as $values){
				// 		$debit_amount[$values['debit_id']] += $discounts['sc'];
				// 		$credit_amount[$values['credit_id']] += $discounts['sc'];
				// 	}
				// }
			}

			if($discounts['ar'] > 0){
				$values_ar = $this->getAccountCodes(arbitrary_discount, '', DISCOUNT_CHARGE);

				$code = array();
				$is_ok = true;
				if($values_ar){
					foreach($values_ar AS $codes_value){
						$code['debit_id'] = $codes_value['debit_id'];
						$code['credit_id'] = $codes_value['credit_id'];
						$this->getDefaultAccountCode('', DISCOUNT_CHARGE, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['debit_id'] = 0;
					$code['credit_id'] = 0;
					$this->getDefaultAccountCode('', DISCOUNT_CHARGE, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($is_ok){
					// foreach($values_ as $values){
						$debit_amount[$code['debit_id']] += $discounts['ar'];
						$credit_amount[$code['credit_id']] += $discounts['ar'];
					// }
				}
				
				// if($values_ar){
				// 	foreach($values_ar as $values){
				// 		$debit_amount[$values['debit_id']] += $discounts['ar'];
				// 		$credit_amount[$values['credit_id']] += $discounts['ar'];
				// 	}
				// }
			}		

			if(($discounts['ar'] > 0 || $discounts['sc'] > 0) && $debit_amount && $credit_amount){

				foreach($debit_amount AS $key => $value){
					$debits[] = array('debitid'=>$key, 'amount'=>$value);
				}

				foreach($credit_amount AS $key => $value){
					$credits[] = array('creditid'=>$key, 'amount'=>$value);
				}

				$items = array('debits'=>$debits, 'credits'=>$credits);
				$items = base64_encode(json_encode($items)); 
			
					$data = array(
							'refno' => $bill_nr,
							'oldrefno' => $bill_nr,
							'pid' => $pid,
							'trdate' => strtotime($bill_date),
							'time' => date('H:i:s', strtotime($bill_date)),
							'src' => 'DSC',
							'accounts' => $items,
							'encounter_nr' => $encounter_nr,
							'person_type' => '2');

					$url = "/modules/api/customer/discount"; 

					$result = $this->rest_post($url, $data);


				if(!$result['code']==FIS_TRUE ){
					return false;
				}
				else
					return true;

			}
			else
				return true;
		}
		else
			return true;
	}

	function getBillingDiscounts($encounter_nr, $bill_nr){
		global $db;

		$sql = "SELECT 
				  IFNULL(SUM(ar_discount), 0) ar, 
				  IFNULL(SUM(sc_discount), 0) sc  
				FROM
				  seg_billing_other_discounts 
				WHERE refno = ".$db->qstr($bill_nr)."
					AND dr_nr = 0 
				GROUP BY refno ";

		if($result = $db->Execute($sql)){
			return $db->GetRow($sql);
		}

		return $discounts;
	}

	//updated by Margaret 06/26/14
	function inpatientBillAmount($data){
		extract($data);

		$debits = array();
		$credits = array();

		if($data){
		$account_codes = $this->getAccountCodes(BILL_PAYMENT, '', BILL_PAYMENT);

			$code = array();
			$is_ok = true;
			if($account_codes){
				foreach($account_codes AS $codes_value){
					$code['debit_id'] = $codes_value['debit_id'];
					$code['credit_id'] = $codes_value['credit_id'];
					$this->getDefaultAccountCode('', BILL_PAYMENT, $code);
				}
				/**
				 * Check if everything contains an account, if not, continue to the next data
				 */
				foreach ($code as $code_key => $code_value) {
					if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
						$is_ok = false;
				}
			}
			else{
				$code['debit_id'] = 0;
				$code['credit_id'] = 0;
				$this->getDefaultAccountCode('', BILL_PAYMENT, $code);
				/**
				 * Check if everything contains an account, if not, continue to the next data
				 */
				foreach ($code as $code_key => $code_value) {
					if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
						$is_ok = false;
				}
			}

			if($is_ok){
				// foreach($account_codes AS $value){
					$debits[] = array('debitid'=>$code['debit_id'], 'amount'=>$amount);
					$credits[] = array('creditid'=>$code['credit_id'], 'amount'=>$amount);
				// }
		}
			// else
			// 	return true;

			if($debits && $credits) {
		$accounts = array('debits'=>$debits, 'credits'=>$credits);

		$accounts = base64_encode(json_encode($accounts)); 

		$data = array(
				'refno' => $or,
				'oldrefno' => $or,
				'trdate' => date('m/d/Y', strtotime($date)),
				'time' => date('H:i:s', strtotime($date)),
				'src' => 'PBP',
				'pid' => $pid,
				'encounter_nr' => $enc,
				'person_type' => '2',
				'accounts' => $accounts
		);
			
		$url = "/modules/api/customer/paybill";

		$result = $this->rest_post($url, $data);

				return $result;
			}
			else
				return true;
		}
		else
			return true;
	}
	// end Margaret

	function inpatientPfAmount($data){
		
		$debits = array();
		$credits = array();

		if($data){
			$account_codes = $this->getAccountCodes(PF_PAYMENT, '', PF_PAYMENT);

			if($account_codes){
				foreach($account_codes AS $value){
					$debits[] = array('debitid'=>$value['debit_id'], 'amount'=>$data['amount']);
					$credits[] = array('creditid'=>$value['credit_id'], 'amount'=>$data['amount']);
				}
			}
			else
				return true;

			$accounts = array('debits'=>$debits, 'credits'=>$credits);

			$accounts = base64_encode(json_encode($accounts)); 

			$data = array(
					'refno' => $data['refno'],
					'oldrefno' => $data['refno'],
					'trdate' => date('m/d/Y', strtotime($data['date'])),
					'time' => date('H:i:s', strtotime($data['date'])),
					'src' => 'PPP',
					'pid' => $data['pid'],
					'encounter_nr' => $data['encounter_nr'],
					'person_type' => '2',
					'accounts' => $accounts
			);
			
			$url = "/modules/api/customer/paybill";

			$result = $this->rest_post($url, $data);
		}
		else
			return true;
	}
	
	function inpatientCashPayment($data){
		extract($data);

		$debits = array();
		$credits = array();

		$account_codes = $this->getAccountCodes(DEPOSIT, '', DEPOSIT);

		if($account_codes){
		foreach($account_codes AS $value){
			$debits[] = array('debitid'=>$value['debit_id'], 'amount'=>$amount);
			$credits[] = array('creditid'=>$value['credit_id'], 'amount'=>$amount);
		}
		}
		else
			return true;

		$accounts = array('debits'=>$debits, 'credits'=>$credits);

		$accounts = base64_encode(json_encode($accounts)); 

		$data = array(
				'refno' => $or,
				'oldrefno' => $or,
				'trdate' => date('m/d/Y', strtotime($date)),
				'time' => date('H:i:s', strtotime($date)),
				'src' => 'DEP',
				'pid' => $pid,
				'encounter_nr' => $enc,
				'person_type' => '2',
				'accounts' => $accounts
		);
			
		$url = "/modules/api/customer/paybill";

		$result = $this->rest_post($url, $data);
	}

	function unpostEntries($data){

		if($data)
		{
		$url = "/modules/api/customer/unpostentries";

		$result = $this->rest_post($url, $data);
		
		if( $result['code']==FIS_TRUE ){
			return true;
		}else{
			return false;
		}
		}
		else
			return true;
	}

	function inpatientDeleteBillEntry($bill_nr, $reverse, $bill_dte){ // Modified by Sarah for Reverse Entry August 28, 2015

		$data = array('refno' => $bill_nr, 'reverse' => $reverse, 'bill_date' => $bill_dte); 

		$url = "/modules/api/customer/unpostbilling";

		$result = $this->rest_post($url, $data);
		
		if( $result['code']==FIS_TRUE ){
			return true;
		}else{
			return false;
		}

	}

	function customerAdd($pid, $type, $firm_arr=null){
		$cust_ref = '';
		$cust_name = '';
		$person_address = '';
		if($type == 'P'){
		$info = $this->getPatientInfo($pid);
			$cust_ref = $info['name'];
			$cust_name = $info['name'];
		$person_address = implode(", ",array_filter(array($info['street_name'], $info["brgy_name"], $info["mun_name"])));
		if ($info["zipcode"])
			$person_address.=" ".$info["zipcode"];
		if ($info["prov_name"])
			$person_address.=" ".$info["prov_name"];
		}
		else if($type == 'I'){
			$cust_ref = $firm_arr['ref'];
			$cust_name = $firm_arr['name'];
			$person_address = $firm_arr['addr'];
		}

		$data = array(
					'cust_no'=>$pid,
					'custname'=>$cust_name,
					'cust_ref'=>$cust_ref,
					'type'=>$type,
					'address'=>$person_address,
					'credit_limit'=>'10000');

		$url = "/modules/api/customer/add";
		$result = $this->rest_post($url, $data);

		return $result;
	}
         # added by alex
          function customerUpdate($pid, $type){
		$cust_ref = '';
		$cust_name = '';
		$person_address = '';

		$info = $this->getPatientInfo($pid);
		$cust_ref = $info['name'];
		$cust_name = $info['name'];
		$person_address = implode(", ",array_filter(array($info['street_name'], $info["brgy_name"], $info["mun_name"])));
		if ($info["zipcode"])
			$person_address.=" ".$info["zipcode"];
		if ($info["prov_name"])
			$person_address.=" ".$info["prov_name"];
		
		$data = array(
					'cust_no'=>$pid,
					'custname'=>$cust_name,
					'cust_ref'=>$cust_ref,
					'type'=>$type,
					'address'=>$person_address,
					'credit_limit'=>'10000');

		$url = "/modules/api/customer/".$pid."/update";
		$result = $this->rest_post($url, $data);

		return $result;
	}

        # added by janjan 08/04/2015
	function customerAddEncounter($enc_nr, $enc_date, $pid){
		$data = array(
			'enc_nr' => $enc_nr,
			'enc_date' => $enc_date,
			'cust_no' => $pid
		);

		$url = "/modules/api/customer/add_encounter";

		$result = $this->rest_post($url, $data);

		return $result;
	}

	# added by janjan 08/04/2015
	function customerUpdateDischarge($enc_nr, $enc_date=null, $discharge_date, $discharge_time, $pid){
		$data = array(
			'enc_nr' => $enc_nr,
			'enc_date' => $enc_date,
			'discharge_date' => $discharge_date,
			'discharge_time' => $discharge_time,
			'cust_no' => $pid
		);

		$url = "/modules/api/customer/update_discharge";

		$result = $this->rest_post($url, $data);

		return $result;
	}

	# added by janjan 08/14/2015
	function customerCancelDischarge($enc_nr){
		$data = array(
			'enc_nr' => $enc_nr
		);

		$url = "/modules/api/customer/cancel_discharge";

		$result = $this->rest_post($url, $data);

		return $result;
	}

	function walkinAdd($pid, $address, $name){
		$data = array(
					'cust_no'=>$pid,
					'custname'=>$name,
					'cust_ref'=>$pid,
					'type'=>'P',
					'address'=>$address,
					'credit_limit'=>'10000');

		$url = "/modules/api/customer/add";

		$result = $this->rest_post($url, $data);
	}

	function checkCustomer($no, $pid='', $type='P', $firm_arr=null){
		if(!$pid && $type == 'P')
			$pid = $this->getPid($no);

		if($type == 'P')
		$pid = str_replace('W', '', $pid);
		else if($type == 'I')
			$pid = $firm_arr['hcare_id'];

		$url = "/modules/api/customer/".$pid;

		$result = $this->rest_get($url);

		// print_r($pid);exit;
		$result = json_decode($result);

		if(!$result){

			if($type == 'P')
			{
			$walkin = $this->searchWalkin($pid);
			if($walkin)
				$this->walkinAdd($pid, $walkin['address'], $walkin['name']);
			else{
					$this->customerAdd($pid, $type);
				}
			}
			else if($type == 'I')
			{
				$this->customerAdd($pid, $type, $firm_arr);
			}
		}
		return $result;
	}

	#added by janken 10/23/2014 for adding insurance in customer data in FIS
	function checkInsurance($firm_id, $name, $address){
		$url = "/modules/api/customer/".$firm_id;

		$result = $this->rest_get($url);

		$result = json_decode($result);

		if(!$result){
			$this->insuranceAdd($firm_id, $name, $address);
		}
	}

	function insuranceAdd($firm_id, $name, $address){
		$data = array(
					'cust_no'=>$firm_id,
					'custname'=>$name,
					'cust_ref'=>$firm_id,
					'type'=>'I',
					'address'=>$address,
					'credit_limit'=>'1000000');

		$url = "/modules/api/customer/add";

		$result = $this->rest_post($url, $data);

	}#end of added functions

	#added by janken 10/23/2014 for adding doctor in supplier data in FIS
	function checkDoctor($dr_nr){
		$url = "/modules/api/suppliers/".$dr_nr;

		$result = $this->rest_get($url);

		$result = json_decode($result);

		if(!$result){
			$this->doctorAdd($dr_nr);
		}
		return $result;
	}

	function doctorAdd($dr_nr){
		$info = $this->getDoctorInfo($dr_nr);

		if($info){
		$person_address = implode(", ",array_filter(array($info['street_name'], $info["brgy_name"], $info["mun_name"])));
		if ($info["zipcode"])
			$person_address.=" ".$info["zipcode"];
		if ($info["prov_name"])
			$person_address.=" ".$info["prov_name"];
		
		$data = array(
					'id'=>$dr_nr,
					'name'=>$info['name'],
					'address'=>$person_address,
					'is_doctor'=>'1');

		$url = "/modules/api/supplier/add";

		$result = $this->rest_post($url, $data);

		return $result;
		}
		else
			return true;

	}#end of added functions

	function getPurchaseDetails($ref){

		$url = "/modules/api/purchasedetail/".$ref;

		$result = $this->rest_get($url);

		return $result;
	}

	function getPurchaseInfo($ref){

		$url = "/modules/api/puchaseinfo/".$ref;

		$result = $this->rest_get($url);

		return $result;
	}

	function saveGRN($data, $items){
		$details = $data;
		// $items = base64_encode(json_encode($items)); 
		$details['line_items'] = $items;
		$url = "/modules/api/purchaseGRN/add"; 

		$result = $this->rest_post($url, $details);
	}


	function updateGRN($reference, $data){

		$url = "/modules/api/".$reference."/deliveredItems";

		$result = $this->rest_post($url, $data);

	}

	function getGRNInfo($reference){
		$url = "/modules/api/batchDeliveries/".$reference;

		$result = $this->rest_get($url);

		return $result;
	}

	function deleteGRN($reference){

		$url = "/modules/api/batchDeliveries/".$reference;

		$result = $this->rest_delete($url);
	}

	function deletePharmaEntry($refno, $item){

		$data = array('refno' => $refno,
						'item_id' => $item);

		$url = "/modules/api/customer/unpostpharma";
		// print_r($data); exit;

		$result = $this->rest_post($url, $data);
	}

	function getAllSupplier(){

		$url = "/modules/api/allsuppliers";

		$result = $this->rest_get($url);

		return $result;
	}

	function getSupplier($id){ //added by maimai 12-15-2014

		$url = "/modules/api/supplier/".$id;

		$result = $this->rest_get($url);

		return $result;
	}

	function returnPharmaItem($items, $info){
		$inv_obj = new SKUInventory;

		extract($info);
		if($items){
		$this->checkCustomer('', $pid);

		$debit_amount = array();
		$credit_amount = array();

		foreach($items AS $key => $value){
			$amount = $this->getPharmaAmount($value['ref_no'], $value['bestellnum']);
			$avg_cost = $inv_obj->getItemAvgCost($value['bestellnum']);

			$account_codes = $this->getAccountCodes($value['bestellnum'], 'PH', CHARGE_ITEM);

				$code = array();
				$is_ok = true;
				if($account_codes){
			foreach($account_codes AS $codes_key => $codes_value){
						$code['income_account'] = $codes_value['income_account'];
						$code['cash_account'] = $codes_value['cash_account'];
						$code['cogs_account'] = $codes_value['cogs_account'];
						$code['inventory_account'] = $codes_value['inventory_account'];
						$this->getDefaultAccountCode('PH', CHARGE_ITEM, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
			}
		}
				else{
					$code['income_account'] = 0;
					$code['cash_account'] = 0;
					$code['cogs_account'] = 0;
					$code['inventory_account'] = 0;
					$this->getDefaultAccountCode('PH', CHARGE_ITEM, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($amount && $avg_cost && $is_ok){
					// foreach($account_codes AS $codes_key => $codes_value){
						$debit_amount[$code['income_account']] += $amount * $value['quantity'];
						$credit_amount[$code['cash_account']] += $amount * $value['quantity'];
						$credit_amount[$code['cogs_account']] += $avg_cost * $value['quantity'];
						$debit_amount[$code['inventory_account']] += $avg_cost * $value['quantity'];
					// }
				}
				// else
				// 	return true;
			}

			if($credit_amount && $debit_amount){
		foreach($debit_amount AS $key => $value){
			$debit[] = array('debitid'=>$key, 'amount'=>$value);
		}

		foreach($credit_amount AS $key => $value){
			$credit[] = array('creditid'=>$key, 'amount'=>$value);
		}

		$accounts = array('debits'=>$debit, 'credits'=>$credit);
		$accounts = base64_encode(json_encode($accounts));

		$data = array(
					'refno' => $refno,
					'oldrefno' => $refno,
					'pid' => $pid,
					'src' => 'CMO',
					'trdate' => strtotime($date),
					'time' => date('H:i:s', strtotime($date)),
					'encounter_nr' => $encounter_nr,
					'person_type' => '2',
					'accounts' => $accounts
		);

		$url = "/modules/api/customer/creditmemo";

		$result = $this->rest_post($url, $data);
		}
		else
			return true;
		}
		else
			return true;

	}

	function creditMemoAdd($nr, $date){
		$inv_obj = new SKUInventory;

		$data = $this->creditMemoSearch($nr);
		$total_costcenters = 0;

		$debit = array();
		$credit = array();
		$pid = '';

		if($data->RecordCount() > 0){
			while($row = $data->FetchRow()){

				$pid = $row['pid'];
				$enc = $row['encounter_nr'];

				if($row['ref_source'] == 'LD'){
					$total_costcenters += $row['quantity'] * $row['price'];

					$account_codes = $this->getAccountCodes($row['service_code'], 'LD', CASH_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
					foreach($account_codes AS $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$this->getDefaultAccountCode('LD', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$this->getDefaultAccountCode('LD', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_value){
					$amount = $row['quantity'] * $row['price'];
					$amount = floatval($amount);
							$vat  = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

							$debit[$code['income_account']] += $amount;
							$credit[$code['cash_account']] += $amount;
						//$debit[$codes_value['tax_account']] += $vat;
						// } 
					}

				}
				else if($row['ref_source'] == 'RD'){
					$total_costcenters += $row['quantity'] * $row['price'];

					$account_codes = $this->getAccountCodes($row['service_code'], 'RD', CASH_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
					foreach($account_codes AS $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$this->getDefaultAccountCode('RD', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$this->getDefaultAccountCode('RD', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_value){
					$amount = $row['quantity'] * $row['price'];
					$amount = floatval($amount);
							$vat  = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

							$debit[$code['income_account']] += $amount;
							$credit[$code['cash_account']] += $amount;
						//$debit[$codes_value['tax_account']] += $vat;
						// }
				}
				}
				else if($row['ref_source'] == 'PH'){
					$total_costcenters += $row['quantity'] * $row['price'];

					$avg_cost = $inv_obj->getItemAvgCost($row['service_code']);

					$account_codes = $this->getAccountCodes($row['service_code'], 'PH', CASH_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
					foreach($account_codes AS $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$code['cogs_account'] = $codes_value['cogs_account'];
							$code['inventory_account'] = $codes_value['inventory_account'];
							$code['tax_account'] = $codes_value['tax_account'];
							$this->getDefaultAccountCode('PH', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$code['cogs_account'] = 0;
						$code['inventory_account'] = 0;
						$code['tax_account'] = 0;
						$this->getDefaultAccountCode('PH', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_value){
						$amount = $row['quantity'] * $row['price'];
						$amount = floatval($amount);
						/*$vat = $amount - ($amount/1.12);
						$nonvatsales = $amount - $vat;

						$debit[$codes_value['income_account']] += $nonvatsales;
						$credit[$codes_value['cash_account']] += $amount;
						$credit[$codes_value['cogs_account']] += $avg_cost * $row['quantity'];
						$debit[$codes_value['inventory_account']] += $avg_cost * $row['quantity'];
						$debit[$codes_value['tax_account']] += $vat;*/

						if($row['vat_amount'] != '0.0000'){
							$is_vat = $this->getVatItemPharma($row['service_code']);

							if($is_vat){
									$vat = $amount - ($amount/default_vat);
								$nonvatsales = $amount - $vat;

									$debit[$code['income_account']] += $nonvatsales;
									$debit[$code['tax_account']] += $vat;
							}
							else{
									$debit[$code['income_account']] += $amount;
							}
						}
						else{

								// or OUTPUT Taxes
								if($code['tax_account'] != '0.0' || $code['tax_account'] != '0'){
									/*
									i.e.
									33/1.12 = 29.46 nontaxsales
									29.46*0.12 = 3.54 output tax
									*/
									// $vat = $amount - ($amount/default_vat);
									// $nonvatsales = $amount - $vat;
									$nontaxsales = $amount/default_vat;

									// $nontaxsales = $amount - ($amount * default_output_tax);
									$outputtax = $nontaxsales*default_output_tax;
									// $outputtax = $amount - $nontaxsales;

									$debit[$code['income_account']] += $nontaxsales;
									$debit[$code['tax_account']] += $outputtax;
								}
								else{
									$debit[$code['income_account']] += $amount;
						}
							}

							$credit[$code['cash_account']] += $amount;
							$credit[$code['cogs_account']] += $avg_cost * $row['quantity'];
							$debit[$code['inventory_account']] += $avg_cost * $row['quantity'];
						// }
				}
				}
				else if($value['ref_source'] == 'OTHER'){
					$total_costcenters += $row['quantity'] * $row['price'];

					$row['service_code'] = substr($row['service_code'], 0, -1);
					$account_codes = $this->getAccountCodes($row['service_code'], 'OT', CASH_ITEM);
					
					$code = array();
					$is_ok = true;
					if($account_codes){
					foreach($account_codes AS $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_value){
						$amount = $row['quantity'] * $row['price'];
						$amount = floatval($amount);
							$vat  = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

							$debit[$code['income_account']] += $amount;
							$credit[$code['cash_account']] += $amount;
						//$debit[$codes_value['tax_account']] += $vat;
						// }
				}
				}
				else if($value['ref_source'] == 'MISC'){
					$total_costcenters += $row['quantity'] * $row['price'];

					$row['service_code'] = $this->getMiscServiceCode($row['service_code']);
					$account_codes = $this->getAccountCodes($row['service_code'], 'OT', CASH_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
					foreach($account_codes AS $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$code['cogs_account'] = $codes_value['cogs_account'];
							$code['inventory_account'] = $codes_value['inventory_account'];
							$code['tax_account'] = $codes_value['tax_account'];
							$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$code['cogs_account'] = 0;
						$code['inventory_account'] = 0;
						$code['tax_account'] = 0;
						$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_value){
					$amount = $row['quantity'] * $row['price'];
					$amount = floatval($amount);
							$vat  = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

							$debit[$code['income_account']] += $amount;
							$credit[$code['cash_account']] += $amount;
						//$debit[$codes_value['tax_account']] += $vat;
						// }
				}
				}
				else if($row['ref_source'] == 'FB'){
					$amount = floatval($row['price']);

					$account_codes = $this->getAccountCodes(BILL_PAYMENT, '', BILL_PAYMENT);

					$code = array();
					$is_ok = true;
					if($account_codes){
					foreach($account_codes AS $codes_value){
							$code['debit_id'] = $codes_value['debit_id'];
							$code['credit_id'] = $codes_value['credit_id'];
							$this->getDefaultAccountCode('', BILL_PAYMENT, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['debit_id'] = 0;
						$code['credit_id'] = 0;
						$this->getDefaultAccountCode('', BILL_PAYMENT, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_value){
							$debit[$code['credit_id']] += $amount;
							$credit[$code['debit_id']] += $amount;
						// }
				}
				}
				else if($row['ref_source'] == 'PP'){
					$amount = floatval($row['price']);

					$account_codes = $this->getAccountCodes(DEPOSIT, '', DEPOSIT);

					if($account_codes){
					foreach($account_codes AS $codes_value){
						$debit[$codes_value['credit_id']] += $amount;
						$credit[$codes_value['debit_id']] += $amount;
					}
				}
			}
				}
			
			if($debit){
		foreach($debit AS $key => $value){
			$debits[] = array('debitid'=>$key, 'amount'=>$value);
			}
			}

			if($credit){
		foreach($credit AS $key => $value){
			$credits[] = array('creditid'=>$key, 'amount'=>$value);
		}
			}

		$accounts = array('debits'=>$debits, 'credits'=>$credits);
		$accounts = base64_encode(json_encode($accounts));

		$this->checkCustomer('', $pid);

		$data = array(
					'refno' => $nr,
					'oldrefno' => $nr,
					'pid' => $pid,
					'src' => 'CMO',
					'trdate' => strtotime($date),
					'time' => date('H:i:s', strtotime($date)),
					'encounter_nr' => $enc,
					'person_type' => '2',
					'accounts' => $accounts,
					'total_costcenters'=>$total_costcenters
		);

		$url = "/modules/api/customer/creditmemo";

		$result = $this->rest_post($url, $data);

			return $result;
		}
		else
			return true;
	}

	function getAllAccounts(){
		$url = "/modules/api/accounts";

		$result = $this->rest_get($url);

		return $result;
	}

	function getPoNo(){
		$url = "/modules/api/purchaseorder";

		$result = $this->rest_get($url);

		return $result;
	}

	#added by janken 10/13/2014 to cater all the cash trans
	function cashTransactions($or_no){
		$inv_obj = new SKUInventory;

		$this->checkCustomer($or_no);

		#-----Pharmacy varianles------
		$credit_pha = array();
		$debit_pha = array();

		#-----Laboratory varianles------
		$credit_lab = array();
		$debit_lab = array();

		#-----Radiology varianles------
		$credit_rad = array();
		$debit_rad = array();

		#-----Miscellaneous varianles------
		$credit_msc = array();
		$debit_msc = array();
               
                #-----Cash PF varianles------
		$credit_pf = array();
		$debit_pf = array();

		#-----cashpf varianles------
		$cashpfamount = 0;

		#-----Partial Payment varianles------
		$pp_amount = 0;

		#-----Bill Payment varianles------
		$fb_amount = 0;
           
         	#-----Bill Payment varianles (DOCTOR'S FEE)------
		$pf_amount_from_fb = 0;		

		#-----Company Payment varianles------
		$com_amount = 0;

		#-----Service Payment varianles------
		$credit_serv = array();
		$debit_serv = array();
		$serv_amount = 0;

		$bill_nr = 0;

		$items = $this->getOrItems($or_no);

		if($items){
		foreach($items AS $key => $value){

			if($value['ref_source'] == 'PH'){
				$avg_cost = $inv_obj->getItemAvgCost($value['service_code']);

				$account_codes = $this->getAccountCodes($value['service_code'], 'PH', CASH_ITEM);
				
				$amount = $value['amount_due'];

					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$code['cogs_account'] = $codes_value['cogs_account'];
							$code['inventory_account'] = $codes_value['inventory_account'];
							$code['tax_account'] = $codes_value['tax_account'];
							$this->getDefaultAccountCode('PH', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$code['cogs_account'] = 0;
						$code['inventory_account'] = 0;
						$code['tax_account'] = 0;
						$this->getDefaultAccountCode('PH', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_key => $codes_value){
					if($value['vat_amount'] != '0.0000'){
						$is_vat = $this->getVatItemPharma($value['service_code']);

						if($is_vat){
									$vat = $amount - ($amount/default_vat);
							$nonvatsales = $amount - $vat;

									$credit_pha[$code['income_account']] += $nonvatsales;
									$credit_pha[$code['tax_account']] += $vat;
						}
						else{
									$credit_pha[$code['income_account']] += $value['amount_due'];
						}
					}
					else{

								// or OUTPUT Taxes
								if($code['tax_account'] != '0.0' || $code['tax_account'] != '0'){
									/*
									i.e.
									33/1.12 = 29.46 nontaxsales
									29.46*0.12 = 3.54 output tax
									*/
									// $vat = $amount - ($amount/default_vat);
									// $nonvatsales = $amount - $vat;
									$nontaxsales = $amount/default_vat;

									// $nontaxsales = $amount - ($amount * default_output_tax);
									$outputtax = $nontaxsales*default_output_tax;
									// $outputtax = $amount - $nontaxsales;

									$credit_pha[$code['income_account']] += $nontaxsales;
									$credit_pha[$code['tax_account']] += $outputtax;
								}
								else{
									$credit_pha[$code['income_account']] += $value['amount_due'];
					}
							}

							$debit_pha[$code['cash_account']] += $value['amount_due'];
							$debit_pha[$code['cogs_account']] += $avg_cost * $value['qty'];
							$credit_pha[$code['inventory_account']] += $avg_cost * $value['qty'];
						// }
					}
			}

			else if($value['ref_source'] == 'LD'){
				$account_codes = $this->getAccountCodes($value['service_code'], 'LD', CASH_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$code['tax_account'] = $codes_value['tax_account'];
							$this->getDefaultAccountCode('LD', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$code['tax_account'] = 0;
						$this->getDefaultAccountCode('LD', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_key => $codes_value){
					$amount = $value['amount_due'];

					if($value['vat_amount'] != '0.0000'){
								$vat = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

							$credit_lab[$code['income_account']] += $nonvatsales;
							$credit_lab[$code['tax_account']] += $vat;
					}
					else
								$credit_lab[$code['income_account']] += $amount;

								$debit_lab[$code['cash_account']] += $amount;
						// }
				}
				}

			else if($value['ref_source'] == 'RD'){
				$account_codes = $this->getAccountCodes($value['service_code'], 'RD', CASH_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$code['tax_account'] = $codes_value['tax_account'];
							$this->getDefaultAccountCode('RD', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$code['tax_account'] = 0;
						$this->getDefaultAccountCode('RD', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_key => $codes_value){
					$amount = $value['amount_due'];

					if($value['vat_amount'] != '0.0000'){
								$vat = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

							$credit_rad[$code['income_account']] += $nonvatsales;
							$credit_rad[$code['tax_account']] += $vat;
				}
					else
								$credit_rad[$code['income_account']] += $amount;

								$debit_rad[$code['cash_account']] += $amount;
						// }
			}
				}

			else if($value['ref_source'] == 'OTHER'){
				$value['service_code'] = substr($value['service_code'], 0, -1);
				$account_codes = $this->getAccountCodes($value['service_code'], 'OT', CASH_ITEM);

					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$code['tax_account'] = $codes_value['tax_account'];
							$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$code['tax_account'] = 0;
						$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_key => $codes_value){
					$amount = $value['amount_due'];

					if($value['vat_amount'] != '0.0000'){
								$vat = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

								$credit_msc[$code['income_account']] += $nonvatsales;
								$credit_msc[$code['tax_account']] += $vat;
					}
					else
								$credit_msc[$code['income_account']] += $amount;

								$debit_msc[$code['cash_account']] += $amount;
						// }
			}
				}

			else if($value['ref_source'] == 'MISC'){
				$value['service_code'] = $this->getMiscServiceCode($value['service_code']);
				$account_codes = $this->getAccountCodes($value['service_code'], 'OT', CASH_ITEM);
				$amount = $value['amount_due'];

					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['income_account'] = $codes_value['income_account'];
							$code['cash_account'] = $codes_value['cash_account'];
							$code['tax_account'] = $codes_value['tax_account'];
							$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['income_account'] = 0;
						$code['cash_account'] = 0;
						$code['tax_account'] = 0;
						$this->getDefaultAccountCode('OT', CASH_ITEM, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_key => $codes_value){
					$amount = $value['amount_due'];

					if($value['vat_amount'] != '0.0000'){
								$vat = $amount - ($amount/default_vat);
						$nonvatsales = $amount - $vat;

								$credit_msc[$code['income_account']] += $nonvatsales;
								$credit_msc[$code['tax_account']] += $vat;
					}
					else
								$credit_msc[$code['income_account']] += $amount;

								$debit_msc[$code['cash_account']] += $amount;
						// }
			}
				}
			else if($value['ref_source'] == 'COM'){
				$amount = $value['amount_due'];

				$com_amount += $amount;

				$name = $value['or_name'];
			}

			else if($value['ref_source'] == 'SP'){
				if($value['trans_type'] == 'insurance'){
					$account_codes = $this->getAccountCodes($value['pid'], '', ARA);
					$name = $value['or_name'];
				}
				else{
					$account_codes = $this->getAccountCodes($value['pid'], '', DISCOUNT_PAY);
					$name = $value['pid'];
				}

					$code = array();
					$is_ok = true;
					if($account_codes){
				foreach($account_codes AS $codes_key => $codes_value){
							$code['credit_id'] = $codes_value['credit_id'];
							$code['debit_id'] = $codes_value['debit_id'];
							if($value['trans_type'])
								$this->getDefaultAccountCode('', ARA, $code);
							else
								$this->getDefaultAccountCode('', DISCOUNT_PAY, $code);
				}
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}
					else{
						$code['credit_id'] = 0;
						$code['debit_id'] = 0;
						if($value['trans_type'])
							$this->getDefaultAccountCode('', ARA, $code);
						else
							$this->getDefaultAccountCode('', DISCOUNT_PAY, $code);
						/**
						 * Check if everything contains an account, if not, continue to the next data
						 */
						foreach ($code as $code_key => $code_value) {
							if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
								$is_ok = false;
						}
					}

					if($is_ok){
						// foreach($account_codes AS $codes_key => $codes_value){
							$credit_serv[$code['credit_id']] += $value['amount_due'];
							$debit_serv[$code['debit_id']] += $value['amount_due'];
						// }
			}
				}

			else if($value['ref_source'] == 'PP'){
				$amount = $value['amount_due'];

				$pp_amount += $amount;
			}

			else if($value['ref_source'] == 'FB'){
				$amount = $value['amount_due'];

				$pf_payments = $this->getPFpayment($or_no);
				if(!empty($pf_payments))
					$amount += $pf_payments['amount_due'];
                                $bill_nr = $value['service_code'];
				$fb_amount += $amount;
			}

			else if(!$value['ref_source']){
				$amount = 0;

				$pf_payments = $this->getPFpayment($or_no);
				if(!empty($pf_payments))
					$amount += $pf_payments['amount_due'];

				$fb_amount += $amount;
			}

			$pid = str_replace('W', '', $value['pid']);
			$date = $value['or_date'];
			$or = $value['or_no'];
			$discount = $value['discount_tendered'];
			$enc = $value['encounter_nr'];
			
		}

		#-----Pharmacy saving to FIS-----
		if(!empty($debit_pha) && !empty($credit_pha)){
			foreach($debit_pha AS $key => $value){
				$debits_pha[] = array('debitid'=>$key, 'amount'=>$value);
			}

			foreach($credit_pha AS $key => $value){
				$credits_pha[] = array('creditid'=>$key, 'amount'=>$value);
			}

			$accounts_pha = array('debits'=>$debits_pha, 'credits'=>$credits_pha);
			$accounts_pha = base64_encode(json_encode($accounts_pha));
				
			$data = array('accounts' => $accounts_pha,
							'or' => $or,
							'pid' => $pid,
							'enc' => $enc,
							'date' => $date);

			$this->walkinPharmaItem($data);
		}
		#-----Laboratory saving to FIS-----
		if(!empty($debit_lab) && !empty($credit_lab)){
			foreach($debit_lab AS $key => $value){
				$debits_lab[] = array('debitid'=>$key, 'amount'=>$value);
			}

			foreach($credit_lab AS $key => $value){
				$credits_lab[] = array('creditid'=>$key, 'amount'=>$value);
			}

			$accounts_lab = array('debits'=>$debits_lab, 'credits'=>$credits_lab);
			$accounts_lab = base64_encode(json_encode($accounts_lab));
				
			$data = array('accounts' => $accounts_lab,
							'or' => $or,
							'pid' => $pid,
							'enc' => $enc,
							'date' => $date);

			$this->walkinLabItem($data);
		}
		#-----Radiology saving to FIS-----
		if(!empty($debit_rad) && !empty($credit_rad)){
			foreach($debit_rad AS $key => $value){
				$debits_rad[] = array('debitid'=>$key, 'amount'=>$value);
			}

			foreach($credit_rad AS $key => $value){
				$credits_rad[] = array('creditid'=>$key, 'amount'=>$value);
			}

			$accounts_rad = array('debits'=>$debits_rad, 'credits'=>$credits_rad);
			$accounts_rad = base64_encode(json_encode($accounts_rad));

			$data = array('accounts' => $accounts_rad,
							'or' => $or,
							'pid' => $pid,
							'enc' => $enc,
							'date' => $date);

			$this->walkinRadioItem($data);
		}

		#-----Miscellaneous saving to FIS-----
		if(!empty($debit_msc) && !empty($credit_msc)){
			foreach($debit_msc AS $key => $value){
				$debits_msc[] = array('debitid'=>$key, 'amount'=>$value);
			}

			foreach($credit_msc AS $key => $value){
				$credits_msc[] = array('creditid'=>$key, 'amount'=>$value);
			}

			$accounts_msc = array('debits'=>$debits_msc, 'credits'=>$credits_msc);
			$accounts_msc = base64_encode(json_encode($accounts_msc));
				
			$data = array('accounts' => $accounts_msc,
							'or' => $or,
							'pid' => $pid,
							'enc' => $enc,
							'date' => $date);

			$this->walkinMiscItem($data);
		}
		#-----Partial Payment saving to FIS-----
		if($pp_amount>0){
			$data = array('amount' => $pp_amount,
							'or' => $or_no,
							'pid' => $pid,
							'enc' => $enc,
							'date' => $date);

			$this->inpatientCashPayment($data);
		}
		#-----Bill Payment saving to FIS-----
		if($fb_amount>0){
			$data = array('amount' => $fb_amount,
							'or' => $or_no,
							'pid' => $pid,
							'enc' => $enc,
							'date' => $date);

			$this->inpatientBillAmount($data);
		}
		#-----Company Payment saving to FIS-----
		if($com_amount>0){
			$data = array('amount' => $com_amount,
							'or' => $or_no,
							'pid' => $pid,
							'comp_id' => $pid,
							'enc' => $enc,
							'date' => $date);

			$this->comPayment($data);
		}
		#-----Service Payment saving to FIS-----
		if(!empty($debit_serv) && !empty($credit_serv)){
			foreach($debit_serv AS $key => $value){
				$debits_serv[] = array('debitid'=>$key, 'amount'=>$value);
			}

			foreach($credit_serv AS $key => $value){
				$credits_serv[] = array('creditid'=>$key, 'amount'=>$value);
			}

			$accounts_serv = array('debits'=>$debits_serv, 'credits'=>$credits_serv);
			$accounts_serv = base64_encode(json_encode($accounts_serv));
				
			$data = array('accounts' => $accounts_serv,
							'or' => $or,
							'pid' => $name,
							'enc' => $enc,
							'date' => $date);

			$this->servicePayment($data);
		}

		$this->postCashierDiscount($or, $pid, $date);

			return true;
	}
		else
			return true;
	}

	function postCashierDiscount($or_no, $pid, $post_dte){
		$discount = $this->getcashierDiscount($or_no);
		$debits = array();
		$credits = array();
		
		$ar_discount = $discount['discount_tendered'];

		if($ar_discount){
		if($ar_discount > 0){
			$account_codes = $this->getAccountCodes(arbitrary_discount, '', DISCOUNT_PAY);

				$code = array();
				$is_ok = true;
			if($account_codes){
					foreach($account_codes AS $codes_key => $codes_value){
						$code['credit_id'] = $codes_value['credit_id'];
						$code['debit_id'] = $codes_value['debit_id'];
						$this->getDefaultAccountCode('', DISCOUNT_PAY, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
				}
			}
				else{
					$code['credit_id'] = $codes_value['credit_id'];
					$code['debit_id'] = $codes_value['debit_id'];
					$this->getDefaultAccountCode('', DISCOUNT_PAY, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}

				if($is_ok){
					// foreach ($account_codes as $keys => $values) {
						$credits[] = array('creditid'=>$code['credit_id'], 'amount'=>floatval($ar_discount));
						$debits[] = array('debitid'=>$code['debit_id'], 'amount'=>floatval($ar_discount));
					// }
				}

				if($credits && $debits){

			$items = array('debits'=>$debits, 'credits'=>$credits);
			$items = base64_encode(json_encode($items)); 
		
			$data = array(
					'refno' => $or_no,
					'oldrefno' => $or_no,
					'pid' => $pid,
					'trdate' => strtotime($post_dte),
					'time' => date('H:i:s', strtotime($post_dte)),
					'src' => 'CSC',
					'accounts' => $items,
					'person_type' => '2');

			$url = "/modules/api/customer/discount"; 

			$result = $this->rest_post($url, $data);
		}	
	}
		}
		else
			return true;
	}

	function getcashierDiscount($or_no){
		global $db;

		$sql = "SELECT 
				  discount_tendered 
				FROM
				  seg_pay 
				WHERE or_no = ".$db->qstr($or_no);

		return $db->GetRow($sql);
	}

	function companyCharge($bill_nr){
		$info = $this->getCompanyInfo($bill_nr);

		$debit = array();
		$credit = array();

		if($info){
		foreach($info AS $key => $value){
			$this->checkInsurance($value['comp_id'], $value['comp_full_name'], $value['comp_add']);

			$account_codes = $this->getAccountCodes($value['comp_id'], '', COMPANY_CHARGE);

				$code = array();
				$is_ok = true;
				if($account_codes){
					foreach($account_codes AS $codes_key => $codes_value){
						$code['credit_id'] = $codes_value['credit_id'];
						$code['debit_id'] = $codes_value['debit_id'];
						$this->getDefaultAccountCode('', COMPANY_CHARGE, $code);
					}
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
					}
				}
				else{
					$code['credit_id'] = $codes_value['credit_id'];
					$code['debit_id'] = $codes_value['debit_id'];
					$this->getDefaultAccountCode('', COMPANY_CHARGE, $code);
					/**
					 * Check if everything contains an account, if not, continue to the next data
					 */
					foreach ($code as $code_key => $code_value) {
						if(!$code_value || $code_value == '0.0' || $code_value == '0' || $code_value == '')
							$is_ok = false;
			}
				}

				if($is_ok){
					// foreach ($account_codes as $keys => $values) {
						$credit[] = array('creditid'=>$code['credit_id'], 'amount'=>floatval($value['bill_amount']));
						$debit[] = array('debitid'=>$code['debit_id'], 'amount'=>floatval($value['bill_amount']));
					// }
				}

			$date = $value['bill_date'];
			$pid = $value['comp_id'];
		}

			if($credit && $debit){
		$items = array('debits'=>$debit, 'credits'=>$credit);
		$items = base64_encode(json_encode($items));

		$data = array(
				'refno' => $bill_nr,
				'oldrefno' => $bill_nr,
				'pid' => $pid,
				'trdate' => date('m/d/Y', strtotime($date)),
				// 'time' => date('H:i:s', strtotime($date)),
				'src' => 'COM',
				'accounts' => $items,
				'encounter_nr' => '',
				'person_type' => '2');

		$url = "/modules/api/customer/charge"; 

		$result = $this->rest_post($url, $data);
	}
		}
		else
			return true;
	}

	function rest_post($url, $data, $headers = null, $method = 'POST')
	{
		$global_config = new GlobalConfig($GLOBAL_CONFIG);
		$global_config->getConfig('fis_%');

		$address = $GLOBAL_CONFIG['fis_address'];
		$folder = $GLOBAL_CONFIG['fis_folder'];
		$user = $GLOBAL_CONFIG['fis_user'];
		$password = $GLOBAL_CONFIG['fis_password'];
		$credentials = '?cmp=0&uid='.$user.'&pwd='.$password;

		$url = "http://".$address."/".$folder.$url.$credentials;

		// convert data properly since there's a problem with curl's json encoding
		$newData = null;
		$this->http_build_query_for_curl($data, $newData);
		$httpheaders = $headers ? $headers : array();
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_HEADER, 0);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $httpheaders);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $newData);
		
		ob_start();
		curl_exec($handle);
		$content = ob_get_contents();
		ob_end_clean();
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		// close cURL resource, and free up system resources
		return array('code' => $code, 'content' => $content);

	}

	function rest_delete($url, $headers = null){
		$global_config = new GlobalConfig($GLOBAL_CONFIG);
		$global_config->getConfig('fis_%');

		$address = $GLOBAL_CONFIG['fis_address'];
		$folder = $GLOBAL_CONFIG['fis_folder'];
		$user = $GLOBAL_CONFIG['fis_user'];
		$password = $GLOBAL_CONFIG['fis_password'];
		$credentials = '?cmp=0&uid='.$user.'&pwd='.$password;

		$url = "http://".$address."/".$folder.$url.$credentials;
		
		$httpheaders = $headers ? $headers : array();
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_HEADER, 0);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $httpheaders);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "DELETE");
		
		ob_start();
		curl_exec($handle);
		$content = ob_get_contents();
		ob_end_clean();
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		// close cURL resource, and free up system resources
		return $content;
	}

	function rest_get($url, $headers = null, $method = 'GET')
	{
		$global_config = new GlobalConfig($GLOBAL_CONFIG);
		$global_config->getConfig('fis_%');

		$address = $GLOBAL_CONFIG['fis_address'];
		$folder = $GLOBAL_CONFIG['fis_folder'];
		$user = $GLOBAL_CONFIG['fis_user'];
		$password = $GLOBAL_CONFIG['fis_password'];
		$credentials = '?cmp=0&uid='.$user.'&pwd='.$password;

		$url = "http://".$address."/".$folder.$url.$credentials;
		// convert data properly since there's a problem with curl's json encoding
		$newData = null;
		// $this->http_build_query_for_curl($data, $newData);
		$httpheaders = $headers ? $headers : array();
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_HEADER, 0);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $httpheaders);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		
		ob_start();
		curl_exec($handle);
		$content = ob_get_contents();
		ob_end_clean();
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		// close cURL resource, and free up system resources
		return $content;

	}

	function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

		if ( is_object( $arrays ) ) {
			$arrays = get_object_vars( $arrays );
		}

		foreach ( $arrays AS $key => $value ) {
			$k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
			if ( is_array( $value ) OR is_object( $value )  ) {
				$this->http_build_query_for_curl( $value, $new, $k );
			} else {
				$new[$k] = $value;
			}
		}
	}

	function getDayDiff($to,$from,$bill_date){
		$tmpTo = strtotime($to);
		$tmpFrom = strtotime($from);
		$tmpBill = strtotime($bill_date);

		$tmpTo = strtotime(date('Y-m-d',$tmpTo));
		$tmpFrom = strtotime(date('Y-m-d',$tmpFrom));
		$tmpBill = strtotime(date('Y-m-d',$tmpBill));

		// $test .= " >> to: " . $to . " >> from: " . $from . " >> bill: " . $bill_date . "\n";

		if ($tmpTo != $tmpFrom){
			if($tmpTo <=0){
				$output = round(($tmpBill - $tmpFrom) / 86400);
				if($output == 0)
					return 1;
				else if($output < 0)
					return 0;
				else
					return $output;
			}
			$output = round(($tmpTo - $tmpFrom) / 86400);
			if($output == 0)
				return 1;
			else if($output < 0)
				return 0;
			else
				return $output;
		}else{
			return 0;
		}
	}

	function getRadioDepartment($code){
		global $db;

		$sql = "SELECT group_code FROM seg_radio_services WHERE service_code = ".$db->qstr($code);

		if($result = $db->Execute($sql)){
			return $result->FetchRow();
		}
		else
			return false;

	}

	function getRadioItems($refno, $item, $or){
		global $db;

		$sql = "SELECT tr.`service_code`, tr.`refno`, tr.`price_cash`, r.`encounter_nr`, r.`pid`, r.`request_date`, rs.`group_code`, spr.`or_no`
					FROM care_test_request_radio AS tr
						INNER JOIN seg_radio_serv AS r
							ON tr.`refno` = r.`refno`
						INNER JOIN seg_radio_services AS rs
							ON tr.`service_code` = rs.`service_code`
						LEFT JOIN seg_pay_request AS spr
							ON spr.`service_code` = tr.`service_code`
								AND spr.`or_no` = '$or'
					WHERE tr.`refno` = $refno
						AND tr.`service_code` = '$item'";

		if($result = $db->Execute($sql)){
			return $this->walkinRadioItem($result->FetchRow());
		}
		else
			return false;		
	}

	function getLabItems($refno, $item, $or){
		global $db;

		$sql = " SELECT ls.`refno`, ls.`encounter_nr`, ls.`pid`, ls.`serv_dt`,  lsd.`price_cash`, lsd.`quantity`, lsd.`service_code`, spr.`or_no`
				  	FROM seg_lab_servdetails AS lsd
				  		INNER JOIN seg_lab_serv AS ls
				  			ON lsd.`refno` = ls.`refno`
				  		LEFT JOIN seg_pay_request AS spr
							ON spr.`service_code` = lsd.`service_code`
								AND spr.`or_no` = '$or'
				  	WHERE lsd.`refno` = $refno
				  		AND lsd.`service_code` = '$item'";
	
		if($result = $db->Execute($sql)){
			return $this->walkinLabItem($result->FetchRow());
		}
		else
			return false;		
	}

	function getPharmaItems($refno, $frombill){
		global $db;

		$refno = $db->qstr($refno);

		if($frombill)
    		$where_refno = "po.`encounter_nr` = ".$refno." AND po.`is_cash` != '1'";
    	else
    		$where_refno = "poi.`refno` = ".$refno;

		$sql = "SELECT poi.`bestellnum`, poi.`quantity`, poi.`pricecharge`, poi.`serve_status`, po.`refno`,
							IFNULL(po.`walkin_pid`, po.`pid`) AS pid, po.`encounter_nr`, po.`orderdate`
						FROM seg_pharma_order_items AS poi
							INNER JOIN seg_pharma_orders AS po 
								ON po.`refno` = poi.`refno`
					WHERE $where_refno AND poi.`is_deleted` != '1'";

		if($result = $db->GetAll($sql)){
			return $result;
		}
		else
			return false;
	}

	function getBillAmount($or_no){
		global $db;

		$sql = "SELECT pid, encounter_nr, or_date
					FROM seg_pay
					WHERE cancelled_by = ''
						AND or_no = '$or_no'";

		if($result = $db->Execute($sql)){
			return $result->FetchRow();
		}
		else
			return false;
	}

// added by Margaret 06/26/14
	// sets debit and credit accounts for ledger entry
	function setDebitCredit($debitId, $creditId, $debitAmt, $creditAmt){
		$accounts = array();
		$accounts['debits'] = array();
		$accounts['credits'] = array();
		if($debitId)
		{
			$accounts['debits'][] = array('debitid'=>$debitId, 'amount'=>$debitAmt);
		}
		if($creditId)
		{
			$accounts['credits'][] = array('creditid'=>$creditId, 'amount'=>$creditAmt);
		}
		
		return $accounts;
	}
	// end


	/**
	* Modified By Jarel 10/16/2014
	* Update Query 
	*/
	function getCoverageData($bill_nr, $include_total=false){
		global $db;
		$bill_nr = $db->qstr($bill_nr);

		if($include_total){
			$total_coverage = ", SUM(sbcr.`amount`) total_coverage, SUM(sbcr.`hci_amount`) total_hci ";
			$total_coverage_join = " LEFT JOIN seg_billing_caserate sbcr
    								ON sbc.`bill_nr` = sbcr.`bill_nr` ";
		}else{
			$total_coverage = "";
			$total_coverage_join = "";
		}

		$this->sql = "SELECT cif.`hcare_id`, cif.`firm_id`, cif.`name`, cif.`addr_mail`,
						(sbc.`total_acc_coverage` + sbc.`total_med_coverage` + 
						sbc.`total_msc_coverage` + sbc.`total_srv_coverage` + sbc.`total_ops_coverage`) AS hospital_coverage,
						(sbc.`total_d1_coverage`+ sbc.`total_d2_coverage` + sbc.`total_d3_coverage`
						+ sbc.`total_d4_coverage`) AS doctor_coverage $total_coverage
					FROM seg_billing_coverage AS sbc
						INNER JOIN care_insurance_firm AS cif
							ON sbc.`hcare_id` = cif.`hcare_id`
						$total_coverage_join
					WHERE sbc.`bill_nr` = ".$bill_nr;
		
		if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
	}

	function getDiscountAmount($bill_nr){
		global $db;

		$bill_nr = $db->qstr($bill_nr);

		$sql = "SELECT (sbd.`hospital_income_discount` + sbd.`professional_income_discount` + sop.`hospital_discount`) AS amount
				FROM seg_billingcomputed_discount AS sbd
					LEFT JOIN seg_other_payment AS sop
						ON sop.`bill_nr` = sbd.`bill_nr`
				WHERE sbd.`bill_nr` = ".$bill_nr;

		$result = $db->GetRow($sql);
		if($result)
			return $result['amount'];
		else
			return false;
	}

	function getPatientInfo($pid){
		global $db;

		$pid = $db->qstr($pid);

		$sql = "SELECT 
				  fn_get_person_name(cp.`pid`) AS name, cp.`street_name`, sb.`brgy_name`, 
				  sm.`zipcode`, sm.`mun_name`, sp.`prov_name`, sr.`region_name`
				FROM
				 care_person AS cp 
				  LEFT JOIN seg_barangays AS sb 
				    ON sb.`brgy_nr` = cp.`brgy_nr` 
				  LEFT JOIN seg_municity AS sm 
				    ON sm.`mun_nr` = cp.`mun_nr` 
				  LEFT JOIN seg_provinces AS sp 
				    ON sp.`prov_nr` = sm.`prov_nr` 
				  LEFT JOIN seg_regions AS sr 
				    ON sr.`region_nr` = sp.`region_nr` 
				WHERE cp.`pid` = ".$pid;

		$result = $db->GetRow($sql);

		if($result)
			return $result;
		else
			return false;
	}

	function getPid($or){
		global $db;

		$or = $db->qstr($or);

		$sql = "SELECT pid
					FROM seg_pay
					WHERE or_no = ".$or;

		$result = $db->GetRow($sql);

		if($result)
			return $result['pid'];
		else
			return false;
	}

	function searchWalkin($pid){
		global $db;

		$pid = $db->qstr($pid);

		$sql = "SELECT fn_get_walkin_name(pid) AS name, address 
					FROM seg_walkin 
					WHERE status != 'deleted'
						AND pid = ".$pid;

		$result = $db->GetRow($sql);

		if($result)
			return $result;
		else
			return false;

	}

	function creditMemoSearch($nr){
		global $db;

		$nr = $db->qstr($nr);

		$sql = "SELECT scm.`memo_nr`, scm.`encounter_nr`, scm.`pid`, scm.`refund_amount`, scmd.`ref_source`, scmd.`quantity`,
						 scmd.`price`, scmd.`service_code`, sp.`vat_amount` 
					FROM seg_credit_memos AS scm
					INNER JOIN seg_credit_memo_details AS scmd
						ON scm.`memo_nr` = scmd.`memo_nr`
					LEFT JOIN seg_pay sp 
    					ON sp.`or_no` = scmd.`or_no` 
					WHERE scm.`memo_nr` = ".$nr;

		$result = $db->Execute($sql);

		if($result)
			return $result;
		else
			return false;
	}

	function getCashDetails($or){
		global $db;

		$or = $db->qstr($or);

		$sql = "SELECT or_date, pid, or_no 
					FROM seg_pay
					WHERE or_no = ".$or;

		if($result = $db->Execute($sql))
			return $result->FetchRow();
		else
			return false;
	}

	function getPharmaAmount($refno, $code){
		global $db;

		$refno = $db->qstr($refno);
		$code = $db->qstr($code);

		$sql = "SELECT pricecharge
					FROM seg_pharma_order_items
					WHERE refno = ".$refno."
						AND bestellnum = ".$code;

		if($result = $db->GetRow($sql))
			return $result['pricecharge'];
		else
			return false;
	}
		#added by ken 10/10/2014 to get the sum of served amount in radio request charges
	function getServiceAmount($refno, $frombill){
		global $db;

		$refno = $db->qstr($refno);

		if($frombill)
    		$where_refno = "srs.`encounter_nr` = ".$refno;
    	else
    		$where_refno = "srs.`refno` = ".$refno;

		$this->sql = "SELECT srs.`refno`, srs.`pid`, srs.`encounter_nr`, srs.`request_date`, 
							srs.`request_time`, srs.`is_cash`, ctrr.`price_charge`, ctrr.`service_code`
						FROM seg_radio_serv AS srs
							INNER JOIN care_test_request_radio AS ctrr
								ON ctrr.`refno` = srs.`refno`
									AND ctrr.`is_served` = '1'
									AND ctrr.`status` != 'deleted'
						WHERE $where_refno AND srs.`status` != 'deleted' ";

		$result = $db->GetAll($this->sql);

		if($result)
			return $result;
		else
			return false; 
	}

	function getMiscServiceAmount($refno, $frombill){
		global $db;

		$refno = $db->qstr($refno);

		if($frombill)
    		$where_refno = "sms.`encounter_nr` = ".$refno;
    	else
    		$where_refno = "sms.`refno` = ".$refno;

		$this->sql = "SELECT sms.`refno`, sms.`encounter_nr`, ce.`pid`, sms.`chrge_dte`, smsd.`chrg_amnt`, smsd.`quantity`, sos.`service_code`
						FROM seg_misc_service AS sms
							INNER JOIN seg_misc_service_details AS smsd
								ON sms.`refno` = smsd.`refno`
							INNER JOIN seg_other_services AS sos
								ON sos.`alt_service_code` = smsd.`service_code`
							INNER JOIN care_encounter AS ce
								ON ce.`encounter_nr` = sms.`encounter_nr`
						WHERE $where_refno ";

		$result = $db->GetAll($this->sql);

		if($result)
			return $result;
		else
			return false; 
	}

	function getBillingDeposits($encounter_nr){
		global $db;

		$encounter_nr = $db->qstr($encounter_nr);

		$this->sql = "SELECT sp.`or_no`, sp.`encounter_nr`, sp.`or_date`, sp.`amount_due`, sp.`pid`
						FROM seg_pay AS sp 
						    INNER JOIN seg_pay_request AS spr 
						      ON sp.`or_no` = spr.`or_no` AND spr.`service_code` = 'DEPOSIT'
						    INNER JOIN care_encounter AS ce 
						      ON ce.`encounter_nr` = sp.`encounter_nr` 
						WHERE sp.`encounter_nr` = ".$encounter_nr." AND sp.`cancel_date` IS NULL";

		$result = $db->GetAll($this->sql);

		if($result)
			return $result;
		else
			return false; 
	}

	function getOrItems($or){
		global $db;

		$or = $db->qstr($or);

		$this->sql = "SELECT spr.*, sp.`encounter_nr`, sp.`pid`, sp.`or_date`, sp.`discount_tendered`, sp.`vat_amount`, sp.`or_name`
						FROM seg_pay AS sp
							LEFT JOIN seg_pay_request AS spr
								ON sp.`or_no` = spr.`or_no`
						WHERE sp.`or_no` = ".$or;

		if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
	}

	function getVatItemPharma($code){
		global $db;

		$code = $db->qstr($code);

		$this->sql = "SELECT is_vat 
						FROM care_pharma_products_main
						WHERE bestellnum = ".$code;

		if($result = $db->GetOne($this->sql))
			return $result;
		else
			return false;
	}

	function getPFpayment($or, $is_sum=false){
		global $db;

		$or = $db->qstr($or);

		if($is_sum)
			$field_set = " SUM(spd.amount_due) amount_due ";
		else
			$field_set = " spd.* ";
		$this->sql = "SELECT $field_set
						FROM seg_pay_doctor AS spd
						WHERE spd.`or_no` = ".$or;

		if($result = $db->GetRow($this->sql))
			return $result;
		else
			return false;
	}
	#ended by ken


	/**
	 * @author Jarel 
	 * 10/15/2014
	 * Get List of Pharma Products Added in Billing 
	 */
	function getPharmaFromBilling($encounter,$bill_date,$from_date)
	{
		global $db;
        $toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($bill_date)));

        $prev_encounter = $db->GetOne("SELECT parent_encounter_nr
                   FROM care_encounter
                   WHERE encounter_nr = ".$db->qstr($encounter));


		if ($prev_encounter != '') $filter = " OR mph.encounter_nr = ".$db->qstr($prev_encounter);
		
		$this->sql = "SELECT mpd.refno, 'Order' AS source, mpd.bestellnum,\n".
	                "CONCAT(artikelname,'----',generic)  AS artikelname,\n".
	                "SUM(quantity) AS qty,\n".
	                "(SUM(unit_price * quantity)/SUM(quantity)) AS price,\n".
	                "SUM(quantity * unit_price) AS itemcharge\n".
		            "FROM seg_more_phorder AS mph\n".
		                "INNER JOIN seg_more_phorder_details AS mpd ON mph.refno = mpd.refno\n".
		                "INNER JOIN care_pharma_products_main AS p ON mpd.bestellnum = p.bestellnum\n".
		            "WHERE\n".
		                "(mph.encounter_nr = ".$db->qstr($encounter)." ".$filter.")\n".
		                "AND (mph.chrge_dte BETWEEN CAST(" .$db->qstr($from_date). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n".
		            "GROUP BY bestellnum, artikelname ORDER BY artikelname\n";

		if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
	}

	#added by ken 10/16/2014 - to check if it is cash trans or charge trans when updating the lab request
	function checkIsCashLab($refno){
		global $db;

		$is_cash = $db->GetOne("SELECT is_cash
									FROM seg_lab_serv
									WHERE refno = ".$db->qstr($refno));

		if($is_cash)
			return $is_cash;
		else
			return false;
	}
	#added by janken 10/22/2014 for account codes in account mapping
	function getAccountCodes($id, $area='', $code){
		global $db;

		$id = $db->qstr($id);

		if($area)
			$cond = "AND dept_code = ". $db->qstr($area);
		else
			$cond = '';

		$this->sql = "SELECT debit_id, credit_id, income_account, cash_account, tax_account, inventory_account, cogs_account
						FROM seg_account_map
						WHERE transaction_code = '$code' AND entry_id = $id $cond";

		if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
	}

	/**
	 * Get the default account based on what the cost center really needs
	 */
	function getDefaultAccountCode($area='', $code, &$accounts){
		global $db;

		$columns = '';
		foreach ($accounts as $key => $value) {
			if(!$value || $value == '0.0' || $value == '0' || $value == '')
				$columns .= $key.', ';
		}

		$columns = substr($columns, 0, -2);

		if(!$columns)
			return true;

		if($area)
			$cond = "AND dept_code = ". $db->qstr($area);
		else
			$cond = '';

		$this->sql = "SELECT $columns 
						FROM seg_account_map
						WHERE  transaction_code = '$code' $cond AND is_default";
						
		if($result = $db->GetAll($this->sql)){
			foreach($result AS $codes_key => $codes_value){
				foreach ($codes_value as $codes_value_key => $account) {
					$accounts[$codes_value_key] = $account;
				}
			}
			return true;
		}
		else
			return false;
	}

	#added by janken 10/22/2014 for getting the misc items in billing
	function getMiscFromBilling($encounter,$bill_date,$from_date)
	{
		global $db;
        $toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($bill_date)));

        $prev_encounter = $db->GetOne("SELECT parent_encounter_nr
                   FROM care_encounter
                   WHERE encounter_nr = ".$db->qstr($encounter));


		if ($prev_encounter != '') $filter = " OR mc.encounter_nr = ".$db->qstr($prev_encounter);
		
		$this->sql = "SELECT mcd.`service_code`, sos.`name`, sos.`description`, mcd.`refno`, SUM(mcd.`quantity`) AS qty, 
							(SUM(quantity * chrg_amnt)/SUM(mcd.`quantity`)) AS avg_chrg,
                          	SUM(quantity * chrg_amnt) AS total_chrg 
                       	FROM (seg_misc_chrg AS mc 
                       		INNER JOIN seg_misc_chrg_details AS mcd 
                       			ON mc.`refno` = mcd.`refno`) 
							INNER JOIN seg_other_services AS sos 
								ON mcd.`service_code` = sos.`service_code` 
                       WHERE (encounter_nr = ".$db->qstr($encounter)." ".$filter.") 
                           AND (mc.chrge_dte BETWEEN CAST(" .$db->qstr($from_date). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))
                       GROUP BY mcd.`service_code`, sos.`name` 
                       ORDER BY sos.`name`";

		if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
	}

	#added by janken 10/23/2014 for dmhi purposes only in getting the doctor amount
	function getDocAmount($bill_nr){
        global $db;

        $bill_nr = $db->qstr($bill_nr);

        $this->sql = "SELECT role_area, dr_claim, dr_nr, dr_charge
                        FROM seg_billing_pf
                        WHERE bill_nr = $bill_nr";

        if($result = $db->GetAll($this->sql))
            return $result;
        else
            return false;
    }

	function getBillingDoctors($encounter_nr, $bill_nr=null){
        global $db;

        $encounter_nr = $db->qstr($encounter_nr);

        $where_bill_nr = '';

        $bill_nr_1 = '';
       	$bill_nr_2 = '';

        if ($bill_nr != null || $bill_nr != 0){
        	$where_bill_nr = ", IF(bpf.`dr_claim` IS NULL, 0, bpf.`dr_claim`) AS claim_deduction";
        	$bill_nr_1 = "LEFT JOIN seg_billing_pf bpf ON dra.`dr_nr` = bpf.`dr_nr` AND bpf.`bill_nr` =". $db->qstr($bill_nr);
        	$bill_nr_2 = "LEFT JOIN seg_billing_pf bpf ON drp.`dr_nr` = bpf.`dr_nr` AND bpf.`bill_nr` =". $db->qstr($bill_nr);
        }
       	else{
       		$bill_nr_1 = '';
       		$bill_nr_2 = '';
       	}

        $this->sql = "	SELECT dra.`dr_nr`, dra.`dr_charge` $where_bill_nr
						FROM seg_encounter_dr_add dra 
							$bill_nr_1
						WHERE dra.`encounter_nr` = $encounter_nr

						UNION ALL

						SELECT drp.`dr_nr`, drp.`dr_charge` $where_bill_nr
						FROM seg_encounter_privy_dr drp 
							$bill_nr_2
						WHERE drp.`encounter_nr` = $encounter_nr";
		
        if($result = $db->GetAll($this->sql))
            return $result;
        else
            return false;
    }

    #added by janken 11/05/2014 to get discounted amount in pf in other discounts
    function getDocDiscAmount($bill_nr){
    	global $db;

    	$bill_nr = $db->qstr($bill_nr);

        $this->sql = "SELECT bill_areas, amnt_discount, dr_nr, discountid
                        FROM seg_billing_other_discounts
                        WHERE refno = $bill_nr";

        if($result = $db->GetAll($this->sql))
            return $result;
        else
            return false;
    }

    #added by janken 10/23/2014 for getting the doctor information
    function getDoctorInfo($dr_nr){
    	global $db;

		$dr_nr = $db->qstr($dr_nr);

		$sql = "SELECT 
				  fn_get_person_name(cp.`pid`) AS name, cp.`street_name`, sb.`brgy_name`, 
				  sm.`zipcode`, sm.`mun_name`, sp.`prov_name`, sr.`region_name`
				FROM care_personell AS cpl
				  INNER JOIN care_person AS cp 
					ON cp.`pid` = cpl.`pid`
				  LEFT JOIN seg_barangays AS sb 
				    ON sb.`brgy_nr` = cp.`brgy_nr` 
				  LEFT JOIN seg_municity AS sm 
				    ON sm.`mun_nr` = cp.`mun_nr` 
				  LEFT JOIN seg_provinces AS sp 
				    ON sp.`prov_nr` = sm.`prov_nr` 
				  LEFT JOIN seg_regions AS sr 
				    ON sr.`region_nr` = sp.`region_nr` 
				WHERE cpl.`nr` = ".$dr_nr;

		$result = $db->GetRow($sql);

		if($result)
			return $result;
		else
			return false;
    }

    #added by janken 10/23/2014 for getting the main refno of misc
    function getMiscServiceCode($service_code){
    	global $db;

    	$service_code = $db->qstr($service_code);

    	$this->sql = "SELECT service_code 
    					FROM seg_other_services
    					WHERE alt_service_code = ".$service_code;

    	if($result = $db->GetOne($this->sql))
    		return $result;
    	else
    		return false;
    }

    #added by janken 10/28/2014 getting the services in lab
    function getLabServices($refno, $frombill){
    	global $db;

    	$refno = $db->qstr($refno);

    	if($frombill)
    		$where_refno = "sls.`encounter_nr` = ".$refno;
    	else
    		$where_refno = "sls.`refno` = ".$refno;

    	$this->sql = "SELECT sls.`refno`, sls.`pid`, sls.`encounter_nr`, sls.`serv_dt`, 
							sls.`serv_tm`, sls.`is_cash`, slsd.`price_charge`, slsd.`service_code`
						FROM seg_lab_serv AS sls
							INNER JOIN seg_lab_servdetails AS slsd
								ON slsd.`refno` = sls.`refno` 
									AND slsd.`is_served` = '1'
									AND slsd.`status` != 'deleted'
						WHERE $where_refno AND sls.`status` != 'deleted' ";

		if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
    }

    #added by janken 11/03/2014 getting the discount data
    function getDiscountData($enc){
    	global $db;

    	$enc = $db->qstr($enc);

    	$this->sql = "SELECT discountid, discountdesc, IFNULL(hosp_acc + hosp_xlo +
							hosp_meds + hosp_ops + hosp_misc,0) AS hcidiscount
    					FROM seg_billingapplied_discount
    					WHERE encounter_nr = ".$enc;

    	if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
    }

    #added by janken 12/10/2014 getting the company data
    function getCompanyInfo($bill_nr){
    	global $db;

    	$bill_nr = $db->qstr($bill_nr);

    	$this->sql = "SELECT scbh.`comp_id`, scbd.`bill_amount`, scbh.`bill_date`, sc.`comp_full_name`, sc.`comp_add`
    					FROM seg_company_billing_d AS scbd
    						LEFT JOIN seg_company_billing_h AS scbh
    							ON scbh.`comp_bill_nr` = scbd.`comp_bill_nr`
    						LEFT JOIN seg_company AS sc
    							ON sc.`comp_id` = scbh.`comp_id`
    					WHERE scbd.`comp_bill_nr` = ".$bill_nr;

    	if($result = $db->GetAll($this->sql))
    		return $result;
    	else
    		return false;
    }

    	//added by julz
    	function GetExcessPFcharge($enc,$dr_nr){
        global $db;

        $enc = $db->qstr($enc);
          $dr_nr = $db->qstr($dr_nr);

        $this->sql = "SELECT 
					  spe.`amount` 
					FROM
					  seg_pf_excess spe 
					  LEFT JOIN seg_billing_encounter sbe 
					    ON (
					      sbe.`encounter_nr` = spe.`encounter_nr` 
					      AND sbe.`is_deleted` IS NULL
					    ) 
					  LEFT JOIN seg_claim_pay_pf scp 
					    ON scp.dr_nr = spe.`dr_nr` 
					    AND scp.`encounter_nr` = sbe.`encounter_nr`
					WHERE spe.`encounter_nr` = $enc
					AND scp.`dr_nr` =  $dr_nr";

            
        if($result = $db->GetOne($this->sql))
            return $result;
        else
            return false;
    }

}