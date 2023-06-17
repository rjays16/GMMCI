<?php
/**
 *
 * TransmittalController.php
 *
 * @author        Alvin Jay Cosare  <ajunecosare15@gmail.com>
 * @author        Christian Joseph Dalisay  <cjsdjoseph098@gmail.com>
 * @author        Gabriel Lagmay  <gablagmay@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

use SegHis\modules\eclaims\models\EclaimsCf4;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\CF4XmlService;
use SegHis\modules\eclaims\services\claims\transmittal\TransmittalService;
use SegHis\modules\eclaims\services\transmittal\TransmittalUploadService;
use SegHis\modules\eclaims\services\transmittal\ReturnService;

Yii::import('eclaims.models.EclaimsTransmittal');
Yii::import('eclaims.models.EclaimsTransmittalDetails');
Yii::import('eclaims.models.EclaimsEncounter');
Yii::import('eclaims.services.ServiceExecutor');
Yii::import('eclaims.models.DocumentType');
Yii::import('eclaims.models.ClaimAttachment');
Yii::import('eclaims.models.ClaimAttachmentForm');
Yii::import('eclaims.models.HospitalConfigForm');
Yii::import('eclaims.models.Claim');
Yii::import('eclaims.models.ClaimStatus');

/**
 *
 */
class TransmittalController extends Controller
{

    // 1 or more - $receipt
    protected $documents;

    // only once - $claim
    protected $document;

    // 1 or more - $documents

