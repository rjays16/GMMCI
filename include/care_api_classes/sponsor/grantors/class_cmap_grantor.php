<?php
require("./roots.php");
require_once $root_path.'include/care_api_classes/sponsor/class_cmap_referral.php';
require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_grantor.php';
require_once $root_path.'include/care_api_classes/sponsor/class_cmap_referral.php';


/**
 *
 */
class SegCmapGrantor extends Grantor
{

	private $id = null;
	private $referral = null;
	private $amount = null;


	private $loggerName = 'sponsor.cmap';
	private $flagValue = 'cmap';

	private static $GrantSources = Array(
		SegRequest::BILLING_REQUEST 		=> 'seg_cmap_entries_bill',
		SegRequest::PHARMACY_REQUEST 		=> 'seg_cmap_entries_pharmacy',
		SegRequest::RADIOLOGY_REQUEST 	=> 'seg_cmap_entries_radiology',
		SegRequest::LABORATORY_REQUEST 	=> 'seg_cmap_entries_laboratory',
		SegRequest::MISC_REQUEST 				=> 'seg_cmap_entries_misc'
	);

	public function __construct($id = null, $type=null) {
		global $db;
		$this->setupLogger($this->loggerName);

		if ($id)
		{
			$this->id = $id;
			$builder = $this->get_builder_def( $type );

			if ($builder)
			{
				$builder['where'][] = array("EQ", 'id', $this->id);
				$query = QueryBuilder::build($builder);

				$row = $db->GetRow($query);
				if (false !== $row)
				{
					$this->referral = new SegCmapReferral( $row['referral'] );
					$this->amount = (float) $row['totalAmount'];
//					var_dump($this->amount);
//					exit;
				}
				else
				{
//					print_r($query);
//					die();
					$this->id = null;
				}
			}
			else
			{
//				var_dump($type);
//				exit;
				$this->id = null;
			}
		}
		else
		{
			$this->id = create_guid();
		}

//		if ($referral)
//		{
//			$this->referral = $referral;
//			if (!$this->referral->getId())
//			{
//				$this->referral = null;
//			}
//		}
	}


	public function getId()
	{
		return $this->id;
	}


	public function getRequest()
	{

	}


	public function getReferral()
	{

	}

	/**
	* put your comment there...
	*
	* @param SegRequest $request
	* @param mixed $amount
	* @param mixed $remarks
	*/
	public function grant(SegRequest $request, $amount=0, $remarks='') {
		global $db;

		$types = SegRequest::getRequestTypes();
		$this->logger->info("Attempting to grant CMAP...type:".$types[$request->getRequestType()]."\nkey:".print_r($request->getKeys(), true));

		$def = $this->get_builder_def( $request->getRequestType() );
		if (!$def) {
			$this->logger->error("Invalid builder def found: ".$request->getRequestType()."def:".print_r($request->getKeys(), true));
			return false;
		}

		$info = $request->fetch();
		if (!$info) {
			return false;
		}

		if($info['pid']!="")
		$pid = $info['pid'];
		else if($info['walkinPid'])
			$walkinPid = $info['walkinPid'];

		$grant_amount = (float)$amount;
		if (!$amount ) {
			$this->logger->error('Invalid amount specified. amount:'.var_export($amount, true));
			return false;
		}


		$amount_due = (float)$info['unitPrice'] * (float)$info['quantity'];
		// get granted total and subtract from total amount
		$amount_due -= $request->getTotalGrants();
		if ($amount_due<0) {
			$this->logger->error('Ooops! Amount due is 0! Check query: '.$request->getQuery());
			return false;
		}

		// Only grant exact amount due for the request, excess amount will be discarded
		if ( $grant_amount > $amount_due) {
			$this->logger->warn('Request amount (due) is lower than grant amount specified...grant:'.$grant_amount."\ndue:".$amount_due);
			$grant_amount = $amount_due;
		}

		$referral = new SegCmapReferral();
		if($pid)
		$referral_balance = (float) $referral->total(array('PID'=>$pid));
		else if($walkinPid)
			$referral_balance = (float) $referral->total(array('WALKIN_PID'=>$walkinPid));

		// Check if patient has enough balance
		if ($referral_balance < $grant_amount) {
			$this->logger->error('Grant amount exceeds referral balance...amount:'.$grant_amount);
			return false;
		}

		// Go through all referrals by descending date and keep deducting from remaining balance until entire amount is covered
		$referrals = $referral->getReferrals(array(
			'PID' => ($pid ? $pid : 'W'.$walkinPid),
			'NOINACTIVE' => true,
			'SORTSQL' => 'referral_date DESC'
		));

		$running_amount = $grant_amount;
		$request_amount = $amount_due;
		if ($referrals) {
			foreach ($referrals as $refer) {

				$balance = (float) $refer['current_balance'];
				if ($balance==0)
					continue;

				if ( $balance>$running_amount ) {
					$grant_amount = $running_amount;
					$balance -= $running_amount;
					$running_amount = 0.0;
				}
				else {
					$grant_amount = $balance;
					$running_amount -= $balance;
					$balance = 0.0;
				}

				# Update referral running balance
				if (($ok = $referral->updateBalance($balance, $refer['id'])) === false)  {
					return false;
				}

				/**
				* @todo Build data array from builder definition for more flexibility to changes in structure
				*/

				$keys = $request->getKeys();
				$data = array(
					'id' => $db->qstr(create_guid()),
					'referral_id' => $db->qstr($refer['id']),
					'pid' => $db->qstr($info['pid']),
					'walkin_pid' => $db->qstr($info['walkinPid']),
					'ref_no' => $db->qstr($keys['refNo']),
					'service_code' => $db->qstr($keys['itemNo']),
					'service_name' => $db->qstr($info['itemName']),
					'quantity' => $db->qstr($info['quantity']),
					'amount' => (float)$grant_amount,
					'remarks' => $db->qstr($remarks),
					'create_id' => $db->qstr($_SESSION['sess_temp_userid']),
					'create_time' => "NOW()"
				);

				// entry_no for Miscellaneous requests
				if ($info['entryNo']) {
					$data['entry_no'] = $db->qstr( $info['entryNo'] );
				}

				if ($this->setResult( $db->Replace($def['coreTable'], $data, 'id', $autoQuote = false) ) === 0) {
					$this->logger->error( 'Cannot save grant information: '.$db->ErrorMsg() );
					return false;
				}
				else {
					$request_amount -= $grant_amount;
				}

				# If running amount reaches 0, no need to search more...
				if ($running_amount == 0)
					break;
			}

			if ($request_amount > 0) {
				$this->logger->warn('Amount not fully covered (referral amount depleted). Outstanding balance: '.$request_amount);
                return false;
			}
			else {
				$ok = $request->flag($this->flagValue);
				if (!$ok) {
					return false;
				}
			}

			// Success
			return true;
		}
		else {
			return false;
		}
	}



