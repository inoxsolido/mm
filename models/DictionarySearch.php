<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Dictionary;

/**
 * DictionarySearch represents the model behind the search form about `app\models\Dictionary`.
 */
class DictionarySearch extends Dictionary
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['word'], 'safe'],
            [['length'], 'validateLength']
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
    public function validateLength($attribute, $params, $validator){
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
        $query = Dictionary::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if(preg_match('/^(?<prefix>=|>|>=|<=|<)(?<value>\d+)$/', $this->length, $match)){
            $condition = $match['prefix'];
            $value = $match['value'];
            $query->andFilterWhere([$condition, 'length', $value]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'word', $this->word]);

        return $dataProvider;
    }
}
