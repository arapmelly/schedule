<?php
$this->pageTitle = 'История';
Yii::app()->getClientScript()->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl() .
        '/jui/css/base/jquery-ui.css'
)
?>
<div id="historyParams">
    <div id="accounts"></div>
    <div id="date">
        <input type="text" id="from" name="from"/>
        <input type="text" id="to" name="to"/>
    </div>
    <a id="go" href="javascript: void(0)" class="btn">Go</a>
</div>
<table id="history"></table>

<script type="text/javascript">
    $(function () {
        var historyUrl = '<?php echo $this->createUrl('accountHistory')?>';
        var accounts = <?php echo CJSON::encode($accounts) ?>;

        var $accounts = $('<select>');
        $accounts.append($('<option>'));
        $.map(accounts, function (acc) {
            $accounts.append($('<option>').text(acc.screen_name).val(acc.id));
        });
        $('#accounts').append($accounts);

        var dates = $("#from, #to").datepicker({
            defaultDate:"+1w",
            changeMonth:true,
            dateFormat: 'dd/mm/yy',
            onSelect:function (selectedDate) {
                var option = this.id == "from" ? "minDate" : "maxDate",
                    instance = $(this).data("datepicker"),
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                            $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
                $(this).data('date', date)
            }
        });

        var search = function () {
            var from = $("#from").data('date'),
                to = $("#to").data('date');
            var data = {
                account:$accounts.val(),
                from:from ? from.getTime() - from.getTimezoneOffset() * 60000 : '',
                to:to ? to.getTime() - to.getTimezoneOffset() * 60000 : ''
            };
            $.get(historyUrl + '?' + $.param(data), function (history) {
                $('#history').empty();
                var prev_group = null;
                $.map(history, function (row) {
                    if (prev_group != row['tweet_variant_group_id']) {
                        $('#history').append($('<tr class="groupDelimeter"><td colspan="10"></td></tr>'));
                    }
                    var $row = $('<tr>');
                    for (var column in row) {
                        var text = row[column] ? row[column] : '-';
                        $row.append($('<td/>').addClass(column).text(text));
                    }
                    $row.addClass(row['http_code'] == 200 ? 'tweet-success' : 'tweet-fail');
                    $('#history').append($row);
                    prev_group = row['tweet_variant_group_id'];
                });
            }, 'json');
        };

        $('#go').click(search).click();

    });
</script>