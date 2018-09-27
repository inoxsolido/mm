<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FrequencyWord */

$this->title = 'Create Frequency Word';
$this->params['breadcrumbs'][] = ['label' => 'Frequency Words', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="frequency-word-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
