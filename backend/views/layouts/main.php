<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Tabs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <?php
    $bodyClass = '';
    if (isset(Yii::$app->controller->id))
        $bodyClass .= Yii::$app->controller->id;
    if (isset(Yii::$app->controller->action->actionMethod))
        $bodyClass .= ' ' . Yii::$app->controller->action->actionMethod;
    ?>
    <body class="<?= $bodyClass; ?>">


        <?php $this->beginBody() ?>
        <div class="wrap">

            <?php if (Yii::$app->user->isGuest): ?>

                <div class="container">
                    <?= $content ?>
                </div>

            <?php else: ?>
                <div class="row">

                    <div class="col-md-2 col-lg-2 no-padding">
                        <div class="widget-user-block">

                        </div>
                        <div class="widget-menu-block">

                            <?php
                            Tabs::widget();

                            function getData() {
                                $data = explode('/', Yii::$app->requestedRoute);
                                return $data;
                            }

                            function isActive($in = NULL) {

                                if (!Yii::$app->requestedRoute && !$in)
                                    return 'active';
                                elseif (!$in)
                                    return;

                                $data = getData();

                                if (in_array($data[0], $in))
                                    return 'active';
                                return;
                            }

                            //  echo Html::a('<div><span class="glyphicon glyphicon-leaf pull-left"></span>Главная</div>', ['/'], ['class' => isActive()]);
                            echo Html::a('<div><span class="glyphicon glyphicon-star-empty pull-left"></span>Razzd</div>', ['/razz/index'], ['class' => isActive(['razz'])]);
                            echo Html::a('<div><span class="glyphicon glyphicon-edit pull-left"></span>Pages</div>', ['/pages/index'], ['class' => isActive([ 'pages'])]);
                            echo Html::a('<div><span class="glyphicon glyphicon-bullhorn pull-left"></span>Comments</div>', ['/comments', 'sort' => '-created_at'], ['class' => isActive(['comments'])]);
                            //echo Html::a('<div><span class="glyphicon glyphicon-list-alt pull-left"></span>Structure</div>', ['/taxonomy-vocabulary'], ['class' => isActive(['taxonomy-items', 'taxonomy-vocabulary', 'alias', 'meta'])]);
                            echo Html::a('<div><span class="glyphicon glyphicon-user pull-left"></span>' . Yii::t('app', 'Users') . '</div>', ['/user/admin'], ['class' => isActive(['user'])]);
                            echo Html::a('<div><span class="glyphicon glyphicon-wrench pull-left"></span>Settings</div>', ['/settings'], ['class' => isActive(['settings'])]);
                            echo Html::a('<div><span class="glyphicon  pull-left"></span>' . Yii::t('app', 'Log out') . '</div>', ['../../site/logout'], ['data-method' => 'post']);
                            ?>
                        </div>
                    </div>

                    <div class="col-md-10 col-lg-10 no-padding">
                        <div class="widget-top-informers">
                            <div class="pull-left">
                                <?php
                                // echo Html::a('<span class="glyphicon glyphicon-edit pull-left"></span>',['/']);
                                ?>
                            </div>
                        </div>
                        <div class="widget-sub-menu-block">

                            <?php
                            if (isActive(['razz1'])) {

                                echo Html::a('<div>Videos</div>', ['/videos'], ['class' => isActive(['razz'])]);
                            }



                            if (isActive(['taxonomy-items', 'taxonomy-vocabulary', 'alias', 'meta'])) {

                                echo Html::a('<div>Vocabularies</div>', ['/taxonomy-vocabulary'], ['class' => isActive(['taxonomy-items', 'taxonomy-vocabulary'])]);
                                //  echo Html::a('<div>Мета теги</div>', ['/meta'], ['class' => isActive(['meta'])]);
                                //  echo Html::a('<div>Url</div>', ['/alias'], ['class' => isActive(['alias'])]);
                            }

                            if (isActive(['objects-apartment', 'objects-office'])) {

                                echo Html::a('<div>Квартиры</div>', ['/objects-apartment'], ['class' => isActive(['objects-apartment'])]);
                                echo Html::a('<div>Офисы</div>', ['/objects-office'], ['class' => isActive(['objects-office'])]);
                            }
                            ?>

                        </div>

                        <div class="container-fluid ">
                            <div class="panel main-panel"><?= $content ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
