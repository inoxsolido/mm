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
                    ['label' => 'Album', 'icon' => 'folder-o', 'url' => ['album/index']],
                    ['label' => 'Media Type Extension', 'icon'=> 'filter', 'url' => ['media-type/index']],
                    ['label' => 'Related Words', 'icon' => 'th-list', 'url' => ['relation/index']],
                    ['label' => 'Frequency Words', 'icon' => 'list-ol', 'url' => ['word/index']],
                    ['label' => 'Dictionary', 'icon' => 'align-left', 'url' => ['dictionary/index']],
                    ['label' => 'User', 'icon' => 'users', 'url' => ['user/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                    ['label' => 'Settings', 'icon' => 'cog', 'url' => ['/settings/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                ],
            ]
        ) ?>
        </div>
        <!-- /.tab-pane -->
    
</aside><!-- /.control-sidebar -->