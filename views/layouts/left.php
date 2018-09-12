<aside class=" main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->

        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => [
                    ['label' => '', 'options' => ['class' => 'header']],
                    ['label' => 'Search', 'icon' => 'search', 'url' => ['/search/index']],
                    ['label' => 'Directory Search', 'icon' => 'folder', 'url' => '#'],
                    ['label' => 'Album', 'icon' => 'book', 'url' => '#'],
                    ['label' => 'Video', 'icon' => 'file-video-o', 'url' => '#'],
                    ['label' => 'Image', 'icon' => 'file-image-o', 'url' => '#'],
                    ['label' => 'Audio', 'icon' => 'file-audio-o', 'url' => '#'],
                    ['label' => 'Document', 'icon' => 'file-text-o', 'url' => '#'],
                    ['label' => 'Etc.', 'icon' => 'file-o', 'url' => '#'],
                    
                ],
            ]
        ) ?>

    </section>

</aside>
