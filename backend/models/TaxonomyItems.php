<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "taxonomy_items".
 *
 * @property integer $id
 * @property integer $vid
 * @property string $name
 */
class TaxonomyItems extends \yii\db\ActiveRecord {

    public $_parent;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%taxonomy_items}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['vid', 'name'], 'required'],
            [['vid', 'pid', 'weight'], 'integer'],
            [['parent'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'vid' => 'Vid',
            'name' => 'Name',
            'parent' => 'Parent',
        ];
    }

    public function getParent() {
        return $this->hasOne(self::className(), ['id' => 'pid']);
    }

    public function setParent($data) {

        if (is_numeric($data['name']))
            $this->pid = $data['name'];
    }

}
