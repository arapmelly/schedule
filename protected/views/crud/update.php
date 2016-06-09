<?php
$this->breadcrumbs = array(
    get_class($model) => array('index'),
    $model->getPrimaryKey() => array('view', $model->getTableSchema()->primaryKey => $model->getPrimaryKey()),
    'Update',
);

$this->menu = array(
    array('label' => 'List', 'url' => array('index')),
    array('label' => 'Create', 'url' => array('create')),
    array('label' => 'View', 'url' => array('view', $model->getTableSchema()->primaryKey => $model->getPrimaryKey())),
    array('label' => 'Manage', 'url' => array('admin')),
);
?>

<h1>Update <?php echo get_class($model) . ' - ' . $model->getPrimaryKey(); ?></h1>

<?php echo $this->renderPartial('//base/_form', array('model' => $model)); ?>