<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "taxonomy_index".
 *
 * @property integer $id
 * @property integer $nid
 * @property integer $model
 * @property integer $tid
 */
class TaxonomyIndex extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'taxonomy_index';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nid', 'model', 'tid'], 'required'],
            [['nid', 'tid'], 'integer'],
            [['model'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nid' => 'Nid',
            'model' => 'Model',
            'tid' => 'Tid',
        ];
    }

    public static function getTerms($vid = NULL, $pid = NULL) {
        return (new \yii\db\Query())
                        ->select('*')
                        ->from('{{%taxonomy_items}}')
                        ->filterWhere([
                            'vid' => $vid,
                            'pid' => $pid
                        ])->orderBy([
                    'weight' => SORT_ASC,
                ])->all();
    }

    public function getTerm($id) {
        return (new \yii\db\Query())
                        ->select('*')
                        ->from('{{%taxonomy_items}}')
                        ->where(['id' => $id])->one();
    }

}
