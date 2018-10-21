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
                    ['label' => 'Directory Search', 'icon' => 'folder', 'url' => ['/search/directory']],
                    ['label' => 'Album', 'icon' => 'book', 'url' => ['/search/album']],
                    ['label' => 'Video', 'icon' => 'file-video-o', 'url' =>['/search/index/?v=1']],
                    ['label' => 'Image', 'icon' => 'file-image-o', 'url' => ['/search/index/?i=1']],
                    ['label' => 'Audio', 'icon' => 'file-audio-o', 'url' => ['/search/index/?a=1']],
                    ['label' => 'Document', 'icon' => 'file-text-o', 'url' => ['/search/index/?d=1']],
                    ['label' => 'Etc.', 'icon' => 'file-o', 'url' => ['/search/index/?e=1']],
                    
                ],
            ]
        ) ?>

    </section>

</aside>
