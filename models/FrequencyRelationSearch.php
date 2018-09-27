<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FrequencyRelation;

/**
 * FrequencyRelationSearch represents the model behind the search form about `\app\models\FrequencyRelation`.
 */
class FrequencyRelationSearch extends FrequencyRelation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['word1', 'word2', 'frequency'], 'safe'],
            [['frequency'], 'validateFrequency']
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
    
    public function validateFrequency($attribute, $params, $validator){
        if(!preg_match('/^(=|>|>=|<=|<)\d+$/', $this->$attribute)){
            $this->addError($attribute, "{attribute} Must be Integer");
        }
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = FrequencyRelation::find();

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
        if(preg_match('/^(?<prefix>=|>|>=|<=|<)(?<value>\d+)$/', $this->frequency, $match)){
            $condition = $match['prefix'];
            $value = $match['value'];
            $query->andFilterWhere([$condition, 'frequency', $value]);
            
        }
        

        $query->andFilterWhere(['like', 'word1', $this->word1])
            ->andFilterWhere(['like', 'word2', $this->word2]);

        return $dataProvider;
    }
}
