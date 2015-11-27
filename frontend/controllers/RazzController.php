<?php

    namespace frontend\controllers;

    use frontend\models\Settings;
    use frontend\models\User;
    use Yii;
    use yii\base\Exception;
    use frontend\components\AccessControl;
    use yii\helpers\Url;
    use yii\web\NotFoundHttpException;
    use yii\web\HttpException;
    use yii\web\Controller;
    use yii\filters\VerbFilter;
    use frontend\models\Razz;
    use frontend\models\RazzSearch;
    use frontend\models\Comments;
    use common\helpers\Twitter as TwitterHelper;

    /**
     * Razz controller
     */
    class RazzController extends Controller
    {

        /**
         * @inheritdoc
         */
        public function behaviors()
        {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['record-save', 'new', 'respond'],
                    'rules' => [
                        [
                            'actions' => ['record-save', 'new', 'respond'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ];
        }

        /**
         * @inheritdoc
         */
        public function actions()
        {
            return [
                'error' => [
                    'class' => 'yii\web\ErrorAction',
                ],
            ];
        }

        public function actionNew($type)
        {

            $model = new Razz();
            $model->scenario = 'create';

            if ($type == 'any') {
                $model->type = $model::ANYONE;
            }
            if ($type == 'some') {
                $model->type = $model::SOMEONE;
            }


            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                if ($model->type == 1 && !$model->fb_friend) {
                    $this->redirect(['/razz/vote-on-challenges', 't' => 'some']);
                }

                if ($model->fb_friend) {

                    $Razz = Yii::$app->request->post('Razz', null);

                    if ($Razz['fileName'] != "") // Ziggeo
                    {
                        $linkToImg = \common\helpers\Ziggeo::getPreview($Razz['fileName']);
                    } else {
                        $linkToImg = $model->preview;
                    }


                    $twitter = new \common\helpers\Twitter();

                    $userModel = new \frontend\models\User();
                    $userName = ucfirst($userModel->getFullname(Yii::$app->user->id));

                    $recipientNick = $model->screen_name;

                    $linkToRazzd = Url::base(true) . "/razz/respond/" . $model->id;

                    $text = "@$recipientNick You have been Razzd by $userName: '{$model->title}' $linkToRazzd";

                    $r = $twitter->tweet($text, ['image' => $linkToImg]);

                    if ($twitter->hasErrors()) {
                        // Error
                    } else {
                        if (isset($r['id'])) {
                            // Success
                        }
                    }

                    $this->redirect(['/razz/vote-on-challenges', 't' => 'some']);

                    /*
                     * Facebook
                    $client = Yii::$app->authClientCollection->getClient('facebook');
                    $host = 'http://' . $_SERVER['HTTP_HOST'];
                    $this->redirect('http://www.facebook.com/dialog/send?app_id=' . $client->clientId . '&to=' . $model->fb_friend . '&link=' . $host . '/razz/respond?hash=' . $model->hash . '&redirect_uri=' . $host . '/razz/vote-on-challenges?t=some');
                    */
                }

                if ($model->type == 2) {
                    $this->redirect(['/razz/vote-on-challenges', 't' => 'any']);
                }
            } else {
                //print_r($model->getErrors());
                //exit();
            }

            return $this->render('new', [
                'model' => $model,
            ]);
        }

        public function actionRecordSave()
        {

            $model = new Razz();

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return $model->saveVideo();
        }

        public function actionRespond($id = null, $hash = null)
        {
            $model = null;
            $userModel = new \frontend\models\User();

            if ($id) {
                $model = Razz::findOne($id);
            } elseif ($hash) {
                $model = Razz::findOne(['hash' => $hash]);
            }

            if (Yii::$app->getUser()->id === $model->uid) {

                //throw new HttpException(404, 'You are not allowed to respond to your own challenge!');

                return $this->render('error', [
                    'model' => $model,
                    'message' => 'You are not allowed to respond to your own challenge!',
                    'userModel' => $userModel,
                ]);
            }

            if (!$model) {
                throw new HttpException(404, 'Razzd not found');
            }

            $model->scenario = 'respond';

            if ($model->responder_stream) {
                throw new HttpException(403, 'Razzd already responded.');
            }

            if (!Yii::$app->user->isGuest && $id && $model->hash && Yii::$app->user->id != $model->responder_uid) {
                // throw new HttpException(403, 'This razzd for other user.');
            } elseif (Yii::$app->user->isGuest && $model->responder_uid) {
                $this->redirect(['/login']);
            }

            if (!Yii::$app->user->isGuest && $id && $model->hash) {
                if (!is_null($model->facebook_id)) {
                    $client_id = User::getClientIdByUid(Yii::$app->user->id);
                    if ((int)$client_id != (int)$model->facebook_id) {
                        throw new HttpException(403, 'This razzd for other user!');
                    }
                } elseif (!is_null($model->responder_uid)) {
                    if (Yii::$app->user->id != $model->responder_uid) {
                        throw new HttpException(403, 'This razzd for other user...');
                    }
                }
            }

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $this->redirect('/razz/' . $model->id);
            } else {
                // print_r($model->getErrors());
                //exit();
            }

            return $this->render('respond', [
                'model' => $model,
                'userModel' => $userModel,
            ]);
        }

        public function actionCommentAjax()
        {

            $commentModel = new Comments();

            if ($commentModel->load(Yii::$app->request->post()) && $commentModel->save()) {
                if (Yii::$app->request->isAjax) {
                    return $this->renderPartial('comments', [
                        'model' => $commentModel,
                    ]);
                }
            }
        }

        public function actionGetConfigAjax()
        {

            $key = Yii::$app->request->post('key', null);
            $keys = Yii::$app->request->post('keys', null);
            $response = [];
            $model = new Settings();

            try {
                if (!is_null($key)) {
                    $response[$key] = $model->getConfigurationParamByKey($key);
                } elseif (!is_null($keys)) {
                    $arr = explode('|', $keys);
                    foreach ($arr as $key) {
                        $response[$key] = $model->getConfigurationParamByKey($key);
                    }
                }
            } catch (Exception $e) {
                $response['error'] = $e->getMessage();
            }

            return json_encode($response);
        }

        public function actionClean()
        {
            $razzModel = new Razz();

            $params = $razzModel->getInitParams();

            $file_1 = $params['tmpDir'] . "/" . $params['tmpFileName'] . ".webm";
            $file_2 = $params['tmpDir'] . "/" . $params['tmpFileName'] . ".wav";
            $file_3 = $params['tmpDir'] . "/" . $params['tmpFileName'] . ".mp4";
            $file_4 = $params['tmpDir'] . "/" . $params['tmpFileName'] . ".png";

            if (file_exists($file_1)) {
                unlink($file_1);
            }
            if (file_exists($file_2)) {
                unlink($file_2);
            }
            if (file_exists($file_3)) {
                unlink($file_3);
            }
            if (file_exists($file_4)) {
                unlink($file_4);
            }

            return json_encode($razzModel->getInitParams());
        }

        /**
         * Действия по завершении загрузки видео
         *
         * @return string
         */
        public function actionMerge()
        {

            $fileName = Yii::$app->request->post('filename', null);
            //$fileName = ""; // [!] Можно брать из сессии

            $response = [];
            $response['result'] = "Ok";

            $dir = '/frontend/web/files/tmp';
            $path = $_SERVER['DOCUMENT_ROOT'] . $dir;

            $img_ext = ".png";

            $Settings = new Settings();

            $enc_params = $options = "";

            // Cкриншот
            $from_file = "$path/$fileName.webm";
            $to_file = "$path/$fileName$img_ext";
            $out_file = str_replace('.webm', '', $path . '/' . $fileName) . '.mp4';
            $sound_file = str_replace('.webm', '', $path . '/' . $fileName) . '.wav';

            if (file_exists($from_file)) {

                $Settings = new \frontend\models\Settings();

                // Snapshot
                try {
                    $useForSnapshot = $Settings->getConfigurationParamByKey("ffmpeg_use_for_snapshot");

                    if (!!$useForSnapshot) {
                        echo shell_exec("ffmpeg -ss 00:00:01 -i $from_file  -f image2 -vframes 1 $to_file -y");
                    }
                } catch (\yii\base\Exception $e) {

                }

                // Cведение видео и аудио
                try {
                    $isMerge = $Settings->getConfigurationParamByKey("ffmpeg_use_for_merge_audiovideo");

                    $enc_params = $Settings->getConfigurationParamByKey("ffmpeg_encoding_params");
                    $options = $Settings->getConfigurationParamByKey("ffmpeg_encoding_options");
                    if (!!$isMerge) {
                        echo shell_exec("ffmpeg -i $sound_file -i $from_file $enc_params $out_file $options");
                    }
                } catch (\yii\base\Exception $e) {

                }
            }

            $response['snapshot'] = $fileName . $img_ext;

            return json_encode($response);
        }

        /**
         * Get the particular razzd; output on individual screen
         * @param $id
         * @return mixed
         * @throws NotFoundHttpException
         */
        public function actionView($id)
        {
            $razzModel = new Razz();
            $razzSearch = new RazzSearch();

            $razzModel->toch($id);
            $object = $razzModel->getRazz($id);

            if (!$object || !$object['responder_uid']) {
                throw new NotFoundHttpException('Razzd not found');
            }

            $razzModel->end($object);

            $commentModel = new Comments();
            $commentModel->eid = $id;

            if ($commentModel->load(Yii::$app->request->post()) && $commentModel->save()) {
                if (Yii::$app->request->isAjax) {
                    return $this->renderPartial('comments', [
                        'model' => $commentModel,
                    ]);
                }
                return $this->redirect(['/razz/' . $id]);
            }

            return $this->render('view', [
                'commentModel' => $commentModel,
                'razzModel' => $razzModel,
                'razzSearch' => $razzSearch,
                'object' => $object,
            ]);
        }

        public function actionSearch()
        {
            $model = new RazzSearch();
            $razzModel = new Razz();
            $model->load(Yii::$app->request->get());
            $model->freshOnly = true;
            $model->get_which_user_i_voted_for = Yii::$app->user->id;

            $model->search();

            return $this->render('search', [
                'model' => $model,
                'razzModel' => $razzModel,
            ]);
        }

        public function actionVoteOnChallenges($t = null)
        {
            $model = new RazzSearch();
            $razzModel = new Razz();
            $model->responder = RazzSearch::RESPONDER;
            $model->freshOnly = true;
            $model->get_which_user_i_voted_for = Yii::$app->user->id;

            $model->search();

            return $this->render('search', [
                'model' => $model,
                'razzModel' => $razzModel,
                't' => $t,
            ]);
        }

        public function actionRelated($id = null)
        {
            $model = new RazzSearch();
            $razzModel = new Razz();
            $model->responder = RazzSearch::RESPONDER;
            $model->category = $id;
            $model->search();

            return $this->render('search', [
                'model' => $model,
                'razzModel' => $razzModel,
            ]);
        }

        public function actionRespondToChallenges()
        {
            $model = new RazzSearch();
            $razzModel = new Razz();
            $model->responder = RazzSearch::NORESPONDER;
            $model->search();

            return $this->render('search', [
                'model' => $model,
                'razzModel' => $razzModel,
            ]);
        }

        public function actionArchive()
        {
            $model = new RazzSearch();
            $razzModel = new Razz();

            $model->isArchive = true;
            $model->getSpoiledRazzd($related_uid = Yii::$app->request->getQueryParam("id"),
                $iam_uid = Yii::$app->user->id);

            return $this->render('search', [
                'model' => $model,
                'razzModel' => $razzModel,
            ]);
        }

        /**
         * Сохранение токена при хранении видео в формате ziggeo
         *
         * @return string
         */
        public function actionStoreVideoAjax()
        {
            $token = Yii::$app->request->post('token', null);

            $response = [];
            $response['result'] = "Ok";

            return json_encode($response);
        }

        /**
         * Возвращает список друзей пользователя, залогиненного через Twitter
         *
         * @return array
         */
        public function actionGetTwitterFriends()
        {
            $response = [];
            $friends = [];
            $response['result'] = "Ok";
            $response['html'] = "";
            $message = "";

            $tw = new TwitterHelper();

            $tw->setParams(Yii::$app->request->post());

            $tw->getContactsList(); // without friends
            /// $tw->getContactsList(null,true,true); // with friends

            if (is_null($tw->getErrors())) {
                $response['html'] = $this->renderPartial('friends',
                    ['friends' => $tw->getFriends(), 'followers' => $tw->getFollowers()]);
            } else {
                if (is_array($tw->getErrors())) {
                    foreach ($tw->getErrors() as $error) {
                        $message .= $error;
                    }
                }
                $response['result'] = "error";
            }

            $response['message'] = $message;

            return json_encode($response);
        }

    }