    /**
     *
     * @return type
     */
    public function filters()
    {
        return array(
            'accessControl',
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    /**
     *
     * @return type
     */
    public function accessRules()
    {
        return array(
            array(
                'deny',
                'actions' => array('index'),
                'users'   => array('?')
            ),
            array(
                'deny',
                'actions'    => array(
                    'uploadAttachment',
                    'removeAttachment',
                    'generate',
                    'upload'
                ),
                'expression' => '!Yii::app()->user->checkPermission("transmittal_sudomanage")',
            ),
            array(
                'allow',
                'actions' => array('index'),
                'users'   => array('@')
            ),
        );
    }

    /**
     *
     * @param type $action
     * @return type
     */
    public function beforeAction($action)
    {
        $this->breadcrumbs['Transmittals'] = array('transmittal/index');
        return parent::beforeAction($action);
    }

    /**
     *
     */
    public function actionIndex()
    {
        $transmittal = new EclaimsTransmittal('search');
        $transmittal->unsetAttributes();
        if (isset($_GET['EclaimsTransmittal'])) {
            $transmittal->attributes = $_GET['EclaimsTransmittal'];


            /**
             * Added by Anne 05-25-18 to search a transmittal# BY DATE
             */
            $transmittal->attributes = $_GET['EclaimsTransmittal'];
            if (!empty($_GET['EclaimsTransmittal']['transmit_dte'])) {
                $transmittal->transmit_dte = date("Y-m-d", strtotime($_GET['EclaimsTransmittal']['transmit_dte']));
            }
        }

        $this->render('index', array(
            'transmittal' => $transmittal
        ));
    }

    /**
     * Main controller for transmittal
     *
     * @throws CHttpException
     */
    public function actionDetails()
    {
        $request        = new CHttpRequest;
        $transmitNumber = $request->getQuery('id');
        $transmittal    = EclaimsTransmittal::model()->findTransmittal($transmitNumber);

        if (!$transmittal) {
            throw new CHttpException(404, 'Transmittalsnsmittal record not found');
        }
        $this->render('transmit', array(
            'transmittal' => $transmittal,
        ));
    }

    /**
     * Handles file attachments for transmittal
     *
     * @throws CHttpException
     */
    public function actionAttachments()
    {
        $transmittal = EclaimsTransmittal::model()->findByPk($_GET['id']);
        if (!$transmittal) {
            throw new CHttpException(404, 'Transmittal record not found');
        }
        $this->render('transmit', array(
            'transmittal' => $transmittal,
        ));
    }


    /**
     * Present manage attachments UI.
     * @author  Jolly Caralos <jadcaralos@gmail.com>
     */
    public function actionManageAttachments()
    {

        Yii::import('eclaims.models.ClaimAttachmentForm');
        Yii::import('eclaims.models.Claim');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo   = $_GET['encounter_nr'];

        $claim = Claim::model()->getClaimByEncounterNo($encounterNo);

        $details = EclaimsTransmittalDetails::model()->findByAttributes(
            array(
                'transmit_no'  => $transmittalNo,
                'encounter_nr' => $encounterNo,
            )
        );

        if (empty($details)) {
            throw new CHttpException(404, 'Transmittal entry record not found');
        }

        $service = new TransmittalService($details);

        $this->render(
            'manageAttachments',
            array(
                'attachmentForm' => new ClaimAttachmentForm,
                'details'        => $details,
                'claim'          => $claim,
                'service'        => $service,
            )
        );
    }

    /**
     * Uploads the attachments selected in the actionManageAttachments.
     * Uploads directly to HieTracer via REST.
     *
     * @author [author] <[email]>
     */
    public function actionUploadAttachment()
    {
        Yii::import('eclaims.models.ClaimAttachment');
        Yii::import('eclaims.models.ClaimAttachmentForm');
        Yii::import('eclaims.services.ServiceExecutor');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo   = $_GET['encounter_nr'];

        $details = EclaimsTransmittalDetails::model()->findByAttributes(array(
            'transmit_no'  => $transmittalNo,
            'encounter_nr' => $encounterNo
        ));

        $errors = array();

        try {
            $service = new TransmittalUploadService($details);
            $details = $_POST['ClaimAttachmentForm'];
            $service->uploadTransmittal($details);
            $data = $service->data;
        } catch (CException $e) {
            Yii::app()->user->setFlash(
                'error',
                '<b>Error in CF4!  </b>' . $e->getMessage()
            );
            $data[] = array(
                'type'  => 'plain/pdf',
                'size'  => 1000,
                'error' => "Error in File Upload",
            );
        }
        echo CJSON::encode($data);
    }


    public function actionRenderCF4Modal()
    {

        $encounter = EclaimsEncounter::model()->findByPk($_POST['id']);
        $service   = new CF4XmlService($encounter, $_POST['transmittalNo']);
        $xml       = $service->createDocument();
        $cf4       = new CF4Service();
        $transNo   = $_POST['transmittalNo'];
        $cf4->saveCF4Xml($xml, $encounter->encounter_nr, $transNo);
        $transNo = CF4Service::getpHciTransNo($encounter->encounter_nr);
        $model   = EclaimsCf4::model()->findByPk($transNo);
        try {
            $form = $this->renderPartial('cf4/cf4', array(
                'model' => $model
            ), true);

            echo CJSON::encode(
                array(
                    'form' => $form,
                )
            );
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }


    }

    public function actionUploadCf4($id)
    {
        $model = new EclaimsCf4;
        $cf4   = $model->findByPk($id);

        try {
            $service = new CF4UploadService($cf4);
            $service->uploadXml();

            echo CJSON::encode(
                array('success' => true)
            );

        } catch (Exception $e) {


            echo CJSON::encode(
                array('success' => false, 'message' => $e->getMessage())
            );
        }
    }

    public function actionDownloadCF4($id)
    {
        $model = new EclaimsCf4;
        $cf4   = $model->findByPk($id);

        $filename = 'CF4' . '-' . $cf4->phic_trans_no;
        header('Content-disposition: attachment; filename=' . $filename . '.xml');
        header('Content-type: "text/xml"; charset="utf8"');
        echo $cf4->xml;
        exit();

    }


    public function actionReupload()
    {
        $transmittalNo = $_GET['transmit_no'];
        $encounterNo   = $_GET['encounter_nr'];


        $details = EclaimsTransmittalDetails::model()->findByAttributes(
            array(
                'transmit_no'  => $transmittalNo,
                'encounter_nr' => $encounterNo,
            ));

        $data = array();
        if (isset($_POST['ClaimAttachmentForm'])) {
            try {
                $service = new TransmittalUploadService($details);
                $details = $_POST['ClaimAttachmentForm']
                    + array('is_return' => 0, 'is_uploaded' => 1);
                $service->uploadTransmittal($details);
                $data = $service->data;
            } catch (CException $e) {

                Yii::app()->user->setFlash(
                    'error',
                    '<b>Error in CF4!  </b>' . $e->getMessage()
                );
                $data[] = array(
                    'type'  => 'plain/pdf',
                    'size'  => 1000,
                    'error' => "Error in File Upload",
                );
            }
        }

        echo CJSON::encode($data);

    }

    public function actionAddDocument()
    {
        $trasmittal = $_POST['transmit'];

        $encounter = $_POST['encounter'];

        $model = new ClaimAttachment();

        $xmlDoc = new DOMDocument();

        $claim = Claim::model()->findByAttributes(
            array(
                'transmit_no'  => $trasmittal,
                'encounter_nr' => $encounter,
            )
        );
        try {
            /* The specific service is tried to be executed */
            $service = new ReturnService($claim);
            $service->getUploadedFiles();
            $service->addReturnedDocument();
            Yii::app()->user->setFlash(
                'success',
                '<b>Great!</b> The attachment was successfully added and Claim status successfully updated!'
            );
            echo CJSON::encode(true);
        } catch (Exception $e) {
            /* If an exception is raised, the message is extracted from that exception. */
            Yii::app()->user->setFlash(
                'error',
                '<b>Error!</b>' . $e->getMessage()
            );
        }

    }

    protected function appendNode(&$parent, &$child, $name, $attrs = array())
    {

        $child = $parent->appendChild(new DOMElement($name));
        foreach ($attrs as $akey => $attr) {
            $child->setAttribute($akey, $attr);
        }
    }


    public function actionGetDocumentTypes()
    {
        $model = new DocumentType();

        $data = $model->active()->findAll();

        $filetypes = array();

        foreach ($data as $value) {
            $filetypes[] = $value['id'];
        }

        echo CJSON::encode($filetypes);
    }


    /**
     * Removes the uploaded attachment saved in the HieTracer via REST.
     */
    public function actionRemoveAttachment()
    {
        Yii::import('eclaims.models.ClaimAttachment');
        Yii::import('eclaims.services.ServiceExecutor');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo = $_GET['encounter_nr'];

        $id = $_POST['id'];
        $attachment = ClaimAttachment::model()->findByAttributes(
            array(
                'id' => $id,
                'transmit_no' => $transmittalNo,
                'encounter_nr' => $encounterNo,
            )
        );
        if (empty($attachment)) {
            Yii::app()->user->setFlash(
                'error',
                '<b>Oops!</b> That attachment apparently does not exist'
            );
        }


        $service = new ServiceExecutor(
            array(
                'endpoint' => 'hie/document/remove',
                'method' => 'GET',
                'params' => array(
                    'file' => $attachment->file_id,
                ),
            )
        );
        try {
            $transmittal = EclaimsTransmittal::model()->findByPk($transmittalNo);

            if ($transmittal->ext->is_uploaded && !$attachment->is_uploaded) {
                Yii::app()->user->setFlash(
                    'error',
                    '<b>Oops!</b> Transmittal from this attachment is Uploaded to PHiC'
                );
            } else {
                if (empty($attachment->cloud_storage_filename)) {
                    $result = $service->execute();
                    if ($result['success']) {
                        $attachment->delete();
                        Yii::app()->user->setFlash(
                            'success',
                            '<b>Great!</b> The attachment was successfully removed from our servers!'
                        );
                    } else {
                        Yii::app()->user->setFlash(
                            'error',
                            '<b>Oops!</b> Somehow, we were not able to remove the selected attachmenta at this time. Please try again!'
                        );
                    }
                } else {
                    $attachment->delete();
                    Yii::app()->user->setFlash(
                        'success',
                        '<b>Great!</b> The attachment was successfully removed from our servers!'
                    );
                }
            }
        } catch (ServiceCallException $e) {
            if ($e->statusCode === 410) {
                $attachment->delete();
                Yii::app()->user->setFlash(
                    'success',
                    '<b>Great!</b> The attachment was successfully removed from our servers!'
                );
            } else {
                Yii::app()->user->setFlash(
                    'error',
                    '<b>Service call error:</b> '.$e->getMessage()
                );
            }
        }

        $this->redirect(
            array(
                'manageAttachments',
                'transmit_no' => $transmittalNo,
                'encounter_nr' => $encounterNo,
            )
        );
    }

    /**
     * [_finalizeErrors description]
     * @param  [type] $errors [description]
     * @return [type]         [description]
     */
    private function _finalizeErrors($errors)
    {

        // Finalize errors entries to be more human readable
        $finalized = array();

        Yii::import('eclaims.models.EclaimsEncounter');
        $count = 0;
        foreach ($errors as $id => $error) {
            $encounter = EclaimsEncounter::model()->findByPk($id);

            // Set patient name as the header
            $finalized[$encounter->person->getFullName()] = $error;
            $count                                        += sizeof($error);
        }

        return array(
            'errors' => $finalized,
            'count'  => $count
        );
    }

    public function actionResetXml()
    {
        $transmittal                    = EclaimsTransmittal::model()->findByPk($_GET['id']);
        $transmittal->ext->xml_cache    = null;
        $transmittal->ext->is_valid_xml = 0;
        if (!$transmittal->save()) {
            throw new CHttpException(500, 'Something went wrong');
        }
    }

    /**
     *
     *
     */
    public function actionGenerate()
    {

        $transmittal = EclaimsTransmittal::model()->findByPk($_GET['id']);
        if (empty($transmittal)) {
            throw new CHttpException(404, 'Transmittal not found');
        }

        if (!$transmittal->isValidAttachments()) {
            Yii::app()->user->setFlash('warning',
                'Some of the claims in this transmittal do not have attachments. You can go to ' .
                CHtml::link('<b>attachments</b>', array(
                    'attachments',
                    'id' => $transmittal->transmit_no,
                )) .
                ' to review this...'
            );
        }

        if (isset($_POST['EclaimsTransmittalExt'])) {
            $transmittal->ext->xml_cache = $_POST['EclaimsTransmittalExt']['xml_cache'];
        }


        $errors = array(
            'errors' => array(),
            'count'  => 0
        );
        if ($transmittal->ext->xml_cache) {
            $result = EclaimsTransmittal::validateXml($transmittal->ext->xml_cache);
            if ($result !== true || is_array($result)) {
                if (isset($result['code'])) {
                    if ($result['code'] == 500) {
                        Yii::app()->user->setFlash('error', "Something went wrong. {$result['reason']}");
                    }
                } else {
                    $errors = $this->_finalizeErrors($result);
                }
                $transmittal->ext->is_valid_xml = 0;
            } else {
                $transmittal->ext->is_valid_xml = 1;
            }
        } else {
            $transmittal->ext->is_valid_xml = 0;
        }

        if (isset($_POST['EclaimsTransmittalExt'])) {
            if ($transmittal->save()) {
                Yii::app()->user->setFlash('success', 'Transmittal XML sucessfully saved!');
            } else {
                Yii::app()->user->setFlash('error', 'Something went wrong. Your changes were not saved!');
            }
        }

        $this->render('transmit', array(
            'transmittal' => $transmittal,
            'errors'      => $errors
        ));
    }

    /**
     * Action to generate a transmittal xml and saves it to the database
     *
     */
    public function actionGenerateXml()
    {
        $transmittal = EclaimsTransmittal::model()->findByPk($_REQUEST['id']);
        if (empty($transmittal)) {
            throw new CHttpException(404, 'Transmittal not found');
        }
        $result = $transmittal->generateXml();
        if ($result) {
            $errors = $transmittal->getValidationErrors();
            echo CJSON::encode(CMap::mergeArray(
                array('content' => $result),
                $this->_finalizeErrors($errors)
            ));
        } else {
            echo CJSON::encode(false);
        }
    }

    /**
     * Controller action
     * @return [type] [description]
     */
    public function actionValidateXml()
    {
        $xml = Yii::app()->getRequest()->getPost('xml');
        if (!$xml) {
            throw new CHttpException(400, 'XML is empty!');
        }
        $result = EclaimsTransmittal::validateXml($xml);
        if ($result !== true) {
            echo CJSON::encode($this->_finalizeErrors($result));
        } else {
            echo CJSON::encode(array(
                'errors' => array(),
                'count'  => 0
            ));
        }
    }

    /**
     * Action to upload the transmittal data(XML) to the HIE web service
     */
    public function actionUpload()
    {
        $transmittal = EclaimsTransmittal::model()->findByPk($_REQUEST['id']);
        if (empty($transmittal)) {
            throw new CHttpException(404, 'Transmittal not found');
        }

        $errors = array();
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'upload':
                    $data = $transmittal->compactUpload($transmittal);
                    /* Instantiates the claimsUpload service */
                    $service = new ServiceExecutor(
                        array(
                            'endpoint' => 'hie/transmittal/upload',
                            'method'   => 'POST',
                            'data'     => $data
                        )
                    );
                    try {

                        /* The specific service is tried to be executed */

                        $result = $service->execute();
                        if (!$result['success']) {
                            $errors[] = $result['message'];
                        }
                        /**
                         * Extracts the results generated by the service,
                         * saves the necessary data based on transmittal number to the database.
                         */
                        $transmittal->extractUploadResult($result["data"]);


                        if (!$transmittal->save()) {
                            $errors[] = 'Saving of upload data failed';
                        }


                    } catch (ServiceCallException $e) {


                        /**
                         * If an exception is raised, the data is extracted from that exception.
                         * If data does not exists, the message is extracted from that exception.
                         */

                        $result   = $e->getData();
                        $errors[] = $result['error']['reason'] ? $result['error']['reason'] : $result;
//                        if (!empty($result['eRECEIPT'])) {
//                            $remarks = EclaimsTransmittal::extractUploadRemarks($result);
//                            foreach ($remarks as $remark) {
//                                if (!empty($remark['code']) && !empty($remark['description'])) {
//                                    $errors[] = '<b>Error Code ' . $remark['code'] . '</b> :' . $remark['description'];
//                                }
//                            }
//                        } else {
//                            // $result = ucwords($e->getMessage() ." ".@$result['reason']). $e->statusCode;
//                            $errors[] = $e->getReason() ? $e->getReason() : $e->getMessage();
//                        }
                    }


                case 'map':
                    if (empty($errors)) {
                        // Proceed with mapping
                    }
                    break;
            }
        }

        $this->render('transmit', array(
            'transmittal' => $transmittal,
            'errors'      => $errors
        ));
    }

