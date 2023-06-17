<?php
/*
 * PharmacyRequestItem.php
 * 
 * @author Alvin Quinones
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

Loader::import('db.Model');
Loader::import('request.pharmacy.PharmacyItem');

/**
 * Description of PharmacyRequestItem
 *
 * @property PharmacyRequest $request The pharmacy request to which this 
 * request item belongs
 * @package request.pharmacy
 */

class PharmacyRequestItem extends Model
{
    
    /**
     * @see Model::getTableName
     */
    public static function getTableName() 
    {
        return 'seg_pharma_order_items';
    }

    /**
     * @see Model::getPrimaryKeys
     */
    public static function getPrimaryKeys() 
    {
        return array(
            'refno',
            'bestellnum'
        );
    }
    
    /**
     * @see Model::getFieldNames
     */
    public static function getFieldNames() 
    {
        return array(
            'refno',
            'bestellnum',
            'quantity',
            'request_flag',
            'discount_class',
            'pricecash',
            'pricecharge',
            'price_orig',
            'is_consigned',
            'serve_status',
            'serve_remarks',
            'serve_id',
            'serve_dt',
            'cancel_reason',
            'is_deleted'
        );
    }

    /**
     * @see Model::getRelations
     */
    public static function getRelations() 
    {
        return array(
            'request' => array(
                'type' => ModelRelation::BELONGS_TO,
                'model' => 'PharmacyRequest',
                'mapping' => array(
                    'refno' => 'refno'
                ),
                'condition' => array()
            ),
            
            'item' => array(
                'type' => ModelRelation::BELONGS_TO,
                'model' => 'PharmacyItem',
                'mapping' => array(
                    'bestellnum' => 'bestellnum'
                ),
                'condition' => array()
            ),
        );
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCash()
    {
        return ($this->request->is_cash == 1);
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCharge()
    {
        return ($this->request->is_cash != 1);
    }

    /**
     * Checks if the item has been flagged as SERVED
     * @return boolean
     */
    public function isServed()
    {
        return $this->serve_status = 'S';
    }
    
    /**
     * Returns the final adjusted price of the pharmacy item in this 
     * request item
     * @return float
     */
    public function getPrice()
    {
        return (float) ($this->isCash() ? $this->pricecash : $this->pricecharge);
    }
}
