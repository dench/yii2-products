<?php

use dench\sortable\grid\SortableColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel dench\products\models\ComplectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Complectation');
$this->params['breadcrumbs'][] = $this->title;

if (!Yii::$app->request->get('all') && $dataProvider->totalCount > $dataProvider->count) {
    $showAll = Html::a(Yii::t('app', 'Show all'), Url::current(['all' => 1]));
} else {
    $showAll = '';
}
?>
<div class="complect-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create {0}', Yii::t('app', 'Complect')), ['create'], ['class' => 'btn btn-success']) ?>
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
            'name',

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