    /**
     * NOT USED
     * @return [type]
     */
    public function actionUploadEx()
    {
        /*The transmittal model is generated by its transmittal number received from the form */
        $transmittal = (isset($_POST['transmit_no'])) ?
            Transmittal::model()->findbyPK($_POST['transmit_no']) :
            Transmittal::model()->findbyPK($_GET['transmit_no']);

        /* Set-ups the necessary data needed for the service to be executed */
        $data = $transmittal->compactUpload();
        /* Instantiates the claimsUpload service */
        $service = new ServiceExecutor(
            array(
                'endpoint' => 'hie/transmittal/upload',
                'method'   => 'POST',
                'data'     => $data
            )
        );
        try {
            /* The specific service is tried to be executed */
            $result = $service->execute();
            /**
             * Extracts the results generated by the service,
             * saves the necessary data based on transmittal number to the database.
             */
            CVarDumper::dump($result, 10, true);
            die();
            $transmittal->extractUploadResult($result["data"]);


        } catch (ServiceCallException $e) {
            /**
             * If an exception is raised, the data is extracted from that exception.
             * If data does not exists, the message is extracted from that exception.
             */
            $result = $e->getData();
            if (!empty($result)) {
                $result = Transmittal::extractRemarks($result);
                $result = (empty($result)) ?
                    ucwords($e->getMessage() . " " . @$result['reason']) . $e->statusCode :
                    $result;
            } else {
                $result = ucwords($e->getMessage() . " " . @$result['reason']) . $e->statusCode;
            }
            if (is_array($result)) {
                $this->renderPartial('error', array('uploadErrors' => $result));
            } else {
                echo CJSON::encode($result);
            }
        }
        /* The result received by using the service call is being passed on to. */
    }

