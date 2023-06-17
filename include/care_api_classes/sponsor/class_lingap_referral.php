<?php

require "./roots.php";
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'include/care_api_classes/sponsor/class_request.php';

/**
*
*/
class SegLingapReferral extends Core {

	private $lingapTable = "seg_lingap_entries";
	private $loggerName = 'sponsor.lingap';
	private $id;
	private $referralData;

	/**
	* Constructor
	*
	*/
	public function __construct($id=null) {
		global $db;
		$this->setupLogger($this->loggerName);
		$this->setTable($this->lingapTable, $fetchMetadata=true);
		if ($id) {
			$this->fetch($id);
		}
	}




	/**
	* Cancels a referral
	*
	* Deletes the referral entry from the database, and unflags all request associated with the
	* referral. This method is extremely volatile, use in moderation.
	*
	* @todo Log the cancellation and provide handle for comments
	*/
	public function cancel( $comment='' ) {
		global $db;

		if (!$this->id) {
			return false;
		}

		$lingapGrantor = new SegLingapGrantor($this);
		$grants = $lingapGrantor->getGrants();

		$saveok= true;
		$this->logger->info("Creating request...info:".print_r($grants, true));
		foreach ($grants as $grant) {
			$request = new SegRequest($grant['type'], array(
				'refNo' => $grant['refNo'],
				'itemNo' => $grant['itemNo'],
				'entryNo' => $grant['entryNo'],
			));

			if ($lingapGrantor->ungrant($request) === false) {
				return false;
			}
		}

		$this->setQuery("DELETE FROM seg_lingap_entries WHERE id=".$db->qstr($this->id));
		$ok = $db->Execute($this->getQuery());
		if ($ok) {
			return true;
		}
		else {
			$this->logger->error("Error deleting Lingap referral entry: ".$db->ErrorMsg()."\n Query:".$this->getQuery());
			return false;
		}
	}






	/**
	* Returns the referral Id (36-char length code)
	*
	*/
	public function getId() {
		return $this->id;
	}



	/**
	* Returns the details of the referral entry.
	*
	* Convenient fetch method for SegReferral, overrides the Core class fetch function
	*
	* @param mixed $id
	* @return ADODB
	*/
	public function fetch($id) {

		$data = parent::fetch(array('id'=>$id));
		if ($data !== false) {
			$this->id=$id;
			$this->referralData = $data;
		}
		else {
			$this->logger->warn('Could not retrieve Lingap entry data: '.$this->getErrorMsg()."\nQuery:".$this->getQuery());
			unset($this->id);
			unset($this->referralData);
		}
		return $data;
	}



	/**
	* Saves the referral information specified in $data
	*
	* @param mixed $data
	* @param mixed $force_insert
	* @return boolean
	*/
	public function save($data, $force_insert=FALSE) {
		if (parent::save($data, $forced_insert) !== false) {
			$this->id = $data['id'];
			$this->referralData = $data;
			return true;
		}
		else {
			$this->logger->error('Save failed: '.$this->getErrorMsg()."\nQuery:".$this->getQuery());
			return false;
		}
	}


}
