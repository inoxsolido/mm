<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FrequencyRelation */

$this->title = 'Create Frequency Relation';
$this->params['breadcrumbs'][] = ['label' => 'Frequency Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="frequency-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