    /**
     * Retrieves the details of a transmittal and displays it in a
     * grid view.
     * @return mixed String content or JSON data:
     * array(
     *     'content', 'urlOpenTransmittal'
     * );
     */
    public function actionViewDetails()
    {
        $this->layout = 'eclaims.views.layouts.transmittalDetail';

        $request                 = Yii::app()->getRequest();
        $clientPreferredDataType = $request->getPreferredAcceptType();

        $result         = array();
        $transmitNumber = $_GET['transmit_no'];

        $transmittal = EclaimsTransmittal::model()->findByPk($transmitNumber);

        /* Prepare TransmittalDetails */
        $details = array();
        foreach ($transmittal->details as $detail) {
            $details[] = $detail->toArray();
        }

        if ($clientPreferredDataType['subType'] == 'json') {
            echo CJSON::encode(array(
                'content'            => $this->renderPartial('transmittalDetail', array('details' => $details), true),
                'urlOpenTransmittal' => $this->createUrl('details', array('id' => $transmitNumber)),
                'transmittalNumber'  => $transmitNumber,
            ));
        } else {
            $this->render('transmittalDetail', array('details' => $details));
        }
    }

    /**
     * Action to get the saved details from the responses of upload and map PHIC services.
     * @return mixed string content or JSON
     */
    public function actionResponseDetails()
    {
        $request                 = Yii::app()->getRequest();
        $clientPreferredDataType = $request->getPreferredAcceptType();

        /* The array containing the upload and map details is setted up. */
        $result = array("upload" => array(), "map" => array());
        /* The transmittal number is received from the form. */
        $transmitNumber = $_GET['transmit_no'];


        /* Gets the details from the upload response and converts to an array. */
        $transmittals = EclaimsTransmittal::model()->findAll(array(
            'condition' => 'transmit_no=:transmitNumber',
            'params'    => array(':transmitNumber' => $transmitNumber)
        ));
        foreach ($transmittals as $transmittal) {
            $result["upload"][] = $transmittal->toResponseArray();

            if ($transmittal->ext->is_mapped == 1 && $transmittal->ext->is_uploaded == 1) {
                /* Gets the details from the map response and converts to an array. */
                $details = EclaimsTransmittalDetails::model()->findAll(array(
                    'condition' => 'transmit_no=:transmitNumber',
                    'params'    => array(':transmitNumber' => $transmitNumber)
                ));
                foreach ($details as $detail) {
                    $result["map"][] = $detail->toResponseArray();
                }
            }
        }

        /* The details retrieved will be displayed on the responseDetail view. */
        $content = $this->renderPartial('/transmittal/responsedetail', array('details' => $result), true, true);

        if ($clientPreferredDataType['subType'] == 'json') {
            // echo  CJSON::encode(array('content' => $content));
            echo CJSON::encode(array(
                'content'                    => $content,
                'urlOpenTransmittalResponse' => $transmitNumber
            ));
        } else {
            echo $content;
        }
        Yii::app()->end();
    }

