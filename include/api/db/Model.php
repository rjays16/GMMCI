<?php
/**
 * Model.php
 *
 * @package db
 */

Loader::import('base.Event');
Loader::import('db.DbCriteria');
Loader::import('db.ModelRelation');

/**
 * Represents a top level domain model
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */
abstract class Model
{
    /**
     * @var boolean $isNewRecord
     */
    protected $isNewRecord = true;
    /**
     * @var array $data
     */
    protected $data = array();
    /**
     * @var array $relations Array containing related models
     */
    protected $relations=array();
    /**
     * @var Mapper $mapper
     */
    protected $mapper = null;
    /**
     * @var boolean $dirtyFlags An array which indicates whether the object's
     * properties has changed and needs to be updated in the database.
     */
    private $dirtyFlags = array();

    /**
     * Description
     * @param mixed $data
     */
    public function __construct($data = array())
    {
        // all model objects start out as clean
        $this->setDirtyFlags(0);
        $this->set($data);
    }

    /**
     * Returns the corresponding table name of the model in the database
     * @return string
     */
    abstract public static function getTableName();

    /**
     * Returns an array containing the primary keys of the table. This will
     * be used to map the model object to a row in the database
     * @return array
     */
    abstract public static function getPrimaryKeys();

    /**
     * Returns the field names of the table
     * @return array
     */
    abstract public static function getFieldNames();

    /**
     * Returns an array that represents the relations of the model class
     * to other model classes. The API recognizes the following format:
     *
     * <code>
     * return array(
     *     '{relationName}' => array(
     *         'type' => (ModelRelation::{HAS_ONE|HAS_MANY|BELONGS_TO}),
     *         // be sure to import the class first through Loader::import
     *         'model' => {NameOfModelClass},
     *         'mapping' => array(
     *             // mapping of fields from source table to target table
     *             'source_table_field' => 'target_table_field'
     *         ),
     *         // compatible as DbCriteria constructor params
     *         'conditions' => array(
     *             // compatible as DbCriteria constructor params
     *             ...
     *         )
     *     )
     * );
     * </code>
     * @see Criteria
     * @return array
     */
    abstract public static function getRelations();

    /**
     * Description
     * @return void
     */
    public function set($field = array(), $value = null)
    {
        if (!empty($field)) {
            if (is_array($field)) {
                foreach ($field as $_field => $_value) {
                    $this->set($_field, $_value);
                }
            } else {
                if (in_array($field, $this->getFieldNames())) {
                    $this->$field = $value;
                }
            }
        } else {
            //do nothing
        }
    }

    /**
     *
     * @return boolean
     */
    private function isDirty()
    {
        return array_sum($this->dirtyFlags) > 0;
    }

    /**
     *
     * @param int $flag
     */
    private function setDirtyFlags($flag=1)
    {
        foreach ($this->getFieldNames() as $field) {
            $this->dirtyFlags[$field] = $flag;
        }
    }

    /**
     *
     * @param string $key
     * @param int $flag
     */
    private function setDirtyFlag($key, $flag=1)
    {
        if (isset($this->dirtyFlags[$key])) {
            $this->dirtyFlags[$key] = $flag;
        }
    }

    /**
     *
     * @return boolean
     */
    public function exists()
    {
        // return immediately if not a new record
        if (!$this->isNewRecord) {
            return true;
        }

        // check if identity is dirty`
        $dirtyCount = 0;
        foreach ($this->getPrimaryKeys() as $key) {
            if ($this->dirtyFlags[$key]) {
                $dirtyCount++;
            }
        }
        if ($this->mapper && $dirtyCount) {
            $found = $this->mapper->exists($this);
            if ($found) {
                $this->isNewRecord = false;

                // mark identity fields as clean
                foreach ($this->getPrimaryKeys() as $key) {
                    $this->setDirtyFlag($key);
                }
            } else {
                $this->isNewRecord = true;
            }
            return $found;
        } else {
            return false;
        }
    }

