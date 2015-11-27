<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TaxonomyVocabularySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vocabularies';


$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxonomy-vocabulary-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            [ 'class' => 'yii\grid\ActionColumn',
                'template' => '{list} {hierarchy} {view} {update} {delete}',
                'buttons' => [
                    'list' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-list-alt"></i>', '/admin/taxonomy-items/?TaxonomyItemsSearch[vid]=' . $model->id, [
                                    'title' => Yii::t('yii', 'List'),
                        ]);
                    },
                            'hierarchy' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-align-right"></i>', '/admin/taxonomy-items/hierarchy/?TaxonomyItemsSearch[vid]=' . $model->id, [

                                    'title' => Yii::t('yii', 'Hierarchy'),
                        ]);
                    },
                            'delete' => function ($url, $model) {

                        $items = \Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{%taxonomy_items}} taxonomy_items WHERE  vid LIKE :vid')->bindValue(':vid', $model->id)->queryScalar();

                        if ($items)
                            return false;

                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('user', 'Are you sure to delete this vocabulary?'),
                                    'title' => Yii::t('yii', 'Delete'),
                        ]);
                    },
                        ],
                    ],
                ],
            ]);
            ?>

</div>
