<?php

require_once 'class_core.php';


class SegUser extends Core {

	const USERS_TBL 		= 'care_users';
	const PERMIT_SYSAD 	= 'System_Admin';
	const PERMIT_ALL 		= '_a_0_all';
	private $id;
	private $name;
	private $permissions;
	private $password;
	private $personnel_nr;

	/**
	* put your comment there...
	*
	*/
	public function __construct($userId)
	{
		$this->setTable( self::USERS_TBL, $fetchMetadata=true );
		$this->setupLogger();
		$this->id = $userId;
		$row = $this->fetch( array('login_id'=>$userId) );
		if ($row !== false) {
			$this->permissions 	= $row['permission'];
			$this->name 				= $row['name'];
			$this->password 		= $row['password'];
			$this->personnel_nr	= $row['personnel_nr'];
		}
		else {
			$this->id 					= '';
			$this->permissions 	= '';
			$this->name 				= '';
			$this->personnel_nr = '';
		}

	}




	public static function getCurrentUser()
	{
		return new SegUser($_SESSION['sess_temp_userid']);
	}


	/**
	* Validates if a user has access to a permission set
	*
	* <p>This is a newer, cleaner, safer method for validating permissions. Instead of validating
	* permission through session variables, the function directly reads the saved permissions
	* from the DB, circumventing the need to relog after permissions are changed.</p>
	*/
	public function hasPermission($permissionSet, $permitTypeAll=true)
	{
		global $db, $level2_permission;

		// special strings

		if (!$this->permissions)
		{
			return false;
		}


		if (false !== strpos($this->permissions, self::PERMIT_SYSAD))
		{
			return 1;
		}

		if ( $permitTypeAll && (false !== strpos($this->permissions, self::PERMIT_ALL)) )
		{
			return 1;
		}


		// check Level 2 Access permissions, whatever that is
		if ( is_array($level2_permission) && $level2_permission )
		{
			$lvl2access_ok=0;

			foreach($level2_permission as $j=>$v)
			{
				if( false !== strpos($permissionSet, $v) ) {
					$lvl2access_ok=1;
					break;
				}
			}
		}
		else
		{
			$lvl2access_ok=1;
		}

		foreach ($permissionSet as $testPermission)
		{
			if (false !== strpos($this->permissions, $testPermission))
			{
				return $lvl2access_ok;
			}
		}

		return 0;
	}


}