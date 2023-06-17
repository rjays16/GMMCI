<?php
/**
 * 
 * Config.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */
    
/**
 * Model class for `config` table
 *
 * @package application.models
 */
class Config extends CareActiveRecord {
    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Category the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
    
    /**
	 * @see CActiveRecord::tableName
	 */
    public function tableName()  {
        return 'care_config_global';
    }
    
    /**
	 * @see CActiveRecord::rules
	 */
	public function rules() {
		return array(
            // reqiuired attributes
			array('type', 'required'),
		);
	}
    
    /**
	 * @see CActiveRecord::relations.
	 */
	public function relations() {
		return array();
	}

    /**
	 * @return CActiveRecord::attributeLabels
	 */
	public function attributeLabels() {
        return array();
	}

    /**
     * Returns the configuration 
     */
    public static function get($configName) {
        $obj = self::model()->findByPk($configName);
        if (!$obj) {
            throw new CException('Cannot retrieve the configuration value for `'.$configName.'`');
        }
        return $obj;
    }
    
    /**
     * Casts the value of the configuration to string
     */
    public function __toString() {
        return $this->value;
    }
    
    /**
     * Commits a seires of configuration parameters to the database
     * @param array $config
     * @return boolean
     * @todo Merge configuration objects referring to the same configuration name
     */
    public static function saveConfigurations($settings) {
        $model = self::model();
        $transaction = $model->getDbConnection()->beginTransaction();
        try {
            foreach ($settings as $parameter=>$value) {
                try {
                    $config = Config::get($parameter);
                } catch (CException $e) {
                    $config = new Config;
                    $config->type = $parameter;
                }
                
                $config->value = $value;
                if (!$config->save()) {
                    throw new CDbException('Invalid configuration encountered');
                }
            }
        } catch (CException $e) {
            $transaction->rollback();
            die($e->getMessage());
            return false;
        }

        $transaction->commit();
        return true;
    }
}
