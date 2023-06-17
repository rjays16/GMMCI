<?php
/**
 * 
 */
require 'roots.php';
require_once $root_path.'include/care_api_classes/class_core.php';

/**
 * 
 * @author Alvin Quinones
 */
class SegCoverage extends Core 
{
    const REQUEST_EQUIPMENT = 'E';
    const REQUEST_LABORATORY= 'L';
    const REQUEST_MEDICINE = 'M';
    const REQUEST_OTHER = 'O';
    const REQUEST_RADIOLOGY = 'R';
    const REQUEST_SUPPLY = 'S';

    protected $target;
    protected $coverage_tb = "seg_applied_coverage";
    
    public function __construct() 
    {
        global $db;
        $this->coretable = $this->coverage_tb;
    }

    /**
     * 
     */
    public function clearReference( $refno, $source ) 
    {
        global $db;
        $this->sql = "DELETE FROM $this->coretable WHERE ref_no=".$db->qstr($refno)." AND source=".$db->qstr($source);
        if($this->result=$db->Execute($this->sql)) {
            return true;
        } else { return false; }
    }
    
    /**
     * 
     */
    public function addCoverage( $ref_array, $src_array, $code_array, $hcare_array, $coverage_array ) 
    {
        global $db;
        
        $bulk = array();
        $j=1;
        foreach ($ref_array as $i=>$ref) {
            $bulk[$i] = array( $ref, $src_array[$i], $code_array[$i], $hcare_array[$i], $j++, $coverage_array[$i] );
        }
        
        $this->sql = "INSERT INTO $this->coretable(ref_no,source,item_code,hcare_id,priority,coverage) VALUES(?,?,?,?,?,?)";

        if($this->result=$db->Execute($this->sql,$bulk, $autoquote=FALSE)) {
            return true;
        } else { 
            return false; 
        }
    }

    /**
     * Returns the quantity and price of a request item
     * @access protected
     * @param string $refSource  Cost center source of the request
     * @param string $refNo Reference no. of the request
     * @param string $itemCode  Code/ID of the item to be covered in the 
     * request
     * @return array Array containing `price` and `quantity` values 
     * of the request item
     * @throws Exception
     * @todo Delegate request queries to the appropriate Request class (i.e., 
     * medicines/supplies to Pharmacy class, lab requests to Laboratory class,
     * etc.)
     * @todo Add explicit exception classes
     */
    protected function getRequestItemDetails($refSource, $refNo, $itemCode) 
    {
        global $db;
        switch ($refSource) {
            case self::REQUEST_MEDICINE:
            case self::REQUEST_SUPPLY:
                // fetch request details
                $query = sprintf("SELECT o.encounter_nr,i.pricecharge `price`,i.quantity\n".
                        "FROM seg_pharma_order_items i\n".
                        "INNER JOIN seg_pharma_orders o ON o.refno=i.refno\n".
                        "WHERE i.refno=%s AND i.bestellnum=%s", 
                    $db->qstr($refNo),
                    $db->qstr($itemCode)
                );
                $this->setQuery($query);
                $request = $db->GetRow($query);
                if (empty($request)) {
                    throw new Exception('Failed to retrieve request details');
                } else {
                    return $request;
                }
                break;
            default:
                throw new Exception('Failed to handle requests from source: "'.$refSource.'"');
        }
    }

