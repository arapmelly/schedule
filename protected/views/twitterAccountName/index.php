<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/chosen.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.chosen.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.tmpl.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/itemSelector.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/modelHasModels.js');
?>
<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array('enableAjaxValidation' => false, 'action' => $this->createUrl('create'))); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row clearfix">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('class' => 'input')); ?>
        <?php echo $form->error($model, 'name'); ?>
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn primary')); ?>
    </div>
    <?php $this->endWidget(); ?>

</div><!-- form -->
<?php $this->renderPartial('_templates') ?>

<div id="groupSelector"></div>
<div id="tags"></div>
<h4>Account -> Hash tag group</h4>
<div id="accountNameHasHashTagGroups"></div>
<br/><br/><br/><br/>
<h4>Account -> Retweet account</h4>
<div id="accountHasAccountNames"></div>
<script type="text/javascript">
    $(function () {
        $('#groupSelector').itemSelector({
            items: <?php echo CJSON::encode($accountNames)?>,
            url:{
                deleteItem:'<?php echo $this->createUrl('delete') ?>'
            },
            itemModel:'HashTag'
        });

        var accountNames = <? echo CJSON::encode(TwitterAccountNameHelper::getAccountNames())?>;
        var hashTagGroups = <? echo CJSON::encode(TwitterAccountNameHelper::getHashTagGroups())?>;
        var updateAccountNameUrl = '<?php echo $this->createUrl('updateAccountNameHasHashTagGroups')?>';

        $.map(accountNames, function (accName) {
            var preparedHashTagGroups = $.extend(true, {}, hashTagGroups);
            $.map(accName.hashTagGroups, function (hashTagGroup) {
                $.map(preparedHashTagGroups, function (pHashTagGroup) {
                    if (pHashTagGroup.id == hashTagGroup.id)
                        pHashTagGroup['has'] = 1;
                });
            });
            $('<div/>').appendTo($('#accountNameHasHashTagGroups')).modelHasModels({
                model:accName,
                hasModels:preparedHashTagGroups,
                sel:{
                    modelHasModelsTemplate:'#modelHasModelsTemplate'
                },
                url:{
                    updateHasModels:updateAccountNameUrl
                }
            });
        });


        var accounts = <? echo CJSON::encode(TwitterAccountHelper::getAccounts())?>;
        var retweetAccounts = <? echo CJSON::encode(TwitterAccountHelper::getRetweetAccounts())?>;
        var updateAccountNameUrl = '<?php echo $this->createUrl('updateAccountHasAccountNames')?>';

        $.map(accounts, function (acc) {
            var preparedAccountNames = $.extend(true, {}, retweetAccounts);
            $.map(acc.retweetAccounts, function (accName) {
                $.map(preparedAccountNames, function (pAccName) {
                    if (accName.id == pAccName.id)
                        pAccName['has'] = 1;
                });
            });
            $('<div/>').appendTo($('#accountHasAccountNames')).modelHasModels({
                model:acc,
                hasModels:preparedAccountNames,
                sel:{
                    modelHasModelsTemplate:'#modelHasModelsTemplate'
                },
                url:{
                    updateHasModels:updateAccountNameUrl
                }
            });
        });

    });
</script>