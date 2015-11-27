<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $message
 * @property string $link
 * @property integer $hide
 * @property string $created_at
 */
class Notification extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%notification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uid', 'hide', 'created_at'], 'integer'],
            [['message', 'created_at'], 'required'],
            [['message'], 'string'],
            [['link'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'message' => 'Message',
            'link' => 'Link',
            'hide' => 'Hide',
            'created_at' => 'Created At',
        ];
    }

    public function getNotifications($uid) {

        return (new \yii\db\Query())
                        ->select('*')
                        ->from('{{%notification}}')
                        ->orderBy([
                            'created_at' => SORT_DESC,
                        ])
                        ->limit(30)
                        ->where([
                            'uid' => $uid,
                            'hide' => NULL
                        ])
                        ->all();
    }

}
