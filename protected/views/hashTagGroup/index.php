<?php
$this->pageTitle = 'Добавление/удаление группы тегов, самих тегов.';
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.tmpl.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/groupSelector.js');
?>
<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array('enableAjaxValidation' => false, 'action' => $this->createUrl('create'))); ?>

    <?php echo $form->errorSummary($newGroup); ?>

    <div class="row clearfix">
        <?php echo $form->labelEx($newGroup, 'name'); ?>
        <?php echo $form->textField($newGroup, 'name', array('class' => 'input')); ?>
        <?php echo $form->error($newGroup, 'name'); ?>
        <?php echo CHtml::submitButton($newGroup->isNewRecord ? 'Create' : 'Save', array('class' => 'btn primary')); ?>
    </div>
    <?php $this->endWidget(); ?>

</div><!-- form -->
<?php $this->renderPartial('_templates') ?>
<div id="groupSelector"></div>
<div id="tags"></div>

<script type="text/javascript">
    $(function () {
        $('#groupSelector').groupSelector({
            groups: <?php echo CJSON::encode($hashTagGroups)?>,
            url:{
                itemsList:'<?php echo $this->createUrl('hashTagList') ?>',
                addItem:'<?php echo $this->createUrl('hashTag/create') ?>',
                deleteItem:'<?php echo $this->createUrl('hashTag/delete') ?>',
                deleteGroup:'<?php echo $this->createUrl('delete') ?>'
            },
            sel:{
                items:'#tags',
                itemTemplate:'#hashTagTemplate',
                addItemTemplate:'#addHashTagTemplate'
            },
            itemModel:'HashTag'
        });
    });
</script>