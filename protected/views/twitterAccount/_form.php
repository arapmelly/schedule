<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'twitter-account-form',
    'enableAjaxValidation' => false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'twitter_id'); ?>
        <?php echo $form->textField($model, 'twitter_id', array('readonly' => 'true', 'class' => 'input')); ?>
        <?php echo $form->error($model, 'twitter_id'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'screen_name'); ?>
        <?php echo $form->textField($model, 'screen_name', array('size' => 60, 'maxlength' => 100, 'class' => 'input')); ?>
        <?php echo $form->error($model, 'screen_name'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'oauth_token'); ?>
        <?php echo $form->textField($model, 'oauth_token', array('size' => 60, 'maxlength' => 225, 'readonly' => 'true', 'class' => 'input')); ?>
        <?php echo $form->error($model, 'oauth_token'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'oauth_token_secret'); ?>
        <?php echo $form->textField($model, 'oauth_token_secret', array('size' => 60, 'maxlength' => 225, 'readonly' => 'true', 'class' => 'input')); ?>
        <?php echo $form->error($model, 'oauth_token_secret'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'account_data'); ?>
        <?php echo $form->textArea($model, 'account_data', array('rows' => 6, 'cols' => 50, 'readonly' => 'true', 'class' => 'input')); ?>
        <?php echo $form->error($model, 'account_data'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'is_active'); ?>
        <?php echo $form->textField($model, 'is_active', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'is_active'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'frequency'); ?>
        <?php echo $form->textField($model, 'frequency', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'frequency'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'follow_relation'); ?>
        <?php echo $form->textField($model, 'follow_relation', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'follow_relation'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'disabled_until'); ?>
        <?php echo $form->textField($model, 'disabled_until', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'disabled_until'); ?>
    </div>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'created'); ?>
        <?php echo $form->textField($model, 'created', array('readonly' => 'true', 'class' => 'input')); ?>
        <?php echo $form->error($model, 'created'); ?>
    </div>

    <div class="actions">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn primary')); ?>
        <?php echo CHtml::link('Delete', $this->createUrl('delete', array('id' => $model->id)), array('class' => 'btn danger', 'id' => 'delete')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function () {
        $('#delete').click(function () {
            if (confirm('Sure?')) {
                $('<form/>')
                    .attr({action:$(this).attr('href'), method:'post'})
                    .appendTo('body')
                    .submit();
            }
            return false;
        });
    })
</script>