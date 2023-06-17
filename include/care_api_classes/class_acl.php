<?php

/**
 * @package care_api
 */

require_once $root_path.'include/care_api_classes/class_core.php';

/**
 * Class that handles user permissions
 * @author Alvin Quinones
 */
class Acl extends Core {

	const PERMISSION_SYSAD = 'System_Admin';
	const PERMISSION_ALL = '_a_0_all';

	protected $user;

	/**
	 *
	 *
	 */
	public function __construct($loginId)
	{
		$this->getUser($loginId);
	}

	/**
	 *
	 */
	public function checkPermissionRaw($permission)
	{
		$permissions = $this->user['permission'];
		if ((strpos($permissions, self::PERMISSION_SYSAD) !== false) ||
			(strpos($permissions, self::PERMISSION_ALL) !== false)) {
			return true;
		}

		if (is_array($permission)) {
			foreach ($permission as $_permission) {
				if ($this->checkPermissionRaw($_permission)) {
					return true;
				}
			}
			return false;
		} else {
			return strpos($permissions, $permission) !== false;
		}
	}

	/**
	 *
	 */
	protected function getUser($loginId)
	{
		global $db;
		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$this->user = $db->GetRow("SELECT * FROM care_users WHERE login_id=" . $db->qstr($loginId));
	}

}