<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
$user = Yii::$app->user->identity or (!Yii::$app->user->isGuest);
?>

<header class="main-header">
    
    <nav class="navbar navbar-static-top" role="navigation" style="padding-right:15px; margin-left:auto ">

        <div class="navbar-header" style="float:left;">
            <a class="logo" href="<?= Yii::$app->homeurl ?>" style="padding:0 !important; float:left;width:auto;">
                
                <?= Html::img(Yii::$app->request->baseUrl."/img/LOGO230.png", ['width' => 230, 'height' => 50, 'class' => '', 'style' => 'min-width:230px !important; margin:0px; display:inline-block;']) ?>
            </a>
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
        </div>


        <div class="navbar-custom-menu">
            
            <ul class="nav navbar-nav">
                <?php if ($user):?>
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-upload"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= Url::to(["upload/video"])?>"><i class="fa fa-file-video-o"></i>&nbsp;Video</a></li>
                            <li><a id="upload-image" href="<?= Url::to(["upload/image"])?>"><i class="fa fa-file-image-o"></i>&nbsp;Image</a></li>
                            <li><a href="<?= Url::to(["upload/other"])?>"><i class="fa fa-file-audio-o"></i>&nbsp;Audio</a></li>
                            <li><a href="<?= Url::to(["upload/other"])?>"><i class="fa fa-file-text-o"></i>&nbsp;Document</a></li>
                            <li><a href="<?= Url::to(["upload/other"])?>"><i class="fa fa-file-o"></i>&nbsp;Other</a></li>
                        </ul>
                    </li>


                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php
                            if (Yii::$app->user->identity->image_path): ?>
                            <?=$user->image?>
                            <?php else: ?>
                                <span class="user-image"><i class="fa fa-user-circle fa-2x"></i></span>
                            <?php endif;?>
                            <span class="hidden-xs"><?=Yii::$app->user->identity->username?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <?php
                                if (Yii::$app->user->identity->image_path): echo Yii::$app->user->identity->imageCircle;
                                else:?>
                                    <span class="user-circle"><i class="fa fa-user-circle fa-4x" style="color:white;"></i></span>
                                <?php endif;?>

                                <p>
                                    <?=Yii::$app->user->identity->name . "&nbsp;&nbsp;" . Yii::$app->user->identity->surname?>
                                    <small style="text-transform: uppercase; "><?=Yii::$app->user->identity->userType->name?></small>
                                </p>
                            </li>

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= Url::to(['/user/update/', 'id'=>Yii::$app->user->getId()]) ?>" class="btn btn-default btn-flat">Change Profile</a>
                                </div>
                                <div class="pull-right">
                                    <?=Html::a('Sign out', ['/site/logout'], ['data-method' => 'post', 'class' => 'btn btn-default btn-flat'])?>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="">
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gear"></i></a>
                    </li>
                <?php else:?>
                    <li class="user user-menu dropdown">
                        <a href="<?=Url::toRoute(['/login'])?>" class="dropdown-toggle">
                            <span class="user-image"><i class="fa fa-user-circle fa-2x"></i></span>
                            Sign in
                        </a>
                    </li>
                <?php endif;?>
                    
            </ul>
        </div>
    </nav>
</header>
