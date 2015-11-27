<?php

namespace frontend\models;

use Yii;

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
            [['comment', 'eid', 'uid'], 'required'],
            [['uid', 'eid'], 'integer'],
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
        ];
    }

    public function beforeValidate() {
        $this->uid = Yii::$app->user->id;
        $this->created_at = time();
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->status = 1;
        return parent::beforeValidate();
    }

    public function getComments($eid = null) {
        return (new \yii\db\Query())
                        ->select('comments.*,user.username,profile.name name')
                        ->from('{{%comments}} comments')
                        ->innerJoin('{{%user}} user', 'user.id = comments.uid ')
                        ->leftJoin('{{%profile}} profile', 'profile.user_id = comments.uid ')
                        ->where([
                            'comments.eid' => $this->eid,
                            'status' => 1
                        ])
                        ->orderBy([
                            'created_at' => SORT_ASC,
                        ])
                        ->all();
    }

}
