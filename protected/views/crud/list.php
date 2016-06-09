<?php
$this->breadcrumbs = array(
    get_class($model) => array('index'),
    'Manage',
);
//
//$this->menu = array(
//    array('label' => 'List Broker', 'url' => array('index')),
//    array('label' => 'Create Broker', 'url' => array('create')),
//);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('broker-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage <? echo get_class($model) ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array_filter(array(
            'id' => get_class($model) . '-grid',
            'itemsCssClass' => 'bordered-table',
            'cssFile' => false,
            'dataProvider' => $model->search(),
            'filter' => $model,
            'columns' => CMap::mergeArray(CrudHelper::modelSearchAttributes($model),
                array(
                    array(
                        'class' => 'CButtonColumn',
                        'template' => '{update} {delete}'
                    )
                ),
                $this->getUser()->isAdmin() ?
                    array(
                        'type' => 'raw',
                        'header' => 'Relation',
                        'value' => 'implode("<br/>", CrudHelper::buildRelationLinks($data, $data->getPrimaryKey()) )'
                    ) :
                    false
            )
        )
    )
);
?>
