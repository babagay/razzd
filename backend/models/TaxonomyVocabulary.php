<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "taxonomy_vocabulary".
 *
 * @property integer $id
 * @property string $name
 */
class TaxonomyVocabulary extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%taxonomy_vocabulary}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

}
