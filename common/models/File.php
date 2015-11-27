<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class File extends \yii\db\ActiveRecord {

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file, $rule;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%file}}';
    }

    /**
     * @return array the validation rules.
     */
    public function rules() {
        $rule = [
            [['nid'], 'integer'],
            [['field', 'model'], 'string', 'max' => 255],
            [['filename', 'path'], 'string', 'max' => 255],
            [['file'], 'file'],
        ];
        if ($this->rule)
            $rule[1] = $this->rule;

        return $rule;
    }

}
