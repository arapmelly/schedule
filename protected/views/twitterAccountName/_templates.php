<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/19/11
 * Time: 3:30 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<script type="text/x-jquery-tmpl" id="accountRetweetAccountTemplate">
    <div class="modelHasModels">
        <a href="<?php echo $this->createUrl('twitterAccount/update')?>/${model.id}"><span>${model.screen_name}</span></a>
        <select class="hasModels" multiple="multiple">
            {{each hasModels}}
            <option value="${$value.id}"
            {{if $value.has == 1}} selected="selected"{{/if}} >
            ${$value.name}
            </option>
            {{/each}}
        </select>
        <input type="button" class="btn primary" name="save" value="Save" style="display:none">
    </div>
</script>
<script type="text/x-jquery-tmpl" id="modelHasModelsTemplate">
    <div class="modelHasModels">
        <span>${model.screen_name} ${model.name}</span>
        <select class="hasModels" multiple="multiple">
            {{each hasModels}}
            <option value="${$value.id}"
            {{if $value.has == 1}} selected="selected"{{/if}} >
            ${$value.name}
            </option>
            {{/each}}
        </select>
        <input type="button" class="btn primary" name="save" value="Save" style="display:none">
    </div>
</script>