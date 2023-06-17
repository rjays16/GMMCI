<?php
/**
 * PhicCoverage.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright Copyright &copy; 2012-2013 Segworks Technologies Corporation
 */

Loader::import('db.Model');
require_once Environment::getRootPath() . 'include/care_api_classes/coverage/class_coverage.php';

/**
 * Represents a PHIC coverage transaction
 *
 * @package request.phic.coverage
 */

class PhicCoverage extends Model
{
    const HCARE_ID = 18;
    const SOURCE_EQUIPMENT = 'E';
    const SOURCE_LABORATORY = 'L';
    const SOURCE_MEDICINE = 'M';
    const SOURCE_OTHERS = 'O';
    const SOURCE_RADIOLOGY = 'R';
    const SOURCE_SUPPLY = 'S';
    /**
     * @see Model::getTableName
     * @return string
     */
    public static function getTableName()
    {
        return 'seg_applied_coverage';
    }

    /**
     * @see Model::getPrimaryKeys
     * @return array()
     */
    public static function getPrimaryKeys()
    {
        return array(
            'ref_no',
            'source',
            'item_code',
            'hcare_id'
        );
    }

    /**
     * @see Model::getRelations
     * @return array
     */
    public static function getRelations()
    {
        return array();
    }

    /**
     * @see Model::getFieldNames
     * @return array()
     */
    public static function getFieldNames()
    {
        return array(
            'ref_no',
            'source',
            'item_code',
            'hcare_id',
            'priority',
            'coverage'
        );
    }

    /**
     * Adapter to SegCoverage::add method
     *
     * @param string $refSource  Cost center source of the request
     * @param string $refNo Reference no. of the request
     * @param string $itemCode  Code/ID of the item to be covered in the
     * request
     * @param int $quantity Number of items to be covered
     * @param int $priority Optional.
     * @return boolean Returns TRUE if the item was successfully added
     * @throws Exception
     *
     * @todo Add audit trail
     * @todo Add checking for coverage limit
     */
    public static function add(
        $refSource,
        $refNo,
        $itemCode,
        $quantity,
        $priority=null)
    {
        $insuranceNo = self::HCARE_ID;
        $legacyCoverage = new SegCoverage;
        try {
            return $legacyCoverage->add(
                $insuranceNo,
                $refSource,
                $refNo,
                $itemCode,
                $quantity,
                $priority
            );
        } catch (Exception $e) {
            throw new DbException(
                'Failed to add coverage',
                $legacyCoverage->getQuery()
            );
        }
    }

    /**
     * Adapter to SegCoverage::remove method
     *
     * @param string $refSource  Cost center source of the request
     * @param string $refNo Reference no. of the request
     * @param string $itemCode  Item code of the item to be removed
     * @param int $quantity Number of items to be removed. If the covered
     * amount is less than the total amount to be removed, the entire
     * row is deleted from the database
     * @return boolean Returns TRUE if the item was successfully removed from
     * the coverage list
     */
    public static function remove(
        $refSource,
        $refNo,
        $itemCode,
        $quantity = null)
    {
        $insuranceNo = self::HCARE_ID;
        $legacyCoverage = new SegCoverage;

        try {
            return $legacyCoverage->remove(
                $insuranceNo,
                $refSource,
                $refNo,
                $itemCode,
                $quantity
            );
        } catch (Exception $e) {
            if ($e->getMessage() == 'Item has no coverage') {
                return true;
            } else {
                throw new DbException(
                    'Failed to remove coverage',
                    $legacyCoverage->getQuery()
                );
            }
        }
    }
}