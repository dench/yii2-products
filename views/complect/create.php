<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model dench\products\models\Complect */

$this->title = Yii::t('app', 'Create Complect');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Complects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="complect-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
