<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/19/11
 * Time: 3:30 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<script type="text/x-jquery-tmpl" id="hashTagTemplate">
    <div class="groupItem">
        <input type="button" name="delete" value="delete">
        <span class="text">${item.text}</span>
    </div>
</script>

<script type="text/x-jquery-tmpl" id="addHashTagTemplate">
    <div class="newItem">
        <input type="text" name="text">
        <input type="button" name="add" value="add">
    </div>
</script>