<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/18/12
 * Time: 12:50 PM
 * To change this template use File | Settings | File Templates.
 */
class DeleteAction extends BackendAction
{
    public function run()
    {
        $this->getModel()->delete();
        $this->redirect();
    }
}