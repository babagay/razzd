<?php

    use yii\authclient\clients\Twitter;
    use yii\authclient\OAuthToken;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use common\models\TaxonomyIndex;
    use common\helpers\Html as HtmlHelper;

    $this->registerJsFile(Yii::$app->request->baseUrl . '/js/RecordRTC.js');
    $this->registerJsFile(Yii::$app->request->baseUrl . '/js/video.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile(Yii::$app->request->baseUrl . '/js/friends.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]);


    /**
     * @var $model frontend\models\Razz
     */

    $twitterHelper = new \common\helpers\Twitter();
    if ($twitterHelper->amIClient()) {
        $isTwitterClient = 1;
    } else {
        $isTwitterClient = 0;
    }

    $email = '';
    $toggleEmail = false;

    $id = Yii::$app->request->getQueryParam("id");
    $type = Yii::$app->request->getQueryParam("type");

    if(!is_null($type) AND !is_null($id))
    if($type == 'some'){

        $User = new \frontend\models\User();
        $email = $User->getInfo($id)['email'];
        $userName = $User->getInfo($id)['username'];

        $toggleEmail = true;
    }

    if($type == "any")
        $header = "razz anyone";
    elseif($type == "some")
        $header = "razz someone";
    else
        $header = "";


    /* @var $this yii\web\View */
    $this->title = 'Razz ' . $type . "one";

?>

<script>

    function getTwFriends() {

        var data = {}

        if (arguments[0] != undefined && arguments[1] != undefined && arguments[2] != undefined) {
            data['entity'] = arguments[0]
            data['cursor_type'] = arguments[1]
            data['cursor'] = arguments[2]
        }

        var razzType = $('input[type=radio][name = "Razz[type]"]:checked').val() * 1;
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        data['_csrf'] = csrfToken;

        if (razzType !== 1) {
            // Если тип разда НЕ SOMEone, вернуть false
            return false;
        }

        $("#ajax-loader").show()

        $.ajax({
            url: "/razz/get-twitter-friends",
            dataType: "json",
            type: "post",
            cache: false,
            data: data
        }).done(
            function (data) {

                if (data['result'])
                    if (data['result'] == "Ok") {

                        var html = data['html'];

                        $("ul#friends-list").html(html)

                        $("#friends-list").show();

                        $("#friends-list li").off().on("click", function () {

                            if ($(this).hasClass("cursor")) {

                                getTwFriends(
                                    $(this).data('entity'),
                                    $(this).data('cursor_type'),
                                    $(this).data('cursor')
                                )

                            } else {

                                //$("input[name = 'Razz[fb_friend]']").val( $(this).attr("name") );
                                //$("input[name = 'Razz[email]']").val( "twitter@fake.email.com" );
                                $("#friend-selected").html($(this).clone());
                                $("#razz-fbfriendname").val($(this).data('name'));
                                $("#razz-screen_name").val($(this).data('screen_name'));
                                $("#razz-fb_friend").val($(this).data('id'));
                                $("#friends-list").hide();
                            }
                        });


                    } else {
                        console.log(data['message'])
                    }

                $("#ajax-loader").hide()

                setTimeout(function () {
                    //hover mouseenter() mouseleave() mousemove() mouseout() mouseover()
                    $("#friends-list li.cursor.empty").off().on("mouseenter", function () {
                        $(this).css("background","white")
                        $(this).css("cursor","default")
                        return false;
                    });
                }, 500)

            }).fail(
            function (jqXHR, textStatus) {
                console.log("Error in getTwitterFriends()")
                console.log(jqXHR)
                console.log(textStatus)

                $("#ajax-loader").hide()
            }
        );
    }

    $(function () {

        var amITwitterClient = <?= $isTwitterClient ?>;
        amITwitterClient *= 1;
        if (amITwitterClient !== 1){
            $("#razz-email").css("width","100%");
            $("#fb-frinds-search").hide();
            $(".f-ico.show-friends-list").hide();
        }

        $("#fb-frinds-search").off().on("click", function (e, selector) {
            // For Twitter
            var isTwitterClient = <?= $isTwitterClient ?>;
                isTwitterClient *= 1;
            if (isTwitterClient === 1)
                getTwFriends();
            else {
                alert("You shoul be a Twitter client")
            }
        });


        $("form#w0").on("submit", function () {
            // Проверка, выбрана ли категория
            var activeCat = $(".categories li a.active");
            activeCat *= 1;
            if (activeCat.length === 0) {
                return false;
            }

        });

        var fbFriendsList = $(".fb-friends-list #friends-list");
        var showFriendsListBtn = $(".show-friends-list");
        var frFriendIsSelected = false;

        $("#razz-email").on("click", function () {

            fbFriendsList.trigger("friend_unchoosen", [false]);

        });

        $("#razz-email").on("blur", function () {

            setTimeout(function () {

                if (!!frFriendIsSelected)
                    if ($("#friend-selected li").text().length > 1) {
                        $(".field-razz-email").removeClass("has-error");
                        $(".field-razz-email .help-block").html("");
                    }

                if (frFriendIsSelected === false) {
                    $(".field-razz-email").addClass("has-error");
                    if ($("#razz-email").val() == "")
                        $(".field-razz-email .help-block").html("Email cannot be blank.");
                }

            }, 500);

        });

        showFriendsListBtn.on("click", function () {

            //if( $("#razz-fb_friend").val() != "" ) { //
            if (true) {
                fbFriendsList.trigger("friend_choosen", [true]);

                setTimeout(function () {
                    $(".field-razz-email").removeClass("has-error");
                    $(".field-razz-email > .help-block").html("");
                }, 500);
            }
        });

        fbFriendsList.on("click", function () {

            $(this).trigger("friend_choosen", [true]);

            setTimeout(function () {
                $(".field-razz-email").removeClass("has-error");
                $(".field-razz-email > .help-block").html("");
            }, 500);
        });

        fbFriendsList.on("friend_choosen", function (e, flag) {

            if (flag === true) {
                frFriendIsSelected = true;
            }
        });

        fbFriendsList.on("friend_unchoosen", function (e, flag) {

            if (flag === false) {
                frFriendIsSelected = false;
            }
        });


    });
