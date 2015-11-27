<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "meta".
 *
 * @property integer $id
 * @property string $title
 * @property string $keywords
 * @property string $description
 * @property string $route
 */
class Meta extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%meta}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['route', 'params'], 'required'],
            [['title', 'keywords', 'description', 'route', 'params'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'keywords' => 'Keywords',
            'description' => 'Description',
            'route' => 'Route',
        ];
    }

    public static function findByRoute($route) {


        return self::find()
                        ->where(
                                [
                                    'route' => $route['route'],
                                    'params' => serialize($route['params']),
                                ]
                        )->one();
    }

}
