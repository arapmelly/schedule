/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/19/11
 * Time: 3:19 PM
 * To change this template use File | Settings | File Templates.
 */
$(function () {
    $.fn.itemSelector = function (options) {
        return this.each(function (idx, elem) {
            var $elem = $(elem);

            var $itemSelect = $('<select/>');
            $.map(options.items, function (item) {
                var $option = $('<option/>').text(item.name).data('item', item).val(item.id);
                $itemSelect.append($option);
            });
            $elem.append($itemSelect);

            var $delete = $('<input type="button" value="X">');
            $delete.click(function () {
                if (!confirm('Delete?')) return;
                $.post(options.url.deleteItem + '?id=' + $itemSelect.val(), function () {
                    $itemSelect.find(':selected').remove();
                    $itemSelect.change();
                });
            });
            $elem.append($delete);
        });
    }
});