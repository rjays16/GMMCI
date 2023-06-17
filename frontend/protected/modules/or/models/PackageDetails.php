<?php

Yii::import('or.models.LabServices');
Yii::import('or.models.RadioServices');
Yii::import('or.models.PharmaProductsMain');
Yii::import('or.models.OtherServices');

/** 
 * This is the model class for table "seg_package_details". 
 * 
 * The followings are the available columns in table 'seg_package_details': 
 * @property integer $item_id
 * @property integer $package_id
 * @property string $item_code
 * @property string $item_name
 * @property string $item_purpose
 * @property double $quantity
 * @property double $price
 * @property string $remarks
 * @property string $area
 * @property string $item_type
 * @property string $unit
 * 
 * The followings are the available model relations: 
 * @property Packages $package
 */ 
class PackageDetails extends CActiveRecord
{
    /**
     *  Item Purpose Constants
     */
    const ITEM_PURPOSE_PHARMACY='PH';
    const ITEM_PURPOSE_LABORATORY='LB';
    const ITEM_PURPOSE_RADIOLOGY='RD';
    const ITEM_PURPOSE_MISCELLANEOUS='MISC';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_package_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() 
    { 
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
            array('package_id', 'required'),
            array('package_id', 'numerical', 'integerOnly'=>true),
            array('quantity, price', 'numerical'),
            array('item_code, unit', 'length', 'max'=>30),
            array('item_name', 'length', 'max'=>50),
            array('item_purpose', 'length', 'max'=>4),
            array('remarks', 'length', 'max'=>100),
            array('area, item_type', 'length', 'max'=>10),
            // The following rule is used by search(). 
            // @todo Please remove those attributes that should not be searched. 
            array('item_id, package_id, item_code, item_name, item_purpose, quantity, price, remarks, area, item_type, unit', 'safe', 'on'=>'search'), 
        ); 
    } 

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'package' => array(self::BELONGS_TO, 'Packages', 'package_id'),
            'pharma' => array(self::BELONGS_TO, 'PharmaProductsMain', 'item_code'),
            'lab' => array(self::BELONGS_TO, 'LabServices', 'item_code'),
            'rad' => array(self::BELONGS_TO, 'RadioServices', 'item_code'),
            'misc' => array(self::BELONGS_TO, 'OtherServices', 'item_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array( 
            'item_id' => 'Item',
            'package_id' => 'Package',
            'item_code' => 'Item Code',
            'item_name' => 'Item Name',
            'item_purpose' => 'Item Purpose',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'remarks' => 'Remarks',
            'area' => 'Area',
            'item_type' => 'Item Type',
            'unit' => 'Unit',
        ); 
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('item_id',$this->item_id);
        $criteria->compare('package_id',$this->package_id);
        $criteria->compare('item_code',$this->item_code,true);
        $criteria->compare('item_name',$this->item_name,true);
        $criteria->compare('item_purpose',$this->item_purpose,true);
        $criteria->compare('quantity',$this->quantity);
        $criteria->compare('price',$this->price);
        $criteria->compare('remarks',$this->remarks,true);
        $criteria->compare('area',$this->area,true);
        $criteria->compare('item_type',$this->item_type,true);
        $criteria->compare('unit',$this->unit,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PackageDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getItemPurposeOptions(){
        return array(
            self::ITEM_PURPOSE_PHARMACY=>'Pharmacy',
            self::ITEM_PURPOSE_LABORATORY=>'Laboratory',
            self::ITEM_PURPOSE_RADIOLOGY=>'Radiology',
            self::ITEM_PURPOSE_MISCELLANEOUS=>'Miscellaneous'
        );
    }

    public function getItemPurposeText(){
        $itemPurposeOptions = $this->itemPurposeOptions;
        return isset($itemPurposeOptions[$this->item_purpose])?$itemPurposeOptions[$this->item_purpose]:"unknown purpose ({$this->item_purpose})";
    }

    public function getDescription(){
        switch ($this->item_purpose) {
            case self::ITEM_PURPOSE_PHARMACY:
                return $this->pharma->name;
                break;
            case self::ITEM_PURPOSE_LABORATORY:
                return $this->lab->name;
                break;
            case self::ITEM_PURPOSE_RADIOLOGY:
                return $this->rad->name;
                break;
            case self::ITEM_PURPOSE_MISCELLANEOUS:
                return $this->misc->name;
                break;
            default:
                return "";
        }
    }
}