    /**
     * Adds a covered item to the coverage list of the specified insurance 
     * provider
     * 
     * @param string $insuranceNo The ID of the insurance provider
     * @param string $refSource  Cost center source of the request
     * @param string $refNo Reference no. of the request
     * @param string $itemCode  Code/ID of the item to be covered in the 
     * request
     * @param int $quantity Number of items to be covered. Optional. 
     * Defaults to the quantity indicated in the request
     * @param int $priority Optional.
     * @return boolean Returns TRUE if the item was successfully added
     * @throws Exception
     * 
     * @todo Add audit trail
     * @todo Add checking for coverage limit
     */
    public function add(
        $insuranceNo, 
        $refSource, 
        $refNo, 
        $itemCode,
        $quantity=null,
        $priority=null) 
    {
        global $db;

        $details = $this->getRequestItemDetails($refSource, $refNo, $itemCode);
        $totalAmount =$details['price'] * (is_null($quantity) ? $details['quantity'] : $quantity);
        $data = array(
            'ref_no' => $db->qstr('T'.$details['encounter_nr']),
            'source' => $db->qstr($refSource),
            'item_code' => $db->qstr($itemCode),
            'hcare_id' => $db->qstr($insuranceNo),
            'coverage' => $totalAmount,
        );

        if (empty($priority)) {
            // fetch latest priority if needed
            $priority = $db->GetOne(
                "SELECT IFNULL(priority,0)+1 FROM seg_applied_coverage\n".
                    "WHERE ref_no=%s AND source=%s\n".
                    "AND hcare_id=%s AND item_code=%s",
                $data['ref_no'],
                $data['source'],
                $data['hcare_id'],
                $data['item_code']
            );
        }
        $data['priority'] = $db->qstr($priority);
        
        $ok = $db->Replace('seg_applied_coverage', 
            $data,
            array('ref_no', 'source', 'item_code', 'hcare_id'),
            $autoQuoute = false
        );

        if ($ok) {
            // Maybe some audit logging here
            $this->setResult(true);
            return true;
        } else {
            throw new Exception('Failed to update coverage details');
        }

    }
    
    /**
     * Removes an item from the coverage list of the specified insurance 
     * provider
     * 
     * @param string $insuranceNo The ID of the insurance provider
     * @param string $refSource  Cost center source of the request
     * @param string $refNo Reference no. of the request
     * @param string $itemCode  Item code of the item to be removed
     * @param int $quantity Number of items to be removed. If the covered 
     * amount is less than the total amount to be removed, the entire
     * row is deleted from the database
     * @return boolean Returns TRUE if the item was successfully removed from 
     * the coverage list
     * @throws Exception
     * 
     * @todo Add audit trail
     */
    public function remove(
        $insuranceNo, 
        $refSource, 
        $refNo, 
        $itemCode, 
        $quantity = null)
    {
        global $db;

        $details = $this->getRequestItemDetails($refSource, $refNo, $itemCode);
        if (is_null($quantity)) {
            $quantity = $details['quantity'];
        }
        $totalAmount = (float)$details['price'] * (float)$quantity;
        $data = array(
            'ref_no' => $db->qstr('T'.$details['encounter_nr']),
            'source' => $db->qstr($refSource),
            'item_code' => $db->qstr($itemCode),
            'hcare_id' => $db->qstr($insuranceNo),
        );
        
        // compare total amount with covered amount
        $query = sprintf(
            "SELECT coverage FROM seg_applied_coverage\n".
                "WHERE ref_no=%s AND source=%s\n".
                "AND hcare_id=%s AND item_code=%s",
            $data['ref_no'],
            $data['source'],
            $data['hcare_id'],
            $data['item_code']
        );
        $this->setQuery($query);
        $coveredAmount = $db->GetOne($query);
        if ($coveredAmount === null || $coveredAmount === false) {
            throw new Exception('Item has no coverage');
        }

        if ($totalAmount < (float)$coveredAmount) {
            // if covered amount is bigger than amount to be removed from 
            // coverage...
            $data['coverage'] = $coveredAmount - $totalAmount;
            $ok = $db->Replace('seg_applied_coverage', 
                $data,
                array('ref_no', 'source', 'item_code', 'hcare_id'),
                $autoQuoute = false
            );

            if ($ok !== 0) {
                
                // some audit logging goes here...
                // ...

                $this->setResult(true);
                return true;
            } else {
                $this->setResult(false);
                throw new Exception('Failed to update coverage details');
            }

        } else {
            // if smaller...
            $query = sprintf(
                "DELETE FROM seg_applied_coverage\n".
                    "WHERE ref_no=%s AND source=%s\n".
                    "AND hcare_id=%s AND item_code=%s",
                $data['ref_no'],
                $data['source'],
                $data['hcare_id'],
                $data['item_code']
            );
            $this->setQuery($query);
            $result = $db->Execute($query);
            if ($result !== false) {
                $affected = $db->Affected_Rows();
                if ($affected === 0) {
                    throw new Exception('Failed to update coverage details');
                }

                // some audit logging goes here...
                // ...

                return true;
            } else {
                // SQL error
                throw new Exception('SQL error');
            }
        }

    }
}

