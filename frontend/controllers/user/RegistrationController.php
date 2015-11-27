<?php

    namespace frontend\controllers\user;

    use dektrium\user\Mailer as dektriumMailer;
    use dektrium\user\models\Token;
    use yii\log\Logger;
    use Yii;
    use dektrium\user\controllers\RegistrationController as BaseRegistrationController;

    class RegistrationController extends BaseRegistrationController
    {
        /**
         * Overrided Confirm action
         *
         * @param int $id
         * @param string $code
         * @return string
         * @throws NotFoundHttpException
         * @throws \Exception
         */
        public function actionConfirm($id,$code){

            $user = $this->finder->findUserById($id);
            //$mailer = \Yii::$container->get(dektriumMailer::className());

            $mailer = new \common\helpers\Mandrill(
                $user->email,
                'Welcome to Razzd!',
                'welcome',
                $sender = null,
                [
                    'user' => $user,
                    'mandrill_template_name' => 'welcome',
                ]);

            if ($user === null || $this->module->enableConfirmation == false) {
                throw new NotFoundHttpException;
            }

            /** @var Token $token */
            $token = $this->findToken($id, $code, Token::TYPE_CONFIRMATION);

            if ($token === null || $token->isExpired) {
                \Yii::$app->session->setFlash('danger', \Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.'));
            } else {
                $token->delete();

                $user->confirmed_at = time();

                \Yii::$app->user->login($user);

                \Yii::getLogger()->log('User has been confirmed', Logger::LEVEL_INFO);

                if ($user->save(false)) {
                    \Yii::$app->session->setFlash('success', \Yii::t('user', 'Thank you, registration is now complete.'));

                    $mailer->sendMessage();

                } else {
                    \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Something went wrong and your account has not been confirmed.'));
                }
            }

            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Account confirmation'),
                'module' => $this->module,
            ]);

        }

        private function findToken($id, $code, $type){

            $t = new \dektrium\user\models\Token();
            $token = $t::find()->where(['code' => $code, 'user_id' => $id, 'type' => $type])->one();

            unset($t);

            return $token;
        }
    }