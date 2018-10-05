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
     * @param string $subject raw sentence
     * @return array of words with lower case
     */
    public function split($subject){
        if(!$subject) return [];
        else $subject = strtolower ($subject);
        
        $dictionary = \app\models\Dictionary::find()->select(['word', 'length'])->orderBy(['length'=>SORT_DESC])->asArray()->all();
        $match_result = [];
        foreach ($dictionary as $word){
            $length = $word['length'];
            $pos = strpos($subject, $word['word']);
            if(!$pos) continue;
            else{
                $replacement = str_repeat('/', $length);
                $subject = $this->mb_str_replace($word['word'], $replacement, $subject, 2);
                $match_result[] = [$word['word'],$pos];
            }
        }
        if(empty($match_result)) return [$subject];
        
        $mismatch_result = [];
        preg_match_all('/[a-zA-Z0-9ก-เแ-๙]/', $subject, $mismatch_result, PREG_OFFSET_CAPTURE);
        if($mismatch_result) $mismatch_result = $mismatch_result[0];//remove array dimension
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
