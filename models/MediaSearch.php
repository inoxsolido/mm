<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use app\models\Media;

/**
 * MediaSearch represents the model behind the search form about `\app\models\Media`.
 */
class MediaSearch extends Media {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'is_public','media_type_id', ], 'integer'],
            [['name',  'album_id', 'file_name', 'file_extension', 'file_path', 'file_upload_date', 'file_thumbnail_path', 'tags'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return SqlDataProvider
     */
    public function search($params){
        if(@$params['q'] != null){
            $params['q'] = trim($params['q']);
            $params['q'] = preg_replace('/[~!@#$%^&*()_+\/\-*\/\.,\?\[\]\|:;"\'\\\\<>\{\}]/', '', $params['q']);
        }
        $splited_q = Yii::$app->word->split(@$params['q']);
        
        $draftQuery = new \yii\db\Query();
        $draftQuery->select("media.*, album.name as album_name, album.tags as album_tags")->from("media");
        $draftQuery->leftJoin('album', 'album.id = album_id');
        $type = [];
        if(@$params['v']==1)$type[]=1;
        if(@$params['i']==1)$type[]=2;
        if(@$params['a']==1)$type[]=3;
        if(@$params['d']==1)$type[]=4;
        if(@$params['e']==1)$type[]=5;
        if($type !== [])$draftQuery->andWhere(['media_type_id'=>$type]);
        if(Yii::$app->user->isGuest)$draftQuery->andWhere(['is_public'=>true]);
        
        $draftQuery->orderBy(['file_upload_date' => SORT_DESC]);
        if(@$params['dr']!=""){
            $date_range = explode(' - ', $params['dr']);
            $draftQuery->andWhere(['BETWEEN', 'file_upload_date', $date_range[0], $date_range[1]]);
        }
        if($splited_q == []){ //search only by type date and publish condition
            $final_sql = $draftQuery->createCommand()->getRawSql();
            $count = $draftQuery->count();
            $dataProvider = new SqlDataProvider([
                'sql' => $final_sql,
                'totalCount' => $count,
                'pagination' => [
                    'pageParam' => 'p',
                    'pageSize' => 12,
                    'pageSizeParam' => false,
                ],
            ]);
            return $dataProvider;
        }
        
        
        //-----keyword search
        //update frequency word
        Yii::$app->word->frequencyWordToDictionary($params['q']);//update
        if(@$params['oq'] != null){
            //cross related word
            $params['oq'] = trim($params['oq']);
            $params['oq'] = preg_replace('/[~!@#$%^&*()_+\/\-*\/\.,\?\[\]\|:;"\'\\\\<>\{\}]/', '', $params['oq']);
            $splited_oq = Yii::$app->word->split($params['oq']);
            Yii::$app->word->updateRelationWord($splited_q, $splited_oq);
        }
        
        $query1 = clone $draftQuery; //1. มีคำครบทุกคำและไม่มีคำอื่นแทรกระหว่างประโยค
        $query2 = clone $draftQuery; //2. มีคำครบทุกคำและมีคำอื่นแทรกระหว่างประโยคได้
        $query3 = clone $draftQuery; //3. มีคำครบทุกคำแต่คำแต่ละคำจะอยู่ตรงไหนของRecordก็ได้ (and condition)
        $query4 = clone $draftQuery; //4. มีคำที่เกี่ยวข้องอยู่ตรงไหนของRecordก็ได้
        
        $condition_1 = ['or'];
        
        $condition_2 = ['or'];
        $pattern1 = '^.*' . join('.*', $splited_q) . '.*$'; //result: /^.*word1.*word2.*$/
        
        foreach ($splited_q as $v) {
            $condition_3 = ['or'];
            if(@$params['omedianame']){
                array_push($condition_3, ['like', 'media.name', $v]);
            }
            if(@$params['omediatag']){
                array_push($condition_3, ['like', 'media.tags', $v]);
            }
            if(@$params['oalbumname']){
                array_push($condition_3, ['like', 'album.name', $v]);
            }
            if(@$params['oalbumtag']){
                array_push($condition_3, ['like', 'album.tags', $v]);
            }
            $query3->andWhere( 
                $condition_3
            );
        }
            
        $condition_4 = ['or'];
        

        if(@$params['omedianame']){
            array_push($condition_1, ['like', 'media.name', $params['q']]);
            array_push($condition_2, ['REGEXP', 'media.name', $pattern1]);
        }
        if(@$params['omediatag']){
            array_push($condition_1, ['like', 'media.tags', $params['q']]);
            array_push($condition_2, ['REGEXP', 'media.tags', $pattern1]);
        }
        if(@$params['oalbumname']){
            array_push($condition_1, ['like', 'album.name', $params['q']]);
            array_push($condition_2, ['REGEXP', 'album.name', $pattern1]);
        }
        if(@$params['oalbumtag']){
            array_push($condition_1, ['like', 'album.tags', $params['q']]);
            array_push($condition_2, ['REGEXP', 'album.tags', $pattern1]);
        }
        $query1->andWhere(
            $condition_1
        );
        $query2->andWhere(
            $condition_2
        );
        
        $final_query = $query1->union($query2)->union($query3);
        
        //query4
        $related_word = FrequencyRelation::getRelatedWord($params['q']);
        if (!empty($related_word)){
            $pattern2 = '^.*' . join('|', $related_word) . '.*$';
            
            if(@$params['omedianame']){
                array_push($condition_4, ['REGEXP', 'media.name', $pattern2]);
            }
            if(@$params['omediatag']){
                array_push($condition_4, ['REGEXP', 'media.tags', $pattern2]);
            }
            if(@$params['oalbumname']){
                array_push($condition_4, ['REGEXP', 'album.name', $pattern2]);
            }
            if(@$params['oalbumtag']){
                array_push($condition_4, ['REGEXP', 'album.tags', $pattern2]);
            }
            
            $query4->orWhere(['or',
                $condition_4
            ]);
            $final_query->union($query4);
        }
        
        $final_sql = $final_query->createCommand()->getRawSql();
        $count = $final_query->count();
        $dataProvider = new SqlDataProvider([
            'sql' => $final_sql,
            'totalCount' => $count,
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
    public function filter($params) {
        $query = Media::find()
                ->joinWith('album', true, 'LEFT JOIN')
                ->joinWith('mediaType', true, 'LEFT JOIN');

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
        $dataProvider->sort->attributes['album_name'] = [
            'asc' => ['album.name' => SORT_ASC],
            'desc' => ['album.name'=> SORT_DESC],
        ];
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'file_upload_date' => $this->file_upload_date,
            'is_public' => $this->is_public,
            'media_type_id' => $this->media_type_id,
//            'album_id' => $this->album_id,
        ]);
        
        if(@$params['dr']!=""){
            $date_range = explode(' - ', $params['dr']);
            $query->andWhere(['BETWEEN', 'file_upload_date', $date_range[0], $date_range[1]]);
        }
        $splited_tag = explode(',', $this->tags);
        foreach($splited_tag as $tag){
            $query->andFilterWhere(['like', 'media.tags', $tag]);
        }
        $query->andFilterWhere(['like', 'media.name', $this->name])
                ->andFilterWhere(['like', 'album.name', $this->album_id]);

        return $dataProvider;
    }

}
