<?php

    use yii\helpers\Html;

    /* @var $this yii\web\View */
    /* @var $name string */
    /* @var $message string */
    /* @var $exception Exception */

   // $this->title = $name;
    $this->title = "";
    // $this->params['breadcrumbs'][] = 'Error';

?>
<div class="site-error full-height">

    <h1><span><?= Html::encode($this->title) ?></span></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>



</div>