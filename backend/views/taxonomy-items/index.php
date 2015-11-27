<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TaxonomyItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$in = Yii::$app->request->get('TaxonomyItemsSearch');

if (isset($in['vid']) && $in['vid']) {

    $_model = \backend\models\TaxonomyVocabulary::findOne($in['vid']);
    $this->title = $_model->name;
} else
    $this->title = 'Vocabulary Items';

$this->params['breadcrumbs'][] = ['label' => 'Taxonomy Vocabularies', 'url' => '/admin/taxonomy-vocabulary'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxonomy-items-index">

    <h1><?= Html::encode($this->title) ?></h1>
<?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
<?= Html::a('Create', ['create?TaxonomyItems[vid]=' . $in['vid']], ['class' => 'btn btn-primary']) ?>
    </p>

<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'name',
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',],
    ],
]);
?>

</div>
