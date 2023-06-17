<?php

/**
 * @package db
 */

/**
 * Template for the Mapper class
 * 
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */
abstract class Mapper
{
    /**
     * Saves a Model instance into the mapped database
     * @param Model $model A Model object
     * @return boolean Returns TRUE if the object was successfully saved
     */
    abstract public function save($model);
    /**
     * Deletes the equivalent row in the mapped database based on the 
     * model instance
     * @param Model $model 
     * @return boolean
     */
    abstract public function delete($model);
    /**
     * Deletes rows from the mapped database. The method references the
     * $model object to determine the target table/fields and uses the
     * $criteria object to determine the conditions.
     * @param Model $model 
     * @param Criteria $criteria 
     * @return int Returns the number of rows deleted
     */
    // abstract public function deleteAll($model, $criteria);
    /**
     * Finds the specified records in the database based on the given
     * parameters.
     * 
     * The options array can contain any of the following items:
     * <ul>
     *   <li>'recursionLevel (int)' - Defaults to FALSE. Specifies how deep 
     * the subquerying for relations will be. FALSE turns off eager loading 
     * of related items</li>
     * </ul>
     *     
     * @param Model $model 
     * @param Criteria $criteria 
     * @param array $fields Fields to eagerly load into the 
     * @param array $options Options to be used for the find operation.
     * @return mixed
     */
    abstract public function find(
        $model, 
        $criteria=null, 
        $fields=array(),
        $options = array()
    );
    /**
     * Checks if the model object exists in the database
     * @param Model $model
     * @return boolean Returns TRUE if the object exists
     */
    abstract public function exists($model);
    /**
     * Description
     * @param Model $model 
     * @return mixed
     */
    abstract public function readField($model, $fieldName);
    /**
     * Performs a direct query to the mapped database
     * @param mixed $query The query object
     * @param array $bindings
     * @param array $options Options to be passed to the query execution
     * @return mixed
     */
    abstract public function query(
        $query, 
        $bindings=array(), 
        $options=array()
    );

    /**
     * Returns the connection object
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }
}