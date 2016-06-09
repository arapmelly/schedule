<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array('id' => 'tweet-form', 'enableAjaxValidation' => false)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'text'); ?>
        <?php echo $form->textArea($model, 'text', array('rows' => 6, 'cols' => 50, 'class' => 'input')); ?>
        <?php echo $form->error($model, 'text'); ?>
    </div>

    <!--    <div class="row clearfix">-->
    <!--        --><?php //echo $form->labelEx($model, 'times'); ?>
    <!--    --><?php //echo $form->textField($model, 'times', array('disabled' => true, 'class' => 'input')); ?>
    <!--        --><?php //echo $form->error($model, 'times'); ?>
    <!--    </div>-->

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'is_active'); ?>
        <?php echo $form->textField($model, 'is_active', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'is_active'); ?>
    </div>

    <!--    <div class="row clearfix">-->
    <!--        --><?php //echo $form->labelEx($model, 'created'); ?>
    <!--        --><?php //echo $form->textField($model, 'created', array('disabled' => true, 'class' => 'input')); ?>
    <!--        --><?php //echo $form->error($model, 'created'); ?>
    <!--    </div>-->

    <div class="actions">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn primary')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->