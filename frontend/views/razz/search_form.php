<?php

use yii\widgets\ActiveForm;
use frontend\models\RazzSearch;
use yii\helpers\ArrayHelper;
use common\models\TaxonomyIndex;
use yii\helpers\Html;

$model = new RazzSearch();
$model->load(Yii::$app->request->get());
?>
<?php
$form = ActiveForm::begin([
            'action' => '/razz/search',
            'method' => 'get',
            'options' => ['class' => 'search']
        ]);
?>

<fieldset>
    <?= $form->field($model, 'search', ['template' => '{input}{error}'])->textInput(['maxlength' => true, 'placeholder' => "SEARCH RAZZ'S"]) ?>
    <!-- <input type="submit"> -->
    <i class="icon-search"></i>
</fieldset>
<div class="search-filter cf">
    <div class="left-side">
        <span class="title">FILTER BY CATEGORY:</span>
        <?= $form->field($model, 'category', ['template' => '{input}{error}'])->checkboxList(ArrayHelper::map(TaxonomyIndex::getTerms(1), 'id', 'name')); ?>
        <?= $form->field($model, 't', ['template' => '{input}{error}'])->checkBoxList([1 => 1, 2 => 2]); ?>
    </div>
    <div class="right-side">
        <a href="#" class="btn" id="toggle-respond">RESPOND TO CHALLENGES</a>
        <a href="#" class="btn" id="toggle-vote">VOTE ON CHALLENGES</a>
        <?= Html::submitButton('SEARCH', ['class' => 'btn']) ?>
    </div>
    <!-- <span class="search-filter-close">X</span> -->
</div>
<?php ActiveForm::end(); ?>