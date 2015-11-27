<?php

namespace frontend\models;

use Yii;

class ProfileImage extends \yii\db\ActiveRecord {

    public $fullPath;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%profile_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['user_id'], 'integer'],
                [['date', 'fullPath'], 'safe'],
                [['file_name', 'file_path'], 'string', 'max' => 255],
                [['fullPath'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
                'id' => 'ID',
                'user_id' => 'User ID',
                'file_name' => 'File Name',
                'file_path' => 'File Path',
                'date' => 'Date',
        ];
    }

    public static function getUserImage($userId) {
        $query = self::find();
        $query->where(['=', 'user_id', $userId]);
        $query->orderBy(['id' => SORT_DESC]);
        return $query->one();
    }

}
