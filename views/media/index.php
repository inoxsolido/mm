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
                'attribute' => 'file_upload_date',
                'value' => function($model){ return Yii::$app->utility->strDateReformat($model['file_upload_date'], 'd/m/Y H:i');},
                'format' => 'html',
                'filter' => kartik\daterange\DateRangePicker::widget([
                        'name'=>'dr',
                        'hideInput'=>true,
                        'convertFormat'=>true,
                        'pluginOptions'=>[
     
                            'timePicker'=>true,
                            "timePicker24Hour"=> true,
                            'timePickerIncrement'=>1,
                            'locale'=>['format'=>'Y-m-d H:i:s'],
                        ],
                        'pluginEvents'=>[
                            'cancel.daterangepicker' => 'function() { $("[name=dr]").val(""); $("[name=dr]").parent().find(".range-value").text(""); }',
                        ]
                    ]),
            ],
            [
                'header' => 'สิทธิ์เข้าถึง',
                'attribute' => 'is_public',
                'value' => function($data){return @$data['is_public']==0?'Private': 'Public';},
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
