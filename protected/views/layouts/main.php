<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="en"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css"/>

    <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico"/>
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <style type="text/css">


    </style>
</head>
<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.tmpl.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/bootstrap/bootstrap-dropdown.js');
?>
<body>

<div class="topbar">
    <div class="fill">
        <div class="container">
            <a class="brand"
               href="<?php echo $this->createUrl('site/index')?>"><?php echo CHtml::encode(Yii::app()->name); ?></a>
            <?php $menu = $this->widget('zii.widgets.CMenu', array(
            'items' => array(
                array('label' => 'Accounts', 'url' => '#', 'visible' => !Yii::app()->user->isGuest,
                    'items' => array(
                        array('label' => 'Account -> Group', 'url' => array('/twitterAccount/twitterAccountHasGroup')),
                        array('label' => 'Manage', 'url' => array('/twitterAccount/admin')),
                        array('label' => 'Add', 'url' => array('/auth/index'), 'visible' => !Yii::app()->user->isGuest),
                    ),
                    'itemOptions' => array('class' => 'dropdown'),
                    'linkOptions' => array('class' => 'dropdown-toggle')
                ),
                array('label' => 'Groups', 'url' => array('/tweetGroup/index'), 'visible' => !Yii::app()->user->isGuest),
                array('label' => 'Tags', 'url' => array('/hashTagGroup/index'), 'visible' => !Yii::app()->user->isGuest),
                array('label' => 'Add Tweets', 'url' => array('/tweet/createTweets'), 'visible' => !Yii::app()->user->isGuest),
                array('label' => 'Retweet', 'url' => array('/twitterAccountName/index'), 'visible' => !Yii::app()->user->isGuest,
                    'items' => array(
                        array('label' => 'Retweet By', 'url' => array('/twitterAccountName/twitterAccountRetweetAccount')),
                        array('label' => 'Accounts', 'url' => array('/twitterAccountName/accountNameHasHashTagGroup')),
                    ),
                    'itemOptions' => array('class' => 'dropdown'),
                    'linkOptions' => array('class' => 'dropdown-toggle')),
                array('label' => 'History', 'url' => array('/statistic/history'), 'visible' => !Yii::app()->user->isGuest),
                array('label' => 'Graphic', 'url' => array('/statistic/historyGraphic'), 'visible' => !Yii::app()->user->isGuest)
            ),
            'htmlOptions' => array('class' => 'nav', 'id' => 'mainMenu'),
            'submenuHtmlOptions' => array('class' => 'dropdown-menu'),
        ));?>
            <script type="text/javascript">$(function () {
                var menuId = '<?php echo $menu->getId()?>';
                $('#' + menuId).dropdown();
            })</script>
            <?php if (Yii::app()->user->isGuest): ?>
            <form action="<?php echo $this->createUrl('site/login')?>" class="pull-right" method="POST">
                <input class="input-small" type="text" placeholder="Username" name="username">
                <input class="input-small" type="password" placeholder="Password" name="password">
                <button class="btn" type="submit">Sign in</button>
            </form>
            <?php else: ?>
            <?php echo CHtml::link('Logout (' . Yii::app()->user->name . ')', $this->createUrl('site/logout'), array('class' => 'logout')) ?>
            <?php endif?>
        </div>
    </div>
</div>

<div class="container">
    <div class="content">
        <div class="page-header">
            <h1><?php echo $this->pageTitle?>
                <small></small>
            </h1>
        </div>
        <div id="flash-messages">
            <?php
            foreach (Yii::app()->user->getFlashes() as $key => $message) {
                if ($key == 'counters') continue;
                echo '<div class="alert-message ' . $key . '">' . $message . "</div>\n";
            }
            ?>
        </div>
        <div class="row">
            <div class="span10">
                <!--                <h2>Main content</h2>-->
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; Суть времени</p>
    </footer>
</div>
</body>
</html>