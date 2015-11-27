<?php

namespace frontend\controllers;

use yii;
use yii\rest\Controller;
use yii\web\HttpException;
use frontend\models\Token;
use frontend\models\RestApi;
use frontend\models\Razz;
use frontend\models\RazzSearch;

class ApiController extends Controller {

    public function behaviors() {
        $behaviors = parent::behaviors();
        unset($behaviors['contentNegotiator']['formats']['application/xml']);
        return $behaviors;
    }

    /*
     * Сброс пароля
     */

    public function actionForgotpassword($email) {

        $user = RestApi::forgotPassword($email);

        if (!$user)
            throw new HttpException(401, 'Email not round');

        return RestApi::response([]);
    }

    /*
     * Информация о польхователе
     */

    public function actionUserinfo() {

        $request = Yii::$app->request;
        $get = array_change_key_case($request->get());

        if (isset($get))
            unset($get['needGetBackUserInfo']);

        $token = Token::checkToken($request->get('token'));

        if (!is_array($token))
            throw new HttpException(401, $token);

        if (count($get) > 1)
            $user = RestApi::updateUser($token['user_id'], $get);
        else
            $user = RestApi::infoUser($token['user_id']);

        if (!$user)
            throw new HttpException(401, 'Registration fail');

        if ($request->get('needGetBackUserInfo'))
            return RestApi::response(['user' => $user, 'profile' => $user->profile]);


        return RestApi::response([]);
    }

    /*
     * Создание пользователя
     */

    public function actionRegistration() {

        $user = null;
        $request = Yii::$app->request;

        if ($request->get('fbToken'))
            $user = RestApi::connectUser('facebook', $request->get('fbToken'), '', $request->get('email'), $request->get('name'));

        if ($request->get('twToken'))
            $user = RestApi::connectUser('twitter', $request->get('twToken'), $request->get('twSecret'), $request->get('email'), $request->get('name'));

        if ($request->get('userName'))
            $user = RestApi::createUser(array_change_key_case($request->get()));

        if ($user)
            return RestApi::response(['token' => Token::getAuthTokenByUserId($user->id)]);

        throw new HttpException(401, 'Registration fail');
    }

    /*
     * Авторизация
     */

    public function actionLogin() {

        $user = null;
        $request = Yii::$app->request;

        if ($request->get('fbToken'))
            $user = RestApi::connectUser('facebook', $request->get('fbToken'), '', $request->get('email'));

        if ($request->get('twToken'))
            $user = RestApi::connectUser('twitter', $request->get('twToken'), $request->get('twSecret'), $request->get('email'));

        if ($request->get('userName'))
            $user = RestApi::loginUserByName($request->get('userName'), $request->get('password'));

        if ($user)
            return RestApi::response(['id' => $user->id, 'token' => Token::getAuthTokenByUserId($user->id)]);

        throw new HttpException(401, 'Login fail');
    }

    /*
     *  -- Работа с видео
     */

    public function actionSearch() {
        $request = Yii::$app->request;

        $token = Token::checkToken($request->get('token'));

        if (!is_array($token))
            throw new HttpException(401, $token);

        $razzModel = new Razz();
        $model = new RazzSearch();
        $model->load(['RazzSearch' => $request->get()]);
        $model->search();

        $items = [];
        foreach ($model->items as $itm) {
            $items[] = $razzModel->getRazz($itm['id']);
        }
        $pager = [
            'totalItems' => $model->pages->totalCount,
            'pageSize' => $model->pages->defaultPageSize,
        ];

        return RestApi::response(['items' => $items, 'pager' => $pager]);
    }

    /*
     * Новое видео
     */

    public function actionRazzCreate() {
        $request = Yii::$app->request;

        $token = Token::checkToken($request->get('token'));

        if (!is_array($token))
            throw new HttpException(401, $token);

        $model = new Razz();
        $model->api = true;
        $model->user_id = $token['user_id'];

        if ($model->load(['Razz' => $request->get()]) && $model->save()) {
            return RestApi::response(['id' => $model->id]);
        } else
            RestApi::error($model);
    }

    /*
     * Ответ на видео видео
     */

    public function actionRazzRespond() {
        $request = Yii::$app->request;

        $token = Token::checkToken($request->get('token'));

        if (!is_array($token))
            throw new HttpException(401, $token);

        $model = Razz::findOne($request->get('id'));

        if (!$model)
            throw new HttpException(404, 'Razzd not found');

        if ($model->responder_stream)
            throw new HttpException(403, 'Razzd already responded.');

        if ($model->hash && $token['user_id'] != $model->responder_uid)
            throw new HttpException(403, 'This razzd for other user.');

        $model->api = true;
        $model->user_id = $token['user_id'];

        if ($model->load(['Razz' => $request->get()]) && $model->save()) {
            return RestApi::response(['id' => $model->id]);
        } else
            RestApi::error($model);
    }

    /*
     * Выборка видео по id
     */

    public function actionRazz() {
        $request = Yii::$app->request;

        $token = Token::checkToken($request->get('token'));

        if (!is_array($token))
            throw new HttpException(401, $token);

        $model = new Razz();
        $razz = $model->getRazz($request->get('id'));

        if (!$razz)
            throw new HttpException(404, 'Razzd not found.');

        return RestApi::response($razz);
    }

    /*
     * Выборка голосовалка
     */

    public function actionRazzVote() {

        $request = Yii::$app->request;

        $token = Token::checkToken($request->get('token'));

        if (!is_array($token))
            throw new HttpException(401, $token);

        $model = new Razz();
        $razz = $model->getRazz($request->get('id'));


        if (!$razz)
            throw new HttpException(404, 'Razzd not found.');

        if (!$razz['responder_stream'])
            throw new HttpException(422, 'Razzd is not started.');

        if (($razz['created_at'] + Razz::DAYS) < time())
            throw new HttpException(422, 'Razzd is ended.');

        $ratingModel = Yii::createObject([
                    'class' => \frontend\widgets\rating\models\Rating::className(),
                    'nid' => $request->get('id'),
                    'model' => 'Razz',
                    'vote' => ($request->get('vote') == 'my') ? ['my' => 1] : ['responder' => 1],
                    'uid' => $token['user_id']
        ]);

        if ($ratingModel->save())
            return RestApi::response($ratingModel->return);




        return RestApi::response($ratingModel->return);
    }

}
