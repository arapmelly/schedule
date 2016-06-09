<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/29/11
 * Time: 10:29 PM
 * To change this template use File | Settings | File Templates.
 */

class AppHelper
{

    public static function mysqlDate($timestamp = null, $hours = true)
    {
        $format = $hours ? 'Y-m-d H:i:s' : 'Y-m-d';
        return is_null($timestamp) ? date($format) : date($format, $timestamp);
    }

    public static function parseConnectionString($connectionString)
    {
        $parsed = array();
        list($driver, $params) = explode(':', $connectionString);
        $parsed['driver'] = $driver;
        foreach (explode(';', $params) as $p)
        {
            list($name, $value) = explode('=', $p);
            $parsed[$name] = $value;
        }
        return $parsed;
    }

    public static function uploadFile($host, $username, $password, $localPath, $serverFolder)
    {
        $localPathInfo = pathinfo($localPath);
        $serverPath = "$serverFolder/{$localPathInfo['basename']}";
        $conn_id = ftp_connect($host, 21) or die ("Cannot connect to host");
        ftp_login($conn_id, $username, $password) or die("Cannot login");
        // turn on passive mode transfers (some servers need this)
        // ftp_pasv ($conn_id, true);
        $upload = ftp_put($conn_id, $serverPath, $localPath, FTP_ASCII);
        return $upload;
    }

    public static function getAppActive($state = null)
    {
        $stateFile = Yii::getPathOfAlias('application.runtime') . '/appActive';
        if (is_null($state)) {
            $data = file_get_contents($stateFile);
            if ($data === false)
                return false;
            return unserialize($data);
        } else {
            file_put_contents($stateFile, serialize($state));
            return true;
        }
    }

    public function logVisitor()
    {
        Yii::app()->db->createCommand()->insert('visitor_log', array('ip' => $_SERVER['REMOTE_ADDR'], 'route' => Yii::app()->request->getPathInfo()));
    }
}
