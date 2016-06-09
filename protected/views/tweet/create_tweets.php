<?php
$this->pageTitle = 'Добавить твиты с файла.';
?>

<div class="clearfix">
    <h3>Select group</h3>
    <div class="input">
        <?php echo CHtml::dropDownList('tweet_group_id', null, $groups) ?>
    </div>
</div>

<h4>Load from file</h4>
<div class="form">
    <?php echo CHtml::form('', 'post', array('enctype' => 'multipart/form-data')); ?>
    <div class="row clearfix">
        <?php echo CHtml::label('File with tweets', 'tweets')?>
        <?php echo CHtml::fileField('tweets', '', array('class' => 'input input-file'))?>
    </div>
    <div class="actions">
        <?php echo CHtml::submitButton('Load from file', array('class' => 'btn primary')); ?>
    </div>
    <?php echo CHtml::endForm() ?>
</div>
<table>
    <?php foreach ($importedTweets as $tweet): ?>
    <tr>
        <td><?php echo $tweet['text']?></td>
        <?php if (($cropped = Tweet::crop($tweet['text'])) != $tweet['text']): ?>
        <td style="color: red"><?php echo $cropped?></td>
        <?php endif?>
    </tr>
    <?php endforeach?>
</table>

<script type="text/javascript">
    $(function () {
        $('#tweet_group_id').change(
            function (e) {
                var $select = $(e.target);
                $('form').each(function (i, form) {
                    var $input = $(form).find('[name="tweet_group_id"]');
                    if ($input.length == 0) {
                        $input = $('<input name="tweet_group_id" type="hidden"/>').appendTo(form);
                    }
                    $input.val($select.val());
                });
                if ($select.val() != 'default') {
                    $select.removeClass('custom-error');
                }
            }).change();
        $('form').each(function (i, form) {
            $(form).submit(function () {
                var $select = $('#tweet_group_id');
                if ($select.val() == 'default') {
                    $select.addClass('custom-error');
                    return false;
                }
            });
        });
    });
</script>