<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'trader-account-form',
    'enableAjaxValidation' => false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <?php $schema = $model->getTableSchema(); foreach (CrudHelper::modelSearchAttributes($model) as $name): ?>
    <div class="row">
        <?php echo $form->labelEx($model, $name); ?>
        <?php echo $form->textField($model, $name, array('class' => 'input', 'readonly' => $name == $schema->primaryKey || isset($schema->foreignKeys[$name]))); ?>
        <?php echo $form->error($model, $name); ?>
    </div>
    <?php endforeach?>


    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->