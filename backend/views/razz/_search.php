<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RazzSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="razz-search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'uid') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'ended') ?>

    <?= $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'message') ?>

    <?php // echo $form->field($model, 'stream') ?>

    <?php // echo $form->field($model, 'stream_preview') ?>

    <?php // echo $form->field($model, 'responder_stream') ?>

    <?php // echo $form->field($model, 'responder_stream_preview') ?>

    <?php // echo $form->field($model, 'responder_uid') ?>

    <?php // echo $form->field($model, 'views') ?>

    <?php // echo $form->field($model, 'views_at') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'hash') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

        <?php // echo $form->field($model, 'updated_at')  ?>

    <div class="form-group">
<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
