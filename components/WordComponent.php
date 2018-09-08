<?php

namespace app\components;

use yii\base\Component;

class WordComponent extends Component
{

    /**
     * This method use to update Dictionary
     * @return boolean
     */
    public function frequencyWordToDictionary()
    {

        //retrive frequency_rate from settings


        return true;
    }

    /**
     * This method use to split sentence to words
     * @param string $sentence raw sentence
     * @return array of words with lower case
     */
    public function split($sentence)
    {
        if(!$sentence) return [];
        else $sentence = strtolower ($sentence);
        $sentence = trim($sentence);
        $words = []; //return value
        //retrive all words from dictionary
        $dictionary = \app\models\Dictionary::find()->orderBy(["length" => SORT_DESC])->select(['word'])->column();

        $prepared_words = [];

        //create pattern to capture
        $pattern = "/" . implode("|", $dictionary) . "/";
//        echo '<pre>';
//        print_r($pattern);
//        echo '</pre>';
//        die();
        //capture
        preg_match_all($pattern, $sentence, $prepared_words, PREG_OFFSET_CAPTURE);
        $missmatched_with_pipe = preg_replace_callback($pattern, function($matched)
        {
            return str_repeat("|", strlen($matched[0]));
        }, $sentence);
        //capture missmatched
        preg_match_all('/\|?[a-zA-Z0-9]+|\|?[ก-๙]+/', $missmatched_with_pipe, $missmatched, PREG_OFFSET_CAPTURE);

        //filter | out from words
        $missmatched = array_map(function($value)
        {
            if (preg_match("/\|/", $value[0]))
                return [str_replace("|", "", $value[0]), $value[1] - 1];
            else
                return [$value[0], $value[1]];
        }, $missmatched[0]);


        if (empty($prepared_words))
        {
            $words = [$sentence];
        }
        else
        {
            //put missmatched to prepared_word
            $prepared_words = array_merge($prepared_words[0], $missmatched);
            //sort array by position
            $pass = uasort($prepared_words, function($a, $b)
            {
                return $a[1] - $b[1];
            });

            if (!$pass) //sort_error
                return [$sentence];
            //recreate array
            $words = array_map(function($key){
                return $key[0];
            }, $prepared_words);
            $words = array_values($words);
            
            
        }

        return $words;


        //retrive all words from frequency_relation with frequency_rate
//        $relation = \app\models\FrequencyRelation::find()->where([">=", "frequency", \app\models\Setting::findOne()->frequency_relation_rate]);
    }

}
