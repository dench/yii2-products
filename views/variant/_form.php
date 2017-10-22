<?php

use dench\products\models\Currency;
use dench\products\models\Product;
use dench\products\models\Unit;
use dench\products\models\Value;
use dench\image\widgets\ImagesForm;
use dench\language\models\Language;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model dench\products\models\Variant */
/* @var $form yii\widgets\ActiveForm */
/* @var $images dench\image\models\Image[] */

if ($model->isNewRecord) {
    $urlPjax = Url::to([0 => null, 'pjax' => 1]);

$js = <<<JS
$('#variant-product_id').change(function(){
    $.pjax.reload({container: "#features-pjax", timeout: 2000, url: '{$urlPjax}&product_id=' + $(this).val() });
});
$(document).on('pjax:complete', function() {});
JS;
    $this->registerJs($js);
}
?>

<div class="variant-form">

    <?php $form = ActiveForm::begin(); ?>

    <ul class="nav nav-tabs">
        <li class="nav-item active"><a href="#tab-main" class="nav-link" data-toggle="tab"><?= Yii::t('app', 'Main') ?></a></li>
        <li class="nav-item"><a href="#tab-images" class="nav-link" data-toggle="tab"><?= Yii::t('app', 'Images') ?></a></li>
        <li class="nav-item"><a href="#tab-feature" class="tab-feature" class="nav-link" data-toggle="tab"><?= Yii::t('app', 'Features') ?></a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab-main">
            <?= $form->field($model, 'product_id')->dropDownList(Product::getList(true), ['prompt' => '', 'disabled' => !$model->isNewRecord]) ?>

            <?php foreach (Language::suffixList() as $suffix => $name) : ?>
                <?= $form->field($model, 'name' . $suffix)->textInput(['maxlength' => true]) ?>
            <?php endforeach; ?>

            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'price')->textInput() ?>

            <?= $form->field($model, 'price_old')->textInput() ?>

            <?= $form->field($model, 'currency_id')->dropDownList(Currency::getList(true)) ?>

            <?= $form->field($model, 'unit_id')->dropDownList(Unit::getList(true)) ?>

            <?= $form->field($model, 'available')->textInput() ?>

            <?= $form->field($model, 'enabled')->checkbox() ?>
        </div>

        <div class="tab-pane fade" id="tab-images">
            <?= ImagesForm::widget([
                'images' => $images,
                'image_id' => $model->image_id,
                'imageEnabled' => $model->imageEnabled,
                'col' => 'col-sm-4 col-md-3',
                'size' => 'fill',
                'label' => null,
                'modelInputName' => $model->formName(),
            ]) ?>
        </div>

        <div class="tab-pane fade" id="tab-feature">
            <?php Pjax::begin(['id' => 'features-pjax']); ?>
            <?php
            if (empty($features)) {
                echo Html::tag('div', Yii::t('app', 'Choose a product!'), ['class' => 'alert alert-danger']);
            } else {
                foreach ($features as $feature) {
                    echo Html::tag('div',
                        Html::label($feature->name . ($feature->after ? ', ' . $feature->after : '')) . ' ' .
                        Html::button('+', ['class' => 'btn btn-default btn-xs']) .
                        Html::checkboxList('Variant[value_ids][]', $model->value_ids, Value::getList($feature->id))
                        , ['class' => 'form-group']);
                }
            }
            ?>
            <?php Pjax::end(); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