</script>

<div id="ajax-loader" class="hidden-box">
    <img src="<?= Url::base(true) ?>/images/load.gif">
</div>

<div class="header <?php if (Yii::$app->user->isGuest): ?>not_registered <?php endif; ?>cf">
    <div class="user_block main_view">
        <?= HtmlHelper::logiINlogOUT() ?>
    </div>
</div>

<div class="some-any-index">

    <div class="jumbotron">

        <div class="razz-section">
            <?php

                if($header != "")
                    echo "<h1 class=\"header-dorazz\">$header</h1>";

                $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data', 'class' => 'some-any-form someone'],
                ]);
            ?>

            <fieldset>
                <?= $form->field($model, 'type', ['template' => '{input}{error}'])->radioList([
                    $model::SOMEONE => $model::SOMEONE,
                    $model::ANYONE => $model::ANYONE,
                ]); ?>
                <div class="razz-info">
                    <div class="razz-container">
                        <div class="input-section">
                            <div class="input-add someone-elem">
                                <span class="f-ico show-friends-list"></span>
                                <!--<input type="submit" class="facebook-search" value="RAZZ YOUR FRIENDS">-->
                                <div class="fb-friends-list">
                                    <div id="fb-frinds-search">
                                        Razz Your Friends
                                        <ul id="friend-selected">
                                        </ul>
                                    </div>
                                    <?= $form->field($model, 'fb_friend',
                                        ['template' => '{input}{error}'])->hiddenInput() ?>
                                    <?= $form->field($model, 'fbFriendName',
                                        ['template' => '{input}{error}'])->hiddenInput() ?>
                                    <?= $form->field($model, 'screen_name',
                                        ['template' => '{input}{error}'])->hiddenInput() ?>
                                    <?= $form->field($model, 'stream',
                                        ['template' => '{input}{error}'])->hiddenInput() ?>

                                    <ul id="friends-list">

                                    </ul>
                                </div>

                                <?php

                                if($toggleEmail === true){

                                    echo $form->field($model, 'emailFake', ['template' => '{input}{error}'])->textInput([
                                        'maxlength' => 255,
                                        'placeholder' => 'Razz Who ?',
                                        'class' => 'email-search',
                                        'value' => $userName
                                    ]);

                                    echo $form->field($model, 'email',
                                        ['template' => '{input}{error}'])->hiddenInput(['maxlength' => 255]);

                                    ?>
                                <script>
                                    $(function () {
                                        setTimeout(function () {
                                            $("#razz-emailfake").click();
                                            $("#razz-email").val(<?php echo "'$email'"; ?>)
                                            $("#razz-emailfake").prop('disabled', true);
                                        }, 1000);
                                    });
                                </script>
                                    <?php

                                } else

                                    echo $form->field($model, 'email', ['template' => '{input}{error}'])->textInput([
                                        'maxlength' => 255,
                                        'placeholder' => 'Razz Who ?',
                                        'class' => 'email-search',
                                        'value' => $email
                                    ]);

                                ?>

                            </div>
                            <!-- /input-add  -->
                            <?= $form->field($model, 'title', ['template' => '{input}{error}'])->textInput([
                                'maxlength' => 255,
                                'placeholder' => 'Title...',
                            ]) ?>
                        </div>
                        <!-- /input-section  -->
                        <div class="categories">
                            <h2>CATEGORIES:</h2>
                            <ul>
                                <li class="love"><a href="#" data-id="1">LOVE</a></li>
                                <li class="family"><a href="#" data-id="2">FAMILY</a></li>
                                <li class="friends"><a href="#" data-id="3">FRIENDS</a></li>
                                <li class="business"><a href="#" data-id="4">BUSINESS</a></li>
                                <li class="sports"><a href="#" data-id="5">SPORTS</a></li>
                                <li class="politics"><a href="#" data-id="6">POLITICS</a></li>
                                <li class="sex"><a href="#" data-id="7">SEX</a></li>
                                <li class="religion"><a href="#" data-id="8">RELIGION</a></li>
                                <li class="technology"><a href="#" data-id="9">TECHNOLOGY</a></li>
                                <li class="random"><a href="#" data-id="10">RANDOM</a></li>
                            </ul>
                            <div class="help-block-categories">
                                <?= $form->field($model, 'category[]',
                                    ['template' => '{error}{input}'])->checkboxList(ArrayHelper::map(TaxonomyIndex::getTerms(1),
                                    'id', 'name')); ?>
                            </div>
                        </div>
                        <!-- /categories  -->
                        <div class="textareas">
                            <?= $form->field($model, 'message', ['template' => '{input}{error}'])->textArea([
                                'placeholder' => 'Message To Razzee',
                                'class' => 'someone-elem',
                            ]) ?>
                            <?= $form->field($model, 'description', ['template' => '{input}{error}'])->textArea([
                                'placeholder' => 'Razzd Description',
                                'class' => '',
                            ]) ?>
                        </div>
                        <!-- /textareas  -->
                    </div>
                    <!-- /razz-container  -->
                </div>
                <!-- /razz-info  -->

                <div class="razz-visual">
                    <div class="razz-video-block">
                        <h2>RECORD YOUR RAZZ</h2>
                        <!--div class="item">
                            <video id="video" width="100%" height="auto" autoplay="autoplay" ></video>
                            <audio id="audio"  autoplay="autoplay" class="hidden" ></audio>
                            <canvas id="canvas" width="100%" height="auto"></canvas>
                        </div-->
                        <div style="width: 100%">
                            <ziggeo
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
                            .progress-conteiner {
                                height: 6px;
                            }

                            #progress {
                                border: 1px solid #555;
                                height: 6px;
                                width: 260px;
                                margin: 3px auto;
                            }

                            #progress div {
                                height: 4px;
                                background-color: #6dbcdb;
                                width: 0%;
                            }
                        </style>
                        <div class="progress-conteiner">
                            <div id="progress">
                                <div></div>
                            </div>
                        </div>
                        <div class="control">
                            <!--a href="#" class="start btn btn-large" id="recordVideo">start</a-->
                            <!--a href="#" class="stop btn btn-large" id="saveVideo">stop</a-->
                            <!--a href="#" class="stop btn btn-large" id="replayVideo">replay</a-->
                            <!--a href="#" class="stop btn" id="flipCam">flip</a-->
                        </div>
                        <?= $form->field($model, 'fileName',
                            ['template' => '{input}{error}'])->hiddenInput(['maxlength' => 255]) ?>
                    </div>
                    <div class="upload-your-razz-block">
                        <!--div class="upload-your-razz">
                            <span class="upload-info">
                                Accepted file formats: MP4
                                <b>Max upload size: 10MB</b>
                            </span>
                            <?= $form->field($model, 'file',
                                ['template' => '{input}{error}'])->fileInput(['multiple' => false]) ?>

                        </div-->
                        <div class="accept  anyone-elem">
                            <?= $form->field($model, 'accept',
                                ['template' => '{input}{error}'])->checkbox(['label' => 'I ACCEPT THE TERMS & CONDITIONS']); ?>

                        </div>
                        <input type="submit" value="SEND" class="btn someone-elem">
                        <input type="submit" value="SUBMIT" class="btn anyone-elem">
                    </div>


                </div>
                <!-- /razz-visual  -->
            </fieldset>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- /razz-section  -->


    </div>

</div>

<script>

    $(document).ready(function () {

    });


    $(".video-recorder-initial a[data-selector=record-video]").hide();

    var token = undefined
    var embedding = ZiggeoApi.Embed.get("razz-embedding");

    ZiggeoApi.Events.on("submitted", function (data) {
        // Triggered when a video has been uploaded / recorded and processed
        token = data.video.token;
    });

    $("form#w0 input[type=submit]").off().on("click", function () {

        if (token === undefined && !$("form#w0 .field-razz-file").hasClass("has-success")) {
            alert("You must upload or capture a video!")
            return false;
        }

        $("form#w0 #razz-stream").val(token)
        $("form#w0 #razz-filename").val(token)

        $("form#w0").submit();

        return false
    });

    $("#recordVideo").off().on("click", function () {
        var embedding = ZiggeoApi.Embed.get("razz-embedding");
        embedding.record();
    });


</script>