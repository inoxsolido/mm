<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Dictionary */

$this->title = 'Add Multiple Word';
$this->params['breadcrumbs'][] = ['label' => 'Dictionary', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-create">

    <h1><?= Html::encode($this->title) ?><?=Html::tag('small', '&nbsp;&nbsp;?', [
    'title'=>'แยกคำโดยใช้ Enter',
    'data-toggle'=>'tooltip',
    'style'=>'text-decoration: underline; cursor:pointer;'
])?></h1>

    <?= $this->render('_form_multiple', [
        'model' => $model,
    ]) ?>

</div>
