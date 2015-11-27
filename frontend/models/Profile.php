<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace frontend\models;

use yii\db\ActiveRecord;
use dektrium\user\models\Profile as BaseProfile;
use common\models\File;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string  $name
 * @property string  $public_email
 * @property string  $gravatar_email
 * @property string  $gravatar_id
 * @property string  $location
 * @property string  $website
 * @property string  $bio
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class Profile extends BaseProfile {

    public $avatar;

    public function behaviors() {

        return [
            'file' => [
                'class' => 'common\behaviors\File',
                'fields' => ['avatar'],
                'styles' => ['avatar' => ['256x256']],
                'id_field' => 'user_id'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {

        $rules = parent::rules();
        $rules[] = [['avatar'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1];
        return $rules;
    }

    /** @inheritdoc */
    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['avatar'] = \Yii::t('user', 'Avatar');
        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvatar() {
        return $this->hasOne(File::className(), ['nid' => 'id']);
    }

}
