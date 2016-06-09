<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/30/11
 * Time: 12:13 AM
 * To change this template use File | Settings | File Templates.
 */
 
?>

<table>
    <?php $i = 0; foreach($tweets as $tweet):?>
        <tr>
            <td style="color: red"><?php echo ++$i?></td>
            <td><?php echo $tweet['text']?></td>
            <td><?php echo $tweet['times']?></td>
            <?php if(($cropped = Tweet::crop($tweet['text'])) != $tweet['text']):?>
                <td style="color: red"><?php echo $cropped?></td>
            <?php endif?>
        </tr>
    <?php endforeach?>
</table>