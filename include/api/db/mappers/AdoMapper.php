<?php

Loader::import('db.Mapper');
Loader::import('db.exceptions.SqlException');
Loader::import('db.exceptions.DbException');

/**
 * ADODB-based implementation of the Mapper object
 * @see Mapper
 * @package db.mapper
 */

class AdoMapper extends Mapper
{
    /**
     * @var ADOConnection $conn
     */
    protected $connection = null;

    /**
     * @param ADOConnection $conn 
     */
    public function __construct(ADOConnection $conn)
    {
        $this->connection = $conn;
    }
    
    /**
     * @see Mapper::exists
     * @param Model $model
     */
    public function exists($model)
    {
        $identity = $model->getIdentity();
        if (empty($identity)) {
            return false;
        }
        
        $query = sprintf(
            "SELECT COUNT(*) FROM %s WHERE %s",
            $model->getTableName(),
            $this->buildCondition($identity)
        );
        $result = $this->connection->GetOne($query);
        if ($result !== false) {
            return $result > 0;
        } else {
            throw new SqlException(
                $this->connection->ErrorNo(),
                'Find operation failed',
                $this->connection->ErrorMsg(),
                $query
            );
        }
    }

    /**
     * @see Mapper::save
     * @param Model $model 
     * @return boolean
     */
    public function save($model)
    {
        $result = $this->connection->Replace(
            $model->getTableName(),
            $model->getData(),
            $model->getPrimaryKeys(),
            $autoQuote = true
        );
        if ($result == 0) {
            throw new SqlException(
                $this->connection->ErrorNo(),
                'Saving failed',
                $this->connection->ErrorMsg()
            );
        } else {
            $model->setIsNewRecord($result == 2);
            return true;
        }
    }

    /**
     * @see Mapper::delete
     * @param Model $model 
     * @return boolean
     */
    public function delete($model)
    {
        $identity = $model->getIdentity();
        if ($model->getIdentity()) {
            return false;
        }
        
        $query = sprintf("DELETE FROM %s WHERE %s LIMIT 1",
            $model->getTableName(),
            $this->buildCondition($identity)
        );

        $result = $this->connection->Execute($query);
        if ($result !== false) {
            $affected = $this->connection->Affected_Rows();
            if ($affected === 0) {
                return false;
            } else {
                // Handles $affected = false (Not supported)
                return true;
            }
        } else {
            throw new SqlException($this->connection->ErrorNo(),
                'Delete failed',
                $this->connection->ErrorMsg(),
                $query
            );
        }

    }

    /**
     * @see Mapper::find
     * @param Model $model 
     * @param Criteria $criteria 
     * @param array|string $fields 
     * @param array $options
     * @return array
     * 
     * @todo Handle HAVING
     * @todo Handle relationships
     * @todo Handle complex WHERE/HAVING conditions
     */
    public function find(
        $model,
        $criteria = null,
        $fields = array(),
        $options = array()
    ) {
        $this->connection->SetFetchMode(ADODB_FETCH_ASSOC);
        if (empty($fields)) {
            $fields = $model->getPrimaryKeys();
        } elseif ($fields == '*') {
            $fields = $model->getFieldNames();
        } else {
            $fields = array_unique(
                array_merge(
                    (array) $fields, 
                    $model->getPrimaryKeys()
                )
            );          
        }

        $query = "SELECT\n".    
            implode(',', $fields) . "\n" .
            "FROM " . $model->getTableName() . "\n".
            "WHERE\n" . 
                $this->buildCondition($criteria->getConditions());

        if ($ordering=$criteria->getOrdering()) {
            $query.="\nORDER BY ".$this->buildOrdering($ordering);
        }

        $limit = $criteria->getLimit();
        $offset = $criteria->getOffset();

        $rs = $this->connection->SelectLimit(
            $query,
            $limit,
            $offset
        );

        if ($rs !== false) {
            $className = get_class($model);
            $resultSet = array();
            foreach ($rs as $row) {
                $new = new $className($row);
                $new->setMapper($this);
                $resultSet[] = $new;
            }

            return $resultSet;
        } else {
            throw new SqlException(
                $this->connection->ErrorNo(),
                'Find operation failed',
                $this->connection->ErrorMsg(),
                $query
            );
        }
    }

