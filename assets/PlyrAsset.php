<?php

namespace app\assets;

use yii\web\AssetBundle;


class PlyrAsset extends AssetBundle
{
    public $sourcePath = '@webroot/lib/plyr';
    public $css = [
        'plyr.css',
    ];
    public $js = [
        'plyr.js',
    ];


}