<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of WorkerController
 *
 * @author Ball
 */
class WorkerController extends UrbanIndo\Yii2\Queue\Worker\Controller
{
    public function actionBar($param1, $param2)
    {
        try {
            // do some stuff
        } catch (\Exception $ex) {
            \Yii::error('Ouch something just happened');
            return false;
        }
    }
}
