<?php

    use yii\helpers\Html;
    use common\helpers\Html as HtmlHelper;
    use yii\widgets\Breadcrumbs;
    use yii\helpers\HtmlPurifier;

    /* @var $this yii\web\View */
    if (!$this->title) {
        $this->title = Html::encode($page['title']);
    }

    //$this->params['breadcrumbs'][] = $this->title;

?>

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
            <?=
            HtmlPurifier::process($page['body'], [
                'HTML.AllowedElements' => ['p', 'a', 'br', 'b', 'ul', 'li'],
            ]);
            ?>
        </div>


    </div>
</div>
<div class="push"></div>
