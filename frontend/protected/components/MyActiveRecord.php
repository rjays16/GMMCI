<?php
Yii::import('application.components.interfaces.IRelationModel');
Yii::import('application.components.behaviors.CancelRequestBehavior');
Yii::import('application.components.behaviors.TransactRequestBehavior');

class MyActiveRecord extends CActiveRecord implements IRelationModel
{

    private static $dbadvert = null;
    private $_rules;

    private $alertIds = [
        //        'pharma_pickup' => 'PHA-PCK',
        //        'cssd_pickup' => 'CSSD-PCK',
        //        'csr_pickup' => 'CSR-PCK',
        //        'doc_order' => 'DR-ORD',
        //        'doc_order_surgery' => 'DR-ORD-SRG',
        //        'nurseLab' => 'NRS-LAB',
        //        'nurseMed' => 'NRS-MED',
        //        'nursePul' => 'NRS-PUL',
        //        'nurseRad' => 'NRS-RAD',
        //        'nurseAdmission' => 'NRS-ADMSN',
        //        'nurseCancelAdmission' => 'NRS-CADMSN',
        //        'nurseSurgery' => 'NRS-SRG',
        //        'nurseDietaryConsult' => 'NRS-DIET-CONS',
        //        'nurseDietaryFeed' => 'NRS-DIET-FEED',
        //        'nurseDietary' => 'NRS-DIET',
        //        'nurseMGH' => 'NRS-MGH',
        //        'nurseRoomTrans' => 'NRS-RM-TRANS',
        //        'nurseDialysis' => 'NRS-DIA',
        //        'nurseReorderMeds' => 'NRS-REMED',
        //        'informAnciliary' => 'INF-ANC',
        //        'labConfirmation' => 'LAB-CONF',
        //        'labResultInitial' => 'LAB-RES-INT',
        //        'labResultOfficial' => 'LAB-RES-OFF',
        //        'patientConf' => 'PX-CONF',
        //        'patientNicu' => 'PX-NICU',
    ];

    /**
     * Contains the room catalog.
     * Reason exist: to return a data from a function without running again an sql query.
     */
    private $data = null;
    /**
     *
     * @var CDbCriteria
     */
    public $criteria;

    /**
     * @var boolean
     * If true the find functions can be merge to build a single criteria.
     * User is also responsible in executing find() or findAll();
     * Else, will not build and continue to execute find() or findAll();
     *
     * @author Jolly Caralos
     */
    public $buildCriteria = false;

    /**
     * Overrides the parent constructor.
     * This constructor can do massive assignment.
     *
     * @param mixed $attributes scenario or array of key => value pair attributes
     */
    public function __construct($params = 'insert')
    {
        $this->criteria = new CDbCriteria();
        if (is_array($params)) {
            parent::__construct('insert');
            $this->attributes = $params;
        } else {
            parent::__construct($params);
        }
    }

    /**
     * Override of delete function, if in the table exists '' field
     * then it logically deletes the record by saving a value like 'Y' in the field.
     * @return boolean whether the deletion is successful.
     * @throws CException if the record is new
     */
    public function delete()
    {
        if (!$this->getIsNewRecord()) {
            Yii::trace(get_class($this) . '.delete()', 'system.db.ar.CActiveRecord');
            if ($this->beforeDelete()) {
                if ($this->hasAttribute('deleted')) {
                    //logical deletion
                    $this->setAttribute('deleted', true);
                    $result = $this->save();
                } else {
                    $result = $this->deleteByPk($this->getPrimaryKey()) > 0;
                }
                $this->afterDelete();
            }

            return $result;
        } else {
            throw new CDbException(Yii::t('yii', 'The active record cannot be deleted because it is new.'));
        }
    }

    public function addRule($array)
    {
        $this->_rules[] = $array;
    }

    /**
     * Modified: Jolly Caralos(2014.06.16)
     *  - Added parameter.
     */
    protected static function getAdvertDbConnection($db_options = [])
    {
        // May cause problems, needs to be QA.
        if (self::$dbadvert !== null) {
            return self::$dbadvert;
        } else {
            /*
                To dynamically select db connection.
            */
            if (!empty($db_options)) {
                self::$dbadvert = $db_options;
            } else {
                self::$dbadvert = Yii::app()->dbadvert;
            }

            if (self::$dbadvert instanceof CDbConnection) {
                self::$dbadvert->setActive(true);

                return self::$dbadvert;
            } else {
                throw new CDbException(Yii::t('yii', 'Active Record requires a "db" CDbConnection application component.'));
            }
        }
    }

