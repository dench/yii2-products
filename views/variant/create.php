<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model dench\products\models\Variant */
/* @var $features dench\products\models\Feature */
/* @var $images dench\image\models\Image[] */

$this->title = Yii::t('app', 'Create Variant');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Variants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'features' => $features,
        'images' => $images,
    ]) ?>

</div>
