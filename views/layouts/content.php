<?php

use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
?>
<style>
    
#right>li>a>span{
    display:inline-block !important;
}
#right > li.header{
    display:inline-block !important;
}
</style>
<div class="content-wrapper" style="background-color:white;">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header']))
        {?>
            <h1><?=$this->blocks['content-header']?></h1>
            <?php }
            else
            {?>
            <h1>
                <?php
//                if ($this->title !== null) {
//                    echo \yii\helpers\Html::encode($this->title);
//                } else {
//                    echo \yii\helpers\Inflector::camel2words(
//                        \yii\helpers\Inflector::id2camel($this->context->module->id)
//                    );
//                    echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
//                } 
                ?>
            </h1>
        <?php }?>

        <?=
        Breadcrumbs::widget(
                [
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]
        )
        ?>
    </section>

    <section class="content">
<?=Alert::widget()?>
<?=$content?>
    </section>
</div>

<!--<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.0
    </div>
    <strong>Copyright &copy; 2014-2015 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
    reserved.
</footer>-->

<aside class="control-sidebar control-sidebar-light">
    <!-- Create the tabs -->
    <!-- Tab panes -->
    

        <!-- Settings tab content -->
        <div class="sidebar" id="control-sidebar-settings-tab">
            <?=                dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'id'=>'right'],
                'items' => [
                    ['label' => 'Managements', 'options' => ['class' => 'header']],
                    ['label' => 'Media', 'icon' => 'film', 'url' => ['media/index']],
                    ['label' => 'Album', 'icon' => 'folder-o', 'url' => "#"],
                    ['label' => 'Type of Media', 'icon' => 'file-o', 'url' => "media-type/index"],
                    ['label' => 'Related Words', 'icon' => 'th-list', 'url' => "#"],
                    ['label' => 'Frequency Words', 'icon' => 'list-ol', 'url' => "#"],
                    ['label' => 'Dictionary', 'icon' => 'align-left', 'url' => ['dictionary/index']],
                    ['label' => 'User', 'icon' => 'users', 'url' => ['user/index']],
                    ['label' => 'Settings', 'icon' => 'cog', 'url' => ['/settings/index']],
                ],
            ]
        ) ?>
        </div>
        <!-- /.tab-pane -->
    
</aside><!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>