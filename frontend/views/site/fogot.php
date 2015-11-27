<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php
$form = ActiveForm::begin([
                'id' => 'password-recovery-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false
        ]);
?>

<fieldset>
    <?php if ($sended): ?>
        <span class=""> Your password was changed.</span>
        <span class=""> Please check your email.</span>
    <?php else: ?>
        <div>
            Please enter the email that you used to sign up for Razzd below.
        </div>
        <div class="inputs">   
            <?= $form->field($model, 'email', ['template' => '{input}{error}', 'inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']])->textInput(['placeholder' => 'EMAIL']); ?>

        </div>
        <div class="popup-submits"> 
            <input name="recover" type="submit" value="recover" class="submit btn">
        </div>
    <?php endif; ?>
</fieldset>

<?php ActiveForm::end(); ?>
<a href="#" class="close_btn"></a> 