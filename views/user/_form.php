<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>
    <?php if($model->getScenario() === 'personal'): ?>
    <?= $form->field($model, 'password_old')->passwordInput() ?>
    <?php endif; ?>
    
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'value'=>'']) ?>
    
    <?= $form->field($model, 'password_confirm')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    
    <?php $imageUrl = '/'.$model->image_path;
    echo $form->field($model, 'image_file')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'showUpload' => false,
            'showRemove' => true,
            'showCaption' => false,
            'fileActionSettings' => [
                'showRemove' => false,
                'showDrag' => false,
                'showZoom' => true,
            ],
            'browseClass' => 'btn btn-primary ',
            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
            'browseLabel' => 'เลือกรูปภาพ',
            'initialPreview'=> $model->image_path?[$imageUrl]:'',
            'initialPreviewAsData'=> true,
            'initialPreviewShowDelete'=> false,
            'initialPreviewFileType'=> 'image',
        ],
        'options' => ['accept' => 'image/*']
    ]);
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
    <?php if($model->getScenario() !== 'personal'): ?>
    <?= $form->field($model, 'user_type_id')->dropDownList(yii\helpers\ArrayHelper::map($selector, 'id', 'name')) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
