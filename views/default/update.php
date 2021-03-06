<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model dench\products\models\Product */
/* @var $modelsVariant dench\products\models\Variant[] */
/* @var $variantImages dench\image\models\Image[] */
/* @var $features dench\products\models\Feature[] */
/* @var $files dench\image\models\File[] */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Product',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsVariant' => $modelsVariant,
        'variantImages' => $variantImages,
        'features' => $features,
        'files' => $files,
    ]) ?>

</div>
