<?php

use dench\products\models\Category;
use dench\language\models\Language;
use dench\sortable\grid\SortableColumn;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model dench\products\models\Feature */
/* @var $form yii\widgets\ActiveForm */
/* @var $values dench\products\models\Value[] */
?>

<div class="feature-form">

    <?php $form = ActiveForm::begin(); ?>

    <ul class="nav nav-tabs">
        <?php foreach (Language::suffixList() as $suffix => $name) : ?>
            <li class="nav-item<?= empty($suffix) ? ' active': '' ?>"><a href="#lang<?= $suffix ?>" class="nav-link" data-toggle="tab"><?= $name ?></a></li>
        <?php endforeach; ?>
        <li class="nav-item"><a href="#main-tab" class="nav-link" data-toggle="tab"><?= Yii::t('app', 'Main') ?></a></li>
        <li class="nav-item"><a href="#values-tab" class="nav-link" data-toggle="tab"><?= Yii::t('app', 'Values') ?></a></li>
    </ul>

    <div class="tab-content">
        <?php foreach (Language::suffixList() as $suffix => $name) : ?>
            <div class="tab-pane fade<?php if (empty($suffix)) echo ' in active'; ?>" id="lang<?= $suffix ?>">
                <?= $form->field($model, 'name' . $suffix)->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'after' . $suffix)->textInput(['maxlength' => true]) ?>
            </div>
        <?php endforeach; ?>

        <div class="tab-pane fade" id="main-tab">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'category_ids')->checkboxList(Category::getList(true)) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'filter_ids')->checkboxList(Category::getList(true)) ?>
                </div>
            </div>
            <?= $form->field($model, 'enabled')->checkbox() ?>
        </div>

        <div class="tab-pane fade" id="values-tab">
            <p>
                <?= Html::a(Yii::t('app', 'Create {0}', Yii::t('app', 'Value')), ['value/create', 'feature_id' => $model->id], ['class' => 'btn btn-success modal-value-open']) ?>
            </p>
            <?php Pjax::begin([
                'id' => 'pjax-grid-values',
            ]) ?>
            <?= GridView::widget([
                'dataProvider' => $values,
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'data-position' => $model->position,
                    ];
                },
                'columns' => [
                    [
                        'class' => SortableColumn::class,
                    ],
                    'name',
                    'feature.after',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'controller' => 'value',
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['value/update', 'id' => $model->id], [
                                    'class' => 'modal-value-open',
                                ]);
                            },
                        ],
                    ],
                ],
                'options' => [
                    'data' => [
                        'sortable' => 1,
                        'sortable-url' => Url::to(['value/sorting']),
                    ]
                ],
            ]); ?>
            <?php
            $script = <<< JS
$('.modal-value-open').on('click', function(e){
    e.preventDefault();
    $('#modal-value').modal('show').find('#modal-value-content').load($(this).attr('href'));
});
JS;
            Yii::$app->view->registerJs($script);
            ?>
            <?php Pjax::end() ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
Modal::begin([
    'id' => 'modal-value',
]);
echo Html::tag('div', '', ['id' => 'modal-value-content']);
Modal::end();
?>
