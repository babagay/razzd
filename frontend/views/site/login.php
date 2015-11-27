<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dektrium\user\widgets\Connect;
?>

<?php
$form = ActiveForm::begin([
                'id' => 'login-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'validateOnBlur' => false,
                'validateOnType' => false,
                'validateOnChange' => false,
        ])
?>

<fieldset>
    <div class="inputs">
        <?= $form->field($model, 'login', ['template' => '{input}{error}', 'inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']])->textInput(['placeholder' => 'USERNAME:']); ?>
        <?= $form->field($model, 'password', ['template' => '{input}{error}', 'inputOptions' => ['class' => 'form-control', 'tabindex' => '2']])->passwordInput(['placeholder' => 'PASSWORD:']) ?>
    </div>
    <div class="popup-submits">
        <!-- <span class="popup-click register-popup-click btn"> REGISTER</span> -->
        <input type="submit" value="LOGIN" class="submit btn">
    </div>
</fieldset>
<?=
Connect::widget([
        'baseAuthUrl' => ['/user/security/auth']
])
?>
<div class="links">
    <a href="#" id="popupRegister">Don't have An Account Yet?</a>
    <a href="#" id="popupFogotPassword">Forgot Username/Password?</a>
</div>
<div class="popup-links">
    <a href="/user/security/auth?authclient=facebook" class="auth-link facebook btn"><i class="fa fa-facebook-official"></i><b>Login</b> with <b>Facebook</b></a>
    <a href="/user/security/auth?authclient=twitter" class="auth-link twitter btn"><i class="fa fa-twitter"></i><b>Login</b> with <b>Twitter</b></a>
</div>
<?php ActiveForm::end(); ?>
<a href="#" class="close_btn"></a>