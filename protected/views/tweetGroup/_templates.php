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
        <a href="<?php echo $this->createUrl('tweet/update')?>/${item.id}"><span class="label">Edit</span></a>
        <span class="text">${item.text}</span>
    </div>
</script>

<script type="text/x-jquery-tmpl" id="addHashTagTemplate">
    <div class="newItem">
        <input type="text" name="text">
        <input type="button" name="add" value="add">
    </div>
</script>

<script type="text/x-jquery-tmpl" id="modelHasModelsTemplate">
    <div class="modelHasModels">
        <span>${model.name}</span>
        <select class="hasModels" multiple="multiple">
            {{each hasModels}}
            <option value="${$value.id}" {{if $value.has == 1}} selected="selected"{{/if}} >
            ${$value.name}
            </option>
            {{/each}}
        </select>
        <input type="button" class="btn primary" name="save" value="Save" style="display:none">
    </div>
</script>