<?php

namespace common\models;

use Yii;
use common\models\File;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property integer $confirmed_at
 * @property string $unconfirmed_email
 * @property integer $blocked_at
 * @property string $role
 * @property string $registration_ip
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $flags
 */
class User extends \yii\db\ActiveRecord {

    const ROLE_USER = 1;
    const ROLE_AGENT = 2;
    const ROLE_COPY = 3;
    const ROLE_EDITOR = 8;
    const ROLE_MODERATOR = 9;
    const ROLE_ADMIN = 10;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username', 'email'], 'required'],
            [['username'], 'string', 'max' => 25],
            [['email'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['email'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'confirmed_at' => 'Confirmed At',
            'unconfirmed_email' => 'Unconfirmed Email',
            'blocked_at' => 'Blocked At',
            'role' => 'Role',
            'registration_ip' => 'Registration Ip',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'flags' => 'Flags',
        ];
    }

    public function getAvatar() {
        return $this->hasOne(File::className(), ['nid' => 'id'])->where('model="Profile" AND field="avatar"');
    }

}
