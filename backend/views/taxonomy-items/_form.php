<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\selectize\SelectizeTextInput;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\TaxonomyItems */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$in = Yii::$app->request->get('TaxonomyItemsSearch');
?>

<div class="taxonomy-items-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'vid', ['template' => '{input}'])->hiddenInput(['value' => $in['vid']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?=
    $form->field($model, 'parent[name]')->widget(SelectizeTextInput::classname(), [
        'loadUrl' => ['taxonomy-items/parent-term?id=' . $model->id . '&vid=' . $model->vid],
        'clientOptions' => [
            'plugins' => ['remove_button'],
            'valueField' => 'id',
            'labelField' => 'name',
            'searchField' => ['name'],
            'create' => false,
            'maxItems' => 1
        ]
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
