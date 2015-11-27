<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use vova07\imperavi\Widget;
use kartik\file\FileInput;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php
    $form = ActiveForm::begin([
                'options' => [],
    ]);
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>



    <?=
    $form->field($model, 'body')->widget(Widget::className(), [
        'settings' => [
            'lang' => 'ru',
            'minHeight' => 200,
            'pastePlainText' => true,
            'plugins' => [
                'clips',
                'fullscreen',
                'imagemanager'
            ],
            'imageUpload' => Url::to(['site/image-upload']),
            'imageManagerJson' => Url::to(['site/images-get'])
        ]
    ]);
    ?>
    <?=
    $this->render('_additional', [
        'model' => $model,
        'form' => $form
    ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs(' adminFileInput();  ');
?>