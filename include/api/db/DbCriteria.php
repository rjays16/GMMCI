<?php
/**
 * 
 * @package db
 */

/**
 * Represents a criteria to be used in Model::find
 * @author Alvin Quinones
 */
class DbCriteria
{
	/**
	 * @var array $conditions
	 */
	protected $conditions;
	/**
	 * @var array $having
	 */
	protected $having;	
	/**
	 * @var int $offset
	 */
	protected $offset;
	/**
	 * @var int $limit
	 */
	protected $limit;
	/**
	 * @var array $grouping
	 */
	protected $grouping;
	/**
	 * @var array $ordering
	 */
	protected $ordering;

	/**
	 * Description
	 * @param mixed $params
	 * @return type
	 */
	public function __construct($params = array()) 
	{
		if (!is_array($params)) {
			$params = (array) $params;
		}

		$params = array_merge(
			array(
				'conditions' => array(),
				'offset' => 0,
				'limit' => -1,
				'grouping' => array(),
				'ordering' => array(),
				'having' => array()

			),
			$params
		);

		extract ($params);

		$this->conditions = $conditions;
		$this->offset = $offset;
		$this->limit = $limit;
		$this->grouping = $grouping;
		$this->having = $having;
		$this->ordering = $ordering;
	}

	/**
	 * Description
	 * @param type DbCriteria $criteria 
	 * @return type
	 */
	public function merge(DbCriteria $criteria)
	{
		// not implemented yet
	}

	/**
	 * Description
	 * @param array $conditions 
	 * @return void
	 */
	public function setConditions($conditions = array())
	{
		$this->conditions =$conditions;
	}

	/**
	 * Description
	 * @return array
	 */
	public function getConditions()
	{
		return $this->conditions;
	}

	/**
	 * Description
	 * @param int $offset 
	 * @return void
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * Description
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * Description
	 * @param int $limit 
	 * @return void
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}

	/**
	 * Description
	 * @return int
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	public function setOrdering($ordering)
	{
		$this->ordering = $ordering;
	}

	public function getOrdering()
	{
		return $this->ordering;
	}

	/**
	 * Description
	 * @param array $grouping 
	 * @return void
	 */
	public function setGrouping($grouping)
	{
		$this->grouping = $grouping;
	}

	/**
	 * Description
	 * @return array
	 */
	public function getGrouping()
	{
		return $this->grouping;
	}

}