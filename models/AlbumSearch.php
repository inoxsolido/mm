<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Album;

/**
 * AlbumSearch represents the model behind the search form about `\app\models\Album`.
 */
class AlbumSearch extends Album
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'tags'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    
    public function search($params){
        $query = @$params['q'];
        if(@$query != null){
            $query = trim($query);
            $query = preg_replace('/[~!@#$%^&*()_+\/\-*\/\.,\?\[\]\|:;"\'\\\\<>\{\}]/', '', $query);
        }
        $splited_q = Yii::$app->word->split(@$query);
        
        $draftQuery = new \yii\db\Query();
        $draftQuery->select("album.*")->from("album")->innerJoin("media")->groupBy('album.id');
        
        
        $draftQuery->orderBy(['name' => SORT_ASC]);
        $final_query = $draftQuery;
        if ($splited_q != []) {
            //update frequency word
            Yii::$app->word->frequencyWordToDictionary($query);
            if(@$params['oq'] != null){
                //cross related word
                $params['oq'] = trim($params['oq']);
                $params['oq'] = preg_replace('/[~!@#$%^&*()_+\/\-*\/\.,\?\[\]\|:;"\'\\\\<>\{\}]/', '', $params['oq']);
                $splited_oq = Yii::$app->word->split($params['oq']);
                if(!in_array(@$params['q'], $splited_q)) array_push($splited_q,$params['q']);
                if(!in_array(@$params['oq'], $splited_oq)) array_push($splited_oq, $params['oq']);
                Yii::$app->word->updateRelationWord($splited_q, $splited_oq);
            }
            $query1 = clone $draftQuery; //1. มีคำครบทุกคำและไม่มีคำอื่นแทรกระหว่างประโยค
            $query2 = clone $draftQuery; //2. มีคำครบทุกคำและมีคำอื่นแทรกระหว่างประโยคได้
            $query3 = clone $draftQuery; //3. มีคำครบทุกคำแต่คำแต่ละคำจะอยู่ตรงไหนของRecordก็ได้ (and condition)
            $query4 = clone $draftQuery; //4. มีคำที่เกี่ยวข้องอยู่ตรงไหนของRecordก็ได้

            //query1
            $query1->andWhere(['or',
                ['like', 'album.name', $query],
                ['like', 'album.tags', $query]
            ]);
            //query2
            $pattern1 = '^.*' . join('.*', $splited_q) . '.*$'; //result: /^.*word1.*word2.*$/
            $query2->andWhere(['or',
                ['REGEXP', 'album.name', $pattern1],
                ['REGEXP', 'album.tags', $pattern1],
            ]);
            //query3
            foreach ($splited_q as $v) {
                $query3->andWhere(['or', 
                    ['like', 'album.name', $v],
                    ['like', 'album.tags', $v]
                ]);
            }
            $final_query = $query1->union($query2)->union($query3);
            //query4
            $related_word = FrequencyRelation::getRelatedWord($query);
            if (!empty($related_word)){
                $pattern2 = '^.*' . join('|', $related_word) . '.*$'; //ปรับปรุงวิธีดึงข้อมูล relatedword
                $query4->andWhere(['or',
                    ['REGEXP', 'album.name', $pattern2],
                    ['REGEXP', 'album.tags', $pattern2]
                ]);
                $final_query->union($query4);
            }
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $final_query,
            'pagination' => [
                'pageParam' => 'p',
                'pageSize' => 12,
                'pageSizeParam' => false,
            ],
        ]);
        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function filter($params)
    {
        $query = Album::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tags', $this->tags]);

        return $dataProvider;
    }
}
