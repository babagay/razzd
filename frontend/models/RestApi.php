<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\HttpException;
use frontend\models\Token;
use frontend\models\User;
use dektrium\user\models\Account;
use dektrium\user\helpers\Password;
use dektrium\user\Mailer;
use dektrium\user\Finder;

class RestApi extends Model {
    /*
     * Востановление пароля
     */

    public static function forgotPassword($email) {

        $user = User::findOne(['email' => $email]);

        if (!$user)
            return;

        $token = \Yii::createObject([
                    'class' => Token::className(),
                    'user_id' => $user->id,
                    'type' => Token::TYPE_RECOVERY
        ]);
        $token->save(false);

        $mailer = Yii::createObject([
                    'class' => Mailer::className(),
                    'reconfirmationSubject' => 'Recovery password',
        ]);
        $mailer->sendRecoveryMessage($user, $token);

        return $user;
    }

    /*
     * Информация о польователе.
     */

    public static function infoUser($user_id) {

        $user = User::findOne($user_id);
        if (!$user)
            return false;

        return self::clearUserData($user);
    }

    public static function updateUser($user_id, $data = null) {

        $user = User::findOne($user_id);
        $user->scenario = 'settings';
        $profile = $user->profile;
        $oldEmail = $user->email;

        if ($user->load(['User' => $data]) && $user->validate()) {

            if ($user->email != $oldEmail) {
                $user->unconfirmed_email = $user->email;
                $user->email = $oldEmail;
                $token = \Yii::createObject([
                            'class' => Token::className(),
                            'user_id' => $user->id,
                            'type' => Token::TYPE_CONFIRM_NEW_EMAIL
                ]);
                $token->save(false);
                $mailer = Yii::createObject([
                            'class' => Mailer::className(),
                            'reconfirmationSubject' => 'Mail confirmation',
                ]);
                $mailer->sendReconfirmationMessage($user, $token);
            }

            if (!$profile) {
                $profile = Yii::createObject([
                            'class' => Profile::className(),
                            'user_id' => $user->id
                ]);
                $profile->save();
            }

            $profile->load(['Profile' => $data]);
            $profile->save();
            $user->save();
        } else
            self::error($user);

        return self::clearUserData($user);
    }

    /*
     * Создание пользователя
     */

    public function createUser($data) {
        $user = new User();
        $user->scenario = 'register';

        if ($user->load(['User' => $data]) && $user->register()) {
            $user->profile->load(['Profile' => $data]);
            $user->profile->save();
            return $user;
        } else {
            self::error($user);
        }
    }

    /*
     * Создание нового пользователя через соц. сети или авторизировать если такой имеется
     */

    public static function connectUser($client_id, $token, $secret, $email = null, $name = null) {


        $accessToken = Yii::createObject([
                    'class' => 'yii\authclient\OAuthToken',
                    'token' => $token,
                    'tokenSecret' => $secret,
        ]);

        $client = Yii::$app->authClientCollection->getClient($client_id);
        $client->accessToken = $accessToken;
        $account = Account::createFromClient($client);
        $clientData = $client->userAttributes;

        if ($account['id'] && !$account['user_id']) {

            if (!$email)
                throw new HttpException(401, 'Invalid user mail');
            else {

                $user = Yii::createObject([
                            'class' => User::className(),
                            'scenario' => 'create',
                            'email' => $email,
                ]);

                if ($user->create()) {
                    $account->link('user', $user);
                } else
                    self::error($user);
            }
        }

        if (!$account->user->profile->name) {

            if ($client->id == 'facebook' && isset($clientData['first_name']) && isset($clientData['last_name'])) {
                $account->user->profile->name = $clientData['first_name'] . ' ' . $clientData['last_name'];
            } elseif ($client->id == 'twitter' && isset($clientData['name'])) {
                $account->user->profile->name = $clientData['name'];
            } elseif ($name) {
                $account->user->profile->name = $name;
            }

            $account->user->profile->save();
        }
        return $account->user;
    }

    /*
     * функция логина
     */

    public static function loginUserByName($username, $password) {

        $user = User::findOne([
                    'username' => $username,
        ]);

        if (!$user || !Password::validate($password, $user->password_hash))
            return;

        return $user;
    }

    public static function clearUserData($user) {

        unset($user->password_hash);
        unset($user->auth_key);
        unset($user->flags);
        unset($user->role);
        unset($user->confirmed_at);
        unset($user->registration_ip);

        if (isset($user->profile)) {
            unset($user->profile->user_id);
            unset($user->profile->location);
            unset($user->profile->website);
            unset($user->profile->bio);
            unset($user->profile->gravatar_email);
            unset($user->profile->gravatar_id);
            unset($user->profile->public_email);
        }

        return $user;
    }

    public static function error($model = NULL) {

        $erros = $model->getErrors();

        if (empty($erros))
            throw new HttpException(400, '');

        foreach ($erros as $attribute) {
            foreach ($attribute as $error) {
                throw new HttpException(401, $error);
            }
        }
    }

    public static function response($data = []) {

        return [
            'status' => 'success',
            'message' => '',
            'data' => $data,
        ];
    }

}
