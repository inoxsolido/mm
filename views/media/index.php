<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\MediaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Media';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-index">

    <h1><?= Html::encode($this->title) ?></h1>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'album_id',
                'value' => 'album.name'
            ],
            [
                'attribute' => 'tags',
                'format' => 'ntext',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'media_type_id',
                'value' => 'mediaType.name'
            ],
            [
                'header' => 'สิทธิ์เข้าถึง',
                'attribute' => 'is_public',
                'value' => function($data){return $data->is_public?"Public":"Private";},
                'filter' => [0=>'Private',1=>'Public'],
                'format' => 'text',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => "{view} {update} {delete}",
                'buttons' => [
                    'view' => function ($model, $key, $index) {
                        $url = Url::to(["media/detail",'id'=>$index]);
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('yii', 'View'),
                        ]);
                    },
                    'update' => function ($model, $key, $index) {
                        $url = Url::to(["media/media-edit",'id'=>$index]);
                        return Html::a('<span class="glyphicon glyphicon-pencill"></span>', $url, [
                            'title' => Yii::t('yii', 'View'),
                        ]);
                    },


                ],

            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
