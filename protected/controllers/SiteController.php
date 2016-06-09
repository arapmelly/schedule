<?php

class SiteController extends BaseController
{

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'login', 'newUser', 'newApp'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('logout'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $this->render('index');
    }


    public function actionNewUser()
    {
        if (!User::current()->isAdmin()) return;

        $user = new User();
        if (isset($_POST['User'])) {
            $user->attributes = $_POST['User'];
            if ($user->save()) {
                $group = new TweetGroup();
                $group->name = TweetGroup::DEFAULT_NAME;
                $group->user_id = $user->id;
                if (!$group->save()) {
                    throw new Exception();
                }
                $user = new User();
            }
        }
        $this->render('new_user', array('model' => $user));
    }

    public function actionNewApp()
    {
        if (!User::current()->isAdmin()) return;

        $model = new TwitterApplication();
        if (isset($_POST['TwitterApplication'])) {
            $model->attributes = $_POST['TwitterApplication'];
            if ($model->save()) {
                $model = new TwitterApplication();
            }
        }
        $this->render('new_app', array('model' => $model));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;
        // collect user input data
        if (isset($_POST['username'], $_POST['password'])) {
            $model->username = $_POST['username'];
            $model->password = $_POST['password'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
            else {
                Yii::app()->user->setFlash('error', "Wrong name or password!");
            }
        }
        // display the login form
        $this->redirect('index');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
}