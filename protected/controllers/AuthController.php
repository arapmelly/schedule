<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/27/11
 * Time: 8:24 PM
 * To change this template use File | Settings | File Templates.
 */

Yii::import('ext.OAuth', true);

class AuthController extends BaseController
{

    public function actions()
    {
        return array(
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'result' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionRequestAccess()
    {
        $twitterApp = TwitterApplication::getDefault();
        $requestToken = TwitterApi::queryRequestToken($twitterApp->consumer_key, $twitterApp->consumer_secret);
        Yii::app()->user->setState('requestToken', $requestToken);

        $callbackUrl = $this->createAbsoluteUrl('callback');
        $authUrl = TwitterApi::createAuthUrl($requestToken, $callbackUrl);
        header("Location: $authUrl");
    }

    public function actionCallback()
    {
        $requestToken = Yii::app()->user->getState('requestToken');
        $twitterApp = TwitterApplication::getDefault();
        $accessToken = TwitterApi::queryAccessToken($twitterApp->consumer_key, $twitterApp->consumer_secret, $requestToken);
        if (empty($accessToken)) {
            $this->redirect(array('result', 'view' => 'failure'));
            return;
        }

        if (($account = TwitterAccount::createFromAccessToken($accessToken, $this->getUser())) !== false) {
            $this->redirect(array('result', 'view' => 'success'));
        } else {
            $this->redirect(array('result', 'view' => 'exists'));
        }
    }
}
