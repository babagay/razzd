<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%settings}}".
 *
 * @property string $key
 * @property string $data
 */
class Settings extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['key'], 'required'],
            [['data'], 'string'],
            [['key'], 'string', 'max' => 100],
            [['key'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'key' => 'Key',
            'data' => 'Data',
        ];
    }

    public static function getValue($key, $default = false) {
        $data = (new \yii\db\Query())
                        ->select('data')
                        ->from('{{%settings}}')
                        ->where([
                            'key' => $key,
                        ])->scalar();

        if (!$data)
            return $default;

        return $data;
    }

}
