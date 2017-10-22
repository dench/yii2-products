<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 20.05.17
 * Time: 17:54
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $modelVariant dench\products\models\Variant
 * @var $index integer
 */

use dench\products\models\Currency;
use dench\products\models\Unit;
use dench\language\models\Language;
use yii\helpers\Html;

?>
<?php
if (!$modelVariant->isNewRecord) {
    echo Html::activeHiddenInput($modelVariant, "[{$index}]id");
}
?>
<div class="row">
    <div class="col-xs-6 col-sm-4 col-md-2">
        <?= $form->field($modelVariant, '['. $index . ']price')->textInput() ?>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-2">
        <?= $form->field($modelVariant, '['. $index . ']price_old')->textInput() ?>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-2">
        <?= $form->field($modelVariant, '['. $index . ']currency_id')->dropDownList(Currency::getList(true)) ?>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-2">
        <?= $form->field($modelVariant, '['. $index . ']code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-2">
        <?= $form->field($modelVariant, '['. $index . ']available')->textInput() ?>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-2">
        <?= $form->field($modelVariant, '['. $index . ']unit_id')->dropDownList(Unit::getList(true)) ?>
    </div>
</div>
<div class="row">
    <?php foreach (Language::suffixList() as $suffix => $name) : ?>
        <div class="col-md-12">
            <?= $form->field($modelVariant, '['. $index . ']name' . $suffix)->textInput(['maxlength' => true]) ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($modelVariant, '['. $index . ']enabled')->checkbox() ?>
    </div>
    <div class="col-xs-6">
        <div class="form-group text-right">
            <?= Html::button(Yii::t('app', 'Remove variant'), ['class' => 'btn btn-default remove-variant']) ?>
        </div>
    </div>
</div>