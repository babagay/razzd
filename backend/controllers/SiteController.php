<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'logout', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['copy'],
                    ],
                    [
                        'actions' => ['index', 'author', 'image-upload', 'images-get', 'delete-file'],
                        'allow' => true,
                        'roles' => ['editor'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => '/files/editor/', // Directory URL address, where files are stored.
                'path' => $_SERVER['DOCUMENT_ROOT'] . '/frontend/web/files/editor' // Or absolute path to directory where files are stored.
            ],
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => '/files/editor/', // Directory URL address, where files are stored.
                'path' => $_SERVER['DOCUMENT_ROOT'] . '/frontend/web/files/editor', // Or absolute path to directory where files are stored.
                'type' => \vova07\imperavi\actions\GetAction::TYPE_IMAGES,
            ],
            'delete-file' => [
                'class' => 'common\actions\DeleteFileAction',
            ],
            'author' => [
                'class' => 'common\actions\AuthorAction',
            ]
        ];
    }

    public function actionIndex() {

        return $this->render('index');
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

}
