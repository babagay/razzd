<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use frontend\widgets\rating\Asset;

Asset::register($this);

$data = array_shift($data);
?>

<?php $form = ActiveForm::begin(['action' => '/site/rate', 'options' => ['class' => 'ratings']]) ?>

<?= $form->field($model, 'nid', ['template' => '{input}'])->hiddenInput() ?>

<?= $form->field($model, 'model', ['template' => '{input}'])->hiddenInput() ?>

<?= $form->field($model, 'return_id', ['template' => '{input}'])->hiddenInput() ?>

<?php foreach ($data as $name => $itm): ?>
    <?php

    echo $form->field($model, 'vote[' . $name . ']', ['template' => '{input}{error}'])->dropDownList($itm, ['data-id' => $model->return_id]);
    ?>

<?php endforeach; ?>

<?= Html::submitButton('Отправить', ['class' => 'btn btn-default btn-block']) ?>

<?php ActiveForm::end(); ?>