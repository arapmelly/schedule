/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/19/11
 * Time: 3:19 PM
 * To change this template use File | Settings | File Templates.
 */
$(function () {
    $.fn.groupSelector = function (options) {
        $(options.sel.itemTemplate).template('item');
        $(options.sel.addItemTemplate).template('addItem');
        return this.each(function (idx, elem) {
            var $elem = $(elem);

            var $groupSelect = $('<select/>');
            $.map(options.groups, function (gr) {
                var $option = $('<option/>').text(gr.name).data('group', gr).val(gr.id);
                $groupSelect.append($option);
            });
            $elem.append($groupSelect);

            var $delete = $('<input type="button" name="deleteGroup" value="X">');
            $delete.click(function () {
                if (!confirm('Delete GROUP?')) return;
                $.post(options.url.deleteGroup + '?id=' + $groupSelect.val(), function () {
                    $groupSelect.find(':selected').remove();
                    $groupSelect.change();
                });
            });
            $elem.append($delete);

            $groupSelect.change(
                function (e) {
                    var $option = $groupSelect.find(':selected');
                    $.post(options.url.itemsList + '?id=' + $option.val(), function (items) {
                        $(options.sel.items).empty();
                        $.map(items, function (it) {
                            $(options.sel.items).append($.tmpl('item', {item:it}).data('item', it));
                        });
                    }, 'json');
                    $elem.trigger('groupChanged', $option.data('group'));
                });

            $(options.sel.items).on('click', '[name="delete"]', function (e) {
                if (!confirm('Sure?')) return;
                var $item = $(e.target).closest('.groupItem');
                var item = $item.data('item');
                $.post(options.url.deleteItem + '?id=' + item.id, function () {
                    $item.remove();
                });
            });

            var $add = $.tmpl('addItem');
            $elem.append($add);
            $add.find('[name="add"]').click(function (e) {
                var $text = $add.find('[name="text"]');
                if ($.trim($text.val()) == '') return;
                var post = {};
                post[options.itemModel] = {text:$text.val() };
                $.post(options.url.addItem + "?group_id=" + $groupSelect.val(),
                    post,
                    function (item) {
                        $text.val('');
                        $(options.sel.items).append($.tmpl('item', {item:item}).data('item', item));
                    }, 'json')
                    .error(function () {
                        alert('Exists.')
                    });
            });
            $add.find('[name="text"]').keypress(function (e) {
                if (e.keyCode == 13)
                    $add.find('[name="add"]').click();
            });

            setTimeout(function () {
                $groupSelect.change()
            }, 100);
        });
    }
});