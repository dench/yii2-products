<?php

namespace dench\products\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use dench\products\models\Value;

/**
 * ValueSearch represents the model behind the search form about `dench\products\models\Value`.
 */
class ValueSearch extends Value
{
    public $all;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'feature_id'], 'integer'],
            [['name'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Value::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'position' => SORT_ASC,
                ],
            ],
        ]);

        if ($this->all) {
            $dataProvider->pagination = false;
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'feature_id' => $this->feature_id,
        ]);

        return $dataProvider;
    }
}
