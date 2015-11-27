<?php

    namespace frontend\controllers\auth;

    use dektrium\user\clients\Twitter as TwitterBase;

    /**
     * Class Twitter
     *
     * @package frontend\controllers\auth
     *
     */

    class Twitter extends TwitterBase
    {
        protected function saveAccessToken($token){

            $params = $token->getParams();
            $params['client'] = 'twitter';

            $session = \Yii::$app->session;
            if (!$session->isActive)
                $session->open();

            $session->set('socilal_params', $params);

            return parent::saveAccessToken($token);
        }
    }