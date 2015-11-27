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

use frontend\models\Token;
use common\models\File;
use Yii;

    /**
     */
    class User extends \dektrium\user\models\User
    {

        public $fullname;

        public function getFullname($id = null)
        {

            if ($this->fullname) {
                return $this->fullname;
            }

            $id = $id ? $id : $this->id;
            $user = (new \yii\db\Query())
                ->select('user.id,user.username, profile.name')
                ->from('{{%user}} user')
                ->leftJoin('{{%profile}} profile', 'profile.user_id = user.id')
                ->where([
                    'user.id' => $id,
                ])->one();
            $this->fullname = $user['name'] ? $user['name'] : $user['username'];
            return $this->fullname;
        }

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getAvatar()
        {
            return $this->hasOne(File::className(), ['nid' => 'id']);
        }

        public static function getClientIdByUid($uid)
        {

            $client_id = null;

            $connection = Yii::$app->getDb();

            $command = $connection->createCommand('
				SELECT *
				FROM {{%social_account}}
				WHERE user_id = :id',
                [':id' => $uid]);
            $result = $command->queryOne();

            if (isset($result['client_id'])) {
                $client_id = $result['client_id'];
            }

            return $client_id;
        }

        public static function getUidByClientId($id = null)
        {

            return (new \yii\db\Query())
                ->select('user_id')
                ->from('{{%social_account}} social_account')
                ->where([
                    'client_id' => $id,
                ])->scalar();
        }

        public static function getUserByClientId($id = null)
        {

            return (new \yii\db\Query())
                ->select('user.*')
                ->from('{{%user}} user')
                ->join('JOIN', '{{%social_account}} social_account', 'social_account.user_id = user.id')
                ->where([
                    'social_account.client_id' => $id,
                ])->one();
        }

        public function getInfo($id = null)
        {
            $id = $id ? $id : $this->id;

            return (new \yii\db\Query())
                ->select('*')
                ->from('{{%user}} user')
                ->where([
                    'id' => $id,
                ])->one();
        }

        /**
         * @param User $user
         * @param $accounts
         */
        public static function assignRazz(User $user, $accounts)
        {
            if (!empty($user->id) && !empty($user->email)) {
                Razz::updateAll(['responder_uid' => $user->id], 'responder_uid IS NULL AND email = :email',
                    [':email' => $user->email]);
            }
            if (!empty($accounts)) {
                foreach ($accounts as $provider => $account) {
                    if ($provider == 'facebook' && !empty($account->user_id)) {
                        Razz::updateAll(['responder_uid' => $account->user_id],
                            'responder_uid IS NULL AND facebook_id = :facebook', [':facebook' => $account->client_id]);
                    }
                }
            }
        }



    } 
