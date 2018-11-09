<?php

namespace app\assets;
use yii\web\AssetBundle;
/**
 * Description of JqueryAutoCompleteAsset
 *
 * @author Bravo
 */
class JqueryAutoCompleteAsset extends AssetBundle {
    public $sourcePath = '@webroot/lib/jquery_autocomplete';
    public $css = [
        'jquery-ui.min.css',
    ];
    public $js = [
        'jquery-ui.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
