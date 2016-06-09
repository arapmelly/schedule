/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/11/11
 * Time: 2:03 PM
 * To change this template use File | Settings | File Templates.
 */
$(function () {
    $.fn.tweetgroup = function (options) {
        return this.each(function (i, el) {
            var $this = $(this);
            var group = options.group;

            var $label;
            if (group.is_active == 1) {
                $label = $('<span class="label success">Enabled</span>');
            } else {
                $label = $('<span class="label warning">Disabled</span>');
            }
            var $is_active = $('<div class="is_active"/>')
                .append($label)
                .append($('<input name="is_active" type="checkbox">').attr('checked', group.is_active == 1));

            var $select = $('<select multiple="on" name="tweets"/>');
            $.map(group.tweets, function (tw) {
                var $option = $('<option/>').text(tw.text).val(tw.id);
                if (tw.is_active == 1)
                    $option.attr('selected', 'selected');
                $select.append($option);
            });
            $this.append($is_active);
            $this.append($('<h6/>').text(options.group.name));
            if (options.group.name != 'Default')
                $this.append($('<button class="btn danger delete"/>').text('Delete'));
            $this.append($select);
            $select.multiselect({addText:'+'});

            $this.on('click', '[name="is_active"]', $.proxy(function (e) {
                group.is_active = $(e.target).attr('checked') ? '1' : '0';
                $.post(
                    options.url.updateGroup + '?id=' + group.id,
                    {
                        'TweetGroup':group
                    },
                    function (group) {
                        var $label;
                        if (group.is_active == 1) {
                            $label = $('<span class="label success">Enabled</span>');
                        } else {
                            $label = $('<span class="label warning">Disabled</span>');
                        }
                        $this.find('.is_active .label').replaceWith($label);
                    },
                    'json'
                );
            }, this));
            $this.on('click', '.mselect-list-item input[type="checkbox"]', function (e) {
                var $checkbox = $(e.target);
                var tweet = {id:$checkbox.val()};
                tweet.is_active = $checkbox.attr('checked') ? '1' : '0';
                $.post(
                    options.url.updateTweet + '?id=' + tweet.id,
                    {
                        'Tweet':tweet
                    },
                    function (tweet) {
                        console.log(tweet);
                    },
                    'json'
                );
            });
            $this.on('click', '.delete', function (e) {
                var $button = $(e.target);
                if (confirm('Sure?')) {
                    $.post(options.url.delete + '?id=' + group.id, {}, function () {
                        $button.text('Deleted').removeClass('danger').addClass('success');
                    });
                }
            });


            $this.wrap('<div class="twitter-group well"/>');
        });
    }
});