    /**
     * Alias for {@link getIsNewRecord} method
     *
     * @return bool
     */
    public function isNewRecord()
    {
        return $this->getIsNewRecord();
    }

    /**
     * @param int $numDigits
     *
     * @return string
     */
    public static function generateId($numDigits = 11)
    {
        return static::model()->generateSysId($numDigits);
    }

    /**
     * @return string
     */
    public static function generateUuid()
    {
        return static::model()->getUuid();
    }

    /**
     * Generates an Id of year in 4 digits concatenated with a series of 11
     * digits by default (e.g. 20130000000001)
     *
     * @param int $numDigits Actual length minus 4 digits
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateSysId($numDigits = 11)
    {

        try {
            $numDigits += 4;
            $now = date('Y');
            $first = str_pad($now, $numDigits, '0', STR_PAD_RIGHT);
            $last = str_pad($now, $numDigits, '9', STR_PAD_RIGHT);

            $priKey = $this->getTableSchema()->primaryKey;
            $sql = "SELECT MAX({$priKey}) FROM {$this->tableName()} WHERE {$priKey} BETWEEN '{$first}' AND '{$last}'";
            $max = $this->getDbConnection()->createCommand($sql)->queryScalar();

            return bcadd($max ?: $first, 1);
        } catch (Exception $e) {
            // Failed to execute query or the value is null
            throw new Exception('Error in generating id: ' . $e->getMessage());
        }
    }

    public function generateLis($numDigits = 11)
    {
        try {
            $priKey = $this->getTableSchema()->primaryKey;
            // Select MAX(_id) FROM `table` FOR UPDATE
            $sql = $this->getDbConnection()->createCommand("SELECT MAX(CAST({$priKey} AS UNSIGNED)) FROM {$this->tableName()} FOR UPDATE");

            $lastId = 2;                                                // id is equ 0 by default.
            $rawSeriesNum = $sql->queryScalar();                        // execute query
            if ($rawSeriesNum) {                                           // if null lastid will be 0
                // converts the old id 0000000001 to a whole number 1
                $lastId = (strlen($rawSeriesNum) > 4) ? (int)substr($rawSeriesNum, 4) : (int)$rawSeriesNum;
            }
            $year = date('my', time());                                  // current Year in 4 digits format
            $series = $this->getSeriesNumber(++$lastId, $numDigits);    // returns String in 0000000001 format
        } catch (Exception $e) {                                        // Failed to execute query or the value is null
            throw new Exception('Error in generating id');
        }

        return $year . $series;                                         // Returns a String in 20130000000001 format
    }


    /**
     * Returns a 10 digit Integer in a format of 0000000001
     * @author Jolly Caralos
     *
     * @param type $value
     * @param type $numDigits
     *
     * @return type
     */
    protected function getSeriesNumber($value, $numDigits)
    {
        return str_pad($value, $numDigits, '0', STR_PAD_LEFT);
    }

    /**
     * Returns a UUID String generated from the Database Server
     * @author Jolly
     * @return String UUID
     */
    public function getUuid()
    {
        $command = $this->dbConnection->createCommand('Select UUID()');

        return $command->queryScalar();
    }

    protected function afterSave()
    {
        return true;
    }

    /**
     * Sends notification depending on type
     * types of notif
     * 1 - user (pid)
     * 2 - specialty
     * 3 - department (dept_id)
     * 4 - area (area_id)
     * @author Justin
     *
     * @param type $type
     * @param type $intended_id
     * @param type $alert_id
     *
     * @return none
     */
    public function runNotification($type, $intended_id, $alert_id)
    {
        //        if(Yii::app()->params['HEIRS_ALERT'] && !empty($intended_id)){
        //            try{
        //                //$alertIds located @ top
        //                if(array_key_exists($alert_id, $this->alertIds))
        //                    $alert_id = $this->alertIds[$alert_id];
        //
        //                $client = new WebsocketClient();
        //                $client->send_notification($_SESSION["pid"], $type, $intended_id, $alert_id);
        //            }catch(Exception $e){
        //
        //            }
        //        }
    }

