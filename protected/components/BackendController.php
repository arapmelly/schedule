<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/23/12
 * Time: 5:59 PM
 * To change this template use File | Settings | File Templates.
 */
class BackendController extends BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'admin', // perform access control for CRUD operations
        );
    }

    public function filterAdmin($chain)
    {
        if (User::current()->isAdmin()) {
            $chain->run();
        }
    }
}
