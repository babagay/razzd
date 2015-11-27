<?php

use \backend\models\Settings;

$params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
        'id' => 'app-frontend',
        'name' => 'Razzd',
        'language' => 'en',
        'homeUrl' => '/',
        'basePath' => dirname(__DIR__),
        'bootstrap' => ['log'],
        'controllerNamespace' => 'frontend\controllers',
        'modules' => [
                'user' => [
                        'class' => 'dektrium\user\Module',
                        'enableUnconfirmedLogin' => false,
                        'confirmWithin' => 21600,
                        'cost' => 12,
                        'admins' => ['root'],
//                        'enablePasswordRecovery' => false,
                        'modelMap' => [
                                'RegistrationForm' => 'frontend\models\RegistrationForm',
                                'User' => [
                                        'class' => 'frontend\models\User',
                                        'on user_create_done' => function ($event) {
                                            frontend\models\User::assignRazz($event->sender, $event->sender->accounts);
                                        },
                                        'on user_register_done' => function ($event) {
                                            frontend\models\User::assignRazz($event->sender, $event->sender->accounts);
                                        },
                                     
                                ],
                                'Account' => 'frontend\models\Account',
                                'Profile' => 'frontend\models\Profile',
                        //'User' => 'frontend\models\User',
                        ],
                        'controllerMap' => [
                                'profile' => 'frontend\controllers\user\ProfileController',
                                'registration' => 'frontend\controllers\user\RegistrationController',
                        ],
                        'mailer' => [
                                'viewPath' => '@common/mail',
                        ],
                ],
        ],
        'components' => [
                'authClientCollection' => [
                    'class'   => \yii\authclient\Collection::className(),
                    'clients' => [
                        'twitter' => [
                            'class'          => 'frontend\controllers\auth\Twitter',
                            'consumerKey'    => 'InZnNj4Moo8BlBOWhsWLr4Fdq',
                            'consumerSecret' => 'ziWfRtmJ3S6PiMiI14a6Ben2XUVti3AIP6iZAQcuRr6b8vhyc3',
                        ],
                    ],
                ],
                'request' => [
                        'baseUrl' => '',
                ],
                'response' => [
                        'class' => 'yii\web\Response',
                        'on beforeSend' => function ($event) {
                            $response = $event->sender;

                            if (isset($response->data['data']['status'])) {
                                $response->data = [
                                        'status' => $response->statusCode,
                                        'statusText' => $response->statusText,
                                        'data' => $response->data['data'],
                                        'message' => isset($response->data['message']) ? $response->data['message'] : '',
                                ];
                            }
                        },
                        ],
                        'urlManager' => [
                                'rules' => [
                                        [
                                                'class' => 'yii\rest\UrlRule',
                                                'controller' => 'api',
                                                'extraPatterns' => [
                                                        'GET userinfo' => 'userinfo',
                                                        'GET forgotpassword' => 'forgotpassword',
                                                        'GET login' => 'login',
                                                        'GET registration' => 'registration',
                                                        'GET search' => 'search',
                                                        'GET razz' => 'razz',
                                                        'GET razz-vote' => 'razz-vote',
                                                        'GET razz-create' => 'razz-create',
                                                        'GET razz-respond' => 'razz-respond',
                                                ],
                                        ],
                                        ['class' => 'frontend\components\SiteUrlRule'],
                                        'razz/<id:\d+>' => 'razz/view',
                                        'razz/new/<type:\w+>' => 'razz/new',
                                        'razz/new/<type:\w+>/<id:\d+>' => 'razz/new',
                                        'razz/related/<id:\d+>' => 'razz/related',
                                        'razz/respond/<id:\d+>' => 'razz/respond',
                                        'razz/archive/<id:\d+>' => 'razz/archive',
                                ],
                        ],
                        'meta' => [
                                'class' => 'frontend\components\Meta',
                        ],
                        'log' => [
                                'traceLevel' => YII_DEBUG ? 3 : 0,
                                'targets' => [
                                        [
                                                'class' => 'yii\log\FileTarget',
                                                'levels' => ['error', 'warning'],
                                        ],
                                ],
                        ],
                        'view' => [
                                'theme' => [
                                        'pathMap' => [
                                                '@dektrium/user/views' => '@frontend/views/user'
                                        ],
                                ],
                        ],
                        'errorHandler' => [
                                'errorAction' => 'site/error',
                        ],
                ],
                'params' => $params,
        ];
        