<?php
/**
 * ActiveRecord.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

/**
 * Extension of Yii's CActiveRecord
 *
 * @package application.components
 */
class ActiveRecord extends CActiveRecord
{

    /**
     *
     * @return type
     */
    protected function beforeSave()
    {
        if ($this->isNewRecord) {
            if ($this->hasAttribute('created')) {
                $this->created = date('YmdHis');
            }
            if ($this->hasAttribute('created_by')) {
                $this->created_by = Yii::app()->user->getId();
            }
        }

        if ($this->hasAttribute('modified')) {
            $this->modified = date('YmdHis');
        }
        if ($this->hasAttribute('modified_by')) {
            $this->modified_by = Yii::app()->user->getId();
        }

        return parent::beforeSave();
    }
}
