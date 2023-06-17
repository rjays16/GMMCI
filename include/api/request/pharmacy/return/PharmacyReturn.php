<?php
/**
 * PharmacyReturn.php
 *
 * @author Alvin Quinones
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

Loader::import('db.Model');
Loader::import('request.pharmacy.PharmacyRequest');
Loader::import('request.pharmacy.return.PharmacyReturnItem');

/**
 * Represents a return transaction
 *
 * @package request.pharmacy.return
 * @property PharmacyReturnItem[] $returnItems
 */
class PharmacyReturn extends Model
{
    /**
     * @var array $items
     */
    protected $items = array();

    /**
     * @see Model::getTableName
     * @return string
     */
    public static function getTableName()
    {
        return 'seg_pharma_returns';
    }

    /**
     * @see Model::getPrimaryKeys
     * @return array()
     */
    public static function getPrimaryKeys()
    {
        return array('return_nr');
    }

    /**
     * @see Model::getRelations
     * @return array
     */
    public static function getRelations()
    {
        return array(
            'returnItems' => array(
                'type' => ModelRelation::HAS_MANY,
                'model' => 'PharmacyReturnItem',
                'mapping' => array(
                    'return_nr' => 'return_nr'
                ),
                'condition' => array()
            )
        );
    }

    /**
     * @see Model::getFieldNames
     * @return array()
     */
    public static function getFieldNames()
    {
        return array(
            'return_nr',
            'return_date',
            'pid',
            'encounter_nr',
            'return_name',
            'return_address',
            'refund_amount',
            'refund_amount_fixed',
            'pharma_area',
            'comments',
            'create_id',
            'create_time',
            'modify_id',
            'modify_time',
            'history'
        );
    }

    /**
     * Description
     * @param PharmacyReturnItem $item
     * @return void
     * @todo Add validation when adding items
     */
    public function addItem(PharmacyReturnItem $item)
    {
        // validation goes here...
        $this->items[] = $item;
    }

    /**
     * Description
     * @param ReturnItem $item
     * @return boolean
     */
    public function hasItem(PharmacyReturnItem $item)
    {
        foreach ($this->items as $_item) {
            if ($_item->equals($item)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param string $hash
     */
    public function getItem($i)
    {
        if (isset($this->items[$i])) {
            return $this->items[$i];
        } else {
            return null;
        }
    }

    /**
     *
     * @param int|PharmacyReturnItem $i
     * @return PharmacyReturnItem|null
     */
    public function removeItem($i)
    {
        if ($i instanceof PharmacyReturnItem) {
            foreach ($this->items as $j=>$_item) {
                if ($_item->equals($item)) {
                    $i = $j;
                    break;
                }
            }
        }
        $item = @$this->items[$i];
        unset($this->items[$i]);
        return $item;
    }

    /**
     *
     * @return string
     */
    public function generateReturnId()
    {
        $year = date('Y');
        $query = 'SELECT MAX(return_nr) `nr` FROM seg_pharma_returns WHERE return_nr LIKE ?';
        $result = $this->mapper->query(
            $query,
            array($year.'%')
        );
        $newID = @$result[0]['nr'];
        if (empty($newID)) {
            $newID = $year.'000000';
        } else {
            $newID++;
        }

        return $newID;
    }

    /**
     * @see Model::beforeSave
     */
    protected function beforeSave(Event $event)
    {
        // insert latest return series number
        if ($this->mapper &&
            $this->isNewRecord() &&
            empty($this->return_nr)
        ) {
            $this->return_nr = $this->generateReturnId();
        }

        // do not save empty items
        if (empty($this->items)) {
            return false;
        }

        // update items
        $totalRefund = 0;
        foreach ($this->items as $returnItem) {
            /* @var $returnItem PharmacyReturnItem */
            $returnItem->return_nr = $this->return_nr;
            $returnItem->setMapper($this->mapper);
            if ($returnItem->requestItem->isCash()) {
                $totalRefund += $returnItem->requestItem->getPrice() * $returnItem->quantity;
            }
        }
        $this->refund_amount = $totalRefund;
        return parent::beforeSave($event);
    }

    /**
     * @see Model::afterSave
     */
    protected function afterSave(Event $event)
    {
        $oldItems = $this->returnItems;
        foreach ($oldItems as $item) {
            /* @var $item PharmacyReturnItem */
            if (!$this->hasItem($item)) {
                if (!$item->delete()) {
                    return false;
                }
            } else {
                $item = $this->removeItem($item->getHash());
                if (!$item->save()) {
                    return false;
                }
            }
        }

        // save items
        foreach ($this->items as $returnItem) {
            if (!$returnItem->save()) {
                return false;
            }
        }
        $this->items = array();
        return parent::afterSave($event);
    }

}