    /**
     * Renders the Show XML page
     *
     */
    public function actionShowXml()
    {
        $transmittal = EclaimsTransmittal::model()->findByPk($_GET['transmit_no']);
        $this->render('showXML', array('transmittal' => $transmittal), false, true);
    }

    /**
     * Action to either validate or save the transmittal xml based on the process passed
     *
     */
    public function actionProcessXml()
    {
        $transmittal = Transmittal::model()->findByPk($_POST['transmit_no']);
        // $size = count($transmittal->details); //TODO

        if ($_POST['process'] == 'save') {
            $result = $transmittal->processValidateXml($_POST['xmlString']);
            if (!is_array($result) && $result) {
                $result = $transmittal->processSaveXml($_POST['xmlString']);
            }
        } else {
            $result = $transmittal->processValidateXml($_POST['xmlString']);
        }

        echo CJSON::encode($result);
        // $result = ((!$result) ? 'invalid' : $result);
    }

    /**
     * Action to validate the transmittal xml  of a specific transmittal against eClaimsDef_1.7.3.dtd
     */
    public function actionSaveXml()
    {
        $transmittal = Transmittal::model()->findByPk($_POST['transmit_no']);
        $xmlString   = ((isset($_POST['xmlString'])) ? $_POST['xmlString'] : '');
        $result      = $transmittal->processSaveXml($_POST['transmit_no'], $xmlString);
        echo CJSON::encode($result);
    }


