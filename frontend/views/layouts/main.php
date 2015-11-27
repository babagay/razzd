<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Menu;
use yii\widgets\Breadcrumbs;
use frontend\widgets\Alert;
use frontend\assets\OrangeAsset;
use frontend\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

    // Asset Router
    $url = Url::to('');
    $arr = explode('/',$url);

    if( ($arr[0] == $arr[1]) AND $arr[0] == '') {
        // Главная страница
        OrangeAsset::register($this);
    } else {
        AppAsset::register($this);
    }

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="/js/jquery-1.11.0.min.js"></script>
        <script src="/js/jcf.js"></script>
        <script src="/js/jcf.file.js"></script>
        <script src="/js/jcf.select.js"></script>
        <script src="/js/jcf.checkbox.js"></script>

        <script>
            $(function () {
                jcf.replaceAll();
            });
        </script>


        <?php
        /**
         * Ziggeo
         * @link ziggeo.com
         */
            $ziggeo_application_token = Yii::$app->params['ziggeo']['application_token'];
        ?>
        <link rel="stylesheet" href="//assets-cdn.ziggeo.com/css/ziggeo-v1.css" />
        <script src="//assets-cdn.ziggeo.com/js/ziggeo-v1-nojquery.js"></script>
        <script>ZiggeoApi.token = "<?= $ziggeo_application_token ?>";</script>
        <script>ZiggeoApi.Config.cdn = true;</script>
        <script>ZiggeoApi.Config.webrtc = true;</script>
        <script>ZiggeoApi.Config.resumable = true;</script>

        <?= Html::csrfMetaTags() ?>
        <title>Razz&trade; | <?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>

    </head>

    <?php
    $controller = '';
    $action = '';
    if (isset(Yii::$app->controller->id))
        $controller = Yii::$app->controller->id;

    if (isset(Yii::$app->controller->module->requestedAction))
        $action = Yii::$app->controller->module->requestedAction->id;
    ?>
    <body class="<?= $controller . ' ' . $action ?>">

    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.5";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

        <?php $this->beginBody() ?>
        <div class="wrap">
            <div class="container">
                <section class="wrapper">
                    <aside id="sidebar" class="cf">
                            <div class="header <?php if (Yii::$app->user->isGuest): ?>not_registered <?php endif;?>cf mobile_view">
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
                            <div class="cf menu_holder">
                                <header class="header-sidebar">
                                    <h1 class="logo"><a href="/">Razzd</a></h1>
                                    <span class="logo-txt">Who`s right? - who`s wrong?</span>
                                </header>
                                <a href="#" class="mobile_menu_switcher"><i class="fa fa-bars"></i></a>
                        </div>
                        <section class="main-sidebar">
                           <?php

                            $some_active = $home_active = $any_active = $archive_active = $challenges_active = $responds_active = $how_active = '';
                                switch(Url::current()){
                                    case '/site/index':
                                        $home_active = "active";
                                        break;
                                    case '/razz/new/any':
                                        $any_active = "active";
                                        break;
                                    case '/razz/archive':
                                        $archive_active = "active";
                                        break;
                                    case '/razz/new/some':
                                        $some_active = "active";
                                        break;
                                    case '/razz/vote-on-challenges':
                                        $challenges_active = "active";
                                        break;
                                    case '/razz/respond-to-challenges':
                                        $responds_active = "active";
                                        break;
                                    case '/pages/index?id=1':
                                        $how_active = "active";
                                        break;
                                }
                            ?>
                            <nav class="navigation">
                                <ul>
                                    <li class="icon icon-left icon-home <?= $home_active ?>"><a href="/">HOME</a></li>
                                    <li class="icon icon-left icon-globe <?= $some_active ?>"><a href="/razz/new/some">RAZZ SOMEONE</a></li>
                                    <li class="icon icon-left icon-video <?= $any_active ?>"><a href="/razz/new/any">RAZZ ANYONE</a></li>
                                    <li class="icon icon-left icon-clipboard <?= $challenges_active ?>"><a href="/razz/vote-on-challenges">VOTE ON CHALLENGES</a></li>
                                    <li class="icon icon-left icon-respond <?= $responds_active ?>"><a href="/razz/respond-to-challenges">RESPOND TO CHALLENGES</a></li>
                                    <?php
                                        //if(!Yii::$app->user->isGuest){
                                    ?>
                                            <li class="icon icon-left icon-archive <?= $archive_active ?>"><a href="/razz/archive">archive</a></li>
                                    <?php //} ?>
                                    <li class="icon icon-left info-circle <?= $how_active ?>"><a href="/how-it-works">HOW IT WORKS</a></li>
                                    <?php if (Yii::$app->user->isGuest): ?>
                                        <!-- <li class="icon icon-left icon-registr-sign-up registr-sign-up">
                                            <span class="popup-ico"><span class="popup-click sign-in-popup-click">SIGN IN </span> / <span class="popup-click register-popup-click"> REGISTER</span></span>
                                        </li> -->
                                    <?php else: ?>
                                        <!-- <li class="ico-071 my-profile icon icon-left icon-registr-sign-up"><span class=""><a class="" href="/user/<?= Yii::$app->user->id ?>">MY PROFILE </a> / <a href="/site/logout" data-method="post"> LOG OUT</a></span></li> -->
                                    <?php endif; ?>
                                </ul>
                            </nav> 
                            <div class="apps_buttons_holder">
                                <a href="#" class="appmarket"></a>
                                <a href="#" class="googleplay"></a>
                            </div>
                        </section>
                        <div class="header <?php if (Yii::$app->user->isGuest): ?>not_registered <?php endif;?>cf mobile_search">
                            <div class="main_search">
    
                                    <?php
                                                echo $this->render('@app/views/razz/search_form', []);

                                                ?>
                                                    
                                </div>
                        </div>
                    </aside><!-- /sidebar  -->
                    <div id="main">

                       <?=
                        Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ])
                        ?>
                        <?= Alert::widget() ?>
                        <?= $content ?>
                        <footer id="footer">
                            <span class="copy">&#169; 2015 RAZZD LLC. All Rights Reserved.</span>
                            <ul class="footer-links">
                                <li class="<?= Yii::$app->request->pathInfo == '' ? 'current-menu-item' : '' ?>"><a href="/">HOME</a></li>
                                <li class="<?= Yii::$app->request->pathInfo == 'about' ? 'current-menu-item' : '' ?>"> <a href="/about">ABOUT RAZZD</a></li>
                                <li class="<?= Yii::$app->request->pathInfo == 'how-it-works' ? 'current-menu-item' : '' ?>"><a href="/how-it-works">HOW IT WORKS</a></li>
                                <li class="<?= Yii::$app->request->pathInfo == 'terms-of-service' ? 'current-menu-item' : '' ?>"><a href="/terms-of-service">TERMS OF SERVICE</a></li>
                                <li class="<?= Yii::$app->request->pathInfo == 'privacy-policy' ? 'current-menu-item' : '' ?>"><a href="/privacy-policy">PRIVACY POLICY</a></li>
                                <li class="<?= Yii::$app->request->pathInfo == 'contact-us' ? 'current-menu-item' : '' ?>"><a href="/contact-us">CONTACT US</a></li>
                            </ul>
                        </footer>
                    </div><!-- /main  -->

                </section><!-- /wrapper  -->
                <div class="popup sign-in-popup">
                    <span  class="popup_bg"></span>
                    <div class="popup-block">
                        <h2>SIGN IN</h2>
                        <form action="#" class="form-holder">
                            <fieldset>
                                <input type="text"  placeholder="USERNAME:">
                                <input type="text"  placeholder="PASSWORD:">
                                <div class="popup-submits">
                                    <input type="submit" value="REGISTER">
                                    <input type="submit" value="SIGN IN">
                                </div>
                            </fieldset>
                        </form>
                        <div class="popup-links">
                            <a href="#" class="facebook btn">SIGN IN WITH FACEBOOK</a>
                            <a href="#" class="twitter btn">SIGN IN WITH TWITTER</a>
                        </div>
                    </div><!-- /popup-block  -->
                </div><!-- /popup  -->
                <div class="popup registr-popup">
                    <span  class="popup_bg"></span>
                    <div class="popup-block">
                        <form action="#" class="form-holder">
                            <h2>REGISTRATION</h2>
                            <fieldset>
                                <input type="text"  placeholder="FULL NAME">
                                <input type="text"  placeholder="USERNAME">
                                <input type="text"  placeholder="EMAIL">
                                <input type="text"  placeholder="PASSWORD">
                                <input type="text"  placeholder="CONFIRM PASSWORD">
                                <div class="popup-submits full-block">
                                    <input type="submit" value="REGISTER">
                                </div>
                            </fieldset>
                        </form>
                        <div class="popup-links">
                            <a href="#" class="facebook btn">SIGN IN WITH FACEBOOK</a>
                            <a href="#" class="twitter btn">SIGN IN WITH TWITTER</a>
                        </div>
                    </div><!-- /popup-block  -->
                </div><!-- /popup  -->
            </div>
        </div>
        <?php $this->endBody() ?>

<script type="text/javascript" src="/js/responsiveCarousel.min.js"></script>
    <script type="text/javascript">
    $(function(){

        //  $('.crsl-items').carousel({
        // visible: 3,
        // itemMinWidth: 300,
        // itemEqualHeight: 370,
        // itemMargin: 30,
        // }); 

        //  $('.crsl-items-voted').carousel({
        // visible: 3,
        // itemMinWidth: 300,
        // itemEqualHeight: 370,
        // itemMargin: 30,
        // });

        //  $('.crsl-items-respond').carousel({
        // visible: 3,
        // itemMinWidth: 300,
        // itemEqualHeight: 370,
        // itemMargin: 30,
        // });
         
    });



    </script>

    <script type="text/javascript" src="/js/jquery.qtip.min.js"></script>
    <script type="text/javascript" src="/js/imagesloaded.pkg.min.js"></script>

        <script type="text/javascript">var switchTo5x=true;</script>
        <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
        <script type="text/javascript">stLight.options({publisher: "8569fd31-22aa-4e0d-8e9f-9b84d720a249", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>

    </body>
</html>
<?php $this->endPage() ?>