    /**
     * @see Mapper::readField
     * @param Model $model 
     * @param string $field 
     * @return mixed
     */
    public function readField($model, $field)
    {
        $this->connection->SetFetchMode(ADODB_FETCH_ASSOC);
        $identity = $model->getIdentity();
        if (empty($identity)) {
            throw new DbException('Unable to read from an unidentified model');
        }
        
        $query = sprintf(
            "SELECT %s FROM %s WHERE %s",
            $field,
            $model->getTableName(),
            $this->buildCondition($identity)
        );
                
        $result = $this->connection->GetRow($query);

        if ($result !== false) {
            if ($field == '*') {
                return $result;
            } else {
                return @$result[$field];
            }
        } else {
            throw new SqlException(
                $this->connection->ErrorNo(),
                'Error reading field',
                $this->connection->ErrorMsg(),
                $query
            );
        }
    }

    /**
     * Description
     * @param mixed $query 
     * @param mixed $options
     * @return mixed
     */
    public function query($query, $bindings=array(), $options=array())
    {
        global $ADODB_COUNTRECS;
        
        $ADODB_COUNTRECS = false;
        $this->connection->SetFetchMode(ADODB_FETCH_ASSOC);
        $statement = $this->connection->Prepare($query);
        $rs = $this->connection->Execute($statement, $bindings);

        if ($rs !== false) {
            return $rs->GetAll();
        } else {
            throw new SqlException(
                $this->connection->ErrorNo(),
                'Failed to execute query',
                $this->connection->ErrorMsg(),
                $query
            );
        }
    }
    
    /**
     * 
     */
    public function startTransaction()
    {
        $this->connection->StartTrans();
    }
    
    /**
     * 
     */
    public function completeTransaction()
    {
        $this->connection->CompleteTrans();
    }
    
    /**
     * 
     */
    public function rollbackTransaction()
    {
        $this->connection->RollbackTrans();
    }
    
    /**
     * 
     */
    public function failTransaction()
    {
        $this->connection->FailTrans();
    }
    
    /**
     * 
     */
    public function hasFailedTransaction()
    {
        $this->connection->HasFailedTrans();
    }

    /**
     * Description
     * @param array $ordering 
     * @return string
     */
    protected function buildOrdering($ordering)
    {
        $sql = array();
        foreach ($ordering as $field => $dir) {
            if (is_numeric($field)) {
                $sql[] = $dir . ' ASC';
            } else {
                $sql[] = $field . (strtoupper($dir) == 'DESC' ? ' DESC' : ' ASC');
            }
        }
        return implode(',', $sql);
    }

    /**
     * Description
     * @param array $conditions 
     * @return string
     */
    protected function buildCondition($conditions, $join='AND')
    {
        $sql = array();
        foreach ($conditions as $key => $value) {
            if (in_array(strtoupper($key), array('AND', 'OR', 'XOR', 'NOT'))) {
                if (!is_array($value)) {
                    throw new DbException('Cannot build condition from criteria');
                }
                if (strtoupper($key) == 'NOT') {
                    $sql[] = "NOT(" . $this->buildCondition($value) . ")";
                } else {
                    $sql[] = $this->buildCondition($value, $key);
                }
                
            } else {
                if (is_array($value)) {
                    $quotedValues = array();
                    foreach ($value as $_value) {
                        $quotedValues[] = $this->connection->qstr($_value);
                    }
                    $sql[] = sprintf("%s IN (%s)",
                        $key,
                        implode(',', $quotedValues)
                    );
                } else {
                    $sql[] = sprintf("%s=%s",$key, $this->connection->qstr($value));
                }
            }
        }

        if (!empty($sql)) {
            return sprintf("(%s)", implode(") {$join} (", $sql));
        } else {
            return "(0)";
        }
        
    }
}