    /**
     *Action to map a transmittal to the HIE web service
     *
     * @return boolean result - success/failure of saving the data to the database
     * @return string result - message raised from the exception
     * @throws ServiceCallException
     */
    public function actionMap()
    {
        /*The transmittal model is generated by its transmittal number received from the form */
        $transmittal = (isset($_POST['transmit_no'])) ?
            EclaimsTransmittal::model()->findbyPK($_POST['transmit_no']) :
            EclaimsTransmittal::model()->findbyPK($_GET['transmit_no']);

        /* Set-ups the necessary params needed for the service to be executed */
        $params = EclaimsTransmittal::compactMap($transmittal);
        /* Instantiates the claimsMap service */


        $service     = new ServiceExecutor(
            array(
                'endpoint' => 'hie/transmittal/claimsMap',
                'method'   => 'GET',
                'params'   => $params
            )
        );
        $call_status = false;
        try {
            /* The specific service is tried to be executed */
            $result = $service->execute();

            /**
             * Extracts the results generated by the service,
             * saves the necessary data based on transmittal number to the database.
             */
            $result      = $transmittal->extractMapResult($transmittal->transmit_no, $result);
            $call_status = true;
        } catch (ServiceCallException $e) {
            /* If an exception is raised, the message is extracted from that exception. */
            $result = ucwords($e->getMessage() . " " . @$result['reason']) . $e->statusCode;
        }
        /*Auto check status of all claims under this transmittal number after map. */
        if ($call_status) {
            $this->autoCheckStatus($transmittal->transmit_no);
        }
        /* The result received by using the service call is being passed on to. */
        echo CJSON::encode($result);
    }

