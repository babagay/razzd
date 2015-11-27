<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\HtmlPurifier;
    use common\helpers\Html as HtmlHelper;

/* @var $this yii\web\View */
if (!$this->title)
    $this->title = Html::encode($page['title']);

//$this->params['breadcrumbs'][] = $this->title;

    //var_dump(time());

?>


<div class="fb-send" data-href="http://adv.razzd.sfdevserver.com/razz/archive/38" data-colorscheme="dark" data-ref="label" data-layout="button_count" data-width="140" data-show-faces="true"></div>


<div class="header <?php if (Yii::$app->user->isGuest): ?>not_registered <?php endif; ?>cf">
    <div class="user_block main_view">
        <?= HtmlHelper::logiINlogOUT() ?>
    </div>
</div>





<div class="page-block">
    <div class="">
        <div class="caption-box">
            <h1><?= Html::encode($page['title']); ?></h1>
        </div>

        <div class="body">
            <?php
            /*
              HtmlPurifier::process($page['body'], [
              'HTML.AllowedElements' => ['p', 'a', 'br', 'b', 'ul', 'li'],
              ]);
             */
            ?>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'contact-form', 'enableClientValidation' => false, 'action' => '/contact-us']); ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'subject') ?>
        <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
        <?=
        $form->field($model, 'verifyCode')->widget(
                Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]
        )
        ?>
        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>
