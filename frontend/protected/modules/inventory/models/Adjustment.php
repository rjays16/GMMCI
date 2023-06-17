<?php
/**
 * Adjustment.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright &copy; , Segworks Technologies Corporation
 */

use SegHis\modules\inventory\models\PharmacyProduct;

/**
 * This is the model class for table "seg_inventory_adjustment".
 *
 * The followings are the available columns in table 'seg_inventory_adjustment':
 * @property string $refno
 * @property string $adjust_date
 * @property integer $adjusting_id
 * @property string $area_code
 * @property string $remarks
 * @property integer $is_deleted
 * @property integer $is_posted
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 *
 * @property string $areaName
 * @property string $personnelName
 * @property string $isPostedToInventory
 *
 * The followings are the available model relations:
 * @property AdjustmentDetails[] $adjustmentDetails
 * @property Personnel $personnel
 * @property Area $area
 * @property Person $personnelInfo
 */
class Adjustment extends CareActiveRecord
{
    /**
     * [$reason description]
     * @var [type]
     */
    public $reason;
    /**
     * Used by getSkuItemProvider method to retrieve the total nu,ber of
     * unfiltered rows.
     * @var int|null
     *
     * @todo This is currently a very lazy implementation. A better way would be to implement something similar to a FilteredDataProvider(?) which provides information on the total and filtered row counts.
     */
    private static $_totalSkuCount = null;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_inventory_adjustment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('refno, adjusting_id, area_code, adjust_date, is_deleted', 'required'),
            array('adjusting_id, is_deleted', 'numerical', 'integerOnly' => true),
            array('refno', 'length', 'max' => 12),
            array('area_code', 'length', 'max' => 10),
            array('modify_id, create_id', 'length', 'max' => 35),
            array('adjust_date, create_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('isPostedToInventory, personnelName, areaName, refno, adjust_date, adjusting_id, area_code, remarks, is_deleted, history, modify_id, modify_dt, create_id, create_dt', 'safe', 'on' => 'search'),
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
            'adjustmentDetails' => array(self::HAS_MANY, 'AdjustmentDetails', 'refno', 'condition' => 'adjustmentDetails.is_deleted = 0'),
            'personnel' => array(self::HAS_ONE, 'Personnel', array('nr' => 'adjusting_id')),
            'personnelInfo' => array(self::HAS_ONE, 'Person', array('pid' => 'pid'), 'through' => 'personnel'),
            'area' => array(self::HAS_ONE, 'Area', array('area_code' => 'area_code')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'refno' => 'Reference #',
            'adjust_date' => 'Adjustment Date',
            'adjusting_id' => 'Authorized by',
            'area_code' => 'Area',
            'remarks' => 'Remarks',
            'is_deleted' => 'Is Deleted',
            // 'is_posted' => 'Is Posted to Inventory',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
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

        $criteria = new CDbCriteria;

        $criteria->compare('refno', $this->refno, true);
        $criteria->compare('adjust_date', $this->adjust_date ? date('Y-m-d', strtotime($this->adjust_date)) : '', true);
        $criteria->compare('adjusting_id', $this->adjusting_id);
        $criteria->compare('area_code', $this->area_code, true);
        // if ($this->is_posted !== '') {
        //     $criteria->compare('is_posted', $this->is_posted);
        // }

        $criteria->compare('remarks', $this->remarks, true);
        $criteria->compare('is_deleted', 0);
        $criteria->compare('history', $this->history, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_dt', $this->modify_dt, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_dt', $this->create_dt, true);
        $criteria->compare('t.area_code', $this->areaName, true);

        if ($this->personnelName) {
            if (is_numeric($this->personnelName)) {
                $criteria->compare('adjusting_id', $this->personnelName);
                $criteria->compare('personnel.pid', $this->personnelName, false, 'OR');
            } else {
                $names = explode(',', $this->personnelName, 2);
                $criteria->compare('personnelInfo.name_first', trim($names[1]), true);
                $criteria->compare('personnelInfo.name_last', trim($names[0]), true);
            }
        }

        $criteria->with = array(
            'area' => array(
                'select' => 'area.area_name'
            ),
            'personnelInfo' => array(
                'select' => 'personnelInfo.name_first,personnelInfo.name_last,personnelInfo.name_middle,personnelInfo.suffix'
            )
        );

        $dataProvider = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));

        // if (strtolower($this->isPostedToInventory) == 'yes')
        //     $criteria->compare('is_posted', 1);
        // else if (strtolower($this->isPostedToInventory) == 'no')
        //     $criteria->compare('is_posted', 0);

        $dataProvider->sort = array(
            'defaultOrder'=>'adjust_date DESC',
            'attributes' => array(
                'refno',
                'adjust_date',
                'personnelName' => array(
                    'asc' => 'personnelInfo.name_last ASC',
                    'desc' => 'personnelInfo.name_last DESC',
                ),
                'areaName' => array(
                    'asc' => 'area.area_name ASC',
                    'desc' => 'area.area_name DESC'
                ),
                // 'isPostedToInventory' => array(
                //     // 'asc' => 'is_posted ASC',
                //     'desc' => 'is_posted DESC'
                // ),
            )
        );

