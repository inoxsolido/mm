<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\MediaTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Media Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'extension:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'visibleButtons' => [
                    'update'=>function($model, $key, $index){
                        return $model->id !== 5;
                    }
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
