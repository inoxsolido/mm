<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Settings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="settings-form">

    <?php $form = ActiveForm::begin(); ?>

    

    <?= $form->field($model, 'frequency_word_rate')->textInput() ?>

    <?= $form->field($model, 'frequency_relation_rate')->textInput() ?>

    <?= $form->field($model, 'ftp_host')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ftp_user')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ftp_password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ftp_part')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'http_part')->textInput(); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
