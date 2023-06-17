<?php

/**
 * CareHttpSession.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

/**
 * Description of CareHttpSession
 *
 * @package
 */
class CareHttpSession extends CDbHttpSession {
    /**
     *
     * @var type
     */
    public $connectionID = 'db';
    /**
     *
     * @var type
     */
    public $autoCreateSessionTable = false;
    /**
     *
     * @var type
     */
    public $sessionTableName = 'care_sessions2';

    /**
	 * Updates the current session sesskey with a newly generated one.
	 * Please refer to {@link http://php.net/session_regenerate_id} for more details.
	 * @param boolean $deleteOldSession Whether to delete the old associated session file or not.
	 * @since 1.1.8
	 */
	public function regenerateID($deleteOldSession=false)
	{
		$oldID=session_id();

		// if no session is started, there is nothing to regenerate
		if(empty($oldID))
			return;

		parent::regenerateID(false);
		$newID=session_id();
		$db=$this->getDbConnection();

		$row=$db->createCommand()
			->select()
			->from($this->sessionTableName)
			->where('sesskey=:sesskey',array(':sesskey'=>$oldID))
			->queryRow();
		if($row!==false)
		{
			if($deleteOldSession)
				$db->createCommand()->update($this->sessionTableName,array(
					'sesskey'=>$newID
				),'sesskey=:oldID',array(':oldID'=>$oldID));
			else
			{
				$row['sesskey']=$newID;
				$db->createCommand()->insert($this->sessionTableName, $row);
			}
		}
		else
		{
			// shouldn't reach here normally
			$db->createCommand()->insert($this->sessionTableName, array(
				'sesskey'=>$newID,
				'expiry'=>time()+$this->getTimeout(),
			));
		}
	}

	/**
	 * Session open handler.
	 * Do not call this method directly.
	 * @param string $savePath session save path
	 * @param string $sessionName session name
	 * @return boolean whether session is opened successfully
	 */
	public function openSession($savePath,$sessionName)
	{
		if($this->autoCreateSessionTable)
		{
			$db=$this->getDbConnection();
			$db->setActive(true);
			try
			{
				$db->createCommand()->delete($this->sessionTableName,'expiry<:expiry',array(':expiry'=>time()));
			}
			catch(Exception $e)
			{
				$this->createSessionTable($db,$this->sessionTableName);
			}
		}
		return true;
	}

	/**
	 * Session read handler.
	 * Do not call this method directly.
	 * @param string $sesskey session ID
	 * @return string the session data
	 */
	public function readSession($sesskey)
	{
		$data=$this->getDbConnection()->createCommand()
			->select('sessdata')
			->from($this->sessionTableName)
			->where('expiry>:expiry AND sesskey=:sesskey',array(':expiry'=>time(),':sesskey'=>$sesskey))
			->queryScalar();
		return $data===false?'':$data;
	}

	/**
	 * Session write handler.
	 * Do not call this method directly.
	 * @param string $sesskey session ID
	 * @param string $data session data
	 * @return boolean whether session write is successful
	 */
	public function writeSession($sesskey,$data)
	{
		// exception must be caught in session write handler
		// http://us.php.net/manual/en/function.session-set-save-handler.php
		try
		{
			$expiry=time()+$this->getTimeout();
			$db=$this->getDbConnection();
			if($db->createCommand()->select('sesskey')->from($this->sessionTableName)->where('sesskey=:sesskey',array(':sesskey'=>$sesskey))->queryScalar()===false)
				$db->createCommand()->insert($this->sessionTableName,array(
					'sesskey'=>$sesskey,
					'sessdata'=>$data,
					'expiry'=>$expiry,
				));
			else
				$db->createCommand()->update($this->sessionTableName,array(
					'sessdata'=>$data,
					'expiry'=>$expiry
				),'sesskey=:sesskey',array(':sesskey'=>$sesskey));
		}
		catch(Exception $e)
		{
			if(YII_DEBUG)
				echo $e->getMessage();
			// it is too late to log an error message here
			return false;
		}
		return true;
	}


	/**
	 * Session destroy handler.
	 * Do not call this method directly.
	 * @param string $sesskey session ID
	 * @return boolean whether session is destroyed successfully
	 */
	public function destroySession($sesskey)
	{
		$this->getDbConnection()->createCommand()
			->delete($this->sessionTableName,'sesskey=:sesskey',array(':sesskey'=>$sesskey));
		return true;
	}

	/**
	 * Session GC (garbage collection) handler.
	 * Do not call this method directly.
	 * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
	 * @return boolean whether session is GCed successfully
	 */
	public function gcSession($maxLifetime)
	{
		$this->getDbConnection()->createCommand()
			->delete($this->sessionTableName,'expiry<:expiry',array(':expiry'=>time()));
		return true;
	}
}