    /**
     *
     * @param boolean $isNewRecord
     * @return void
     */
    public function setIsNewRecord($isNewRecord)
    {
        $this->isNewRecord = $isNewRecord;
    }

    /**
     *
     * @return boolean
     */
    public function isNewRecord()
    {
        return $this->isNewRecord;
    }

    /**
     * Description
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns an array containing the primary keys and values for this
     * model object.
     * @return array
     */
    public function getIdentity()
    {
        foreach ($this->getPrimaryKeys() as $key) {
            if (!isset($this->data[$key])) {
                // Unable to retrieve identity
                return null;
            }
        }
        $identity = array_intersect_key(
            $this->data,
            array_flip($this->getPrimaryKeys())
        );
        return $identity;
    }

    /**
     * Description
     * @param Mapper $mapper
     * @return void
     */
    public function setMapper(Mapper $mapper)
    {
        if ($mapper !== $this->mapper) {
            $this->mapper = $mapper;
            $this->exists();
        }
    }

    /**
     * Description
     * @return Mapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Creates a new model object and passes this model's mapper
     * mapper to the new model
     * @param string $className
     * #param mixed $params Optional. Defaults to an empty array.
     * @return Model
     */
    public function createModel($className, $params=array())
    {
        $model = new $className($params);
        $model->setMapper($this->mapper);
        return $model;
    }

    /**
     * Description
     * @return boolean
     */
    public function save()
    {
        $result = false;
        if ($this->mapper) {
            if ($this->beforeSave(new Event($this))) {
                $result = $this->mapper->save($this);
                if ($result) {
                    return $this->afterSave(new Event($this));
                } else {
                    // No exceptions...just FALSE
                    // ...
                }
            }
        }
        return $result;
    }

    /**
     * Description
     * @return type
     */
    public function delete()
    {
        $result = false;
        if ($this->mapper) {
            $this->beforeDelete(new Event($this));
            return $this->mapper->delete($this);
            $this->afterDelete(new Event($this));
        }
        return $result;
    }

    /**
     * Description
     * @param array $fields
     * @param array $conditions
     * @return array
     */
    public function find(
        $conditions = array(),
        $fields = array(),
        $options = array()
    ) {
        $result = null;
        if ($this->mapper) {
            $result = array();
            if (!$conditions instanceof DbCriteria) {
                $conditions = new DbCriteria($conditions);
            }
            $this->beforeFind(new Event($this, array(
                'conditions' => $conditions,
                'fields' => $fields,
                'options' => $options
            )));
            $result = $this->mapper->find(
                $this,
                $conditions,
                $fields,
                $options
            );

            $this->afterFind(new Event($this, array(
                'result' => $result
            )));
        }
        return $result;
    }

    /**
     *
     * @param array $identity
     * @return Model
     */
    public function findByIdentity($identity)
    {
        $result = $this->find(
            array(
                'conditions' => $identity,
                'limit' => 1
            )
        );
        return current($result);
    }

    /**
     * Returns the value of a model object's field
     * @param string $field
     * @return mixed
     */
    public function readField($field)
    {
        if ($this->mapper) {
            // sanitize
            if ($field != '*' && !in_array($field, $this->getFieldNames())) {
                throw new DbException('Invalid field name');
            }

            $value = $this->mapper->readField($this, $field);
            if ($field == '*') {
                $this->data = array_merge($this->data, $value);
                $this->setDirtyFlags(0);
            } else {
                $this->data[$field] = $value;
                $this->setDirtyFlag($field, 0);
            }
            return $value;
        } else {
            return null;
        }
    }


    /**
     * Returns a hash value for this model object based on the identity
     * array
     * @return string
     */
    public function getHash()
    {
        $identity = $this->getIdentity();
        if (empty($identity)) {
            return null;
        }
        return sha1(
            implode(':', array_keys($identity)) . '|' .
            implode(':', $identity)
        );
    }

    /**
     * Resets the model object so that it behaves like a newly created
     * instance
     * @return void
     */
    public function clean()
    {
        $this->isNewRecord = true;
        foreach ($this->data as $key=>$value) {
            unset($this->data[$key]);
        }

        foreach ($this->relations as $key=>$relation) {
            unset($this->relations[$key]);
        }
    }

