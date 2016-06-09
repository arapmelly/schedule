<?php
//$this->breadcrumbs = array(
//    'Tweets' => array('index'),
//    'Manage',
//);
//
//$this->menu = array(
//    array('label' => 'List Tweet', 'url' => array('index')),
//    array('label' => 'Create Tweet', 'url' => array('create')),
//);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('tweet-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Tweets</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'tweet-grid',
    'itemsCssClass' => 'bordered-table',
    'cssFile' => false,
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
//        'id',
        'text',
        'times',
        'is_active',
        'created',
        array(
            'class' => 'CButtonColumn',
            'buttons' => array(
//                'delete' => array('visible' => 'false'),
                'view' => array('visible' => 'false')
            )
        ),
    ),
)); ?>
