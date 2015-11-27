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
    <li><a href="#edit-tab-created" data-toggle="tab">Created</a></li>
    <li><a href="#edit-tab-meta" data-toggle="tab">Meta</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane  active fade in " id="edit-tab-state">
        <br /><br />
    </div>
    <div class="tab-pane fade" id="edit-tab-slug">
        <br /><br />
    </div>
    <div class="tab-pane  fade" id="edit-tab-user">
        <br />
        <br />
    </div>
    <div class="tab-pane fade" id="edit-tab-created">
        <br />
        <?=
        DateTimePicker::widget([
            'name' => 'event_time',
            'value' => date("Y-m-d H:i", $model->created_at),
            'removeButton' => false,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd hh:mm',
                'autoclose' => true,
                'todayHighlight' => true,
            ],
            'pluginEvents' => [
                "changeDate" => "function(e,b) { console.log(e.date.getTimezoneOffset());   $('#created_at').val(Math.round(e.date.valueOf()/1000)+(e.date.getTimezoneOffset()*60)); }",
            ]
        ]);
        ?>
        <br />
    </div>
    <div class="tab-pane fade" id="edit-tab-meta">
        <br /><br />
    </div>
</div>
<?php
$this->registerJs('$(".widget-file-remove").click(function(a){  l = $(this); $.get(l.attr("href"),function(d){ if(d == "deleted") l.parent().remove(); });  return false; });');
?>




