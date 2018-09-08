<?php
/* @var $this yii\web\View */
/* @var $setting \app\models\Settings */
use yii\helpers\Html;

app\assets\JqueryAutoCompleteAsset::register($this);
?>
<div class="search-main">
    <center>
    <div class="section-search-box" style="display:inline-block; min-width:500px; max-width:50%;">
        <?=Yii::$app->controller->renderPartial('search_box',['selectionMediaType'=>$selectionMediaType])?>
    </div><!--include search option box-->
    </center>
    
    <div class="section-content">
        <?=Yii::$app->controller->renderPartial('search_content',[
            'dataProvider' => $dataProvider,
            'setting' => $setting,
        ])?>
    </div>
    
</div>
<?php
$this->registerJsFile("@web/js/search.js",  ['depends' => [\yii\web\JqueryAsset::className()]]);
?>