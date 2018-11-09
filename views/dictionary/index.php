<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DictionarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dictionary';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Add Word (Single)', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Add Word (multiple)', ['create-multiple'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'word',
            [
                'attribute' => 'length',
                'filter' => Yii::$app->controller->renderPartial('/template/input_with_comparation_dropdown',['attributeName'=>'DictionarySearch[length]'])
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template'=>"{update}{delete}",
                ],
        ],
    ]); ?>
</div>
