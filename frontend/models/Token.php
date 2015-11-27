<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace frontend\models;

use dektrium\user\models\Token as BaseToken;

/**
 * Token Active Record model.
 *
 * @property integer $user_id
 * @property string  $code
 * @property integer $created_at
 * @property integer $type
 * @property string  $url
 * @property bool    $isExpired
 * @property User    $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Token extends BaseToken {

    const TYPE_AUTH = 101;
    const expires = 7200;

    public function checkToken($token) {

        $token = (new \yii\db\Query())
                        ->select('*')
                        ->from('{{%token}}')
                        ->where([
                            'code' => $token,
                            'type' => self::TYPE_AUTH,
                        ])->one();
        if (!$token)
            return 'Invalide token';

        if ($token && $token['created_at'] + self::expires < time())
            return 'Token expired';

        return $token;
    }

    public static function getAuthTokenByUserId($user_id) {

        if (!$user_id)
            return false;

        $token = BaseToken::findOne([
                    'user_id' => $user_id,
                    'type' => self::TYPE_AUTH
        ]);

        if (!$token) {

            $token = \Yii::createObject([
                        'class' => BaseToken::className(),
                        'user_id' => $user_id,
                        'type' => self::TYPE_AUTH
            ]);
        } else {
            $token->created_at = time();
            $token->code = \Yii::$app->security->generateRandomString();
        }

        $token->save();

        return $token->code;
    }

}
