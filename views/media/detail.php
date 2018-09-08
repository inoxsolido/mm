<?php
/* @var $this yii\web\View */
/* @var $media \app\models\Media */

\app\assets\PlyrAsset::register($this);
use \richardfan\widget\JSRegister;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<?php if($media->media_type_id == 1): ?>
<div style="width:400px; display:inline-block">
<video poster="<?=$media->getThumbnailHttpPath()?>" src="<?=$media->getHttpPath()?>" id="player" controls data-plyr-config='{ "title": "<?=$media->name?>"}'></video>
</div>
<?php elseif($media->media_type_id == 2): ?>

<?php endif; ?>

<?php JSRegister::begin(['position'=>\yii\web\View::POS_READY]) ?>
<script>
    const player = new Plyr('#player');
</script>
<?php JSRegister::end(); ?>