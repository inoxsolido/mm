<?php


namespace app\assets;

use yii\web\AssetBundle;

class JqueryFormAsset extends AssetBundle
{
    public $sourcePath = '@webroot/lib/jqueryform';
    public $css = [
        'progressbar.css',
    ];
    public $js = [
        'jquery.form.min.js',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
