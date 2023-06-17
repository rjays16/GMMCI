<?php
/**
 *
 * TransmittalController.php
 *
 * @author  Alvin Jay Cosare  <ajunecosare15@gmail.com>
 * @author  Christian Joseph Dalisay  <cjsdjoseph098@gmail.com>
 * @author  Gabriel Lagmay  <gablagmay@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.EclaimsTransmittal');
Yii::import('eclaims.models.EclaimsTransmittalDetails');
Yii::import('eclaims.services.ServiceExecutor');

/**
 *
 */
class TransmittalController extends Controller {

    /**
     *
     * @return type
     */
    public function filters(){
        return array('accessControl',
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    /**
     *
     * @return type
     */
    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('index'),
                'users' => array('?')
            ),
            array(
                'deny',
                'expression' => '!Yii::app()->user->checkPermission("eclaims")',
            ),
            array(
                'deny',
                'actions' => array(
                    'uploadAttachment', 'removeAttachment', 'generate', 'upload'
                ),
                'expression' => '!Yii::app()->user->checkPermission("transmittal_sudomanage")',
            ),
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@')
            ),
        );
    }

    /**
     *
     * @param type $action
     * @return type
     */
    public function beforeAction($action) {
        $this->viewPath = 'eclaims.views.transmittal';
        $this->breadcrumbs['Transmittals'] = array('transmittal/index');
        return parent::beforeAction($action);
    }

    /**
     *
     */
    public function actionIndex() {
        $transmittal = new EclaimsTransmittal('search');
        $transmittal->unsetAttributes();
        if(isset($_GET['EclaimsTransmittal'])) {
            $transmittal->attributes=$_GET['EclaimsTransmittal'];
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
    public function actionDetails() {
        $request = new CHttpRequest;
        $transmitNumber = $request->getQuery('id');
        $transmittal = EclaimsTransmittal::model()->findTransmittal($transmitNumber);
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
    public function actionAttachments() {
        $transmittal = EclaimsTransmittal::model()->findByPk($_GET['id']);
        if (!$transmittal) {
            throw new CHttpException(404, 'Transmittal record not found');
        }
        $this->render('transmit', array(
            'transmittal' => $transmittal,
        ));
    }

    /**
     * Uploads the attachments selected in the actionManageAttachments.
     * Uploads directly to HieTracer via REST.
     *
     * @author [author] <[email]>
     */
    public function actionUploadAttachment() {
        Yii::import('eclaims.models.ClaimAttachment');
        Yii::import('eclaims.models.ClaimAttachmentForm');
        Yii::import('eclaims.services.ServiceExecutor');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo = $_GET['encounter_nr'];

        $details = EclaimsTransmittalDetails::model()->findByAttributes(array(
            'transmit_no' => $transmittalNo,
            'encounter_nr' => $encounterNo
        ));

        $errors = array();

        if (isset($_POST['ClaimAttachmentForm'])) {
            $data = array();
            foreach ($_POST['ClaimAttachmentForm']['type'] as $key => $type) {
                $model = new ClaimAttachmentForm;
                $model->type = $_POST['ClaimAttachmentForm']['type'][$key];
                $model->attachment = CUploadedFile::getInstance($model, "attachment[{$key}]");
                // skip if empty(no associated file in $_FILES)
                if(!$model->attachment) {
                    continue;
                }

                $model->transmit_no = $details->transmit_no;
                $model->encounter_nr = $details->encounter_nr;

                $service = new ServiceExecutor(
                    array(
                        'endpoint'=>'hie/document/upload',
                        'method' => 'POST',
                        'data'=> $model->getUploadParams()
                    )
                );
                try {
                    /* The specific service is tried to be executed */
                    $result = $service->execute();
                    /**
                     * Extracts the results generated by the service,
                     * saves the necessary data based on transmittal number to the database.
                     */
                    if ($result['success']) {
                        $attachment = new ClaimAttachment;
                        $attachment->extractUploadResult($result['data']);
                        $attachment->transmit_no = $details->transmit_no;
                        $attachment->encounter_nr = $details->encounter_nr;
                        $attachment->attachment_type = $model->type;

                        if (!$attachment->save()) {
                            $errors[] = 'Unable to save attachment <b>' . $attachment->filename . '</br>';
                        }

                    }
                    $data[] = array(
                        'name' => $attachment->filename,
                        'type' => 'plain/pdf',
                        'size' => 1000,
                        'attachment_type' => $attachment->getAttachmentType()
                    );
                } catch (ServiceCallException $e) {
                    /* If an exception is raised, the message is extracted from that exception. */
                    $errors[] = '<b>Service call error</b>: ' . $e->getMessage();
                }

                if($model->attachment) {
                    break;
                }
            }
        }

        // if ($errors) {
        //     Yii::app()->user->setFlash('error', '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>');
        // } else {
        //     Yii::app()->user->setFlash('success', 'Claim attachment/s succesfully uploaded to the server');
        // }

        // $this->redirect(array('manageAttachments',
        //     'transmit_no' => $details->transmit_no,
        //     'encounter_nr' => $details->encounter_nr
        // ));
        echo CJSON::encode($data);
    }

    /**
     * Removes the uploaded attachment saved in the HieTracer via REST.
     */
    public function actionRemoveAttachment() {
        Yii::import('eclaims.models.ClaimAttachment');
        Yii::import('eclaims.services.ServiceExecutor');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo = $_GET['encounter_nr'];

        $id = $_POST['id'];
        $attachment = ClaimAttachment::model()->findByAttributes(array(
            'id' => $id,
            'transmit_no' => $transmittalNo,
            'encounter_nr' => $encounterNo
        ));
        if (empty($attachment)) {
            Yii::app()->user->setFlash('error', '<b>Oops!</b> That attachment apparently does not exist');
        }

        $service = new ServiceExecutor(
            array(
                'endpoint'=>'hie/document/remove',
                'method' => 'GET',
                'params'=> array(
                    'file' => $attachment->file_id
                )
            )
        );
        try {
            $result = $service->execute();
            if ($result['success']) {
                $attachment->delete();
                Yii::app()->user->setFlash('success', '<b>Great!</b> The attachment was successfully removed from our servers!');
            } else {
                Yii::app()->user->setFlash('error', '<b>Oops!</b> Somehow, we were not able to remove the selected attachmenta at this time. Please try again!');
            }
        } catch (ServiceCallException $e) {
            if ($e->statusCode === 410) {
                $attachment->delete();
                Yii::app()->user->setFlash('success', '<b>Great!</b> The attachment was successfully removed from our servers!');
            } else {
                Yii::app()->user->setFlash('error', '<b>Service call error:</b> ' . $e->getMessage());
            }
        }

        $this->redirect(array('manageAttachments',
            'transmit_no' => $transmittalNo,
            'encounter_nr' => $encounterNo
        ));
    }

    /**
     * [_finalizeErrors description]
     * @param  [type] $errors [description]
     * @return [type]         [description]
     */
    private function _finalizeErrors($errors) {

        // Finalize errors entries to be more human readable
        $finalized = array();

        Yii::import('eclaims.models.EclaimsEncounter');
        $count = 0;
        foreach ($errors as $id => $error) {
            $encounter = EclaimsEncounter::model()->findByPk($id);

            // Set patient name as the header
            $finalized[$encounter->person->getFullName()] = $error;
            $count += sizeof($error);
        }

        return array(
            'errors' => $finalized,
            'count' => $count
        );
    }

    /**
     *
     *
     */
    public function actionGenerate() {
        $transmittal = EclaimsTransmittal::model()->findByPk($_GET['id']);

        if (empty($transmittal)) {
            throw new CHttpException(404, 'Transmittal not found');
        }

        if (!$transmittal->isValidAttachments()) {
            Yii::app()->user->setFlash('warning', 'Some of the claims in this transmittal do not have attachments. You can go to ' .
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
            'count' => 0
        );
        if ($transmittal->ext->xml_cache) {
            $result = EclaimsTransmittal::validateXml($transmittal->ext->xml_cache);
            if ($result !== true || is_array($result)) {
                if(isset($result['code'])) {
                    if($result['code'] == 500) {
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
            'errors' => $errors
        ));
    }

    /**
     * Action to generate a transmittal xml and saves it to the database
     *
     */
    public function actionGenerateXml() {
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
    public function actionValidateXml() {
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
                'count' => 0
            ));
        }
    }

    /**
     * Action to upload the transmittal data(XML) to the HIE web service
     */
    public function actionUpload() {
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
                            'endpoint'=>'hie/transmittal/upload',
                            'method' => 'POST',
                            'data'=> $data
                        )
                    );
                     try {
                        /* The specific service is tried to be executed */
                        $result = $service->execute();
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

                        $result = $e->getData();
                        if(!empty($result['eRECEIPT'])) {
                            $remarks = EclaimsTransmittal::extractUploadRemarks($result);
                            foreach ($remarks as $remark) {
                                if(!empty($remark['code']) && !empty($remark['description']))
                                    $errors[] = '<b>Error Code ' . $remark['code'] . '</b> :' . $remark['description'];
                            }
                        } else {
                            // $result = ucwords($e->getMessage() ." ".@$result['reason']). $e->statusCode;
                            $errors[] = $e->getReason() ? $e->getReason() : $e->getMessage();
                        }
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
            'errors' => $errors
        ));
    }

    /**
     * NOT USED
     * @return [type]
     */
    public function actionUploadEx() {
        /*The transmittal model is generated by its transmittal number received from the form */
        $transmittal = (isset($_POST['transmit_no'])) ?
         Transmittal::model()->findbyPK($_POST['transmit_no']) :
         Transmittal::model()->findbyPK($_GET['transmit_no']);

        /* Set-ups the necessary data needed for the service to be executed */
        $data = $transmittal->compactUpload();
        /* Instantiates the claimsUpload service */
        $service = new ServiceExecutor(
            array(
                'endpoint'=>'hie/transmittal/upload',
                'method' => 'POST',
                'data'=> $data
            )
        );
        try {
            /* The specific service is tried to be executed */
            $result = $service->execute();
            /**
             * Extracts the results generated by the service,
             * saves the necessary data based on transmittal number to the database.
             */
            CVarDumper::dump($result, 10, true);die();
            $transmittal->extractUploadResult($result["data"]);


        } catch (ServiceCallException $e) {
            /**
             * If an exception is raised, the data is extracted from that exception.
             * If data does not exists, the message is extracted from that exception.
             */
            $result = $e->getData();
            if(!empty($result)) {
                $result = Transmittal::extractRemarks($result);
                 $result = (empty($result)) ?
                    ucwords($e->getMessage() ." ".@$result['reason']). $e->statusCode :
                    $result ;
            } else {
            $result = ucwords($e->getMessage() ." ".@$result['reason']). $e->statusCode;
            }
            if(is_array($result)) {
                $this->renderPartial('error', array ('uploadErrors' => $result));
            }
            else {
            echo CJSON::encode($result);
            }
        }
        /* The result received by using the service call is being passed on to. */
    }

    /**
     * Retrieves the details of a transmittal and displays it in a
     * grid view
     *
     */
    public function actionViewDetails() {
        $request = Yii::app()->getRequest();
        $clientPreferredDataType = $request->getPreferredAcceptType();

        $result = array();
        $transmitNumber = $_GET['transmit_no'];

        $transmittal = EclaimsTransmittal::model()->findByPk($transmitNumber);

        /* Prepare TransmittalDetails */
        $details = array();
        foreach ($transmittal->details as $detail) {
            $details[] = $detail->toArray();
        }
        
        if($clientPreferredDataType['subType'] == 'json') {
            echo CJSON::encode(array(
                'content' => $this->renderPartial('transmittalDetail', array('details' => $details), true),
                'urlOpenTransmittal' => $this->createUrl('details', array('id' => $transmitNumber))
            ));
        } else {
            $this->render('transmittalDetail', array('details' => $details));
        }
    }

    /**
     * Action to get the saved details from the responses of upload and map PHIC services.
     *
     */
    public function actionResponseDetails() {
        /* The array containing the upload and map details is setted up. */
        $result = array("upload" => array(), "map" => array());
        /* The transmittal number is received from the form. */
        $transmitNumber = $_GET['transmit_no'];


        /* Gets the details from the upload response and converts to an array. */
        $transmittals = EclaimsTransmittal::model()->findAll(array('condition' => 'transmit_no=:transmitNumber', 'params' => array(':transmitNumber' => $transmitNumber)));
        foreach($transmittals as $transmittal){
            $result["upload"][] = $transmittal->toResponseArray();

            if($transmittal->ext->is_mapped == 1 && $transmittal->ext->is_uploaded == 1) {
                /* Gets the details from the map response and converts to an array. */
                $details = EclaimsTransmittalDetails::model()->findAll(array('condition' => 'transmit_no=:transmitNumber', 'params' => array(':transmitNumber' => $transmitNumber)));
                foreach($details as $detail){
                    $result["map"][] = $detail->toResponseArray();
                }
            }
        }
        /* The details retrieved will be displayed on the responseDetail view. */
        $this->renderPartial('responsedetail', array('details' => $result), false, true);
    }

    /**
     * Renders the Show XML page
     *
     */
    public function actionShowXml() {
       $transmittal = EclaimsTransmittal::model()->findByPk($_GET['transmit_no']);
       $this->render('showXML', array('transmittal' => $transmittal), false, true);
    }

    /**
    * Action to either validate or save the transmittal xml based on the process passed
    *
    */
    public function actionProcessXml() {
        $transmittal = Transmittal::model()->findByPk($_POST['transmit_no']);
        // $size = count($transmittal->details); //TODO

        if($_POST['process'] == 'save') {
            $result = $transmittal->processValidateXml($_POST['xmlString']);
            if(!is_array($result) && $result) {
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
    public function actionSaveXml() {
        $transmittal = Transmittal::model()->findByPk($_POST['transmit_no']);
        $xmlString = ((isset($_POST['xmlString'])) ? $_POST['xmlString'] : '');
        $result = $transmittal->processSaveXml($_POST['transmit_no'],$xmlString);
        echo CJSON::encode($result);
    }


    /**
    *Action to map a transmittal to the HIE web service
     *
     * @return boolean result - success/failure of saving the data to the database
     * @return string result - message raised from the exception
     * @throws ServiceCallException
    */
    public function actionMap() {
        /*The transmittal model is generated by its transmittal number received from the form */
        $transmittal = (isset($_POST['transmit_no'])) ?
         EclaimsTransmittal::model()->findbyPK($_POST['transmit_no']) :
         EclaimsTransmittal::model()->findbyPK($_GET['transmit_no']);

        /* Set-ups the necessary params needed for the service to be executed */
        $params = EclaimsTransmittal::compactMap($transmittal);
        /* Instantiates the claimsMap service */
        $service = new ServiceExecutor(
            array(
                'endpoint'=>'hie/transmittal/claimsMap',
                'method' => 'GET',
                'params'=> $params
            )
        );
        try {
            /* The specific service is tried to be executed */
            $result = $service->execute();
            /**
             * Extracts the results generated by the service,
             * saves the necessary data based on transmittal number to the database.
             */
            $result = $transmittal->extractMapResult($transmittal->transmit_no, $result);
        } catch (ServiceCallException $e) {
            /* If an exception is raised, the message is extracted from that exception. */
            $result = ucwords($e->getMessage() ." ".@$result['reason']). $e->statusCode;
        }
        /* The result received by using the service call is being passed on to. */
        echo CJSON::encode($result);
    }

    /**
     *
     */
    public function actionGenerateTagSchema() {
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

    /* ------------------------ FIXER Functions ------------------------- */

    /**
     * Present manage attachments UI.
     * @author  Jolly Caralos <jadcaralos@gmail.com>
     */
    public function actionManageAttachments() {

        Yii::import('eclaims.models.ClaimAttachmentForm');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo = $_GET['encounter_nr'];

        $details = EclaimsTransmittalDetails::model()->findByAttributes(array(
            'transmit_no' => $transmittalNo,
            'encounter_nr' => $encounterNo
        ));

        if (empty($details)) {
            throw new CHttpException(404, 'Transmittal entry record not found');
        }

        $this->viewPath = 'eclaims.views.testTransmittal';
        $this->render('manageAttachments', array(
            'attachmentForm' => new ClaimAttachmentForm,
            'details' => $details
        ));
    }

    /**
     * Fix all attachments in a transmittal and encounter by adding/updating
     * contentType to docMimeType.
     */
    public function actionFixAttachmentsProperty() {
        Yii::import('eclaims.models.ClaimAttachmentForm');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo = $_GET['encounter_nr'];

        $details = EclaimsTransmittalDetails::model()->findByAttributes(array(
            'transmit_no' => $transmittalNo,
            'encounter_nr' => $encounterNo
        ));

        if (empty($details)) {
            throw new CHttpException(404, 'Transmittal entry record not found');
        }

        foreach($details->searchAttachments()->getData() as $attachment) {

            $service = new ServiceExecutor(
                array(
                    'endpoint'=>'hie/document/fixattachment',
                    'params'=> array(
                        '_id' => $attachment->file_id
                    )
                )
            );
            try {
                /* The specific service is tried to be executed */
                $result = $service->execute();
                /**
                 * Extracts the results generated by the service,
                 * saves the necessary data based on transmittal number to the database.
                 */
                if ($result['success']) {
                    $attachment->extractUploadResult($result['data']);

                    if (!$attachment->save()) {
                        $errors[] = 'Unable to save attachment <b>' . $attachment->filename . '</br>';
                    }

                }
            } catch (ServiceCallException $e) {
                /* If an exception is raised, the message is extracted from that exception. */
                $errors[] = '<b>Service call error</b>: ' . $e->getMessage();
            }

        }
        CVarDumper::dump($errors, 10, true);
    }

    public function actionFixAttachments() {
        Yii::import('eclaims.models.ClaimAttachment');
        Yii::import('eclaims.models.ClaimAttachmentForm');
        Yii::import('eclaims.services.ServiceExecutor');

        $transmittalNo = $_GET['transmit_no'];
        $encounterNo = $_GET['encounter_nr'];

        $errors = array();
        if (isset($_POST['ClaimAttachmentForm'])) {
            $data = array();
            
            $fileid = 0;
            $filename = '';
            array_walk($_FILES['ClaimAttachmentForm']['name']['attachment'], function($val, $key) use(&$fileid, &$filename) {
                $fileid = $key;
                $filename = $val;
            });

            /* Find the Attachment */
            $attachment = ClaimAttachment::model()->findByAttributes(array(
                'transmit_no' => $transmittalNo,
                'filename' => $filename,
                'encounter_nr' => $encounterNo
            ));
            
            $model = new ClaimAttachmentForm;
            $model->type = $attachment->attachment_type;
            $model->attachment = CUploadedFile::getInstance($model, "attachment[{$fileid}]");
            $model->transmit_no = $attachment->transmit_no;
            $model->encounter_nr = $attachment->encounter_nr;

            $service = new ServiceExecutor(
                array(
                    'endpoint'=>'hie/testDocument/FixReUpload',
                    'method' => 'POST',
                    'data'=> CMap::mergeArray($model->getUploadParams(), array('_id' => $attachment->file_id))
                )
            );
            try {
                /* The specific service is tried to be executed */
                $result = $service->execute();
                /**
                 * Extracts the results generated by the service,
                 * saves the necessary data based on transmittal number to the database.
                 */
                if ($result['success']) {
                    $attachment->extractUploadResult($result['data']);

                    if (!$attachment->save()) {
                        $errors[] = 'Unable to save attachment <b>' . $attachment->filename . '</br>';
                    }

                }
                $data[] = array(
                    'name' => $attachment->filename,
                    'type' => 'plain/pdf',
                    'size' => 1000,
                    'attachment_type' => $attachment->getAttachmentType()
                );
            } catch (ServiceCallException $e) {
                /* If an exception is raised, the message is extracted from that exception. */
                $errors[] = '<b>Service call error</b>: ' . $e->getMessage();
            }
        }
        echo CJSON::encode($data);
    }

}