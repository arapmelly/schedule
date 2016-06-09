<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/5/11
 * Time: 11:40 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<script id="tweetTemplate" type="text/x-jquery-tmpl">
    <div class="tweet">
        {{if tweet.is_active == '1'}}
        <input type="checkbox" name="is_active" checked=""/>
        <span class="label success">Enabled</span>
        {{else}}
        <input type="checkbox" name="is_active"/>
        <span class="label warning">Disabled</span>
        {{/if}}
        <span class="tweet-text">${tweet.text}</span>
    </div>
</script>