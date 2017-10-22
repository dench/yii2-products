<?php

namespace dench\products\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductSearch represents the model behind the search form about `dench\products\models\Product`.
 */
class ProductSearch extends Product
{
    public $category_id;

    public $status_id;

    public $all;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->detachBehavior('slug');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'created_at', 'updated_at', 'position', 'enabled', 'category_id', 'status_id'], 'integer'],
            [['slug', 'name', 'title', 'keywords', 'description', 'text'], 'safe'],
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
        $query = Product::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'position' => SORT_DESC,
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

        if ($this->status_id) {
            $query->joinWith(['statuses']);
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
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'position' => $this->position,
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['like', 'product_lang.name', $this->name]);
        $query->andFilterWhere(['like', 'product.slug', $this->slug]);

        return $dataProvider;
    }
}
