<?php

namespace backend\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "{{%comments}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $eid
 * @property string $comment
 * @property string $created_at
 * @property integer $status
 * @property string $ip
 */
class Comments extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%comments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uid', 'eid', 'comment', 'created_at', 'status', 'ip'], 'required'],
            [['uid', 'eid', 'created_at', 'status'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'eid' => 'Eid',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'status' => 'Status',
            'ip' => 'Ip',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

}
