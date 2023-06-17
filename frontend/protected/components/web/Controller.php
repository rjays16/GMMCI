<?php
/**
 * Controller should be the base of all controllers within modules
 *
 * @uses CController
 * @version $id$
 * @copyright 2010 &copy; The YAP Group
 * @author Flavius Aspra <flav@yet-another-project.com>
 * @license License {@link http://yet-another-project.com/project/yap/LICENSE.txt}
 */

namespace SegHEIRS\components\web;

use CLogger;
use SegHEIRS\components\event\Emitter;
use SegHEIRS\components\event\view\ViewRenderedEvent;
use SegHEIRS\components\event\view\ViewRenderingEvent;
use Yii;

/**
 * Class Controller
 *
 */
class Controller extends \CController
{

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = [];
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = [];
    /**
     * @var bool
     */
    public $showFooter = true;
    /**
     * @var bool
     */
    public $showLeftContent = true;

    /**
     * @var string the content title of the current page.
     */
    public $content_title = '';

    /**
     * @var string the content sub title of the current page.
     */
    public $sub_title = '';

    public $pageSubtitle = '';
    /**
     * Header/Content title html options
     */
    public $headerHtmlOptions = [];
    /**
     * Simple and advance search container
     * array(
     *     'simple' => null,
     *     'advanced' => null,
     *     'htmlOptions' => array(),
     * )
     * @since 10-21-2015 You can use the widget HIMSSearch to set standards when setting the properties for the search.
     * @see protected/widgets/HIMSBootstrap/HIMSSearch/HIMSSearch.php Search widget.
     * @var array
     */
    public $search = [];

    /**
     * Menu items that will be appended.
     * @var array The menu items that the user wants to append in the main navbar.
     */
    public $append_main_items = [];

    /**
     * @var \PersonnelCatalog
     */
    public $personnel;

    /**
     * Default Controller/Action
     * May be modified depending on what module is running
     * @var String
     */
    public $homeUrl = '/site/index';

    /**
     * Menu items that will overrides the default menu items.
     * @var array The menu items that the user wants in the main navbar.
     */
    public $main_menu = [];
    public $toolbar = [];
    /**
     * @var string
     */
    private $viewPath;
    /**
     * @var string
     */
    protected $defaultViewPath;
    /**
     * @var string
     */
    protected $defaultId;
    /**
     * @var bool
     */
    public $rbacNavbar = false;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $class = new \ReflectionClass($this);
        //@todo use string functions instead

        preg_match('/(.+)Controller/', $class->getShortName(), $controllerId);

        $controllerId = lcfirst($controllerId[1]);

        $this->defaultViewPath = dirname($class->getFileName()) .
            DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views' .
            DIRECTORY_SEPARATOR . $controllerId;

        $this->defaultId = $controllerId;

