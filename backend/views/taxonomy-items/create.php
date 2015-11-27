<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TaxonomyItems */


$in = Yii::$app->request->get('TaxonomyItemsSearch');
$_model = \backend\models\TaxonomyVocabulary::findOne($in['vid']);

$this->params['breadcrumbs'][] = ['label' => 'Taxonomy Vocabularies', 'url' => '/admin/taxonomy-vocabulary'];

$this->title = 'Create Taxonomy Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxonomy-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

<?=
$this->render('_form', [
    'model' => $model,
])
?>

</div>
