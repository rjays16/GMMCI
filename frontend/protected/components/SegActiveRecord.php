<?php

/**
 * Extension of Yii's CActiveRecord
 *
 * @package application.components
 */
class SegActiveRecord extends CActiveRecord {

    /**
     *
     * @return type
     */
    protected function beforeSave() {
        if ($this->isNewRecord) {
            if ($this->hasAttribute('create_dt')) {
                $this->create_dt = date('YmdHis');
            }
            if ($this->hasAttribute('create_id')) {
                $this->create_id = Yii::app()->user->getId();
            }
        }

        if ($this->hasAttribute('modify_dt')) {
            $this->modify_dt = date('YmdHis');
        }
        if ($this->hasAttribute('modify_id')) {
            $this->modify_id = Yii::app()->user->getId();
        }

        return parent::beforeSave();
    }
}
