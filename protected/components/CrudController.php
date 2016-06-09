<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/23/12
 * Time: 11:42 AM
 * To change this template use File | Settings | File Templates.
 */
class CrudController extends BaseController
{
    public $defaultAction = 'list';

    public function actions()
    {
        return array(
            'list' => 'application.actions.ListAction',
            'update' => 'application.actions.UpdateAction',
            'delete' => 'application.actions.DeleteAction',
            'restore' => 'application.actions.RestoreAction',
        );
    }


}
