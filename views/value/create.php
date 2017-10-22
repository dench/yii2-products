<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model dench\products\models\Value */
/* @var $modal boolean */

$this->title = Yii::t('app', 'Create Value');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Values'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="value-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modal' => $modal,
    ]) ?>

</div>
