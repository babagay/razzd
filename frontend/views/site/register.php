<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dektrium\user\widgets\Connect;
?>

<?php
$form = ActiveForm::begin([
            'id' => 'registration-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
            'validateOnChange' => false,
        ])
?>
<!-- <h2>REGISTRATION</h2> -->

<fieldset>
    <div class="inputs">
        <?= $form->field($model, 'name', ['template' => '{input}{error}'])->textInput(['placeholder' => 'FULL NAME']) ?>
        <?= $form->field($model, 'username', ['template' => '{input}{error}'])->textInput(['placeholder' => 'USERNAME']) ?>
        <?= $form->field($model, 'email', ['template' => '{input}{error}'])->textInput(['placeholder' => 'EMAIL']) ?>
        <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput(['placeholder' => 'PASSWORD']) ?>
        <?= $form->field($model, 'passwordConfirm', ['template' => '{input}{error}'])->passwordInput(['placeholder' => 'CONFIRM PASSWORD']) ?>
    </div>
    <div class="popup-submits full-block">
        <input type="submit" value="REGISTER" class="submit btn">
    </div>
</fieldset>
<?=
Connect::widget([
    'baseAuthUrl' => ['/user/security/auth']
])
?>
<!-- <div class="popup-links">
    <a href="/user/security/auth?authclient=facebook" class="auth-link facebook btn">SIGN IN WITH FACEBOOK</a>
    <a href="/user/security/auth?authclient=twitter" class="auth-link twitter btn">SIGN IN WITH TWITTER</a>
</div> -->
<div class="popup-links">
    <a href="/user/security/auth?authclient=facebook" class="auth-link facebook btn"><i class="fa fa-facebook-official"></i><b>Login</b> with <b>Facebook</b></a>
    <a href="/user/security/auth?authclient=twitter" class="auth-link twitter btn"><i class="fa fa-twitter"></i><b>Login</b> with <b>Twitter</b></a>
</div>

<?php ActiveForm::end(); ?>
<a href="#" class="close_btn"></a>