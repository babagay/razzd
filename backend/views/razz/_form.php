<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Razz */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="razz-form">

    <?php $form = ActiveForm::begin(); ?>

    <? $form->field($model, 'uid')->textInput() ?>

    <? $form->field($model, 'type')->textInput() ?>

    <? $form->field($model, 'ended')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'stream')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stream_preview')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'responder_stream')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'responder_stream_preview')->textInput(['maxlength' => true]) ?>

    <? $form->field($model, 'responder_uid')->textInput() ?>

    <? $form->field($model, 'views')->textInput(['maxlength' => true]) ?>

    <? $form->field($model, 'views_at')->textInput(['maxlength' => true]) ?>

    <? $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <? $form->field($model, 'hash')->textInput(['maxlength' => true]) ?>

    <? $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <? $form->field($model, 'updated_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publish')->checkBox(['label' => "Published"]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
