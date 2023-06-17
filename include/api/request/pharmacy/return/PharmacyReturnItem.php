<?php
/**
 * PharmacyReturnItem.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 * @package request.pharmacy.return
 */

Loader::import('db.Model');
Loader::import('request.pharmacy.PharmacyRequestItem');
Loader::import('request.coverage.phic.PhicCoverage');

/**
 * Represents a single item returned through a return transaction
 *
 * @property PharmacyRequestItem $requestItem
 */
class PharmacyReturnItem extends Model
{
    protected $oldAttributes = null;

    /**
     * @see Model::getTableName
     * @return string
     */
    public static function getTableName()
    {
        return 'seg_pharma_return_items';
    }

    /**
     * @see Model::getPrimaryKeys
     * @return array()
     */
    public static function getPrimaryKeys()
    {
        return array(
            'return_nr',
            'ref_no',
            'bestellnum'
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
            'ref_no',
            'bestellnum',
            'quantity'
        );
    }

    /**
     * @see Model::getRelations
     * @return array
     */
    public static function getRelations()
    {
        return array(
             'requestItem' => array(
                 'type' => ModelRelation::BELONGS_TO,
                 'model' => 'PharmacyRequestItem',
                 'mapping' => array(
                     'ref_no' => 'refno',
                     'bestellnum' => 'bestellnum'
                 )
             )
        );
    }

    /**
     *
     * @see Model::beforeSave
     */
//    protected function beforeSave(Event $event)
//    {
//        if (!$this->isNewRecord && empty($this->oldAttributes)) {
//            $old = $this->findByIdentity($this->getIdentity());
//            $this->oldAttributes = $old->readField('*');
//        }
//
//        return parent::beforeSave($event);
//    }

    /**
     * @see Model::afterSave
     */
    protected function afterSave(Event $event)
    {
        if ($this->isNewRecord()) {
            $requestItem = $this->requestItem;
            if ($requestItem->isServed() && $requestItem->request->isPhic()) {

                $source = null;
                switch (strtoupper($requestItem->item->prodclass)) {
                    case PharmacyItem::CLASS_SUPPLY:
                        $source = PhicCoverage::SOURCE_SUPPLY;
                        break;
                    case PharmacyItem::CLASS_MEDICINE: // fall through
                    default:
                        $source = PhicCoverage::SOURCE_MEDICINE;
                        break;
                }

                // remove coverage
                PhicCoverage::remove(
                    $source,
                    $this->ref_no,
                    $this->bestellnum,
                    $this->quantity
                );
            }

        } else {
            // :(
        }

        return true;
    }

    /**
     *
     * @param PharmacyReturnItem $obj
     * @return boolean
     */
    public function equals($obj)
    {
        return (
            $obj instanceof PharmacyReturnItem &&
            $obj->ref_no == $this->ref_no &&
            $obj->bestellnum == $this->bestellnum
        );
    }
}