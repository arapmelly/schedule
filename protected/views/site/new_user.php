<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/11/11
 * Time: 12:26 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'twitter-account-form',
    'enableAjaxValidation' => false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'username'); ?>
        <?php echo $form->textField($model, 'username', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'username'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'password'); ?>
        <?php echo $form->passwordField($model, 'password', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'password'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'superuser'); ?>
        <?php echo $form->checkBox($model, 'superuser', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'superuser'); ?>
    </div>

    <div class="actions">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn primary')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->