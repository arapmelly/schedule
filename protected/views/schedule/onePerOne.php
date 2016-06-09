<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/29/11
 * Time: 11:58 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<table>
    <?php foreach ($posts as $post): ?>
    <tr>
        <td><?php echo $post['account']->screen_name?></td>
        <?php if (!is_array($post['variants'])): ?>
        <td><?php echo $post['variants']->text?></td>
        <?php endif ?>
    </tr>
    <?php if (is_array($post['variants'])): ?>
        <?php foreach ($post['variants'] as $variant): ?>
            <tr></tr>
            <tr>
                <td><?php echo $variant->text?></td>
            </tr>
            <?php endforeach ?>
        <?php endif ?>
    <?php endforeach?>
</table>