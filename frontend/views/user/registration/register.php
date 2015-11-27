<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dektrium\user\widgets\Connect;

/**
 * @var yii\web\View              $this
 * @var dektrium\user\models\User $user
 * @var dektrium\user\Module      $module
 */
$this->title = Yii::t('user', 'Sign up');
?>
        <div class="header <?php if (Yii::$app->user->isGuest): ?>not_registered <?php endif;?>cf">
            <div class="main_search">
                
                <?php
                            echo $this->render('@app/views/razz/search_form', []);

                            ?>
                                
            </div>
            <div class="user_block">
                <?php if (Yii::$app->user->isGuest): ?>
                    <ul class="icon-registr-sign-up registr-sign-up">
                        <li><a href="#" class="popup-click sign-in-popup-click">Sign in</a></li>
                        <li><a href="#" class="popup-click register-popup-click">register</a></li>
                    </ul>
                <?php else: ?>
                    <ul class="registered">
                        <li><a href="/user/<?= Yii::$app->user->id ?>">my profile</a></li>
                        <!--li><a href="#">settings</a></li-->
                        <li><a href="/site/logout" data-method="post">logout</a></li>
                    </ul>
                <?php endif; ?>
                
            </div>
        </div>
<div class="login_block cf">
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
        <div class="popup-submits full-block cf">
            <input type="submit" value="REGISTER" class="submit btn">
        </div>
    </fieldset>
    <!-- <div class="popup-links">
        <a href="/user/security/auth?authclient=facebook" class="auth-link facebook btn">SIGN IN WITH FACEBOOK</a>
        <a href="/user/security/auth?authclient=twitter" class="auth-link twitter btn">SIGN IN WITH TWITTER</a>
    </div> -->
    <div class="popup-links cf">
        <a href="/user/security/auth?authclient=facebook" class="auth-link facebook btn"><i class="fa fa-facebook-official"></i><b>Login</b> with <b>Facebook</b></a>
        <a href="/user/security/auth?authclient=twitter" class="auth-link twitter btn"><i class="fa fa-twitter"></i><b>Login</b> with <b>Twitter</b></a>
    </div>

    <?php ActiveForm::end(); ?>
</div>