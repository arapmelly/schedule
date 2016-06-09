$(function () {
    $.fn.tweetManage = function (options) {
        this.options = options;
        $(options.sel.tweetTemplate).template('tweetTemplate');
        var $list = $('<ol/>');
        $.map(options.tweets, function (tweet) {
            var $tweet = $('<li/>').append($.tmpl('tweetTemplate', {tweet:tweet}).data('tweet', tweet));
            $list.append($tweet);
        });

        var self = this;
        $list.on('click', 'input[name="is_active"]', function (e) {
            var $clickedTweet = $(e.target).closest('.tweet');
            var tweet = $clickedTweet.data('tweet');
            tweet.is_active = $(e.target).attr('checked') ? '1' : '0';
            $.post(
                self.options.url.update + '?id=' + tweet.id,
                {
                    'Tweet':tweet
                },
                function (tweet) {
                    var $tweet = $.tmpl('tweetTemplate', {tweet:tweet}).data('tweet', tweet);
                    $clickedTweet.replaceWith($tweet);
                },
                'json'
            );
        });

        this.append($list);
    }
});