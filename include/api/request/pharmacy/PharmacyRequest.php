<?php
/**
 * PharmacyRequest.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright Copyright &copy; 2012-2013 Segworks Technologies Corporation
 */

Loader::import('db.Model');

/**
 * Represents a pharmacy transaction
 *
 * @package request.pharmacy
 */
class PharmacyRequest extends Model 
{
    /**
     * @see Model::getTableName
     */
    public static function getTableName() 
    {
        return 'seg_pharma_orders';
    }
    
    /**
     * @see Model::getFieldNames
     */
    public static function getFieldNames() 
    {
        return array(
            'refno',
            'department',
            'pharma_area',
            'request_source',
            'orderdate',
            'pid',
            'walkin_pid',
            'request_dept',
            'encounter_nr',
            'related_refno',
            'related_refsource',
            'ordername',
            'orderaddress',
            'discountid',
            'discount',
            'charge_type',
            'is_cash',
            'is_tpl',
            'is_urgent',
            'amount_due',
            'comments',
            'history',
            'create_id',
            'create_time',
            'modify_id',
            'modify_time',
            'is_deleted'
        );
    }

    /**
     * 
     * @see Model::getPrimaryKeys
     */
    public static function getPrimaryKeys() 
    {
        return array('refno');
    }

    /**
     * @see Model::getRelations
     */
    public static function getRelations() 
    {
        return array();
    }

    /**
     * Check whether the request is a PHIC request
     */
    public function isPhic()
    {
        return $this->is_cash !== 1 && 
            strtoupper($this->charge_type) == 'PHIC';
    }

}