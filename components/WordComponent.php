<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Settings;
use app\models\FrequencyWord;
use app\models\Dictionary;
use app\models\FrequencyRelation;

class WordComponent extends Component
{
    /*
     * This method use to updateRelationWord's counter
     * @param array $splited_q present query splited word 
     * @param array $splited_oq previous query splited word
     */
//    public function updateRelationWord($splited_q, $splited_oq){
//        $related_word_query = "INSERT INTO relation_word ";
//        $value_counter = 0;
//        $related_word_query_values = [];
//
//        foreach($splited_q as $kq => $vq){
//            for($i=$kq+1; $i<count($splited_oq); $i++){
//                if($value_counter > 0) $related_word_query .= ",";
//                $related_word_query .= "(\"$vq\", \"{$splited_q[$i]}\")";
//                $value_counter+=1;
//            }
//            foreach($splited_oq as $koq => $voq){
//                if($value_counter > 0) $related_word_query .= ",";
//                $related_word_query .= "(\"$vq\", \"$voq\")";
//                $value_counter+=1;
//            }
//        }
//        return $related_word_query;
//        return Yii::$app->getDb()->createCommand($related_word_query)->execute();
//    }
    public function updateRelationWord($splited_q, $splited_oq = []){
        if(!$splited_oq) $splited_oq = [];
        $i = 0;
        foreach($splited_q as $kq => $vq){
            $i=$kq+1;
            if($i<count($splited_q))
            {
                $self_relation = FrequencyRelation::findByWords($vq, $splited_q[$i]);
                if(!$self_relation) {
                    $self_relation = new FrequencyRelation;
                    $self_relation->word1 = $vq;
                    $self_relation->word2 = $splited_q[$i];
                    $self_relation->frequency = 1;
                    $self_relation->save(false);
                }else{
                    $self_relation->updateCounters(['frequency'=>1]);
                }
                
            }
            foreach($splited_oq as $koq => $voq){
                $cross_relation = FrequencyRelation::findByWords($vq, $voq);
                if(!$cross_relation){
                    $cross_relation = new FrequencyRelation;
                    $cross_relation->word1 = $vq;
                    $cross_relation->word2 = $voq;
                    $cross_relation->frequency = 1;
                    $cross_relation->save(false);
                }else{
                    $cross_relation->updateCounters(['frequency'=>1]);
                }
                
            }
        }
        for($i=0,$j=$i+1;$i<count($splited_oq)-1;$i++, $j=$i+1){
            $self_relation = FrequencyRelation::findByWords($splited_oq[$i], $splited_oq[$j]);
            if(!$self_relation) {
                $self_relation = new FrequencyRelation;
                $self_relation->word1 = $splited_oq[$i];
                $self_relation->word2 = $splited_oq[$j];
                $self_relation->frequency = 1;
                $self_relation->save(false);
            }else{
                $self_relation->updateCounters(['frequency'=>1]);
            }
        }
    }
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
            return true;
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
        unset($dictionary);//memory optimization
        
        if(empty($match_result)) return [$subject];
        
        $mismatch_result = [];
        preg_match_all('/[a-zA-Z0-9]|[ก-เแ-๙]+/', $subject, $mismatch_result, PREG_OFFSET_CAPTURE);
        if($mismatch_result) $mismatch_result = $mismatch_result[0];//remove array dimension
//        print_r($mismatch_result);
        $merged_result = array_merge($match_result,$mismatch_result);
        unset($mismatch_result);//memory optimization
        uasort($merged_result, function($a,$b){ return $a[1]-$b[1]; });//sort result by index ASC
        
        $words = array_map(function($key){
                return $key[0];
            }, $merged_result);
        unset($merged_result);//memory optimization
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
