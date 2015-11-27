<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "alias".
 *
 * @property string $id
 * @property integer $eid
 * @property string $model
 * @property string $url
 * @property string $alias
 */
class Alias extends \yii\db\ActiveRecord {

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
            [['eid', 'model', 'url', 'alias'], 'required'],
            [['eid'], 'integer'],
            [['model'], 'string', 'max' => 40],
            [['url', 'alias'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'eid' => 'Eid',
            'model' => 'Model',
            'url' => 'Url',
            'alias' => 'Alias',
        ];
    }

}
