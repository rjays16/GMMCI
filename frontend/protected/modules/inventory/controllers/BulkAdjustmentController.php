<?php

use SegHis\models\inventory\Unit;
use SegHis\modules\inventory\models\InventoryExporter;
use SegHis\modules\inventory\models\InventoryImporter;
use SegHis\modules\inventory\models\StockKeepingUnit;

class BulkAdjustmentController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout='//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array();
    }

    /**
     *
     */
    public function actionIndex()
    {
        $exporter = new InventoryExporter();
        $importer = new InventoryImporter();

        $exporterName = CHtml::modelName($exporter);
        if (isset($_POST[$exporterName])) {

            $exporter->setAttributes($_POST[$exporterName]);
            if ($exporter->validate()) {

                $excel = $exporter->exportToXls();
                $this->renderPartial('downloadTemplate', array(
                    'excel' => $excel
                ), false, false);
                Yii::app()->end();
            }
        }


        $this->render('index', array(
            'exporter' => $exporter,
            'importer' => $importer,
        ));
    }

    /**
     *
     */
    public function actionReviewImport()
    {
       
        $importer = new InventoryImporter();
        $importerName = CHtml::modelName($importer);

        if (!isset($_POST[$importerName])) {
            $this->redirect(array('index'));
        }

        $importer->setAttributes($_POST[$importerName]);

        if ($importer->validate()) {
            $file = \CUploadedFile::getInstance($importer, 'import_file');
            $data = $importer->importFromXlsFile($file->getTempName());

            $this->render('reviewImport', array(
                'data' => $data ?: array(),
                'importer' => $importer
            ));
            Yii::app()->end();
        } else {

            Yii::app()->user->setFlash('error', '<b>Error encountered while importing!</b> <br> Please fix the following: <br><ul>' .
                array_reduce($importer->getErrors(), function($carry, $item) {
                    return $carry.'<li>' . implode('</li><li>', $item) . '</li>';
                }, '') .
                '</ul>'
            );

            // pass control back to the index view to show form errors
            $this->redirect(array('index'));
//            $this->render('index', array(
//                'importer' => $importer
//            ));
        }
    }

    /**
     *
     */
    public function actionImport()
    {
        $importer = new InventoryImporter();
        $importerName = CHtml::modelName($importer);
        
        $import_date = $_GET['date'];
        $import_time = $_GET['time'];
        
        $import_date = $import_date." ".$import_time; //date and time
        $import_date = strtotime($import_date);
        
        if (!isset($_POST[$importerName])) {
            $this->redirect(array('index'));
        }
        // echo "<pre>";
        // print_r(array($_POST, $_POST[$importerName])); die();

        $importer->setAttributes($_POST[$importerName]);

        $transaction = Yii::app()->getDb()->beginTransaction();

        try {
            $importer->import($import_date);
            $transaction->commit();
            Yii::app()->user->setFlash('success', '<b>Congratulations</b> Inventory data successfully imported!');
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::app()->user->setFlash('error', '<b>Import error</b>: ' . $e->getMessage());
        }

        $this->redirect(array('index'));
    }
}