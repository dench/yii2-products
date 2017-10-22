<?php

namespace dench\products\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use dench\products\models\Feature;

/**
 * FeatureSearch represents the model behind the search form about `dench\products\models\Feature`.
 */
class FeatureSearch extends Feature
{
    public $category_id;

    public $all;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position', 'category_id'], 'integer'],
            [['name', 'after'], 'safe'],
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
        $query = Feature::find();

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

        if ($this->category_id) {
            $query->joinWith(['categories']);
        }

        if ($this->name) {
            $query->joinWith(['translations']);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'position' => $this->position,
            'enabled' => $this->enabled,
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['like', 'feature_lang.name', $this->name]);

        return $dataProvider;
    }
}
