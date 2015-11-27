<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\widgets\ActiveForm;


$this->registerJsFile(Yii::$app->request->baseUrl . '/js/RecordRTC.js');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/video.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

    /**
     * @var \frontend\models\User $userModel
     */

/* @var $this yii\web\View */
$this->title = Html::encode($model->title);
$description = HtmlPurifier::process($model->description, ['HTML.AllowedElements' => []]);
Yii::$app->view->registerMetaTag(['name' => 'og:image', 'content' => 'http://' . $_SERVER['HTTP_HOST'] . '/images/logo.gif'], 'image');
Yii::$app->view->registerMetaTag(['name' => 'og:title', 'content' => $this->title], 'title');
//Yii::$app->view->registerMetaTag(['name' => 'og:url', 'content' => '//' . $_SERVER['HTTP_HOST'] . '/' . Yii::$app->request->pathInfo], 'url');
Yii::$app->view->registerMetaTag(['name' => 'og:description', 'content' => $description], 'description');

/**
    facebook-тэги Формировать на лету

     <meta property="og:url"                content="http://adv.razzd.sfdevserver.com/contact-us" />
     <meta property="og:site_name"          content="Razz" />
     <meta property="og:type"               content="video.movie" />
     <meta property="og:title"              content="When Great Minds Don’t Think Alike" />
     <meta property="og:description"        content="How much does culture influence creative thinking?" />
     <meta property="og:image"              content="http://static01.nyt.com/images/2015/02/19/arts/international/19iht-btnumbers19A/19iht-btnumbers19A-facebookJumbo-v2.jpg" />
*/

$this->registerJsFile('/js/respond.js');

$Ziggeo = new Ziggeo(Yii::$app->params['ziggeo']['application_token'], Yii::$app->params['ziggeo']['private_key'], Yii::$app->params['ziggeo']['encryption_key']);

    $RazzTitle = $userModel->getInfo($model->uid)['username'] . " HAS RAZZD YOU";

    if( substr_count(Yii::$app->request->referrer, "respond-to-challenges") > 0 ){
        $RazzTitle = "Respond to " . Html::encode($model->title);
    }


?>

        <div class="header <?php if (Yii::$app->user->isGuest): ?>not_registered <?php endif;?>cf">
            <div class="user_block mobile_view">
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
            <div class="main_search">
                
                <?php   echo $this->render('@app/views/razz/search_form', []);                            ?>
                                
            </div>
            <div class="user_block main_view">
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



<div class="site-raze-respons">

    <div class="jumbotron">

        <div class="razz-section  border-bottom">
            <?php
            $form = ActiveForm::begin([
                        'options' => ['enctype' => 'multipart/form-data', 'class' => 'some-any-form'],
            ]);
            ?>
            <fieldset>
                <div class="razz-info">
                    <div class="rotate-clip"></div>
                    <h2 class="title-1"><?= $RazzTitle  ?>
                        <span class="txt-title-top">Watch the video below then upload or record your counter arguement.</span>
                    </h2>

                    <input type="hidden" value="0" name="rotary">
                    <input type="hidden" value="0" name="initW">
                    <input type="hidden" value="0" name="initH">
                    <input type="hidden" value="<?= $model->stream ?>" name="video_src">
                    <input type="hidden" value="<?= Yii::$app->user->id ?>" name="user_id">

                    <div class="video-hidden">
                        <?php
                            $video = "";
                            if( preg_match('/^http(s)?:\/\/([\w\d-\.]*)(.)*\.(png|jpg|jpeg|gif)$/i',$model->stream_preview) ){
                                $video = "<video id=\"video2\" width=\"100%\" height=\"auto\" src=\"{$model->stream}\" poster=\"{$model->stream_preview}\" controls></video>";
                                echo $video;

                            } elseif( preg_match('/([\w\d])*/i',$model->stream) ){

                                if( preg_match('/(http)/i',$model->stream) ){

                                } else {
                                    ?>
                                    <ziggeo ziggeo-video=<?= $model->stream ?>
                                            ziggeo-responsive="true"
                                            ziggeo-id="razz-respond"
                                            ziggeo-disable_first_screen="false"
                                            >
                                    </ziggeo>
                                    <?php
                                }

                            }


                        ?>


                    </div>

                    <h3 class="title-2 txt-title-info"><?= Html::encode($model->title); ?></h3>
                    <p class="txt-content-info">                        
                        <?=
                        HtmlPurifier::process($model->description, [
                            'HTML.AllowedElements' => [],
                        ]);
                        ?>
                    </p>  

                    <button class="btn btn-blue  margin-bottom rotate" style="display:none;">Rotate</button>

                </div><!-- /razz-info  -->


                <?php if (!Yii::$app->user->id): ?>
                    <a href="/login" >Login to respond</a>
                <?php elseif ($model->uid != Yii::$app->user->id): ?>

                    <div class="razz-visual">
                        <div class="razz-video-block">

                            <h2>RECORD YOUR RAZZ</h2>
                            <div class="item recorder-box">
                                <!--video id="video" width="100%" height="auto" autoplay="autoplay" ></video-->
                                <!--canvas id="canvas" width="100%" height="auto"></canvas-->
                                <script>
                                    // Подстановка w и h не работает при выбранной опции ziggeo-responsive="true"
                                    var w = $(".recorder-box").width() * 0.95
                                    var base_width = 320
                                    var h = w/1.3333
