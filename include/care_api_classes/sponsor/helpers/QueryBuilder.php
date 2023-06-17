<?php
/**
*
*
* QueryBuilder provides a convenient but, to some extent, strict convention for accessing
* HIS data tables
*
* QueryBuilder adds another layer of abstraction for accessing the SegHIS database
* based on a defined logic <em>blueprint</em> that is normally defined in the
* initialization process of an API class. Once the logic is defined, QueryBuilder
* abstacts interaction with the database by generating the queries for generic database
* operations especially Selections. QueryBuilder is ideal for operations where uniform
* data formats are expected from multiple data sources with incompatible formats (e.g.,
* Cashier module extracting data from Cost Center requests).
*
* @todo Specifications for QueryBuilder blueprint, add Blueprint as a separate class (?)
* @todo Validator and access methods for blueprint data
* @todo Generate query for delete, update and other common database operations
* @author aquinones
* @copyright Segworks Technologies Corp (c) 2010
* @version 0.1
*/

class QueryBuilder {

	/**
	* Private constructor to prevent instantiation
	*
	*/
	private function __construct() {
	}




	/**
	* Simple express function for generating simple expressions derived from the logic
	* described in the blueprint. Syntax is:
	* QueryBuilder::express( <blueprint>, <opration>, <field>, <literal value> ).
	*
	* @static
	* @param Array $blueprint The blueprint to be used
	* @param String operation The type of operation to be used. Can be any of the ff: (TRUE, FALSE, EQ, NOTEQ, LIKE, REGEXP and DATE)
	* @param String field The field identifier to be used as operand
	* @param mixed Value The value for the field to be used as operand
	* @return String the generated SQL expression
	*/
	public static function express() {
		global $db;

		$args = func_num_args();
		list($blueprint, $operation, $field, $value) = func_get_args();

		if ($blueprint['fields'][$field]) {
			$field = $blueprint['fields'][$field];
		}
		else {
			return false;
		}

		switch($operation) {
			case 'TRUE':
				return "1=1";
			case 'FALSE':
				return "1=0";
			case 'EQ':
				// Provision for NULL values comparison
				if (is_null($value)) {
					return "$field IS NULL";
				}
				else {
					return "$field=".$db->qstr($value);
				}
			case 'NOTEQ':
				// Provision for NULL values comparison
				if (is_null($value)) {
					return "$field IS NOT NULL";
				}
				else {
					return "$field<>".$db->qstr($value);
				}
			case 'LIKE':
				return "$field LIKE ".$db->qstr($value);
			case 'REGEXP':
				return "$field REGEXP ".$db->qstr($value);
			case 'DATE':
				return "DATE($field)=".$db->qstr($value);
			case 'DATEBEFORE':
				return "DATE($field)<=".$db->qstr($value);
			case 'DATEAFTER':
				return "DATE($field)>=".$db->qstr($value);
			case 'MONTHSBEFORE':
				return "DATE($field)>=DATE(NOW())-INTERVAL ".((int) $value)." MONTH";
			case 'DATEBETWEEN':	//added by cha, 11-12-2010
				return "DATE($field) BETWEEN ".$db->qstr($value[0])." AND ".$db->qstr($value[1]);
			case 'INTERVALFROMDATE':
				if (!in_array(strtoupper($value['interval']), array('MICROSECOND', 'SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'QUARTER', 'YEAR')))
				{
					$value['interval'] = 'DAY';
				}
				return "DATE($field)BETWEEN " . $db->qstr($value['date']) . " -INTERVAL ".((int) $value['count'])." ".$value['interval']." AND " . $db->qstr($value['date']);
			case 'INTERVAL':
				if (!in_array(strtoupper($value[1]), array('MICROSECOND', 'SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'QUARTER', 'YEAR')))
				{
					$value[1] = 'DAY';
				}
				return "DATE($field) BETWEEN $field-INTERVAL ".((int) $value[0])." ".$value[1]." AND $field";
			default:
				return false;
		}
	}


	/**
	* Builds the SELECT query based on the logic blueprint specified.
	*
	* @param mixed $blueprint
	* @param mixed $calculateFoundRows
	* @return mixed
	*/
	public static function build( $blueprint, $calculateFoundRows=false ) {
		global $db;
		$query = "SELECT\n";
		if ($calculateFoundRows) {
			$query.="SQL_CALC_FOUND_ROWS\n";
		}
		if (!is_array($blueprint) || !count($blueprint)) {
			//$this->logger->warn('Invalid builder array passed to fetch function: '.var_export($blueprint, true));
			return '';
		}
		if (is_array($blueprint['fields'])) {
			$fields = array();
			foreach ($blueprint['fields'] as $field=>$value) {
				if ($blueprint['selectMask']) {
					// if the field is included in the specified Select Mask
					if (in_array($field, $blueprint['selectMask'])) {
						$fields[] = "$value `$field`";
					}
				}
				else {
					$fields[] = "$value `$field`";
				}
			}
			$query.=implode(",", $fields)."\n";
		}
		else {
			// return NULL if no fields are specified
			//$this->logger->warn('Invalid builder array passed to fetch function...'.var_export($blueprint, true));
			$query.="NULL\n";
		}
		if ($blueprint['coreTable']) {
			$query.="FROM ".$blueprint['coreTable']."\n";
			if (is_array($blueprint['joins'])) {
				foreach ($blueprint['joins'] as $join) {
					$query.=$join."\n";
				}
			}
			$where = array();
			if ($blueprint['where']) {
				foreach ($blueprint['where'] as $expr) {
					if (is_array($expr)) {
						list($op, $fieldId, $value) = $expr;
						if ($queryExpr = self::express($blueprint, $op, $fieldId, $value)) {
							$where[] = $queryExpr;
						}
					}
					else {
						$where[] = (string) $expr;
					}
				}
			}

			if ($where) {
				$query.="WHERE (".implode(") AND (", $where).")\n";
			}
			if ($blueprint['orderBy']) {
//				if (!is_array($blueprint['orderBy'])) {
//					$blueprint['orderBy'] = Array( $blueprint['orderBy'] );
//				}

//				$orderBy = array();
//				foreach ($blueprint['orderBy'] as $order) {
//					if ($blueprint['fields'][$order]) {
//						$orderBy[]=$blueprint['fields'][$order];
//					}
//				}
				if (!is_array($blueprint['orderBy'])) {
					$blueprint['orderBy'] = Array( $blueprint['orderBy'] );
				}
				$query.="ORDER BY ".implode(",",$blueprint['orderBy'])."\n";
			}
		}
		return $query;
	}


}