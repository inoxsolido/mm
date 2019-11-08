<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AlbumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Albums';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'name',
                'attribute' => 'name',
                'value' => function($model){
                    if(@$model->name != null)
                        return Html::a($model->name, Url::to(['media/index','MediaSearch[album_id]'=>$model->name]));
                    return null;
                },
                'format'=>'html',
            ],
            [
                'attribute' => 'tags',
                'value'=>function($model){
                    if(@$model->tags == null)return null;
                    $tags = explode(',', $model->tags);
                    $html = '';
                    foreach($tags as $tag){
                        $html .= Html::a($tag, Url::current(['AlbumSearch[tags]'=>$tag])).'&nbsp;&nbsp;';
                    }
                    return $html;
                },
                'format' => 'html',
                'enableSorting' => false,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'detail' =>function($url, $model, $key){
                        $title = Yii::t('yii', 'detail');
                        $options = [
                            'title' => $title,
                            'aria-label' => $title,
                            'data-pjax' => '0',
                        ];
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-eye-open"]);
                        return Html::a($icon, $url, $options);
                    },
                ],
                'template'=>'{detail}{update}{delete}'
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
