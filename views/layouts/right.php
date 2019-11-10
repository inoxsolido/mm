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
                    ['label' => 'Media Type Extension', 'icon'=> 'filter', 'url' => ['media-type/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                    ['label' => 'Related Word', 'icon' => 'th-list', 'url' => ['relation/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                    ['label' => 'Frequency Words', 'icon' => 'list-ol', 'url' => ['word/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                    ['label' => 'Dictionary', 'icon' => 'align-left', 'url' => ['dictionary/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                    ['label' => 'User', 'icon' => 'users', 'url' => ['user/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                    ['label' => 'Settings', 'icon' => 'cog', 'url' => ['/settings/index'], 'visible'=>Yii::$app->user->identity != NULL && Yii::$app->user->identity->getIsAdmin()],
                ],
            ]
        ) ?>
        </div>
        <!-- /.tab-pane -->
    
</aside><!-- /.control-sidebar -->