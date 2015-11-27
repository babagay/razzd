<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TaxonomyVocabulary */

$this->title = 'Create Taxonomy Vocabulary';
$this->params['breadcrumbs'][] = ['label' => 'Taxonomy Vocabularies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxonomy-vocabulary-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
