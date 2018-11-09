<?php

namespace app\components;

use yii\base\Component;
use app\models\Settings;
use app\models\FrequencyWord;
use app\models\Dictionary;

class WordComponent extends Component
{

    /**
     * This method use to update frequency word's counter and add to Dictionary
     * @param string $word query word
     * @param \app\models\Settings $setting  Site settings object
     * @return boolean
     */
    public function frequencyWordToDictionary($word, $setting=null)
    {
        if($word == '') return false;
        if($setting == null) $setting = Settings::getSetting();
        
        $dictionaryModel = Dictionary::find()->where(['word'=>$word])->one();
        if($dictionaryModel) return false; //reject if exist in Dictionary
        $wordModel = FrequencyWord::find()->where(['word'=>$word])->one();
        if($wordModel === null){ 
            $wordModel = new FrequencyWord();
            $wordModel->word = $word;
            $wordModel->frequency = 1;
            $result = $wordModel->save();
            if(!$result) return false; //reject if fail on saving
        }
        if(!$wordModel->updateCounters(['frequency'=>1])) return true;
        
        if($wordModel->frequency >= $setting->frequency_word_rate){
            if($dictionaryModel === null){
                $dictionaryModel = new Dictionary();
                $length = mb_strlen($wordModel->word);
                $dictionaryModel->word = $wordModel->word;
                $dictionaryModel->length = $length;
                if(!$dictionaryModel->save()) return false; //reject if fail on saving
            }
            return $wordModel->delete();
        }
        return false; //reject if fail on updating counter
        
    }

    /**
     * This method use to split sentence to words
     * @param string $subject raw sentence
     * @return array of words with lower case
     */
    public function split($subject){
//        $start = microtime(true);
        if(!$subject) return [];
        else $subject = strtolower ($subject);
        
        $len = mb_strlen($subject);
        
        $dictionary = \app\models\Dictionary::find()->select(['word', 'length'])->orderBy(['length'=>SORT_DESC])->where(['<=','length',$len])->asArray()->all();
        $match_result = [];
//        $time_elapsed_secs = microtime(true) - $start;
//        print_r($time_elapsed_secs);
        foreach ($dictionary as $word){
            $length = $word['length'];
            $pos = strpos($subject, $word['word']);
            if($pos === FALSE) continue;
            else{
                $replacement = str_repeat('/', $length);
                $subject = $this->mb_str_replace($word['word'], $replacement, $subject, 2);
                $match_result[] = [$word['word'],$pos];
            }
        }
        
        
        if(empty($match_result)) return [$subject];
        
        $mismatch_result = [];
        preg_match_all('/[a-zA-Z0-9]|[ก-เแ-๙]+/', $subject, $mismatch_result, PREG_OFFSET_CAPTURE);
        if($mismatch_result) $mismatch_result = $mismatch_result[0];//remove array dimension
//        print_r($mismatch_result);
        $merged_result = array_merge($match_result,$mismatch_result);
        uasort($merged_result, function($a,$b){ return $a[1]-$b[1]; });//sort result by index ASC
        
        $words = array_map(function($key){
                return $key[0];
            }, $merged_result);
        $words = array_values($words);
        return $words;
    }
    
    /**
     * mb_str_replace
     * str_replace for Multi-byte string
     * @param string $needle  The regular expression pattern.
     * @param string $replacement  The replacement value that replaces found $needle values. An array may be used to designate multiple replacements.
     * @param string $subject  The string or array being searched and replaced on.
     * @param int $limit  [optional] limit of mb_split result array.
     * @return string
     */
    private function mb_str_replace($needle, $replacement, $subject, $limit = -1) {
        return implode($replacement, mb_split($needle, $subject, $limit));
    }

}
