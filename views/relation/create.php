<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FrequencyRelation */

$this->title = 'Create Related Word';
$this->params['breadcrumbs'][] = ['label' => 'Related Word', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="frequency-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
