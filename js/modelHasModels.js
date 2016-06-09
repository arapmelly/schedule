/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/19/11
 * Time: 6:14 PM
 * To change this template use File | Settings | File Templates.
 */
$(function () {
    $.fn.modelHasModels = function (options) {
        $(options.sel.modelHasModelsTemplate).template('modelHasModels');
        return this.each(function (idx, elem) {
            var $elem = $(elem);
            $.tmpl('modelHasModels', {
                title: options.title,
                model:options.model,
                hasModels:options.hasModels
            }).appendTo($elem);
            var $select = $elem.find('.hasModels');
            $select.chosen().change(function (e) {
                var $button = $(e.target).closest('.modelHasModels').find('[name="save"]');
                $button.show();
            });

            $elem.on('click', '[name="save"]', function (e) {
                var $button = $(e.target);
                $.post(options.url.updateHasModels + '?id=' + options.model.id, {
                    hasModels:$select.val()
                }, function () {
                    $button.hide();
                })
            });
        });
    }
});