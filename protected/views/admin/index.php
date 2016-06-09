<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/23/12
 * Time: 9:43 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<a class="btn success" href="<?php echo $this->createUrl('backup/restore', array('key' => Yii::app()->params['backupKey']))?>">Restore</a>
<a class="btn success"
   href="<?php echo $this->createUrl('updateApplication', array('id' => TwitterApplication::getDefault()->id))?>">Update
    Application Key </a>
<a class="btn <?php echo AppHelper::getAppActive() ? 'error' : 'success'?>"
   href="<?php echo $this->createUrl('toggleApplicationActive')?>">
    <?php echo AppHelper::getAppActive() ? 'Deactivate' : 'Activate'?> </a>
