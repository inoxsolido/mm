<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

/**
 * Description of UtilityComponent
 *
 * @author Bravo
 */
class UtilityComponent extends \yii\base\Component {

    static function array_key_exists_or($keys, $search_r) {
        $keys_r = split('\|', $keys);
        foreach ($keys_r as $key)
            if (!array_key_exists($key, $search_r))
                return false;
        return true;
    }

    static function array_key_exists_and($keys, $search_r) {
        $keys_r = split('\|', $keys);
        foreach ($keys_r as $key)
            if (!array_key_exists($key, $search_r))
                return false;
        return true;
    }
    /*
     *  this function use to display variable
     * @var mixed variable to display
     * @stop bool true for stop interpreter false for not
     */
    
    static function debug($var,$vdump=FALSE, $stop=TRUE){
        echo '<pre>';
        if($vdump)
            var_dump ($var);
        else
            print_r($var);
        echo '</pre>';
        if($stop) die();
    }
    /*
     * This function use to re-format date(str)
     * @source string source string in date format
     * @format string new date format to change
     * @return string with new format or '' for fail
     */
    
    static function strDateReformat($source, $format){
        $date = date_create($source);
        $result = date_format($date,$format);
        if($result)
            return $result;
        else
            return '';
        
    }

}
