<?php
//$this->breadcrumbs=array(
//	'Twitter Accounts'=>array('index'),
//	$model->id=>array('view','id'=>$model->id),
//	'Update',
//);
//
//$this->menu=array(
//	array('label'=>'List TwitterAccount', 'url'=>array('index')),
//	array('label'=>'Create TwitterAccount', 'url'=>array('create')),
//	array('label'=>'View TwitterAccount', 'url'=>array('view', 'id'=>$model->id)),
//	array('label'=>'Manage TwitterAccount', 'url'=>array('admin')),
//);
?>

<h6>Update TwitterAccount <?php echo $model->id; ?></h6>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>