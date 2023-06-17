<?php
/**
 * CareActiveRecord.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

/**
 * Extension of Yii's CActiveRecord
 *
 * @package application.components
 */
class CareActiveRecord extends CActiveRecord {

    /**
     *
     * @return type
     */
    protected function beforeSave() {
        if ($this->isNewRecord) {
            if ($this->hasAttribute('create_time')) {
                $this->create_time = date('YmdHis');
            }
            if ($this->hasAttribute('create_id')) {
                $this->create_id = Yii::app()->user->getId();
            }
        }

        if ($this->hasAttribute('modify_time')) {
            $this->modify_time = date('YmdHis');
        }
        if ($this->hasAttribute('modify_id')) {
            $this->modify_id = Yii::app()->user->getId();
        }

        return parent::beforeSave();
    }


    /**
     * Yii's default magic getter for the CActiveRecord class returns null
     * for HAS_ONE or BELONGS_TO relationsships when it does not find a
     * related record in the database. We need a mechanism to be able to
     * extract a model object from the relationship. This is useful for widgets
     * that can consume objects as model which will fail when the relation
     * returns a null.
     *
     * @return CModel An empty instance of the related model
     * @throws CDbException If
     */
    public function getRelatedModel($relation) {
        $md = $this->getMetaData();
        $relation = $md->relations[$relation];

        if (empty($relation)) {
            throw new CDbException(Yii::t('yii','{class} does not have relation "{name}".',
                array('{class}'=>get_class($this), '{name}'=>$relation)));
        }

        return new $relation->className;
    }
}