    /**
     * Description
     * @param Event $event
     * @return boolean
     */
    protected function beforeSave(Event $event)
    {
        return true;
    }
    /**
     * Description
     * @param Event $event
     * @return boolean
     */
    protected function afterSave(Event $event)
    {
        return true;
    }
    /**
     * Description
     * @param Event $event
     * @return boolean
     */
    protected function beforeDelete(Event $event)
    {
        return true;
    }
    /**
     * Description
     * @param Event $event
     * @return boolean
     */
    protected function afterDelete(Event $event)
    {
        return true;
    }
    /**
     * Description
     * @param Event $event
     * @return boolean
     */
    protected function beforeFind(Event $event)
    {
        return true;
    }
    /**
     * @param Event $event
     * @return void
     */
    protected function afterFind(Event $event)
    {
        return true;
    }

    /**
     * Magic getter method that looks for field within data array or for
     * a specific getter method with name __get$FieldName.
     *
     * @param string $var
     * @return mixed|false
     */
    public function __get($var)
    {
        $methodName = "get$var";

        if (method_exists($this, $methodName)) {
            return call_user_func(array($this, $methodName));
        }

        // check if there is a corresponding field
        if (in_array($var, $this->getFieldNames())) {
            // the data already exists
            if (isset($this->data[$var])) {
                return $this->data[$var];
            } else {
                $value = $this->readField($var);
                return $value;
            }
        } else {
            // check if there is a corresponding relation
            if (array_key_exists($var, $this->getRelations())) {
                if (isset($this->relations[$var])) {
                    return $this->relations[$var];
                } else {
                    return $this->getRelatedModels($var);
                }
            } else {
                return null;
            }
        }
    }

    /**
     * Magic function to allow fields to have their values set as if they
     * are public properties.
     *
     * If the method __set$FieldName($value) exists then that
     * method is called instead to do the setting
     *
     * @param string $fieldName
     * @param mixed $value
     * @return void
     */
    public function __set($fieldName, $value)
    {
        $methodName = "set$fieldName";
        if (method_exists($this, $methodName)) {
            call_user_func(array($this, $methodName), $value);
        } else {
            if (!$this->isNewRecord &&
                in_array($fieldName, $this->getPrimaryKeys())
            ) {
                throw new DbException('Cannot alter the identity of an existing model object');
            }
            if (in_array($fieldName, $this->getFieldNames())) {
                if (@$this->data[$fieldName] !== $value) {
                    $this->setDirtyFlag($fieldName, 1);
                    $this->data[$fieldName] = $value;
                }
            }
        }
    }

    /**
     *
     * @param string $var
     * @return boolean
     */
    public function __isset($var)
    {
        return isset($this->data[$var]);
    }

    /**
     *
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
        $this->dirtyFlags[$name] = false;
    }

    /**
     * Description
     * @param string $relation
     * @return mixed
     */
    protected function getRelatedModels($relation)
    {
        $relations = $this->getRelations();
        if ($this->mapper && isset($relations[$relation])) {
            $settings = $relations[$relation];
            $model = $this->createModel($settings['model']);

            //build find criteria
            $conditions = array();
            foreach ($settings['mapping'] as $thisField=>$thatField) {
                $conditions[$thatField] = $this->$thisField;
            }

            switch ($settings['type']) {
                case ModelRelation::BELONGS_TO:
                case ModelRelation::HAS_ONE:
                    $result = $model->find(array(
                        'conditions' => $conditions,
                        'limit' => 1
                    ));
                    $this->relations[$relation] = current($result);
                    break;
                case ModelRelation::HAS_MANY:
                    $result = $model->find(array(
                        'conditions' => $conditions,
                    ));
                    $this->relations[$relation] = $result;
                    break;
                default:
                    // Relation type not supported
                    return null;
                    break;
            }

            return $this->relations[$relation];
        } else {
            return null;
        }
    }
}