        return $dataProvider;
    }

    public function getAreaName()
    {
        return $this->area->area_name;
    }

    public function setAreaName($value)
    {
        $this->areaName = $value;
    }

    public function getPersonnelName()
    {
        return $this->personnelInfo->fullName;
    }

    public function setPersonnelName($value)
    {
        $this->personnelName = $value;
    }

    public function getIsPostedToInventory()
    {
        return false ? 'Yes' : 'No';
    }

    public function setIsPostedToInventory($value)
    {
        $this->isPostedToInventory = $value;
    }

    /**
     *
     *
     * @throws CException
     */
    public function updateInventory()
    {
        foreach ($this->adjustmentDetails as $detail) {
            $detail->updateInventory();
        }

        // if (!$this->saveAttributes(array('is_posted' => 1))) {
        //     throw new CException('Adjustment entry did not update successfully');
        // }
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Adjustment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string
     *
     * @throws CDbException
     */
    public static function getNewRefNo()
    {
        $year = date('Y');
        $query = 'SELECT MAX(refno) `nr` FROM `'.static::model()->tableName().'` WHERE refno LIKE :year';
        $latest = self::model()->getDbConnection()->createCommand($query)->queryScalar(array(':year' => $year . '%'));
        if ($latest) {
            $latest++;
        } else {
            $latest = $year . '000001';
        }
        return (string) $latest;
    }

    /**
     * Returns the total unfiltered record count for the last
     * getSkuItemProvider method call.
     *
     * @todo Very iffy implementation that should be refactored later.
     *
     * @return int|null
     */
    public static function getTotalSkuItemsCount()
    {
        return self::$_totalSkuCount;
    }

    /**
     * [getSkusForAdjustment description]
     *
     * @param string $area [description]
     * @param string $searchKey [description]
     * @param array|bool $pagination The pagination object passed as a parameter when creating the data provider
     *
     * @return CDataProvider
     */
    public static function getSkuItemProvider($area, $searchKey='', $pagination=false, $tr_date)
    {
        if(!$tr_date){
          $tr_date = date('Y-m-d');
        }
        /** @var CDbCommand $command */
        $command = Yii::app()->db->createCommand();
        $command->select = "
          p.bestellnum,
          p.artikelname,
          p.generic,
          s.sku_id,
          s.unit_id,
          s.expiry_date,
          s.serial_no,
          s.lot_no,
          e.pc_unit_id,
          e.pack_unit_id,
          e.qty_per_pack
        ";
        $command->from('care_pharma_products_main p');
        $command->join('seg_item_extended e', 'e.item_code=p.bestellnum');
        $command->leftJoin('seg_sku_catalog s', 's.item_code=p.bestellnum');
        $command->where("s.area_code=:area");
        $command->where("p.is_deleted=0");
        $command->params[':area'] = $area;

        $summaryCommand = clone $command;
        $summaryCommand->select = "COUNT(*)";
        $filterSummaryCommand = clone $summaryCommand;

        self::$_totalSkuCount = $summaryCommand->queryScalar();

        // Apply filters here
        if ($searchKey) {
            $searchKeyFilter = "p.artikelname LIKE :key OR p.generic LIKE :key OR s.item_code LIKE :key";
            $searchKeyParams =  array(':key' => '%'.$searchKey . '%');
            $command->andWhere($searchKeyFilter, $searchKeyParams);
            $filterSummaryCommand->andWhere($searchKeyFilter, $searchKeyParams);
        }
           
        $command->group(array('p.bestellnum'));
        $command->order = array('p.artikelname ASC', 'p.generic ASC');
    
        if ($pagination['limit'] !== null && $pagination['offset'] !== null) {
            $command->limit($pagination['limit'], $pagination['offset']);
            $pagination = false;
        }
        
    
      //echo  $command->getText(); 
 //die;
        return new CSqlDataProvider($command, array(
            'totalItemCount' => $filterSummaryCommand->queryScalar(),
            'pagination'=> false
        ));
    }

    /**
     * @return bool
     */
    protected function beforeSave()
    {
//        if ($this->is_posted)
//            $this->history .= 'Posted to inventory by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";
//        else

        if ($this->getIsNewRecord())
            $this->history = 'Created by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";
        else
            $this->history .= 'Updated by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";

        $this->adjust_date = date('Y-m-d H:i:s', strtotime($this->adjust_date));

//        if ($this->is_posted) {
//            $this->updateInventory();
//        }

        return parent::beforeSave();
    }

  
     public static function getMedExpry($refno,$itemCode){
        global $db;
         $sql = "SELECT 
                  expiry_date
                FROM
                  seg_inventory_adjustment_details 
                  where refno = '$refno'
                  AND item_code = '$itemCode'";
        $ExpDate = $db->GetOne($sql);   
        return $ExpDate ? date('m/d/Y', strtotime($ExpDate)) : '0000-00-00';
    }

}
