<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TaxonomyVocabulary */

$this->title = 'Update Taxonomy Vocabulary: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Taxonomy Vocabularies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="taxonomy-vocabulary-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
