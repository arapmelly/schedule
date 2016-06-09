<?php
$this->pageTitle = 'Какие группы твитов использует аккаунт?';
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/chosen.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.chosen.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.tmpl.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/modelHasModels.js');
?>
<?php $this->renderPartial('_templates') ?>
<div id="accountHasGroup"></div>
<script type="text/javascript">
    $(function () {
        var accounts = <? echo CJSON::encode(TwitterAccountHelper::getAccounts())?>;
        var groups = <? echo CJSON::encode(TwitterAccountHelper::getGroups())?>;
        var twitterAccountHasGroupsUrl = '<?php echo $this->createUrl('twitterAccountHasGroups')?>';

        $.map(accounts, function (acc) {
            var preparedGroups = $.extend(true, {}, groups);
            $.map(acc.groups, function (accGroup) {
                $.map(preparedGroups, function (pGroup) {
                    if (accGroup.id == pGroup.id)
                        pGroup['has'] = 1;
                });
            });
            $('<div/>').appendTo($('#accountHasGroup')).modelHasModels({
                model:acc,
                hasModels:preparedGroups,
                sel:{
                    modelHasModelsTemplate:'#modelHasModelsTemplate'
                },
                url:{
                    updateHasModels:twitterAccountHasGroupsUrl
                }
            });

        });
    })
</script>