<?php
require "./roots.php";
require_once $root_path.'include/care_api_classes/class_core.php';

// Load grantor class defs
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_cmap_grantor.php';
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_lingap_grantor.php';


/**
* SegGrant is a singleton class that maintains all active Grantor objects used in the Sponsor module
*
*
*
* @author aquinones
*/
class SegGrant extends Core {

	const GRANT_LINGAP 			= 'lingap';
	const GRANT_CMAP 				= 'cmap';

	private static $loggerName = 'sponsor';

	private static $GrantorClasses;
	private static $Me;

	private function __construct() {
		self::$GrantorClasses = Array(
			self::GRANT_CMAP 		=> 'SegCmapGrantor',
			self::GRANT_LINGAP 	=> 'SegLingapGrantor'
		);
	}


	/**
	* put your comment there...
	*
	*/
	public static function getInstance() {
		if (!self::$Me) {
			self::$Me = new SegGrant();
		}
		return self::$Me;
	}


	/**
	* Returns the Grantor class name for the Grant type specified
	*
	* @param mixed $grantor
	* @return String
	*/
	public function getGrantorClass($grantType) {
		return self::$GrantorClasses[$grantType];
	}



	/**
	* put your comment there...
	*
	* @param SegRequest $request
	*/
	public function getTotalGrants( SegRequest $request ) {
		$total = 0;
		$sql = array();
		foreach (self::$GrantorClasses as $gc) {
			if (class_exists($gc) && method_exists($gc, 'getTotalGrants')) {
				$grantor = new $gc;
				$total += $grantor->getTotalGrants($request);
				$sql[] = $grantor->getQuery();
			}
		}
		$this->setQuery(implode("\n\n",$sql));
		return $total;
	}




}