	/**
     * put your comment there...
     *
     * @param SegRequest $request
     * @param mixed $amount
     */
	public function ungrant(SegRequest $request)
	{
		global $db;

		if (!($this->referral instanceof SegCmapReferral) || !$this->getId()) {
			// need to specify a referral for this method
			return false;
		}

		$types = SegRequest::getRequestTypes();
		$this->logger->info("Attempting to UNgrant CMAP...type:".$types[$request->getRequestType()]."\nkey:".print_r($request->getKeys(), true));
		$builder = $this->get_builder_def( $request->getRequestType() );

		$where = array();
		foreach ($request->getKeys() as $key=>$value)
		{
			$expr = QueryBuilder::express($builder, 'EQ', $key, $value);
			if ($expr) $where[] = $expr;
		}
		$expr = QueryBuilder::express($builder, 'EQ', 'id', $this->getId());
		if ($expr) $where[] = $expr;


		$query = "DELETE FROM ".$builder['coreTable']." WHERE (".implode(") AND (", $where).")";

		$ok = $this->setResult( $db->Execute($this->setQuery($query)) );
		if ($ok !== false)
		{

			$amount = $this->referral->getBalance();
			$grantAmount = $amount + $this->amount;

			if (!$grantAmount)
			{
				$this->logger->error("grantAmount: ".print_r($grantAmount,true));
				return false;
			}

			if ( false === ($this->referral->updateBalance($grantAmount)) )
			{
				$this->logger->error("updateBalance: ".$db->ErrorMsg());
				return false;
			}
		}
		else
		{
			$this->logger->error('Failed to delete Lingap entry detail: '.$db->ErrorMsg()."\nQuery: ".$query);
			return false;
		}

		if (!$request->unflag())
		{
			return false;
		}

		return true;
	}



	/**
     * put your comment there...
     *
     * @param SegRequest $request
     * @return ADODB
     */
	public function getTotalGrants(SegRequest $request) {
		global $db;

		$def = $this->get_builder_def( $request->getRequestType() );

		if (!$def) {
			$this->logger->info( 'Cannot process request of this type: '. $request->getRequestType() );
			return 0;
		}

		$sql = "SELECT SUM(".$def['fields']['totalAmount'].") FROM {$def['coreTable']} ";
		$where = array();
		foreach ($request->getKeys() as $key=>$value) {
			$expr = QueryBuilder::express($def, 'EQ', $key, $value);
			if ($expr) $where[] = $expr;
		}
		if ($where) {
			$sql.=" WHERE (".implode(") AND (", $where).")";
		}
		$this->setQuery($sql);
		$this->setResult($db->GetOne($this->getQuery()));
		//$this->logger->info($sql);
		if ($this->getResult() === false) {
			$this->logger->error("Cannot retrieve amount! Error: ".$db->ErrorMsg()."\nQuery: ".$this->getQuery());
		}
		return $this->getResult();
	}




	/**
     * put your comment there...
     *
     * @param SegRequest $request
     */
	public function getGrants(SegRequest $request=null) {

	}


	/**
     * put your comment there...
     *
     * @access private
     * @param mixed $request_type
     */
	private function get_builder_def($request_type) {
		$def = Array(
			'coreTable' => self::$GrantSources[$request_type],
			'joins' => Array(),
			'fields' => Array(
				'id' => 'id',
				'referral' => 'referral_id',
				'refNo' => 'ref_no',
				'pid' => 'pid',
				'itemNo' => 'service_code',
				'itemName' => 'service_name',
				'unitPrice' => 'amount/quantity',
				'quantity' => 'quantity',
				'totalAmount' => 'amount',
				'remarks' => 'remarks'
			),
			'orderBy'		=> Array(),
			'where' 		=> Array()
		);

		switch($request_type) {
			case SegRequest::MISC_REQUEST:
				$def['fields']['entryNo'] = 'entry_no';
				return $def;
			case SegRequest::BILLING_REQUEST:
			case SegRequest::PHARMACY_REQUEST:
			case SegRequest::RADIOLOGY_REQUEST:
			case SegRequest::LABORATORY_REQUEST:
				return $def;
			default:
				return false;
		}
	}

}

