<?php
$this->pageTitle = 'Добавление/удаление группы твитов, самих твитов. Связь между группой твитов и группой тегов.';
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/chosen.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.chosen.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/lib/jquery.tmpl.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/groupSelector.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/modelHasModels.js');
?>
<div class="row">
    <form action="<?php echo $this->createUrl('create')?>" method="POST">
        <div class="row clearfix">
            <label for="tweet_group_name">New group</label>
            <input type="text" id="tweet_group_name" name="TweetGroup[name]" class="input">
            <input type="submit" value="Create" class="btn primary">

            <div id="status"></div>
        </div>
    </form>
</div>
<?php $this->renderPartial('_templates') ?>
<div id="groupSelector"></div>
<div id="modelHasModels"></div>
<div id="tags"></div>

<script type="text/javascript">
    $(function () {
        var getHashTagGroupsUrl = '<?php echo $this->createUrl('getHashTagGroups')?>';
        $('#groupSelector').groupSelector({
            groups: <?php echo CJSON::encode($tweetGroups)?>,
            url:{
                itemsList:'<?php echo $this->createUrl('tweetList') ?>',
                addItem:'<?php echo $this->createUrl('tweet/create') ?>',
                deleteItem:'<?php echo $this->createUrl('tweet/delete') ?>',
                deleteGroup:'<?php echo $this->createUrl('delete') ?>'
            },
            sel:{
                items:'#tags',
                itemTemplate:'#hashTagTemplate',
                addItemTemplate:'#addHashTagTemplate'
            },
            itemModel:'Tweet'
        }).bind('groupChanged', function (e, group) {
                $.post(getHashTagGroupsUrl + '?id=' + group.id, function (hashGroups) {
                    $('#modelHasModels').empty().modelHasModels({
                        model:group,
                        hasModels:hashGroups,
                        sel:{
                            modelHasModelsTemplate:'#modelHasModelsTemplate'
                        },
                        url:{
                            updateHasModels:'<?php echo $this->createUrl('tweetGroupHasHashTagGroups')?>'
                        }
                    });
                }, 'json')
            });


    });
</script>