    /**
     *
     */
    public function actionGenerateTagSchema()
    {
        $dtd = file_get_contents(Yii::getPathOfAlias('eclaims.views.transmittal') . '/eClaimsDef.dtd');

        preg_match_all("/<!ELEMENT (\w+) (.*)>|<!ATTLIST (\w+)\s+(.*)>/siU", $dtd, $matches);

        $schema = array();

        foreach ($matches[0] as $i => $rule) {

            if (strpos($rule, 'ELEMENT') !== false) {
                $element = $matches[1][$i];

                preg_match_all("/\w+/", $matches[2][$i], $children);

                $schema[$element] = array(
                    'children' => $children[0]
                );
            }

            if (strpos($rule, 'ATTLIST') !== false) {
                $element = $matches[3][$i];
                preg_match_all("/(\w+) (.*) #REQUIRED/", $matches[4][$i], $attrs);

                $attributes = array();
                foreach ($attrs[0] as $i => $attr) {
                    $name = $attrs[1][$i];
                    $type = $attrs[2][$i];
                    if ($type == 'CDATA') {
                        $attributes[$name] = null;
                    } else {
                        preg_match_all("/\w+/", $type, $values);
                        $attributes[$name] = $values[0];
                    }
                }
                $schema[$element]['attrs'] = $attributes;
            }
        }

        echo CJSON::encode($schema);
    }

    public function getClaimStatusReturn($encounter_nr)
    {

        $model    = new Claim;
        $criteria = new CDbCriteria();

        $criteria->with = array(
            'status',
            'status.return' => array('joinType' => 'INNER JOIN'),
        );

        $criteria->params = array(
            ':encounter_nr' => $encounter_nr,
        );


        $criteria->condition = 't.encounter_nr = :encounter_nr';

        $data = $model->find($criteria);

        if (!empty($data->id)) {
            return true;
        }

        return false;

    }

    public function autoCheckStatus($transmittal_no)
    {
        $claimStatus = new ClaimStatus();
        $claim_list  = Claim::model()->findAllByAttributes(array('transmit_no' => $transmittal_no));
        foreach ($claim_list as $key => $claim) {
            $data = $claim->compactClaimStatus();

            $service = new ServiceExecutor(array(
                'endpoint' => 'hie/claim/status',
                'params'   => $data
            ));
            try {
                $result = $service->execute();
                $claimStatus::extractResult($result['data']);
                Yii::app()->user->setFlash('info', '<b>Success!</b> Claim status successfully updated!');

            } catch (ServiceCallException $e) {
                $result = $e->getData();
                Yii::app()->user->setFlash('error', '<strong>' . $e->getMessage() . ':</strong> ' . @$result['reason'] . ' <small> (Code:' . $e->statusCode . ')</small>');
            }
        }
        return true;
    }

    public function getReturnCounts($enc)
    {
        $connection = Yii::app()->db;
        $command    = $connection->createCommand(
            'SELECT 
                                                  IF(r.`id` IS NULL ,0,1) AS cnt
                                                FROM
                                                  `seg_eclaims_claim` AS e 
                                                  LEFT JOIN `seg_eclaims_claim_status` AS s 
                                                    ON s.`claim_id` = e.`id` 
                                                  LEFT JOIN `seg_eclaims_return_claim_status` AS r 
                                                    ON r.`status_id` = s.`id` 
                                                WHERE e.`encounter_nr` ='
            . $enc
        );
        $result     = $command->queryRow();
        if ($result['cnt'] == 1) {
            return true;
        }
        return false;
    }
}
