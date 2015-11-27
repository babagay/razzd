<?php

namespace backend\models;

use Yii;
use common\models\User;
use yii\web\UploadedFile;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $title
 * @property string $body
 * @property integer $publish
 * @property integer $promote
 */
class Pages extends \yii\db\ActiveRecord {

    public $alias, $_username, $meta_title, $meta_keywords, $meta_description;

    public function behaviors() {

        return [
            'alias' => [
                'class' => 'common\behaviors\UrlRule',
                'field' => 'alias',
                'pattern' => '{title}',
                'url' => ['pages', 'id'], // 0: url, 1,2...n:field-param
                'transliterate' => true
            ],
            'meta' => [
                'class' => 'common\behaviors\Meta',
                'route' => ['pages', 'id'], // 0: url, 1,2...n:field-param
            ],
            'user' => [
                'class' => 'common\behaviors\User',
            ],
            'timestamp' => [
                'class' => 'common\behaviors\Timestamp',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%pages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uid', 'publish', 'promote', 'created_at'], 'integer'],
            [['title', 'body'], 'required'],
            [['body'], 'string'],
            [['username'], 'safe'],
            [['title', 'alias', 'meta_title', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'username.username' => 'User',
            'title' => 'Title',
            'body' => 'Body',
            'publish' => 'Publish',
            'promote' => 'Promote',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsername() {
        return $this->_username;
    }

    public function setUsername($data) {
        $this->_username = $data;
    }

}
