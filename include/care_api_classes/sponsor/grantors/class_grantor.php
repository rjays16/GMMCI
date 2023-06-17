<?php
require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

/**
* @abstract
* @author alvin
*/
abstract class Grantor extends Core {
	abstract function grant(SegRequest $request, $amount=0, $remarks='');
	abstract function ungrant(SegRequest $request);
	abstract function getTotalGrants(SegRequest $request);
	abstract function getGrants(SegRequest $request=null);
}