<?php



    namespace common\helpers;

    class Html extends \yii\helpers\Html
    {

        const DEFAULT_LENGTH = 10;

        static function cut($str, $length = null)
        {
            if (!$length) {
                $length = self::DEFAULT_LENGTH;
            }

            $str = strip_tags($str);

            if (mb_strlen($str, 'UTF-8') > $length) {
                $pos = mb_strpos($str, " ", $length, 'UTF-8');
                if(!$pos){
                    $pos = $length;
                }
                $str = mb_substr($str, 0, $pos, 'UTF-8');
                return $str . '...';
            }

            return $str;
        }
        
        static function noImage(){
            return \Yii::getAlias('@web') . '/images/no_photo.jpg';
        }

        static function logiINlogOUT($page = "", $profile_id = null ){

            if($page == "profile"){
                if (\Yii::$app->user->isGuest) {
                    $html = "
                            <ul class=\"icon-registr-sign-up registr-sign-up\" >
                                <li ><a href = \"#\" class=\"popup-click sign-in-popup-click\" > Sign in </a ></li >
                                <li ><a href = \"#\" class=\"popup-click register-popup-click\" > register</a ></li >
                            </ul >
                     ";
                } else {
                    if( (int)$profile_id === (int)\Yii::$app->user->id ){
                        $html = "
                                <ul class=\"registered\" >
                                    <li><a href = \"/site/logout\" data-method=\"post\" > logout</a></li>
                                </ul >
                        ";
                    } else {
                        $html = "
                                <ul class=\"registered\" >
                                        <li><a href = \"/user/" . \Yii::$app->user->id . "\"> my profile </a></li>
                                        <!--li><a href = \"#\" > settings</a ></li-->
                                        <li><a href = \"/site/logout\" data-method=\"post\" > logout</a></li>
                                    </ul >
                        ";
                    }
                }

            } else {

                if (\Yii::$app->user->isGuest) {

                    $html = "
                            <ul class=\"icon-registr-sign-up registr-sign-up\" >
                                <li ><a href = \"#\" class=\"popup-click sign-in-popup-click\" > Sign in </a ></li >
                                <li ><a href = \"#\" class=\"popup-click register-popup-click\" > register</a ></li >
                            </ul >
                     ";

                } else {
                    $html = "
                                <ul class=\"registered\" >
                        <li><a href = \"/user/" . \Yii::$app->user->id . "\"> my profile </a></li>
                        <!--li><a href = \"#\" > settings</a ></li-->
                        <li><a href = \"/site/logout\" data-method=\"post\" > logout</a></li>
                    </ul >
                      ";
                }
            }

            return $html;

        }

    }