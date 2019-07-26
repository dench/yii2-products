<?php

use dench\products\models\Feature;
use dench\products\models\Category;
use dench\sortable\grid\SortableColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel dench\products\models\FeatureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Features');
$this->params['breadcrumbs'][] = $this->title;

if (!Yii::$app->request->get('all') && $dataProvider->totalCount > $dataProvider->count) {
    $showAll = Html::a(Yii::t('app', 'Show all'), Url::current(['all' => 1]));
} else {
    $showAll = '';
}
?>
<div class="feature-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create {0}', Yii::t('app', 'Feature')), ['create'], ['class' => 'btn btn-success']) ?>
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
                'class' => SortableColumn::class,
            ],
            [
                'attribute' => 'name',
                'content' => function($model, $key, $index, $column){
                    return Html::a($model->name, ['value/index', 'ValueSearch[feature_id]' => $model->id]);
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
            'after',
            'enabled',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
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
