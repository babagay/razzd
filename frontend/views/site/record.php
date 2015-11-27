<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/RecordRTC.js');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/video.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = 'Recording';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .item{ float: left;}
    input[type=button]{
        color: #000;
    }
    .video_cont{ overflow: hidden; background-color: #dee1e2; padding: 30px; border: 1px solid #555;}
</style>
<div class="video_cont">
    <div class="item">
        <video id="video" width="320" height="240" autoplay="autoplay" ></video>
    </div>
    <div class="item">
        <video controls id="video2" width="320" height="240" autoplay="autoplay" ></video>
    </div>
</div>
<div>
    <input id="recordVideo" type="button" value="record" />
    <input id="saveVideo" type="button" value="save" />
    <input id="replayVideo" type="button" value="replay" />
    <!--a href="#" class="stop btn" id="flipCam">flip</a-->
</div>

<div id="progr"> </div>
