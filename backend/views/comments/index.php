<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CommentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comments-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Comments', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'uid',
                'value' => function ($model) {
                    return isset($model->user->username) ? $model->user->username . '[' . $model->uid . ']' : 'none[' . $model->uid . ']';
                },
                'format' => 'html',
            ],
            // 'eid',
            'comment',
            'created_at:datetime',
            'status:boolean',
            'ip',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
