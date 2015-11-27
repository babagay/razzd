<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/comments.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>


<div class="comments">
    <h3>Comments</h3>

    <div class="comments-list">

        <ul>
            <?php foreach ($model->getComments() as $itm): ?>
                <li>
                    <span class="coment-text"><?= $itm['comment'] ?></span>
                    <div class="comment-info">
                        <?php $userName = $itm['name'] ? $itm['name'] : $itm['username']; ?>
                        <span class="name red-color"><a href="/user/<?= $itm['uid'] ?>"><?= Html::encode($userName) ?></a>  </span>
                        <span class="date red-color"><a href="#"><?= Yii::$app->formatter->asDatetime($itm['created_at']); ?></a></span>
                    </div>
                </li>
            <?php endforeach; ?>

        </ul>
    </div><!-- /comments-list -->
    <div class="comments-form">
        <?php if (!Yii::$app->user->isGuest): ?>
            <?php
            $form = ActiveForm::begin([
                        'action' => '/razz/comment-ajax',
                        'options' => ['class' => 'comment-form'],
            ]);
            ?>
            <?= $form->field($model, 'eid', ['template' => '{input}'])->hiddenInput() ?>
            <fieldset>

                <?= $form->field($model, 'comment', ['template' => '{input}'])->textInput(['maxlength' => 255, 'placeholder' => "Share Your Thoughts..."]) ?>
            </fieldset>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>

    </div><!-- /comments-form -->
</div><!-- /comments  -->
