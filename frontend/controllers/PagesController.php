<?php

namespace frontend\controllers;

use dektrium\user\models\Token;
use frontend\models\ContactForm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use Aws\S3\S3Client;
use frontend\models\Settings;

/**
 * Site controller
 */
class PagesController extends Controller {

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
                'error' => [
                        'class' => 'yii\web\ErrorAction',
                ],
        ];
    }

    /**
     * Contact Us page render
     *
     * @param $page
     * @return string
     * @throws Mandrill_Error
     * @throws \Exception
     */
    private function renderContactUs($page) {

        $model = new ContactForm();

        $mess = '';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $settings = new Settings();
            /** @var string $sendTo - address which collects feedback from customers
             *  @fixme change this emails
             */
            try {
                $support = $settings->getConfigurationParamByKey('support_email');
            } catch (\yii\base\Exception $e) {
                $support = null;
            }
            try {
                $support = $settings->getConfigurationParamByKey('admin_email');
            } catch (\yii\base\Exception $e) {
                $admin = null;
            }
            if (!$support) {
                $sendTo = \Yii::$app->params['supportEmail'];
            } else {
                $sendTo = $support;
            }
            if (!$admin) {
                $replyTo = \Yii::$app->params['adminEmail'];
            } else {
                $replyTo = $admin;
            }

            $name = Yii::$app->request->post("ContactForm")['name'];
            $body = Yii::$app->request->post("ContactForm")['body'];
            $usermail = Yii::$app->request->post("ContactForm")['email'];
            $subject_internal = Yii::$app->request->post("ContactForm")['subject'];
            $subject = "New Feedback from $name ($usermail)";

            $mailer = new \common\helpers\Mandrill(
                    $sendTo, $subject, $local_tpl_name = null, $sender = null, [
                    'from_name' => '[Auto-generated]',
                    'reply_to' => $replyTo,
                    'mandrill_template_name' => 'contactrazzd',
                    'vars' => [
                            'header' => $subject_internal,
                            'body' => $body,
                            'usermail' => $usermail
                    ]
                    ]
            );

            $result = $mailer->sendWithMandrillTemplate();

            $mess = (string) $result;
            if ($result) {
                Yii::$app->session->setFlash('success', 'Your message has been sent.');
            } else {
                Yii::$app->session->setFlash('error', 'Is there something wrong. Contact Support.'); 
            }
            $model = new ContactForm();
        }


        return $this->render('contact_us', [
                        'page' => $page,
                        'model' => $model,
        ]);
    }

    public function actionIndex($id) {

        $request = Yii::$app->request;

        Yii::$app->meta->setMeta();

        $page = (new \yii\db\Query())
                        ->select('*')
                        ->from('{{%pages}}')
                        ->where([
                                'id' => $id,
                                'publish' => 1
                        ])->one();

        if (empty($page))
            throw new NotFoundHttpException('Page not found.');

        if (str_replace('/', '', $request->url) == 'contact-us') {
            return $this->renderContactUs($page);
        }

        return $this->render('index', [
                        'page' => $page,
        ]);
    }

}