        register_shutdown_function([$this, 'onShutdownHandler']);
    }

    /**
     *
     */
    public function onShutdownHandler()
    {

        // 1. error_get_last() returns NULL if error handled via set_error_handler
        // 2. error_get_last() returns error even if error_reporting level less then error
        $error = error_get_last();

        // Fatal errors
        $errorsToHandle = E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING;

        if (!is_null($error) && ($error['type'] & $errorsToHandle)) {
            // It's better to set errorAction = null to use system view "error.php" instead of
            // run another controller/action (less possibility of additional errors)
            Yii::app()->errorHandler->errorAction = null;

            $message = 'FATAL ERROR: ' . $error['message'];
            if (!empty($error['file'])) {
                $message .= ' (' . $error['file'] . ' :' . $error['line'] . ')';
            }

            // Force log & flush as logs were already processed as the error is fatal
            Yii::log($message, CLogger::LEVEL_ERROR, 'php');
            Yii::getLogger()->flush(true);

            // Pass the error to Yii error handler (standard or a custom handler you may be using)
            Yii::app()->handleError($error['type'], 'Fatal error: ' . $error['message'], $error['file'], $error['line']);
        }
    }


    /**
     * @param $path
     */
    public function setViewPath($path)
    {
        $this->viewPath = $path;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->defaultId;
    }

    /**
     * @param string $title
     */
    public function setContentTitle($title)
    {
        $this->content_title = $title;
    }

    /**
     * @param string $title
     */
    public function setPageTitle($title)
    {
        parent::setPageTitle($title);
        $this->setContentTitle($title);
    }

    /**
     * @param string $title
     */
    public function setSubTitle($title)
    {
        $this->sub_title = $title;
    }

    /**
     * This replicates the access control module in the base controller and lets us
     * do our own special rules that insure we fail closed.
     *
     * @param \CFilterChain $filterChain
     */
    public function filterAccessControl($filterChain)
    {
        $rules = $this->accessRules();

        // default deny
        $rules[] = ['deny'];

        $filter = new \CAccessControlFilter;
        $filter->setRules($rules);
        $filter->filter($filterChain);
    }

    /**
     * @param $view
     * @param null $data
     * @param bool $return
     * @param bool $processOutput
     *
     * @return string|null
     */
    public function renderModal($view, $data = null, $return = false, $processOutput = false)
    {
        try {
            ob_start();
            $json = \CJSON::encode([
                'content' => $this->renderPartial($view, $data, true, $processOutput),
            ]);
            ob_end_clean();
        } catch (\Exception $e) {
            //            ob_end_clean();
            $json = \CJSON::encode([
                'message' => $e->getMessage(),
            ]);
        }

        if ($return) {
            return $json;
        } else {
            echo $json;

            return null;
        }
    }

    /**
     * Renders a JSON response
     *
     * @param $data
     * @param int $status
     */
    public function renderJson($data, $status = 200)
    {
        header("Content-Type: application/json;charset=utf-8");
        http_response_code($status);
        echo \CJSON::encode($data);
        \Yii::app()->end();
    }

    /**
     * renderAjax render a view based on whether the request is synchronous or
     * asynchronous
     *
     * @param string $view
     * @param array $data
     * @param bool $return
     * @param bool $processOutput
     *
     * @return string|void
     *
     * @throws \CHttpException If the view file can't be found.
     */
    public function renderAjax($view, $data = null, $return = false, $processOutput = false)
    {
        if (\Yii::app()->request->isAjaxRequest) {
            return $this->renderPartial($view, $data, $return, $processOutput);
        } else {
            return $this->render($view, $data, $return);
        }
    }

    /**
     * renderParents renders everything by respecting module's nesting.
     *
     * No noticeable overhead is involved for first-level application modules,
     * but it allows you to render the module's layout inside the
     * application's layout.
     *
     * @param mixed $view
     * @param mixed $data
     * @param mixed $return
     *
     * @access public
     *
     * @return string|void
     */
    public function renderParents($view, $data = null, $return = false)
    {
        $layoutName = $this->layout;
        if (empty($layoutName) && $parent = $this->getModule()) {
            $layoutName = $parent->layout;
        }
        $layouts = $this->getLayoutFiles($layoutName);
        $output = $this->renderPartial($view, $data, true);
        foreach ($layouts as $layout) {
            $output = $this->renderFile($layout, ['content' => $output], true);
        }
        $output = $this->processOutput($output);
        if ($return) {
            return $output;
        }
        echo $output;
    }

    /**
     * getLayoutFiles get all the layout files of the parent modules starting from
     * the module owning this controller and ending with the application layout
     *
     * @param mixed $layoutName
     *
     * @access public
     * @return array|bool the list of layouts
     */
    public function getLayoutFiles($layoutName)
    {
        if (false === $layoutName) {
            return false;
        }
        /**
         * @var \WebModule $module
         */
        $module = $this->getModule();
        $r = [];
        while (null !== $module) {
            if (is_readable($t = $this->resolveViewFile($layoutName, $module->getLayoutPath(), $module->getViewPath()))) {
                $r[$module->getId()] = $t;
            }
            $module = $module->getParentModule();
        }
        if (is_readable($t = $this->resolveViewFile($layoutName, \Yii::app()->getLayoutPath(), \Yii::app()->getViewPath()))) {
            $r[\Yii::app()->getId()] = $t;
        }

        return $r;
    }

    /**
     * @param $url
     * @param bool|true $terminate
     * @param int $statusCode
     */
    public function redirectWaypoint($url, $terminate = true, $statusCode = 302)
    {
        $url = \Yii::app()->user->getWaypoint($this->route, $url);
        $this->redirect($url, $terminate, $statusCode);
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->content_title)) {
            return \Yii::app()->name . ' - ' . strip_tags($this->content_title);
        } else {
            return parent::getPageTitle();
        }
    }

    /**
     * @return \TracerModule
     */
    public function getTracerModule()
    {
        return \Yii::app()->getModule('tracer');
    }

    /**
     * @author jolly
     *
     * @return String Base Url of the Core Application
     */
    public function getBaseUrl()
    {
        return \Yii::app()->getBaseUrl();
    }

    /**
     * baseUrl/js/
     * @author jolly
     *
     * @return String Base Url of Js Files
     */
    public function getJsUrl()
    {
        return \Yii::app()->getBaseUrl() . '/js/';
    }

    /**
     * baseUrl/css/
     * @author jolly
     *
     * @return String Base Url of Js Files
     */
    public function getCssUrl()
    {
        return \Yii::app()->getBaseUrl() . '/css/';
    }

    /**
     * Current Theme Base Url
     *
     * /theme/
     *
     * @author jolly
     *
     * @return String
     */
    public function getThemeUrl()
    {
        return empty(\Yii::app()->theme->baseUrl) ? null : \Yii::app()->theme->baseUrl;
    }

    /**
     * Pass the array of js files that should not be
     * loaded if request is an Ajax Request.
     *
     * @author jolly
     *
     * @param array $jsFiles
     */
    public function ajaxJsUnLoader($jsFiles = [])
    {
        $cs = \Yii::app()->getClientScript();
        $default = ['jquery', 'jquery.livequery', 'util'];
        $jsFiles = \CMap::mergeArray($default, $jsFiles);

        foreach ($jsFiles as $file)
            $cs->scriptMap[$file . '.js'] = false;
    }

    /**
     * @author jolly
     *
     * @param mixed $req
     * @param mixed $params
     *
     * @throws \CHttpException
     */
    public function isBadRequest($req, $params)
    {
        foreach ($req as $key) {
            if (!isset($params[$key])) {
                throw new \CHttpException(400, 'Bad request :<');
            }
        }
    }

    /**
     * @author jolly
     *
     * @param \Exception $e
     * @param string $defaultMsg
     *
     * @throws \CHttpException
     * @throws \HeirsAppException
     */
    public function CHttpExceptionRun(\Exception $e, $defaultMsg = '')
    {
        if ($e->getMessage()) {
            throw new \CHttpException(400, $e->getMessage());
        } else {
            if (!empty($defaultMsg)) {
                throw new \CHttpException(400, $defaultMsg);
            } else {
                throw new \HeirsAppException(400);
            }
        }
    }


    /**
     * This method is invoked at the beginning of {@link render()}.
     * You may override this method to do some preprocessing when rendering a view.
     *
     * @param string $view the view to be rendered
     *
     * @return boolean whether the view should be rendered.
     * @since 1.1.5
     */
    protected function beforeRender($view)
    {
        if (!parent::beforeRender($view)) {
            return false;
        }

        /** @var Emitter $emitter */
        $emitter = Yii::app()->emitter;

        /** @var ViewRenderingEvent $event */
        $event = $emitter->emit(new ViewRenderingEvent($view));

        return !$event->isCancelled();
    }

    /**
     * This method is invoked after the specified view is rendered by calling {@link render()}.
     * Note that this method is invoked BEFORE {@link processOutput()}.
     * You may override this method to do some postprocessing for the view rendering.
     *
     * @param string $view the view that has been rendered
     * @param string $output the rendering result of the view. Note that this parameter is passed
     * as a reference. That means you can modify it within this method.
     *
     * @since 1.1.5
     */
    protected function afterRender($view, &$output)
    {
        parent::afterRender($view, $output);

        /** @var Emitter $emitter */
        $emitter = Yii::app()->emitter;

        /** @var ViewRenderedEvent $event */
        $event = $emitter->emit(new ViewRenderedEvent($view, $output));
        if ($event->isChanged()) {
            $output = $event->getOutput();
        }
    }
}
