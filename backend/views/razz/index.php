<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\Razz;
use common\helpers\DataHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RazzSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Razzs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="razz-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Razz', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',
            [
                'attribute' => 'uid',
                'value' => function ($model) {
                    return isset($model->user->username) ? $model->user->username . '[' . $model->uid . ']' : 'none[0]';
                }
            ],
            [
                'attribute' => 'responder_uid',
                'value' => function ($model) {
                    return isset($model->responder->username) ? $model->responder->username . '[' . $model->responder_uid . ']' : 'none[0]';
                }
            ],
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return ($model->type == 1) ? 'some[1]' : 'any[2]';
                }
            ],
            [
                'label' => 'Time left',
                'value' => function ($model) {
                    if ($model->responder_uid) {
                        $time = DataHelper::downcounter(date("Y-m-d H:i:s", ($model->created_at + Razz::DAYS)));
                    } else
                        $time = 'Waiting';
                    return $time ? $time : 'Ended';
                }
            ],
            // 'type',
            // 'ended',
            // 'description:ntext',
            // 'message:ntext',
            // 'stream',
            // 'stream_preview',
            // 'responder_stream',
            // 'responder_stream_preview',
            // 'responder_uid',
            // 'views',
            // 'views_at',
            // 'email:email',
            // 'hash',
            // 'status',
            // 'created_at',
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
