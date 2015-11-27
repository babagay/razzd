<?php

use kartik\datetime\DateTimePicker;
use dosamigos\selectize\Selectize;
use yii\web\JsExpression;
use yii\jui\AutoComplete;
?>

<?= $form->field($model, 'created_at', ['template' => '{input}'])->hiddenInput(['id' => 'created_at']); ?>

<ul class="nav nav-tabs" id="edit-admin-tabs">
    <li class="active"><a href="#edit-tab-state" data-toggle="tab">State</a></li>
    <li><a href="#edit-tab-slug" data-toggle="tab">Alias</a></li>
    <li><a href="#edit-tab-user" data-toggle="tab">User</a></li>
    <!--li><a href="#edit-tab-created" data-toggle="tab">Created</a></li-->
    <li><a href="#edit-tab-meta" data-toggle="tab">Meta</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane  active fade in " id="edit-tab-state">
        <div class="checkbox">
            <?= $form->field($model, 'publish')->checkBox(['label' => "Published"]); ?>
            <? $form->field($model, 'promote')->checkBox(['label' => "Promoted"]); ?>
        </div>



    </div>
    <div class="tab-pane fade" id="edit-tab-slug">
        <br />
        <?= $form->field($model, 'alias', ['template' => '{input}{error}'])->textInput(['maxlength' => 255]); ?>

    </div>
    <div class="tab-pane  fade" id="edit-tab-user">
        <br />


        <?=
        $form->field($model, 'username')->widget(AutoComplete::className(), [
            'clientOptions' => ['source' => '/admin/site/author'],
            'options' => ['class' => 'form-control']
        ]);
        ?>





        <br />
    </div>
    <div class="tab-pane fade" id="edit-tab-created">
        <br />
        <?=
        DateTimePicker::widget([
            'name' => 'event_time',
            'value' => date("d/m/Y h:i:s", (int) $model->created_at),
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose' => true,
                'todayHighlight' => true,
            ],
            'pluginEvents' => [
                "changeDate" => "function(e) {  $('#created_at').val(Math.round(e.timeStamp/1000)); }",
            ]
        ]);
        ?>
        <br />
    </div>
    <div class="tab-pane fade" id="edit-tab-meta">
        <br />
        <?= $form->field($model, 'meta_title')->textInput(['maxlength' => 255]); ?>
        <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => 255]); ?>
        <?= $form->field($model, 'meta_description')->textArea(['maxlength' => 255]); ?>
    </div>
</div>
<?php
$this->registerJs('$(".widget-file-remove").click(function(a){  l = $(this); $.get(l.attr("href"),function(d){ if(d == "deleted") l.parent().remove(); });  return false; });');
?>