/*
                                    // Не пашет опция загрузки видео для плеера, который отрисовывается динамически

                                    $(".recorder-box").prepend(
                                    '<ziggeo ziggeo-width='+w+'  ziggeo-height='+h+
                                        ' ziggeo-id = "razz-embedding" '+
                                        ' ziggeo-rerecordings=3 ' + '' +
                                        ' ziggeo-disable_first_screen="true" '+
                                        ' ziggeo-responsive="true" '+
                                        ' ziggeo-disable_first_screen="false" '+
                                        ' ziggeo-perms="allowupload" '+
                                        ' ziggeo-limit=<?= Yii::$app->params['ziggeo']['record_duration']; ?> '+
                                    '></ziggeo>')
*/
                                </script>

                                <ziggeo
                                    ziggeo-width=320
                                    ziggeo-height=240
                                    ziggeo-id="razz-embedding"
                                    ziggeo-responsive="true"
                                    ziggeo-rerecordings=3
                                    ziggeo-disable_first_screen="false"
                                    ziggeo-perms="allowupload"
                                    ziggeo-limit=<?= Yii::$app->params['ziggeo']['record_duration']; ?>
                                    >
                                </ziggeo>

                            </div>
                            <style>
                                .progress-conteiner{ height: 6px;}
                                #progress{ border: 1px solid #555; height: 6px; width: 260px; margin: 3px auto; }
                                #progress div{ height: 4px; background-color: #6dbcdb; width: 0%;}
                            </style>
                            <div class="progress-conteiner">
                                <div id="progress">
                                    <div></div>
                                </div>
                            </div>
                            <div class="control">
                                <!--a href="#" class="start btn btn-large" id="recordVideo">start</a-->
                                <!--a href="#" class="stop btn btn-large" id="saveVideo">stop</-->
                                <!--a href="#" class="stop btn btn-large" id="replayVideo">replay</-->
                                <!--a href="#" class="stop btn" id="flipCam">flip</a-->
                            </div>
                            <?= $form->field($model, 'fileName', ['template' => '{input}{error}'])->hiddenInput(['maxlength' => 255]) ?>
                            
                        </div>

                        <div class="upload-your-razz-block">
                            <!--div class="upload-your-razz">
                                <span class="upload-info">
                                    ACCEPTED FILE FORMATS: MP4
                                   <b>MAX UPLOAD SIZE: 10MB</b>
                                </span>
                                <?= $form->field($model, 'file', ['template' => '{input}{error}'])->fileInput(['multiple' => false]) ?>
                            </div-->
                            <!--input type="hidden" value="" name="snapshot"-->
                            <input type="submit" value="SEND" class="btn someone-elem">
                        </div>

                    </div><!-- /razz-visual  -->


                <?php endif; ?>



            </fieldset>
            <?php ActiveForm::end(); ?>
        </div><!-- /razz-section  -->

    </div>

</div>

<script>
    var token = undefined
    var embedding = ZiggeoApi.Embed.get("razz-embedding");

    ZiggeoApi.Events.on("submitted", function (data) {
        // Triggered when a video has been uploaded / recorded and processed
        token = data.video.token;
    });

    $("#recordVideo").off().on("click",function(){
        var embedding = ZiggeoApi.Embed.get("razz-embedding");
        embedding.record();
    });

    $("form#w1 input[type=submit]").off().on("click", function () {

        if(token === undefined){
            if( $("form#w0 .field-razz-file").hasClass("has-success") || $("form#w1 .field-razz-file").hasClass("has-success") ) {
            } else {
                alert("You must upload or capture a video!")
                return false;
            }
        }

        $("form#w1 #razz-stream").val(token)
        $("form#w1 #razz-filename").val(token)

        $("form#w1").submit();

        return false
    });
</script>

