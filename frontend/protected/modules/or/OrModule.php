<?php
/**
 * EclaimsModule
 *
 * @author Alvin Quinones <ajmquinones@gmail,com>
 * @copyright Copyright &copy; 2014. Segworks Technologies Corporation
 */

/**
 * @property EntityModel[] $entityModels
 */
class OrModule extends WebModule {
    /**
     *
     * @var type
     */
    public $layout = 'or.views.layouts.or-main';
    /**
     * @var string $title
     */
    public $title = 'OR module';

    /**
     *
     * @param Controller $controller
     * @param CAction $action
     */
    public function beforeControllerAction($controller, $action) {
        $controller->breadcrumbs['OR'] = array('main/index');
        return parent::beforeControllerAction($controller, $action);
    }

}