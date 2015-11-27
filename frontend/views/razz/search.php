<?php

use yii\helpers\Html;
use frontend\models\RazzSearch;

switch (Yii::$app->controller->action->id) {
    case 'related': $this->title = 'RELATED VIDEOS ';
        break;
    case 'vote-on-challenges':
        $this->title = 'VOTE ON CHALLENGES';
        break;
    case 'respond-to-challenges': $this->title = 'RESPOND TO CHALLENGES ';
        break;
    case 'archive': $this->title = 'Archive';
        break;
    default: $this->title = 'Search';
        break;
}

if (!isset($t))
    $t = null;
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
                
                <?php
                            echo $this->render('@app/views/razz/search_form', []);

                            ?>
                                
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
<div class="site-vote-to-chaleng">

    <div class="jumbotron">

        <section class="border-bottom clearfix">
            <?php if ($t == 'some'): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    X
                  </button>
                  YOUR RAZZ HAS BEEN SENT!
                </div>
                <p class="alert-txt"><a href="">VOTE ON</a> OR <a href="">RESPOND TO SOME OTHER CHALLENGES</a> WHILE YOU WAIT FOR A RESPONSE!</p>
            <?php endif; ?>

            <?php if ($t == 'any'): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    X
                  </button>
                  YOUR RAZZ HAS BEEN CREATED!
                </div>
                <p class="alert-txt"><a href="">VOTE ON</a> OR <a href="">RESPOND TO SOME OTHER CHALLENGES</a> WHILE YOU WAIT FOR A RESPONSE!</p>

            <?php endif; ?>
            <h2>
                <?= Html::encode($this->title); ?>
            </h2>
            <?php
            $sort = Yii::$app->request->get('sort');
            $sort = $sort ? $sort : 'date';
            $sort = str_replace('-', '', $sort);

            function q($sort) {
                $url = Yii::$app->request->url;
                parse_str(parse_url($url, PHP_URL_QUERY), $query);
                $query['sort'] = $sort;
                return http_build_query($query);
            }

                $r = Yii::$app->request->get('sort');
                $t = $sort;
            ?>
            <div class="filter-list">
                <span class="sort">Sort by:</span>
                <select class="present facebook-search" data-jcf='{"wrapNative": false, "wrapNativeOnMobile": false}'>
                    <option class="hideme" <?= 'views' == $sort ? 'selected' : ''; ?> data-query="<?= q('views'); ?>">Views</option>
                    <option <?= 'votes' == $sort ? 'selected' : ''; ?> data-query="<?= q('votes'); ?>">Votes</option>
                    <option <?= 'date' == $sort ? 'selected' : ''; ?> data-query="<?= q('date'); ?>">Date</option>
                </select>
                <div class="add-filter">
                    <a href="<?= '?' . q($sort); ?>" class="ascending <?= Yii::$app->request->get('sort') == $sort ? 'active' : ''; ?>">Ascending</a>
                    /
                    <a href="<?= '?' . q('-' . $sort); ?>" class="descending <?= Yii::$app->request->get('sort') == '-' . $sort ? 'active' : ''; ?>">Descending</a>
                </div>
            </div>

            <div class="video-list all-list">
                <?php
                if ($model->items)
                    echo $this->render('item', [
                        'items' => $model->items,
                        'model' => $model,
                        'razzModel' => $razzModel
                    ]);
                else
                    //echo 'No results found for "' . $model->search . '".';
                    echo 'No results found';
                ?>
            </div>

            <div class="pagination-section">
                    <?=
                    \yii\widgets\LinkPager::widget([
                        'pagination' => $model->pages,
                        'options' => ['class' => 'pagination pagination-page center-block clearfix'],
                        'nextPageLabel' => '',
                        'prevPageLabel' => '',
                    ]);
                    ?>
            </div>
        </section><!-- /related-videos  -->
    </div>

</div>