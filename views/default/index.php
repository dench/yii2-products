<?php

use dench\sortable\grid\SortableColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use dench\products\models\Category;
use dench\products\models\Brand;
use dench\products\models\Status;

/* @var $this yii\web\View */
/* @var $searchModel dench\products\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;

if (!Yii::$app->request->get('all') && $dataProvider->totalCount > $dataProvider->count) {
    $showAll = Html::a(Yii::t('app', 'Show all'), Url::current(['all' => 1]));
} else {
    $showAll = '';
}
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create {0}', Yii::t('app', 'Product')), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                'data-position' => $model->position,
            ];
        },
        'layout' => "{summary}\n{$showAll}\n{items}\n{pager}",
        'columns' => [
            [
                'class' => SortableColumn::className(),
            ],
            [
                'attribute' => 'name',
                'content' => function($model, $key, $index, $column){
                    return Html::a($model->name, ['variant/index', 'VariantSearch[product_id]' => $model->id]);
                },
            ],
            [
                'attribute' => 'category_id',
                'value' => function ($model, $key, $index, $column) {
                    $result = [];
                    foreach ($model->categories as $category) {
                        $result[] = $category->name;
                    }
                    return implode(', ', $result);
                },
                'filter' => Category::getList(null),
                'label' => Yii::t('app', 'Categories'),
            ],
            [
                'attribute' => 'brand_id',
                'value' => 'brand.name',
                'filter' => Brand::getList(null),
            ],
            'created_at:date',
            [
                'attribute' => 'status_id',
                'value' => function ($model, $key, $index, $column) {
                    $result = [];
                    foreach ($model->statuses as $status) {
                        $result[] = $status->name;
                    }
                    return implode(', ', $result);
                },
                'filter' => Status::getList(null),
                'label' => Yii::t('app', 'Status'),
            ],
            [
                'attribute' => 'enabled',
                'filter' => [
                    Yii::t('app', 'Disabled'),
                    Yii::t('app', 'Enabled'),
                ],
                'content' => function($model, $key, $index, $column){
                    if ($model->enabled) {
                        $class = 'glyphicon glyphicon-ok';
                    } else {
                        $class = '';
                    }
                    return Html::tag('i', '', ['class' => $class]);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/product/index', 'slug' => $model->slug], [
                            'target' => '_blank',
                        ]);
                    },
                ],
            ],
        ],
        'options' => [
            'data' => [
                'sortable' => 1,
                'sortable-url' => Url::to(['sorting']),
            ]
        ],
    ]); ?>
</div>
