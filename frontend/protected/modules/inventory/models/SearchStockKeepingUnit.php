<?php
namespace SegHis\modules\inventory\models;

class SearchStockKeepingUnit extends StockKeepingUnit
{

    const LIMIT = 10;

    /**
     * @var \CActiveDataProvider
     */
    public $dataProvider;

    public $pageCount;

    public $itemCount;

    public $filters;

    public $currentPage;

    public function __construct(\CActiveDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->itemCount = intval($dataProvider->getTotalItemCount());
        $this->pageCount = round($this->itemCount / self::LIMIT);
    }


    /**
     * @param $q string item name, or item id
     * @param $areaCode
     * @param $dateOffset
     * @return SearchStockKeepingUnit
     */
    public static function filter($q, $areaCode, $dateOffset)
    {
        $criteria = self::filterCriteria($q, $areaCode, $dateOffset);

        $searchStockKeepingUnit = new SearchStockKeepingUnit(new \CActiveDataProvider(new StockKeepingUnit(), array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => SearchStockKeepingUnit::LIMIT
            )
        )));

        $searchStockKeepingUnit->filters = array(
            'query' => $q,
            'areaCode' => $areaCode,
            'dateOffSet' => $dateOffset
        );

        return $searchStockKeepingUnit;
    }

    /**
     * @param $q
     * @param $areaCode
     * @param $dateOffset
     * @return \CDbCriteria
     */
    public static function filterCriteria($q, $areaCode, $dateOffset)
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array('ledgerEntries', 'unit', 'item');

        if (is_numeric($q)) {
            $criteria->addColumnCondition(array('item_code' => $q));
        } else {
            $criteria->compare('item.artikelname', $q, true);
        }

        $criteria->addColumnCondition(array('area_code' => trim($areaCode)));

        if (strtotime($dateOffset) != 0 && strtotime($dateOffset) != false) {
            $criteria->addCondition('ledgerEntries.tr_date <= :dateOffset');
            $criteria->params = \CMap::mergeArray($criteria->params, array(
                ':dateOffset' => date('Y-m-d', strtotime($dateOffset))
            ));
        }

        $criteria->order = 'artikelname,order_no';
        $criteria->together = true;
        $criteria->group = 't.sku_id';
        return $criteria;
    }

    public function stats()
    {
        return array(
            'itemCount' => $this->itemCount,
            'pageCount' => $this->pageCount,
            'currentPage' => $this->currentPage,
            'filters' => $this->filters,
        );
    }

    /**
     * @param int $page
     * @return StockKeepingUnit[]
     */
    public function getPageData($page = 1)
    {
        if ($page <= 0 || $page > $this->pageCount)
            throw new \InvalidArgumentException('Invalid page number ' . $page);

        $this->currentPage = intval($page);

        $criteria = $this->dataProvider->getCriteria();

        $dataProvider = new \CActiveDataProvider(new StockKeepingUnit(), array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => SearchStockKeepingUnit::LIMIT,
                'currentPage' => $page - 1
            )
        ));

        return $dataProvider->getData();
    }

}