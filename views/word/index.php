<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\FrequencyWordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Frequency Words';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="frequency-word-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Frequency Word', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'word:ntext',
            [
                'attribute' => 'frequency',
                'filter' => Yii::$app->controller->renderPartial('/template/input_with_comparation_dropdown',['attributeName'=>'FrequencyWordSearch[frequency]'])
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
