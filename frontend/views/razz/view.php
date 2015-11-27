<?php

    use common\components\Paginator;

    use frontend\models\Account;
    use frontend\models\RazzSearch;
    use frontend\models\RestApi;
    use yii\authclient\clients\Twitter;
    use yii\authclient\OAuthToken;
    use yii\helpers\Html;
    use frontend\models\Razz;
    use common\helpers\DataHelper;
    use common\helpers\Html as HtmlHelper;
    use frontend\widgets\rating\Rating;
    use yii\helpers\HtmlPurifier;
    use yii\widgets\ActiveForm;
    use frontend\assets\OrangeAsset;
    use frontend\assets\AppAsset;
    use yii\helpers\Url;
    use common\helpers\Ziggeo as ZiggeoHelper;
    use yii\web\HttpException;

    /**
     * @var $razzModel frontend\models\Razz
      */

    $this->registerJsFile(Yii::$app->request->baseUrl . '/js/video.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    // Asset Router
    $url = Url::to('');
    $arr = explode('/',$url);

    if( ($arr[0] == $arr[1]) AND $arr[0] == '') {
        // Главная страница
        OrangeAsset::register($this);
    } else {
        AppAsset::register($this);
    }

    /* @var $this yii\web\View */
    if(isset($object)) {
        $this->title = Html::encode($object['title']);

        $description = HtmlPurifier::process($object['description'], ['HTML.AllowedElements' => []]);
    }

    if(isset($description)) {
        Yii::$app->view->registerMetaTag([
            'name' => 'og:image',
            'content' => '//' . $_SERVER['HTTP_HOST'] . '/images/logo.gif'
        ], 'image');
        Yii::$app->view->registerMetaTag(['name' => 'og:title', 'content' => $this->title], 'title');
        Yii::$app->view->registerMetaTag(['name' => 'og:description', 'content' => $description], 'description');
    }

?>

<div class="site-index">

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
                <?= $this->render('@app/views/razz/search_form', []) ?>
            </div>
                <div class="user_block main_view">
                    <?= HtmlHelper::logiINlogOUT() ?>
                </div>
        </div>

    <div class="jumbotron">
        <?php
            if(isset($object)) {
                $razzdViews = $razzModel->getViewsTotalByRid($object['id']);
        ?>
        <section class="main-video border-bottom">
            <h2 class="title-visual-time">
                <?= Html::encode($object['title']); ?>
                <span class="time highlight">TIME REMAINING:
                    <?php

                        $razzModel->addView($object['id'],Yii::$app->user->id);

                        if ($object['created_at'] + Razz::DAYS > time())
                            echo DataHelper::downcounter(date("Y-m-d H:i:s", ($object['created_at'] + Razz::DAYS)));
                        else
                            echo 'ENDED';
                    ?>
                    </span>
            </h2>
            <div class="visual">
                <div class="visual-sections">
                    <div class="visual-section">
                        <div class="visual-person">
                            <?= ZiggeoHelper::getImage($object['id'], $object['stream'],$object['stream_preview'], false, "video-preview", true) ?>
                        </div>
                        <div class="info-person" id="r1">
                            <ul class="vote-info">
                                <li><span class="vote-info-txt <?php if ($object['my_votes'] == $object['responder_votes'])if ( $object['my_votes'] == 0 ) echo "empty"; ?>">
                                        <?php
                                            if ($object['my_votes'] > $object['responder_votes'])
                                                echo 'WINNING :)';
                                            if ($object['my_votes'] < $object['responder_votes'])
                                                echo 'LOSING :(';
                                            if ($object['my_votes'] == $object['responder_votes'])
                                                if ( $object['my_votes'] != 0 )
                                                    echo 'Draw';
                                        ?>
                                    </span></li>
                                <?php if (($object['created_at'] + Razz::DAYS) > time() && !Yii::$app->user->isGuest){ ?>
                                    <li class="vote-section">
                                        <?php $rating = Rating::begin(['nid' => $object['id'], 'model' => 'Razz', 'return_id' => 'r1']); ?>
                                        <?=
                                            $rating->rate('my', [
                                                '' => '',
                                                1 => 'vote',
                                            ]);
                                        ?>
                                        <?php Rating::end(); ?>
                                    </li>


                                <?php }
                                    if(Yii::$app->user->id > 0) {
                                        $votedForMy = Rating::amIVoted($object['id'], "my");
                                        if ($votedForMy > 0) {
                                            ?>
                                            <li class="vote-section">
                                                <a href="#" onclick="javascript:return false;">voted</a>
                                            </li>
                                        <?php }
                                    }
                                ?>
                            </ul>
                            <span class="title"><?= Html::a($object['name1'] . ' <span class="r1">(' . $object['my_votes'] . ')</span>', ['/user/profile/show', 'id' => $object['uid']]); ?></span>

                        </div>
                        <!-- info-person  -->
                    </div>
                    <!-- visual-section  -->
                    <div class="visual-section">
                        <div class="visual-person">
                            <?= ZiggeoHelper::getImage($object['id'], $object['responder_stream'],$object['responder_stream_preview'], true, "video-preview", true) ?>
                        </div>
                        <div class="info-person" id="r2">
                            <ul class="vote-info">
                                <li><span class="vote-info-txt <?php if ($object['my_votes'] == $object['responder_votes'])if ( $object['my_votes'] == 0 ) echo "empty"; ?>">
                                        <?php

                                            if ($object['my_votes'] < $object['responder_votes'])
                                                echo 'WINNING :)';
                                            if ($object['my_votes'] > $object['responder_votes'])
                                                echo 'LOSING :(';
                                            if ($object['my_votes'] == $object['responder_votes'])
                                            if ( $object['my_votes'] != 0 )
                                                echo 'Draw';
                                        ?>
                                    </span></li>
                                <?php if (($object['created_at'] + Razz::DAYS) > time() && !Yii::$app->user->isGuest){ ?>
                                    <li class="vote-section">
                                        <?php $rating = Rating::begin(['nid' => $object['id'], 'model' => 'Razz', 'return_id' => 'r2']); ?>
                                        <?=
                                            $rating->rate('responder', [
                                                '' => '',
                                                1 => 'vote',
                                            ]);
                                        ?>
                                        <?php Rating::end(); ?>
                                    </li>
                                <?php }
                                    if(Yii::$app->user->id > 0) {
                                        $votedForResponder = Rating::amIVoted($object['id'], "responder");
                                        if ($votedForResponder > 0) {
                                            ?>
                                            <li class="vote-section">
                                                <a href="#" onclick="javascript:return false;">voted</a>
                                            </li>
                                        <?php }
                                    }
                                ?>
                            </ul>
                            <span class="title"><?= Html::a($object['name2'] . ' <span class="r2">(' . $object['responder_votes'] . ')</span>', ['/user/profile/show', 'id' => $object['responder_uid']]); ?></span>

                        </div>
                        <!-- info-person  -->
                    </div>
                    <!-- visual-section  -->
                </div>
                <!-- visual-sections  -->
                <div class="description-video">
                    <ul class="list-unstyled nav-justified table_view">
                        <li>
                            <span>CATEGORY:</span>
                            <?= Html::a($object['category'], ['/razz/search', 'RazzSearch[category]' => $object['tid'], 'RazzSearch[category][]' => $object['tid']]); ?>
                        </li>
                        <li><span>VIEWS:</span>
                               <span>
                                    <?= $razzdViews ?>
                               </span>
                        </li>
                        <li>
                            <span>SHARE:</span>
                            <div class="social-block">

                                <?php
                                    $st_summary = HtmlPurifier::process($object['message'], [
                                        'HTML.AllowedElements' => [],
                                    ]);
                                    $st_summary .="\n\r";
                                    $st_summary .= HtmlPurifier::process($object['description'], [
                                        'HTML.AllowedElements' => [],
                                    ]);
                                    $st_summary .="\n\r";
                                    $st_summary .="Go to answer the Razzd: http://" . $_SERVER['HTTP_HOST'] . '/razz/' . $object['id'];

                                    $st_title = Html::encode(ucfirst($object['name1']) . ' has sent you a Razzd  ‘' . $object['title'] . '’');
                                ?>
                                <!--
                                <span class='st_facebook_large' displayText='Facebook'></span>
                                <span class='st_twitter_large' displayText='Tweet'></span>
                                <span class='st_googleplus_large' displayText='Google +'></span>
                                <span class='st_email_large' displayText='Email' st_title="<?= $st_title; ?>" st_summary="<?= $st_summary; ?>"></span>
                                <script type="text/javascript">
                                    var switchTo5x = true;
                                </script>
                                <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
                                <script type="text/javascript">
                                    stLight.options({
                                        publisher: "7f004ac0-25ae-4bc6-af7c-101f2d210dcf",
                                        doNotHash: true,
                                        doNotCopy: false,
                                        hashAddressBar: true
                                    });
                                </script>
                                -->
                                <span class='st_facebook' displayText=''></span>
                                <span class='st_linkedin' displayText=''></span>
                                <span class='st_pinterest' displayText=''></span>
                                <span class='st_stumbleupon' displayText=''></span>
                                <span class='st_reddit' displayText=''></span>
                                <span class='st_twitter' displayText=''></span>
                                <span class='st_digg' displayText=''></span>
                                <span class='st_tumblr' displayText=''></span>
                                <span class='st_googleplus' displayText=''></span>
                                <span class='st_blogger' displayText=''></span>
                                <span class='st_email' displayText=''></span>

                            </div>
                            <!-- /social-block  -->
                        </li>
                    </ul>

                    <div class="description-section">DESCRIPTION:
                        <?php
                            if(isset($object)) {
                                echo  HtmlPurifier::process($object['description'], [
                                    'HTML.AllowedElements' => [],
                                ]);
                            }
                        ?>
                    </div>
                </div>
                <!-- /description-video  -->
            </div>
            <!-- /visual  -->

            <?php
                if(isset($commentModel)) {
                    echo $this->render('comments', [
                        'model' => $commentModel
                    ]);
                }
            ?>

        </section>
        <!-- /main-video  -->
        <?php } else { ?>

        <section id="no-razzd">
            <h1 class="no-razzd">no-razzd</h1> <span style="color:#d85950">do it fast!</span>
        </section>

        <?php } ?>

        <?php
            //        $m = new RazzSearch();
            //        $m->responder = RazzSearch::NORESPONDER;
            //        $m->search();
            //
            //        $RtC_paginator = Paginator::widget([
            //                        'pagination' => $m->pages,
            //                        'options' => ['class' => 'pagination pagination-page clearfix'],
            //                        'nextPageLabel' => '',
            //                        'prevPageLabel' => '',
            //                        'url' => "razz/respond-to-challenges/?page=",
            //                        'disableHighlightActive' => true,
            //                        'noArrows' => true,
            //                        'wrapperClass' => 'pagination-section pos-right'
            //        ]);
            //
            //        $m->responder = RazzSearch::RESPONDER;
            //        $m->search();
            //
            //        $VoC_paginator = Paginator::widget([
            //                        'pagination' => $m->pages,
            //                        'options' => ['class' => 'pagination pagination-page clearfix'],
            //                        'nextPageLabel' => '',
            //                        'prevPageLabel' => '',
            //                        'url' => "razz/vote-on-challenges/?page=",
            //                        'disableHighlightActive' => true,
            //                        'noArrows' => true,
            //                        'wrapperClass' => 'pagination-section pos-right'
            //        ]);
            //
            //        $m->responder = RazzSearch::NORESPONDER;
            //        $cat_id = $object['tid'];
            //        $m->category = $cat_id;
            //        $m->search();
            //
            //        $rel_videos_url = (bool) $cat_id ? "razz/related/$cat_id/?page=" : "razz/related/?page=1";
            //
            //        $RV_paginator = Paginator::widget([
            //                        'pagination' => $m->pages,
            //                        'options' => ['class' => 'pagination pagination-page clearfix'],
            //                        'nextPageLabel' => '',
            //                        'prevPageLabel' => '',
            //                        'url' => $rel_videos_url,
            //                        'disableHighlightActive' => true,
            //                        'noArrows' => true,
            //                        'wrapperClass' => 'pagination-section pos-right'
            //        ]);
        ?>

        <?php
            $htmRelated = "";
            if(isset($object)) {
                $htmRelated = $this->render('item', [
                    'items' => $razzSearch->getRazzRelated($object['tid'], $object['id']),
                    'razzModel' => $razzModel
                ]);
            }

            if( trim($htmRelated) != "" ){
                ?>
                <section class="related-videos border-bottom row-position">
                    <h2>                RELATED VIDEOS            </h2>
                    <nav class="slidernav">
                        <div id="navbtns" class="clearfix">
                            <a href="#" class="previous"></a>
                            <a href="#" class="next"></a>
                        </div>
                    </nav>
                    <div class="crsl-items" data-navigation="navbtns">
                        <div class="video-list crsl-wrap">
                            <?php    echo $htmRelated;   ?>
                        </div>
                        <!-- /video-list  -->
                    </div>
                </section>
                <!-- /related-videos  -->
            <?php } ?>

        <section class="vote-on-challenges border-bottom row-position">
            <?php

                $vote_on_challenges_html = "";
                if(isset($razzSearch)) {
                    if(isset($razzModel)) {
                        $vote_on_challenges_html = $this->render('item', [
                            'items' => $razzSearch->getRazzVoteOnChallenges(),
                            'razzModel' => $razzModel
                        ]);
                    }
                }

                if(trim($vote_on_challenges_html) != "") {
            ?>
            <h2>
                vote on challenges
            </h2>
            <nav class="slidernav">
                <div id="vote-navbtns" class="clearfix">
                    <a href="#" class="previous"></a>
                    <a href="#" class="next"></a>
                </div>
            </nav>

            <div class="crsl-items-voted" data-navigation="vote-navbtns">
                <div class="video-list crsl-wrap">
                    <?= $vote_on_challenges_html ?>
                </div>
                <!-- /video-list  -->
            </div>
                <?php } ?>

        </section>
        <!-- /vote-on-challenges  -->
        
        <?php
            $respond_to_challenges_html = "";
            
            if(!isset($razzSearch)){
                $razzSearch = new RazzSearch();
                $razzModel = new frontend\models\Razz();
            }

            $respond_to_challenges_html = $this->render('item', [
                'items' => $razzSearch->getRazzRespondToChallenges(),
                'razzModel' => $razzModel
            ]);

            if( trim($respond_to_challenges_html) != "" ){
        ?>
        <section class="respond-to-challenges row-position">
            <h2>
                RESPOND TO CHALLENGES
            </h2>
            <nav class="slidernav">
                <div id="respond-navbtns" class="clearfix">
                    <a href="#" class="previous"></a>
                    <a href="#" class="next"></a>
                </div>
            </nav>
            <div class="crsl-items-respond" data-navigation="respond-navbtns">
                <div class="video-list video-list-small crsl-wrap">
                    <?= $respond_to_challenges_html ?>
                </div>
            </div>
        </section>
        <!-- /respond-to-challanges  -->
            <?php } ?>

    </div>

</div>

<script>
    $(document).ready(function () {

        $(".vote-section form#w1").find(".br-widget a").on("click",function(){
            $("#r1 .vote-info").append("<li class=\"vote-section\">" +
                "<a href=\"#\" onclick=\"javascript:return false;\">voted</a>"+
                "</li>")
        });

        $(".vote-section form#w2").find(".br-widget a").on("click",function(){
            $("#r2 .vote-info").append("<li class=\"vote-section\">" +
                "<a href=\"#\" onclick=\"javascript:return false;\">voted</a>"+
                "</li>")
        });
    });
</script>