<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/23/12
 * Time: 6:01 PM
 * To change this template use File | Settings | File Templates.
 */
class Backup
{
    public static function dbBackup()
    {
        $connectionString = AppHelper::parseConnectionString(Yii::app()->db->connectionString);
        $dbuser = Yii::app()->db->username;
        $dbpass = Yii::app()->db->password;
        $dbhost = @$connectionString['host'];
        $backupPathInfo = pathinfo(Yii::app()->params['backupPath']);
        $backupPath = "{$backupPathInfo['dirname']}/" . date('G_d_m_y') . $backupPathInfo['basename'];
        $command = "mysqldump --opt " . ($dbhost ? "-h $dbhost" : '') . " -u $dbuser --password=$dbpass {$connectionString['dbname']} > $backupPath";
        exec($command);
        return $backupPath;
    }

    public static function dbRestore()
    {
        $restorePathInfo = pathinfo(Yii::app()->params['restorePath']);
        $files = glob("{$restorePathInfo['dirname']}/*{$restorePathInfo['basename']}");
        if (empty($files)) return false;
        array_multisort(array_map('filemtime', $files), SORT_NUMERIC, SORT_DESC, $files);
        $restorePath = $files[0];
        $sql = file_get_contents($restorePath);
        foreach (explode(";\n", $sql) as $string)
        {
            $res = Yii::app()->db->createCommand($string)->query();
        }


        //        $connectionString = AppHelper::parseConnectionString(Yii::app()->db->connectionString);
        //        $dbuser = Yii::app()->db->username;
        //        $dbpass = Yii::app()->db->password;
        //        $dbhost = @$connectionString['dbhost'];
        //        $command = "gunzip < $restorePath | mysql " . ($dbhost ? "-h $dbhost" : '') . "-u $dbuser --password=$dbpass {$connectionString['dbname']}";
        //        exec($command);

        return $restorePath;
    }

    public static function uploadBackup($backupPath)
    {
        $backupServers = @Yii::app()->params['backupServers'];
        $status = array();
        if ($backupServers)
            foreach ($backupServers as $server)
            {
                $status[$server['host']] = AppHelper::uploadFile($server['host'], $server['username'], $server['password'], $backupPath, $server['backupFolder']);
            }
        return $status;
    }
}
