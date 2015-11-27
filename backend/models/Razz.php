<?php

namespace backend\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "{{%razz}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $type
 * @property integer $ended
 * @property string $title
 * @property string $description
 * @property string $message
 * @property string $stream
 * @property string $stream_preview
 * @property string $responder_stream
 * @property string $responder_stream_preview
 * @property integer $responder_uid
 * @property string $views
 * @property string $views_at
 * @property string $email
 * @property string $hash
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Razz extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%razz}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uid', 'type', 'ended', 'responder_uid', 'views', 'views_at', 'status', 'publish', 'created_at', 'updated_at'], 'integer'],
            [['title', 'description', 'views_at', 'created_at', 'updated_at'], 'required'],
            [['description', 'message'], 'string'],
            [['title', 'stream', 'stream_preview', 'responder_stream', 'responder_stream_preview', 'email', 'hash'], 'string', 'max' => 255],
            [['hash'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'uid' => 'User',
            'type' => 'Type',
            'ended' => 'Ended',
            'title' => 'Title',
            'description' => 'Description',
            'message' => 'Message',
            'stream' => 'Stream',
            'stream_preview' => 'Stream Preview',
            'responder_stream' => 'Responder Stream',
            'responder_stream_preview' => 'Responder Stream Preview',
            'responder_uid' => 'Responder',
            'views' => 'Views',
            'views_at' => 'Views At',
            'email' => 'Email',
            'hash' => 'Hash',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function getResponder() {
        return $this->hasOne(User::className(), ['id' => 'responder_uid']);
    }

}
