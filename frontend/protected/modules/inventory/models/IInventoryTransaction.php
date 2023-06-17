<?php
namespace SegHis\modules\inventory\models;

interface IInventoryTransaction {

    public function getTransactionType();

    public function getReferenceNo();

    public function getTransactionDate();

    public function getSkuId();

    public function getUnitCost();

    public function getPreviousCost();

    public function getMovementQuantity();

    public function getPreviousQuantity();

    public function getPackQuantity();

    public function getPostDate();

}