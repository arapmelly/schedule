<?php
$this->pageTitle = 'Панель управления аккаунтами';
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('twitter-account-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Twitter Accounts</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'twitter-account-grid',
    'itemsCssClass' => 'bordered-table',
    'cssFile' => false,
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
//        'id',
        'twitter_id',
        'screen_name',
//		'oauth_token',
//		'oauth_token_secret',
//		'account_data',
        'is_active',
        'frequency',
        'disabled_until',
        'follow_relation',
//		'created',
        array(
            'class' => 'CButtonColumn',
            'buttons' => array(
                'delete' => array('visible' => 'false'),
                'view' => array('visible' => 'false')
            )
        ),
    ),
)); ?>

