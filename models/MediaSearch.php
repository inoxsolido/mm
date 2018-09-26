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
    public function search($params) {

        if(@$params['q'] != null){
            $params['q'] = trim($params['q']);
            $params['q'] = preg_replace('/[~!@#$%^&*()_+\/\-*\/\.,\?\[\]\|:;"\'\\\\<>\{\}]/', '', $params['q']);
        }
        $splited_q = Yii::$app->word->split(@$params['q']);
        
        $draftQuery = new \yii\db\Query();
        $draftQuery->select("media.*, album.name as album_name, album.tags as album_tags")->from("media");
        $type = [];
        if(@$params['v']==1)$type[]=1;
        if(@$params['i']==1)$type[]=2;
        if(@$params['a']==1)$type[]=3;
        if(@$params['d']==1)$type[]=4;
        if(@$params['e']==1)$type[]=5;
        if($type !== [])$draftQuery->andWhere(['media_type_id'=>$type]);
        
        
        $draftQuery->orderBy(['file_upload_date' => SORT_DESC]);
        if(@$params['dr']!=""){
            $date_range = explode(' - ', $params['dr']);
            $draftQuery->andWhere(['BETWEEN', 'file_upload_date', $date_range[0], $date_range[1]]);
        }
        
        if (@$params['oAlbum'] && $splited_q != []) {
            $draftQuery->innerJoin('album', 'album.id = album_id');
            
            $query1 = clone $draftQuery; //1. มีคำครบทุกคำและไม่มีคำอื่นแทรกระหว่างประโยค
            $query2 = clone $draftQuery; //2. มีคำครบทุกคำและมีคำอื่นแทรกระหว่างประโยคได้
            $query3 = clone $draftQuery; //3. มีคำครบทุกคำแต่คำแต่ละคำจะอยู่ตรงไหนของRecordก็ได้ (and condition)
            $query4 = clone $draftQuery; //4. มีคำบางคำและคำแต่ละคำจะอยู่ตรงไหนของRecordก็ได้ (Or condition)
            $query5 = clone $draftQuery; //5. มีคำที่เกี่ยวข้องอยู่ตรงไหนของRecordก็ได้

            
            $pattern1 = ''; //for $query2
//            $pattern2 = ''; //for $query3
            $pattern3 = ''; //for $query4
            $pattern4 = ''; //for $query5
            if ($splited_q != []) {
                $pattern1 = '^.*' . join('.*', $splited_q) . '.*$';
                //pattern2 & query3
                foreach ($splited_q as $v) {
                    $query3->andWhere(['or', 
                        ['like', 'album.name', $v],
                        ['like', 'album.tags', $v]
                    ]);
                }
                $pattern3 = '^.*' . join('|', $splited_q) . '.*$';
                if ($params['related_word'] != [])
                    $pattern4 = '^.*' . join('|', $params['related_word']) . '.*$'; //รอ
            }
            $query1->andWhere(['or',
                ['like', 'album.name', $params['q']],
                ['like', 'album.tags', $params['q']]
            ]);
            $query2->andWhere(['or',
                ['REGEXP', 'album.name', $pattern1],
                ['REGEXP', 'album.tags', $pattern1],
            ]);
            $query4->andWhere(['or',
                ['REGEXP', 'album.name', $pattern3],
                ['REGEXP', 'album.tags', $pattern3]
            ]);
            $query5->andWhere(['or',
                ['REGEXP', 'album.name', $pattern4],
                ['REGEXP', 'album.tags', $pattern4]
            ]);
            

            $final_query = $query1->union($query2)
                    ->union($query3)
//                    ->union($query4)
                    ;
            if($pattern4!=''){
                $final_query->union($query5);
            }

        } else {
            $draftQuery->leftJoin('album', 'album.id = album_id');

            if ($splited_q != []) {


                $query1 = clone $draftQuery; //1. มีคำครบทุกคำและไม่มีคำอื่นแทรกระหว่างประโยค
                $query2 = clone $draftQuery; //2. มีคำครบทุกคำและมีคำอื่นแทรกระหว่างประโยคได้
                $query3 = clone $draftQuery; //3. มีคำครบทุกคำแต่คำแต่ละคำจะอยู่ตรงไหนของRecordก็ได้ (and condition)
                $query4 = clone $draftQuery; //4. มีคำบางคำและคำแต่ละคำจะอยู่ตรงไหนของRecordก็ได้ (Or condition)
                $query5 = clone $draftQuery; //5. มีคำที่เกี่ยวข้องอยู่ตรงไหนของRecordก็ได้

                $pattern1 = ''; //for $query2
//            $pattern2 = ''; //for $query3
                $pattern3 = ''; //for $query4
                $pattern4 = ''; //for $query5
                if ($splited_q != []) {
                    $pattern1 = '^.*' . join('.*', $splited_q) . '.*$';

//                $pattern2 = '^.*'; //start p2
                    foreach ($splited_q as $v) {
                        $query3->andWhere(['or',
                            ['like', 'media.name', $v],
                            ['like', 'media.tags', $v],
                            ['like', 'album.name', $v],
                            ['like', 'album.tags', $v]
                        ]);
                    }

                    $pattern3 = '^.*' . join('|', $splited_q) . '.*$';
                    if ($params['related_word'] != [])
                        $pattern4 = '^.*' . join('|', $params['related_word']) . '.*$'; //รอ
                }

                $query1->andWhere(['or',
                    ['like', 'media.name', $params['q']],
                    ['like', 'media.tags', $params['q']],
                    ['like', 'album.name', $params['q']],
                    ['like', 'album.tags', $params['q']]
                ]);
                $query2->andWhere(['or',
                    ['REGEXP', 'media.name', $pattern1],
                    ['REGEXP', 'media.tags', $pattern1],
                    ['REGEXP', 'album.name', $pattern1],
                    ['REGEXP', 'album.tags', $pattern1]
                ]);
                $query4->andWhere(['or',
                    ['REGEXP', 'media.name', $pattern3],
                    ['REGEXP', 'media.tags', $pattern3],
                    ['REGEXP', 'album.name', $pattern3],
                    ['REGEXP', 'album.tags', $pattern3]
                ]);
                $query5->andWhere(['or',
                    ['REGEXP', 'media.name', $pattern4],
                    ['REGEXP', 'media.tags', $pattern4],
                    ['REGEXP', 'album.name', $pattern4],
                    ['REGEXP', 'album.tags', $pattern4]
                ]);


                $final_query = $query1->union($query2)
                        ->union($query3)
//                        ->union($query4)
                        ;
                if ($pattern4 != '') {
                    $final_query->union($query5);
                }
            }else{
                $final_query = $draftQuery;
            }
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
            $query->andFilterWhere(['like', 'tags', $tag]);
        }
        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'album.name', $this->album_id]);

        return $dataProvider;
    }

}
