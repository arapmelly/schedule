<?php
$this->pageTitle = 'Какой аккаунт кого ретвитит?';
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/chosen.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.chosen.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.tmpl.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/itemSelector.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/modelHasModels.js');
?>
<?php $this->renderPartial('_templates') ?>
<div id="accountHasAccountNames"></div>
<script type="text/javascript">
    $(function () {
        var accounts = <? echo CJSON::encode(TwitterAccountHelper::getAccounts())?>;
        var retweetAccounts = <? echo CJSON::encode(TwitterAccountHelper::getRetweetAccounts())?>;
        var updateAccountNameUrl = '<?php echo $this->createUrl('twitterAccountHasAccountNames')?>';

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
                    modelHasModelsTemplate:'#accountRetweetAccountTemplate'
                },
                url:{
                    updateHasModels:updateAccountNameUrl
                }
            });
        });

    });
</script>