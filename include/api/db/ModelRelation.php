<?php
/**
 * ModelRelation.php
 *
 * @author Alvin Quinones
 * @copyright Copyright &copy; 2012-2013 Segworks Technologies Corporation
 */



/**
 * Represents a model's relationship to another model
 *
 * @version 1.0
 * @package db
 */

class ModelRelation
{
	const HAS_ONE = 'HAS_ONE';
	const HAS_MANY = 'HAS_MANY';
	const BELONGS_TO = 'BELONGS_TO';
	// not supported
	//const MANY_MANY = 'MANY_MANY';

	protected $type;
	protected $relatedModel;
	protected $keys = array();
	protected $criteria = null;

	/**
	 * Description
	 * @param string $type 
	 * @param string $relatedModel 
	 * @param array $keys 
	 * @param DbCriteria|array $DbCriteria 
	 */
	public function __construct(
		$type, 
		$relatedModel, 
		$keys,
		$DbCriteria = null)
	{
		$this->type = $type;
		$this->relatedModel = $relatedModel;
		$this->keys = (array) $keys;
		if (!$criteria instanceof DbCriteria) {
			$criteria = new DbCriteria($criteria);
		}
		$this->criteria = $criteria;
	}

	/**
	 * Description
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Description
	 * @return string
	 */
	public function getRelatedModel()
	{
		return $this->relatedModel;
	}

	public function getKeys()
	{
		return $this->keys;
	}

	/**
	 * Description
	 * @return DbCriteria
	 */
	public function getCriteria()
	{
		return $this->criteria;
	}
}