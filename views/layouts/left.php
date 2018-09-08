<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->

        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => [
                    ['label' => '', 'options' => ['class' => 'header']],
                    ['label' => 'Search', 'icon' => 'search', 'url' => ['/site/index']],
                    
                ],
            ]
        ) ?>

    </section>

</aside>
