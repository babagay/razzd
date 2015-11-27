<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "url_rule".
 *
 * @property integer $id
 * @property string $slug
 * @property string $route
 * @property string $params
 * @property integer $redirect
 * @property integer $status
 */
class UrlRule extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%alias}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['alias', 'url'], 'required'],
            [['eid'], 'integer'],
            [['alias', 'url', 'model'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'slug' => 'Alias',
            'route' => 'Route',
            'params' => 'Params',
            'redirect' => 'Redirect',
            'status' => 'Status',
        ];
    }

    public static function findByUrl($url) {


        return self::find()
                        ->where(
                                [
                                    'url' => $url,
                                ]
                        )->one();
    }

    public static function findByAlias($alias) {
        return self::find()
                        ->where(
                                [
                                    'alias' => $alias
                                ]
                        )->one();
    }

}
