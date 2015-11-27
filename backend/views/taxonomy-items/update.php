<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TaxonomyItems */

$this->title = 'Update Taxonomy Items: ' . ' ' . $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Taxonomy Vocabularies','url' => '/admin/taxonomy-vocabulary'];

$_model = \backend\models\TaxonomyVocabulary::findOne($model->vid); 
$this->params['breadcrumbs'][] = ['label' => $_model->name,'url' => '/admin/taxonomy-items?TaxonomyItemsSearch[vid]=' . $model->vid];        
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="taxonomy-items-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
