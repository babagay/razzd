<?php

    /**
     * <code>
     *   $twitter = new \common\helpers\Twitter();
     *   $provider = $twitter->getApiProvider();
     *   $provider->api($twitter->getBaseUrl()."/statuses/update",'POST',['status' => "asd"],[]);
     * </code>
     */

    namespace common\helpers;

    use frontend\models\User;
    use Yii;
    //use yii\authclient\clients\Twitter as TwApiProvider;
    use frontend\controllers\auth\Twitter as TwApiProvider;

    use yii\authclient\OAuthToken;
    use yii\base\Exception;
    use yii\base\Object;
    use common\helpers\DataHelper;
    use yii\helpers\Url;

    class Twitter extends Object
    {
        /**
         * Application based provider
         *
         * @var null
         */
        private $apiProvider = null;

        /**
         * User based provider
         *
         * @var null
         */
        private $apiProviderPersonal = null;

        private $oauth_access_token = null;
        private $oauth_access_token_secret = null;

        private $friendsUrl = null;
        private $meUrl = null;
        private $uploadUrl = null;
        private $followersUrl = null;
        private $tweetUrl = null;
        private $errors = [];
        private $friends = [];
        private $followers = [];

        private $client_id = null;
        private $params = [];

        const UPLOAD_MEDIA = 'media/upload.json';
        const ME = 'account/verify_credentials.json';
        const FRIENDS = 'friends/list.json';
        const FOLLOWERS = 'followers/list.json';
        const TWEET = 'statuses/update.json';
        const MESSAGE = 'direct_messages/new.json';

        const PERSONAL_TOKEN_LENGTH = 40;

        public function __get($key)
        {

            return isset($this->params[$key]) ? $this->params[$key] : null;

        }

        public function __set($key, $value)
        {

            $this->params[$key] = $value;

        }

        public function setParams($params = [])
        {

            if (is_array($params)) {
                foreach ($params as $key => $val) {
                    $this->$key = $val;
                }
            }
        }

        public function getFriends()
        {
            return $this->friends;
        }

        public function getFollowers()
        {
            return $this->followers;
        }

        private function getPersonalTokens()
        {
            $tokens = [];
            $oauth_access_token = $oauth_access_token_secret = null;

            $socilal_params = Yii::$app->session->get('socilal_params');
            if( !is_null($socilal_params) ) {
                if(isset($socilal_params['client']))
                    if($socilal_params['client'] == 'twitter'){
                        $oauth_access_token = $socilal_params['oauth_token'];
                        $oauth_access_token_secret = $socilal_params['oauth_token_secret'];
                        //$user_id = $socilal_params['user_id'];
                        //$screen_name = $socilal_params['screen_name'];
                    }
            }

            if(is_null($oauth_access_token_secret)) {
                $sessionArr = Yii::$app->session;
                if (is_array($sessionArr)) {
                    foreach ($sessionArr as $key => $val) {

                        if (preg_match('/.*Twitter_([\d\w]{' . self::PERSONAL_TOKEN_LENGTH . '})_token/i', $key,
                            $matches)) {
                            $another_token = $matches[1];
                            if ($val instanceof OAuthToken) {
                                $oauth_access_token = $val->getToken();
                                $oauth_access_token_secret = $val->getTokenSecret();
                            }
                        }
                    }
                }
            }

            if (!is_null($oauth_access_token)) {
                if (!is_null($oauth_access_token_secret)) {
                    $tokens['oauth_access_token'] = $oauth_access_token;
                    $tokens['oauth_access_token_secret'] = $oauth_access_token_secret;
                }
            }

            return $tokens;
        }

        /**
         * Возвращает апи-провайдер, связанный приложением или с конкретным пользователем
         *
         * @param string $key (user|application)
         * @return TwApiProvider|null
         */
        private function getApiProvider($key = 'user')
        {

            if ($key == 'user') {
                if (is_null($this->apiProviderPersonal)) {
                    $personalTokens = $this->getPersonalTokens();
                    if (isset($personalTokens['oauth_access_token'])) {
                        $this->oauth_access_token = $personalTokens['oauth_access_token'];
                        $this->oauth_access_token_secret = $personalTokens['oauth_access_token_secret'];
                    } else {
                        $this->oauth_access_token = Yii::$app->params['twitter']['oauth_access_token'];
                        $this->oauth_access_token_secret = Yii::$app->params['twitter']['oauth_access_token_secret'];
                    }
                    $this->apiProviderPersonal = $this->createProvider();
                }
                return $this->apiProviderPersonal;

            } elseif ($key == 'application') {
                if (is_null($this->apiProvider)) {
                    $this->oauth_access_token = Yii::$app->params['twitter']['oauth_access_token'];
                    $this->oauth_access_token_secret = Yii::$app->params['twitter']['oauth_access_token_secret'];
                    $this->apiProvider = $this->createProvider();
                }
                return $this->apiProvider;
            }

        }

        private function createProvider()
        {
            $token = new OAuthToken([
                'token' => $this->oauth_access_token,
                'tokenSecret' => $this->oauth_access_token_secret,
            ]);

            $twitter = new TwApiProvider([
                'accessToken' => $token,
                'consumerKey' => Yii::$app->params['twitter']['consumer_key'],
                'consumerSecret' => Yii::$app->params['twitter']['consumer_secret'],
            ]);

            return $twitter;
        }

        private function getBaseUrl()
        {
            $baseUrl = Yii::$app->params['twitter']['url'] . "/" . Yii::$app->params['twitter']['api_version'];

            return $baseUrl;
        }

        private function getFriendsUrl()
        {
            if (is_null($this->friendsUrl)) {
                $this->friendsUrl = Yii::$app->params['twitter']['url'] . "/" . Yii::$app->params['twitter']['api_version'] . "/" . self::FRIENDS;
            }

            return $this->friendsUrl;
        }

        private function getFollowersUrl()
        {
            if (is_null($this->followersUrl)) {
                $this->followersUrl = Yii::$app->params['twitter']['url'] . "/" . Yii::$app->params['twitter']['api_version'] . "/" . self::FOLLOWERS;
            }

            return $this->followersUrl;
        }

        private function getMeUrl()
        {
            if (is_null($this->meUrl)) {
                $this->meUrl = Yii::$app->params['twitter']['url'] . "/" . Yii::$app->params['twitter']['api_version'] . "/" . self::ME;
            }

            return $this->meUrl;
        }

        private function getUploadUrl()
        {
            if (is_null($this->uploadUrl)) {
                $this->uploadUrl = Yii::$app->params['twitter']['upload_url'] . "/" . Yii::$app->params['twitter']['api_version'] . "/" . self::UPLOAD_MEDIA;
            }

            return $this->uploadUrl;
        }

        private function getTweetUrl()
        {
            if (is_null($this->tweetUrl)) {
                $this->tweetUrl = Yii::$app->params['twitter']['url'] . "/" . Yii::$app->params['twitter']['api_version'] . "/" . self::TWEET;
            }

            return $this->tweetUrl;
        }

        public function getErrors()
        {
            if (!sizeof($this->errors)) {
                return null;
            }

            return $this->errors;
        }

        public function hasErrors()
        {
            return !!sizeof($this->errors);
        }

        public function getClientId()
        {
            return $this->client_id;
        }

        public function amIClient($uid = null)
        {
            $isLogged = false;

            if (is_null($uid)) {
                if (!Yii::$app->user->isGuest) {
                    $uid = Yii::$app->getUser()->id;
                }
            }

            if (!is_null($uid)) {
                if (($client_id = (int)User::getClientIdByUid($uid)) > 0) {
                    $this->client_id = $client_id;
                    $isLogged = true;
                }
            }

            return $isLogged;
        }

        /**
         * @param null $uid
         * @param bool|true $followers
         * @param bool $friends
         * @return array
         */
        public function getContactsList($uid = null, $followers = true, $friends = false)
        {
            $client_id = null;

            try {
                if (!is_null($this->client_id)) {
                    $client_id = $this->client_id;
                } else {
                    if (!$this->amIClient($uid)) {
                        throw new Exception("User is not a Twitter client");
                    } else {
                        $client_id = $this->client_id;
                    }
                }

                if($friends)
                    $this->friends = $this->_getFriends($client_id);

                if ($followers) {
                    $this->followers = $this->_getFollowers($client_id);
                }


            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }

            return $this->friends;
        }

        public function tweet($twit, $params)
        {

            $data = [];
            $data['status'] = $twit;

            $result = null;

            try {

                if (isset($params['media_ids'])) {
                    $data['media_ids'] = $params['media_ids'];
                } elseif (isset($params['image'])) {
                    $r = $this->upload($params['image']);
                    if (isset($r['media_id'])) {
                        $data['media_ids'] = $r['media_id'];
                    }
                }

                $data['text'] = $twit;

                $result = $this->getApiProvider()->api($this->getTweetUrl(), 'POST', $data, []);
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }

            return $result;
        }

        private function upload($path_to_media)
        {
            $upload_url = $this->getUploadUrl();

            $params = [];
            $params['media_data'] = base64_encode(file_get_contents($path_to_media));

            $result = $this->getApiProvider()->api($upload_url, 'POST', $params);

            if (isset($result['media_id'])) {
                return $result;
            }

            return null;
        }

        /**
         * Возвращает тех, за кем пользователь следует, включая друзей
         *
         * @param int|null $client_id
         * @return array
         * @throws Exception
         */
        private function _getFriends($client_id = null)
        {
            $params = [];
            $params['user_id'] = $client_id;

            if (!is_null($this->cursor)) {
                if (!is_null($this->cursor_type)) {
                    if (!is_null($this->entity)) {
                        if ($this->cursor_type == 'next') {
                            if ((int)$this->cursor > 0) {
                                if ($this->entity == 'friends') {
                                    $params['cursor'] = $this->cursor;
                                }
                            }
                        } else {
                            if ((int)$this->cursor < 0) {
                                if ($this->entity == 'friends') {
                                    $params['cursor'] = $this->cursor;
                                }
                            }
                        }
                    }
                }
            }

            $friends = $this->getApiProvider()->api($this->getFriendsUrl(), 'GET', $params, []);

            return $friends;
        }

        /**
         * Возвращает исключительно последователей пользователя
         *
         * @param int|null $client_id
         * @return array
         * @throws Exception
         */
        private function _getFollowers($client_id = null)
        {
            $params = [];
            $params['user_id'] = $client_id;

            if (!is_null($this->cursor)) {
                if (!is_null($this->cursor_type)) {
                    if (!is_null($this->entity)) {
                        if ($this->cursor_type == 'next') {
                            if ((int)$this->cursor > 0) {
                                if ($this->entity == 'followers') {
                                    $params['cursor'] = $this->cursor;
                                }
                            }
                        } else {
                            if ((int)$this->cursor < 0) {
                                if ($this->entity == 'followers') {
                                    $params['cursor'] = $this->cursor;
                                }
                            }
                        }
                    }
                }
            }

            return $this->getApiProvider()->api($this->getFollowersUrl(), 'GET', $params, []);
        }

        public static function friendsArrayUnique(array $friends)
        {

            $friends_unique = [];
            $ids = [];

            if (sizeof($friends)) {
                foreach ($friends as $friend) {
                    if (!in_array($friend['id'], $ids)) {
                        $ids[] = $friend['id'];
                        $friends_unique[] = $friend;
                    }
                }
            }

            return $friends_unique;
        }

    }