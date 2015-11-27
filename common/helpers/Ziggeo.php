<?php

    namespace common\helpers;

    use Mandrill_Error;
    use Yii;
    use yii\base\Exception;
    use yii\base\Object;
    use common\helpers\DataHelper;
    use yii\helpers\Url;
    use Ziggeo as ZiggeoApi;
    use frontend\models\Razz;

    class Ziggeo extends Object
    {
        private static $i = 0;

        private static $Ziggeo = null;

        private static $errors = [];

        public static function getInstance(){

            if( is_null(self::$Ziggeo) )
                self::$Ziggeo = new ZiggeoApi(Yii::$app->params['ziggeo']['application_token'], Yii::$app->params['ziggeo']['private_key'], Yii::$app->params['ziggeo']['encryption_key']);

            return self::$Ziggeo;
        }

        public static function addError($message){
            $errors = self::$errors;
            $errors[] = $message;
        }

        public static function getErrors(){
            return self::$errors;
        }

        /**
         * @param $token
         * @return string
         */
        static function getPreview($token){

            $url = "";

            try {
                $video = self::getInstance()->videos()->get($token);

                if (isset($video->embed_image_url)) {
                    $url = $video->embed_image_url;
                }

            } catch (\Exception $e){
                self::addError($e->getMessage());
            }

            return $url;
        }

        /**
         * @param $id
         * @param $stream
         * @param $stream_preview
         * @param bool|false $isRespond
         * @param string $class
         * @param bool $showPlayer - выводить Ziggeo player
         * @return string
         */
        static function getImage($id, $stream, $stream_preview, $isRespond = false, $class = "video-preview", $showPlayer = false){

            $image = "<img class=\"no-image\" src=\"" . Url::base(true) ."".\common\helpers\Html::noImage() . "\" >";
            ///$default_ext = ".mp4";

            if(is_null($stream)) {
                return $image;
            }

            $ziggeo_video_url = Yii::$app->params['ziggeo']['video_url'] . "/" . Yii::$app->params['ziggeo']['api_version'] . "/applications/" . Yii::$app->params['ziggeo']['application_token'] . "/videos/:token/video.mp4";

            $scheme = "";

            // Stream is an Image
            if( preg_match('/^http(s)?:\/\/([\w\d-\.]*)(.)*\.(png|jpg|jpeg|gif|wav|mp3|mp4)$/i',$stream) AND is_null($stream_preview) ) {
                return $image;
            }

            if( preg_match('/^http(s)?:\/\/([\w\d-\.]*)(.)*\.(png|jpg|jpeg|gif|wav|mp3|mp4)$/i',$stream_preview) ) {
                // Stream is pure Url

                $image = "<img class=\"$class\"  data-video=\"{$stream}\" src=\"{$stream_preview}\">";

            } elseif( preg_match('/(http(s)?:\/\/){0,1}[a-z\.]*ziggeo(.)*/i',$stream_preview) ){
                // Preview is Ziggeo url

                if($showPlayer === true) {
                    $image = self::getPlayer($stream);
                } else {

                    if( !preg_match('/http/i',$stream_preview) )
                        $scheme = "http://";

                    $src = str_replace(':token',$stream,$ziggeo_video_url);

                    $image = "<img class=\"$class\"  data-video=\"{$src}\" src=\"$scheme{$stream_preview}\">";
                }

            } else {

                if( (preg_match('/([\w\d])*/i',$stream) AND !preg_match('/(http)/i', $stream)) OR is_null($stream_preview) ) {
                    // It seems like Stream is a Ziggeo Token

                    if (is_null($stream_preview)) {
                        // Запросить урл имаджа через апи и положить его в базу

                        try {

                            $video = self::getInstance()->videos()->get($stream);
                            if (is_object($video)) {

                                ///$default_ext = $video->default_stream->video_type;

                                if (isset($video->embed_image_url)) {

                                    $src = str_replace(':token', $stream, $ziggeo_video_url);

                                    $image = "<img class=\"$class\" data-video=\"$src\" src=\"http://{$video->embed_image_url}\">";

                                    $razz = Razz::findOne($id);
                                    $razz->pureUpdate = true;

                                    if ($isRespond) {
                                        $razz->responder_stream_preview = $video->embed_image_url;
                                    } else {
                                        $razz->stream_preview = $video->embed_image_url;
                                    }

                                    $razz->fb_friend = $razz->facebook_id;

                                    if( !$razz->save()) {
                                        self::addError(serialize($razz->getErrors()));

                                        $logger = Yii::$app->getLog()->getLogger();
                                        $logger->log('ZiggeoHelper error',\yii\log\Logger::LEVEL_INFO);
                                        $logger->log($razz->getErrors(),\yii\log\Logger::LEVEL_ERROR);
                                    }

                                }
                            }
                        } catch (ZiggeoException $e) {
                            self::addError($e->getMessage());
                        }

                    } else {
                        // Только сгенерить имадж
                        // FIXME - Сюда заходит, когда в базе битый урл

                        $razz = Razz::findOne($id);

                        $video_src = str_replace(':token', $stream, $ziggeo_video_url);

                        if ($isRespond) {
                            $preview = $razz->responder_stream_preview;
                        } else {
                            $preview = $razz->stream_preview;
                        }

                        if (!preg_match('/http/i', $preview)) {
                            $preview = "http://" . $preview;
                        }

                        $image = "<img class=\"$class\" data-video=\"$video_src\" src=\"$preview\">";
                    }

                    if($showPlayer === true)
                        $image = self::getPlayer($stream);

                }

            }

            return $image;
        }

        static function getPlayer($token){

            $limit = Yii::$app->params['ziggeo']['record_duration'];

            $player = "  <ziggeo
                              ziggeo-video=\"$token\"
                              ziggeo-responsive=\"true\"
                              ziggeo-limit=$limit
                              >
                            </ziggeo>";

            return $player;
        }

    }