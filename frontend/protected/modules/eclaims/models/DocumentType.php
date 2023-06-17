<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com> 
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 *
 */

/**
 * This is the model class for table "seg_eclaims_eligibility_document".
 *
 * The followings are the available columns in table 'seg_eclaims_eligibility_document':
 * @property string $id
 * @property string $eligibility_id
 * @property string $code
 * @property string $name
 * @property string $reason
 *
 */
class DocumentType extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_eclaims_document_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array();
	}


    public function scopes()
    {
        $alias = $this->tableAlias;
        return array(
            'active' => array(
                'condition' => "{$alias}.existing != 0"
            ),

        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Document Code',
			'name' => 'Document Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EligibilityDocument the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}