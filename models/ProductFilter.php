<?php

namespace dench\products\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * ProductFilter represents the model behind the search form about `dench\products\models\Product`.
 */
class ProductFilter extends Product
{
    public $category_id;

    public $feature_ids = [];

    public $product_ids = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feature_ids'], 'each', 'rule' => ['each', 'rule' => ['integer']]],
            [['product_ids'], 'each', 'rule' => ['integer']],
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
        $query = Product::find();

        $query->joinWith(['categories']);



        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'position'=>SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        if ($this->feature_ids) {

            foreach ($this->feature_ids as $feature_id => $value_ids) {
                if ($value_ids) {
                    $variant_ids[$feature_id] = (new Query())->from('variant_value')->andFilterWhere(['value_id' => $value_ids])->column();
                }
            }

            if (isset($variant_ids)) {

                $this->product_ids = [0];

                if (count($variant_ids) > 1) {
                    $variant_ids = call_user_func_array('array_intersect',$variant_ids);
                } else {
                    $variant_ids = current($variant_ids);
                }

                if (!empty($variant_ids)) {
                    $this->product_ids = (new Query())->from('variant')->select('product_id')->where(['id' => $variant_ids])->groupBy('product_id')->column();
                }
            }
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'product.id' => $this->product_ids,
            'category_id' => $this->category_id,
            'product.enabled' => $this->enabled,
        ]);

        //$query->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}
