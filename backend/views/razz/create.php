<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Razz */

$this->title = 'Create Razz';
$this->params['breadcrumbs'][] = ['label' => 'Razzs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="razz-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
