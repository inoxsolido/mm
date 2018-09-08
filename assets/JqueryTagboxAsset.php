<?php
/**
 * Created by PhpStorm.
 * User: Bravo
 * Date: 11/1/2017
 * Time: 4:45 AM
 */

namespace app\assets;

use yii\web\AssetBundle;


class JqueryTagboxAsset extends AssetBundle
{
    public $sourcePath = '@webroot/lib/jquerytagbox';
    public $css = [
        'css/jquery.tagbox.css',
    ];
    public $js = [
        'js/jquery.tagbox.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $publishOptions = [
        'forceCopy' => true,
    ];
    
}