    public function runCustomNotification($type, $intended_id, $msg, $spin = null)
    {
        //        if(Yii::app()->params['HEIRS_ALERT'] && !empty($intended_id)){
        //            try{
        //                $message = $msg;
        //                if(!empty($spin)){
        //                    $message .= "<span class='spin-identifier' data-spin='{$spin}'></span>";
        //                }
        //                $client = new WebsocketClient();
        //                $client->send_custom_notif($_SESSION["pid"], $type, $intended_id, $message);
        //            }catch(Exception $e){}
        //        }
    }

    /**
     * Checks if instance is not Null;
     * Then returns a single instance or false if not empty.
     * @author jolly
     * @return MyActiveRecord|boolean
     */
    public static function getSingleIntanceFromArray($checkLists)
    {
        if (count($checkLists)) {
            return $checkLists[0];
        } else {
            return false;
        }
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * * Saves if no errors, throws an exception if not.
     * To output the saved instance, pass boolean true as the second parameter
     * To enable throwing an exception pass boolean TRUE as the third parameter.
     * @author jollybool
     *
     * @param CModel $model
     * @param CModel $outputModel
     * @param bool $is_throwException
     * @param type $runValidation
     *
     * @return boolean|\MyActiveRecord
     * @throws Exception
     */
    public function saveModel($model = false, $outputModel = false, $is_throwException = true, $runValidation = true)
    {
        if (!$model) {
            $model = $this;
        }
        $model->scenario = $this->getScenario();
        if (!$model->save($runValidation)) {
            if ($is_throwException) {
                $errors = $model->getSingleArrayErrors();
                throw new CHttpException(400, CJSON::encode($errors));
            } else {
                return false;
            }
        }
        if ($outputModel) {
            return $model;
        } else {
            return true;
        }
    }

    /**
     * Will do multiple insert. Accepts array of data to be inserted.
     *
     * @param Array $sqlQueries
     *
     * @throws Exception
     */
    public function multiInsert($sqlQueries)
    {
        try {
            $builder = $this->getCommandBuilder();
            if (!empty($sqlQueries)) {
                $query = $builder->createMultipleInsertCommand($this->tableName(), $sqlQueries);

                $query->execute();
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getTraceAsString(), 500);
        }
    }

    /**
     * Returns relation value in relations() method.
     * @author jolly
     *
     * @param type $name
     *
     * @return type
     */
    private function getRelation($name)
    {
        $relations = $this->relations();
        if (isset($relations[$name])) {
            return $relations[$name];
        }

        return false;
    }

    /**
     * Returns relation Model Name.
     * @author Jolly Caralos
     *
     * @param String Name
     * The current obj context property
     * (that can be found in its relations() method).
     */
    public function getRelatedModelName($name)
    {
        $relation = $this->getRelation($name);
        if ($relation) {
            if ($relation[0] !== 'CStatRelation') {
                return $relation[1];
            }
        }

        return false;
    }

    /**
     * Returns a new Instance of the related property.
     * Returns FALSE if doesn't exists.
     *
     * @author Jolly Caralos
     *
     * @param String $name
     * Property Name
     */
    protected function createNewInstance($name)
    {
        $model = $this->getRelatedModelName($name);
        if ($model) {
            return new $model;
        }

        return false;
    }

    /**
     * Instanstiates an instance of the related property.
     * @author Jolly Caralos
     *
     * @param MyActiveRecord $name
     * Key Name of the related property in the relations() method.
     *
     * @return \model
     */
    private function getInstanceOfRelation($name)
    {
        if (isset($this->$name)) {
            if (!empty($this->$name)) {
                return $this->$name;
            } else {
                return $this->createNewInstance($name);
            }
        } else {
            return $this->createNewInstance($name);
        }
    }

    /**
     * Return an instance of the related property.
     * @author jolly
     *
     * @param type $name
     * Key name of the related model
     *
     * @return MyActiveRecord
     */
    public function getRelatedProperty($name)
    {
        $model = $this->getInstanceOfRelation($name);

        return $model;
    }

    private function getActiveDataProvider($options = [])
    {
        $options = CMap::mergeArray([
            'criteria' => $this->criteria,
        ], $options);

        return new CActiveDataProvider($this, $options);
    }

    private function executeFind($is_dataProvider = true, $options = [], $arMethod)
    {
        $data = null;
        switch ($is_dataProvider) {
            case true:
                $data = $this->getActiveDataProvider($options);
                break;
            case false:
                $data = $this->$arMethod($this->criteria);
        }

        return $data;
    }

    public function getDataRunMethod(
        $behaviors,
        $is_dataProvider,
        $options = [],
        $arMethod = 'findAll',
        $refresh = true
    ) {
        if ($refresh) {
            if (is_array($behaviors)) {
                foreach ($behaviors as $behavior) {
                    $this->criteria = $this->getCriteria($behaviors, $this->criteria);
                }
            } else {
                $this->criteria = $this->getCriteria($behaviors, $this->criteria);
            }

            return $this->executeFind($is_dataProvider, $options, $arMethod);
        }

        return $this->data;
    }

    public function logicalDelete($condition = '', $params = [])
    {
        $builder = $this->getCommandBuilder();
        $criteria = $builder->createCriteria($condition, $params);
        $command = $builder->createUpdateCommand($this->getTableSchema(), ['is_deleted' => 1], $criteria);

        return $command->execute();
    }

    /**
     * Returns all errors of a model in a single level array
     * @return mixed
     * Returns Array of errors if there are errors, else, return FALSE
     * @author jolly
     */
    public function getSingleArrayErrors($other_errors = [])
    {
        $error_list = [];
        foreach ($this->getErrors() as $field_errors) {
            foreach ($field_errors as $error) {
                $error_list[] = $error;
                /* Only one error per field */
                break;
            }
        }
        if (empty($error_list) && empty($other_errors)) {
            return false;
        }

        return CMap::mergeArray($other_errors, $error_list);
    }

    /**
     * Returns a List in CHtml::listData format.
     *
     * @author Jolly Caralos
     * Date: 14.05.07
     */
    public function findListData(
        $value_attr,
        $present_attr,
        $addEmpty = false,
        $criteria = null
    ) {
        if (isset($criteria)) {
            $rawList = $this->findAll($criteria);
        } else {
            $rawList = $this->findAll();
        }
        $list = CHtml::listData($rawList, $value_attr, $present_attr);

        if ($addEmpty) {
            $list = CMap::mergeArray(['' => '- SELECT -'], $list);
        }

        return $list;
    }

    /**
     * @param mixed $owner
     * The class that holds the DATE_FORMAT AND TIME_FORMAT
     * @param String $dateTime
     *
     * @author Jolly Caralos
     */
    public static function formatDateTime($owner, $dateTime = '')
    {
        if (empty($dateTime)) {
            return null;
        }

        return date($owner::$DATE_FORMAT, strtotime($dateTime)) . " "
            . date($owner::$TIME_FORMAT, strtotime($dateTime));
    }

    /**
     * Builds a comma separated string of scenarios.
     *
     * @param array $scenarios
     *
     * @author Jolly Caralos
     */
    public static function buildScenarios($scenarios = [])
    {
        return implode(', ', $scenarios);
    }

    /**
     * Instructs user defined function to return the context($this) in order
     * to combine criteria stored in the context of the object.
     *
     * @return $this
     * @author Jolly Caralos
     */
    public function buildCriteria()
    {
        $this->buildCriteria = true;

        return $this;
    }

    public function resetCriteria()
    {
        $this->setDbCriteria(new CDbCriteria());

        return $this;
    }

    /**
     * Returns the current status of $buildCriteria property.
     * @return Boolean
     * @author Jolly Caralos
     */
    public function isBuildCriteria()
    {
        return empty($this->buildCriteria) ? false : true;
    }

    /**
     * Reverts back $buildCriteria to FALSE of the context
     * that was used to execute find(not the one that was
     * returned to the user), then executes
     * parent::beforeFind();
     *
     * @author Jolly Caralos
     */
    public function beforeFind()
    {
        if ($this->isBuildCriteria()) {
            $this->buildCriteria = false;
        }

        parent::beforeFind();
    }

    /**
     * Will check if the intent is to build criteria.
     * If TRUE, return the current context of the class or return a record
     * base on the criteria and the passed method(find, findAll, etc.).
     *
     * @param CDbCriteria $criteria
     * @param String $method
     *
     * @return mixed
     *
     * @author Jolly Caralos
     * @since 1.0
     */
    public function decorateBeforeBuildCriteria(CDbCriteria $criteria = null, $defaultMethod = 'findAll')
    {
        if (!empty($criteria)) {
            $this->getDbCriteria()->mergeWith($criteria);
        }

        if ($this->isBuildCriteria()) {
            return $this;
        }
        $result = $this->{$defaultMethod}();
        $this->resetCriteria();

        return $result;
    }

    /**
     * @author Jazel Bretch
     *  1/22/2015 10:59:07 AM
     *
     * if model has document_id attribute and document is not empty
     *
     * models should also include a tracerModelName property
     * - the name of the EntityModel used by the document
     */
    protected function beforeDelete()
    {
        $saveOk = parent::beforeDelete();

        if (
            $this->hasAttribute('document_id')
            && property_exists($this, 'tracerModelName')
            && !empty($this->document_id)
        ) {
            $saveOk = EDMSQuery::instance('entities.' . strtolower($this->tracerModelName))->remove([
                '_id' => new MongoId(
                    $this->document_id
                )
            ]);
        }

        return $saveOk;
    }


    /**
     * Functions for adding Nurse Notes
     * Used in Kardex Classes
     */

    public function getNurseNoteAction()
    {
        return "";
    }

    public function getNurseNoteCreateDate()
    {
        if ($this->hasAttribute('carryout_dt')) {
            return $this->carryout_dt;
        } elseif ($this->hasAttribute('create_dt')) {
            return $this->create_dt;
        }

        return null;
    }

    public function getNurseNoteCurEncounter()
    {
        if ($this->hasAttribute('encounter_no')) {
            return $this->encounter_no;
        } elseif ($this->getRelation('docorder') || !empty($this->docorder)) {
            return $this->docorder->orderBatch->encounter_no;
        }

        return null;
    }

    public function getNurseNoteGroupId()
    {
        if ($this->hasAttribute('kardex_groupid')) {
            return $this->kardex_groupid;
        }

        return null;
    }


    public function getCancelledBy()
    {
        $cancelledBy = !empty($this->cancel_id) ? $this->cancel->p->fullname : "";
        if (!empty($this->cancel->personnelAssignment)) {
            $cancelledBy .= " (" . $this->cancel->personnelAssignment->area->area_desc . ")";
        }

        return $cancelledBy;
    }

    /**
     * Display cancel information
     */
    public function getCancelInformation()
    {
        $detail = CHtml::tag(
            'p',
            [],
            CHtml::tag('i', ['class' => 'icon-user'])
                . "&nbsp&nbsp" . $this->cancelledBy
        );
        $detail .= CHtml::tag('hr', ['style' => 'margin:0']);
        $detail .= CHtml::tag(
            'p',
            [],
            CHtml::tag('i', ['class' => 'icon-calendar'])
                . "&nbsp&nbsp" . date("F d, Y", strtotime($this->cancel_dt))
        );
        $detail .= CHtml::tag('hr', ['style' => 'margin:0']);
        $detail .= CHtml::tag(
            'p',
            [],
            CHtml::tag('i', ['class' => 'icon-time'])
                . "&nbsp&nbsp" . date("h:i:s A", strtotime($this->cancel_dt))
        );
        $detail .= CHtml::tag('hr', ['style' => 'margin:0']);
        $detail .= CHtml::tag(
            'p',
            [],
            CHtml::tag('i', ['class' => 'icon-flag'])
                . "&nbsp&nbsp" . $this->cancel_reason
        );

        return $detail;
    }

    /**
     * behaviors for cancelling of request
     */
    public function behaviors()
    {
        $behaviors = [
            'CancelRequestBehavior' => [
                'class' => 'application.components.behaviors.CancelRequestBehavior',
            ],
            'TransactRequestBehavior' => [
                'class' => 'application.components.behaviors.TransactRequestBehavior',
            ],
        ];

        return CMap::mergeArray(parent::behaviors(), $behaviors);
    }
}
