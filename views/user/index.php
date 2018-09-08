<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'header'=>'รูปภาพ',
                'format'=>'html',
                'value'=> function($data){
                    if($data->image_path)
                        return \yii\helpers\Html::img("/".$data->image_path, ['class'=>'img-circle', 'style'=>'height:50px']);
                    else
                        return '<span class="user-image"><i class="fa fa-user-circle fa-4x"></i></span>';
                },
            ],
//            'id',
            'username',
//            'password',
            'email:email',
            'name',
            'surname',
            [
                'attribute'=>'type',
                'value'=>'userType.name',
            ],
            // 'user_type_id',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}'
            ],
        ],
    ]); ?>
</div>
