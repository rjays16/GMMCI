<?php
/**
 * PharmacyItem.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation 
 */

Loader::import('db.Model');

/**
 * Description of PharmacyItem
 * 
 * @package 
 */
class PharmacyItem extends Model
{
    const CLASS_MEDICINE = 'M';
    const CLASS_SUPPLY = 'S';
    
    /**
     * @see Model::getTableName
     */
    public static function getTableName()
    {
        return 'care_pharma_products_main';
    }
    
    /**
     * @see Model::getPrimaryKeys
     */
    public static function getPrimaryKeys()
    {
        return array('bestellnum');
    }
    
    /**
     * @see Model::getFieldNames
     */
    public static function getFieldNames() 
    {
        return array(
            'bestellnum',
            'artikelname',
            'generic',
            'description',
            'prod_class',
            'lock_flag',
            'price_cash',
            'price_charge',
            'is_socialized',
            'is_restricted',
            'is_deleted'
        );
    }
    
    /**
     * @see Model::getRelations
     */
    public static function getRelations()
    {
        return array();
    }

}
