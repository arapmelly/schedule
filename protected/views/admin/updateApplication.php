<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'twitter-account-form',
    'enableAjaxValidation' => false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'consumer_key'); ?>
        <?php echo $form->textField($model, 'consumer_key', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'consumer_key'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'consumer_secret'); ?>
        <?php echo $form->textField($model, 'consumer_secret', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'consumer_secret'); ?>
    </div>

    <div class="actions">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn primary')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
