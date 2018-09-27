<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FrequencyRelation */

$this->title = 'Update Frequency Relation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Frequency Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="frequency-relation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
