<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/23/12
 * Time: 5:58 PM
 * To change this template use File | Settings | File Templates.
 */
class BackupController extends BaseController
{

    public function filters()
    {
        return array();
    }

    function actionBackup($key)
    {
        if ($key != Yii::app()->params['backupKey']) return;
        $backupPath = Backup::dbBackup();
        $status = Backup::uploadBackup($backupPath);
        var_dump($status);
        //        $this->render('//test/index');
    }

    function actionRestore($key)
    {
        if ($key != Yii::app()->params['backupKey']) return;

        if (($backupFile = Backup::dbRestore()) !== false)
            $this->message("DB restored from $backupFile");
        else
            $this->message("Restore error", self::MSG_ERROR);
        $this->redirect(Yii::app()->request->getUrlReferrer());
    }

}
