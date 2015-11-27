<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Razz */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Razzs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="razz-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'uid',
                'value' => isset($model->user->username) ? $model->user->username . '[' . $model->uid . ']' : 'deleted[' . $model->uid . ']'
            ],
            [
                'attribute' => 'type',
                'value' => ($model->type == 1) ? 'some[1]' : 'any[2]'
            ],
            'ended',
            'title',
            'description:ntext',
            'message:ntext',
            'stream',
            'stream_preview',
            'responder_stream',
            'responder_stream_preview',
            [
                'attribute' => 'responder_uid',
                'value' => isset($model->responder->username) ? $model->responder->username . '[' . $model->responder_uid . ']' : 'none[' . $model->responder_uid . ']'
            ],
            'views',
            // 'views_at',
            'email:email',
            'hash',
            [
                'attribute' => 'publish',
                'value' => $model->publish ? 'Published' : 'Unpublished'
            ],
            'created_at:datetime',
        ],
    ])
    ?>

</div>
