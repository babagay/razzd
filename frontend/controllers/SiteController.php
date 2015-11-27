<?php

namespace frontend\controllers;

use dektrium\user\helpers\Password;
use Yii;
use dektrium\user\models\LoginForm;
use dektrium\user\models\RegistrationForm;
use yii\authclient\clients\Twitter;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use frontend\models\Razz;
use frontend\models\RazzSearch;
use frontend\models\Comments;
use frontend\models\Notification;

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
                        'only' => [ 'rate', 'hide-notifi-ajax', 'fb-friends-ajax'],
                        'rules' => [
                                [
                                        'actions' => ['rate', 'hide-notifi-ajax', 'fb-friends-ajax'],
                                        'allow' => true,
                                        'roles' => ['@'],
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
                'captcha' => [
                        'class' => 'yii\captcha\CaptchaAction',
                        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                ],
                'delete-file' => [
                        'class' => 'common\actions\DeleteFileAction',
                ],
                'rate' => [
                        'class' => 'frontend\widgets\rating\actions\RateAction',
                ],
                'auth' => [
                        'class' => 'yii\authclient\AuthAction',
                        'successCallback' => [$this, 'successCallback'],
                ],
        ];
    }

    public function successCallback($client) {
        $attributes = $client->getUserAttributes();

        if ($client instanceof Twitter) {
            
        }
    }

    public function actionFbFriendsAjax() {

        $fb = Yii::$app->authClientCollection->getClient('facebook');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $me = $fb->api('/me');
            $response = $fb->api('/me/friends?offset=' . Yii::$app->request->get('offset', 0) . '&limit=' . Yii::$app->request->get('limit', 50));
            // $response['summary']['offset'] = Yii::$app->request->get('offset', 0);
            //  $response['summary']['limit'] = Yii::$app->request->get('limit', 0);
        } catch (\Exception $ex) {
            $response = ['error' => 'login'];
            return $response;
        }

        $user_id = (new \yii\db\Query())
                        ->select('user_id')
                        ->from('{{%social_account}}')
                        ->limit(1)
                        ->where([
                                'client_id' => $me['id'],
                        ])->scalar();

        if ($user_id != Yii::$app->user->id)
            $response = ['error' => 'login'];



        return $response;
    }

    public function actionHideNotifiAjax($id) {
        $model = Notification::findOne($id);

        if ($model && $model->uid == Yii::$app->user->id) {
            $model->hide = 1;
            $model->save();
        }
    }

    public function actionLoginAjax() {

        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $model = \Yii::createObject(LoginForm::className());
        //$model = new LoginForm();
        //$this->performAjaxValidation($model);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $this->redirect(Yii::$app->user->getReturnUrl());
        }

        return $this->renderAjax('login', [
                        'model' => $model,
                        //  'module' => $this->module,
        ]);
    }

    public function actionRegisterAjax() {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $model = \Yii::createObject(RegistrationForm::className());

        //   $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post()) && $model->register()) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['refresh' => 1];
        }

        return $this->renderAjax('register', [
                        'model' => $model,
                        // 'module' => $this->module,
        ]);
    }

    public function actionIndex() {

        $razzModel = new Razz();
        $razzSearch = new RazzSearch();

        // FIXME [?] if needed
        $razzSearch->freshOnly = true;

        $object = false;

        $i = 0;

        while (!$object) {
            $id = $razzModel->getRazzRandom($fresh = true);
            $object = $razzModel->getRazz($id, $fresh);
            $i++;

            if ($i > 50)
                break;
        }

        if (!$object || !$object['responder_uid']) {
            return $this->render('/razz/view', [
            ]);
            //throw new NotFoundHttpException('Razzd not found');
        }

        $razzModel->toch($id);

        $commentModel = new Comments();
        $commentModel->eid = $id;

        return $this->render('/razz/view', [
                        'commentModel' => $commentModel,
                        'razzModel' => $razzModel,
                        'razzSearch' => $razzSearch,
                        'object' => $object,
                        // 'related' => $model->getRazzRelated($object['tid'], $id),
                        // 'voteOnChallenges' => $model->getRazzVoteOnChallenges(),
                        // 'respondToChallenges' => $model->getRazzRespondToChallenges()
        ]);
    }

    public function actionContact() {

        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Ваше сообщение отправлено. В ближайшее время мы свяжемся с Вами.');
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка при отправке.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                            'model' => $model,
            ]);
        }
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFogotPassword()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $sended = $result = false;

        $model = \Yii::createObject([
            'class' => \frontend\models\RecoveryForm::className(),
            'scenario' => 'request',
        ]);

        $isLoaded = $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isPost && $isLoaded && $model->validate()) {
            /** @var Token $token */

            $password = \dektrium\user\helpers\Password::generate(6);

            if ($model->user->resetPassword($password)) {

                $sendTo = $model->user->email;
                $subject = "Razzd Password Recovery";
                $replyTo = isset(\Yii::$app->params['supportEmail']) ? \Yii::$app->params['supportEmail'] : "";
                $subject_internal = "Your Password was changed";
                $password = "$password";
                $username = $model->user->username;

                $mailer = new \common\helpers\Mandrill(
                    $sendTo, $subject, $local_tpl_name = null, $sender = null, [
                        'from_name' => '[Auto-generated]',
                        'reply_to' => $replyTo,
                        'mandrill_template_name' => 'forgotpassword',
                        'vars' => [
                            'header' => $subject_internal,
                            'username' => $username,
                            'password' => $password,
                        ],
                    ]
                );

                $result = $mailer->sendWithMandrillTemplate();
            }

            $mess = (string)$result;
            if ($result) {
                Yii::$app->session->setFlash('success', 'Your password has been changed.');
                $sended = true;
            } else {
                Yii::$app->session->setFlash('error', 'There is something wrong. Contact Support.');
            }
        }

        return $this->renderAjax('fogot', [
            'model' => $model,
            'sended' => $sended,
        ]);
    }

}
