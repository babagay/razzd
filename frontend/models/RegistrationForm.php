<?php

namespace frontend\models;

use dektrium\user\models\RegistrationForm as BaseRegistrationForm;
use frontend\models\User;

class RegistrationForm extends BaseRegistrationForm {

    /**
     * Add a new field
     * @var string
     */
    public $name;
    public $passwordConfirm;

    /** @inheritdoc */
    public function init() {
        $this->user = \Yii::createObject([
                    'class' => User::className(),
                    'scenario' => 'register'
        ]);
        $this->module = \Yii::$app->getModule('user');

        $this->user->on(User::USER_REGISTER_DONE, function ($event) {
            $event->sender->profile->name = $this->name;
            $event->sender->profile->save();
        });
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();
        $rules[] = ['name', 'required'];
        $rules[] = ['name', 'string', 'max' => 255];
        $rules[] = ['passwordConfirm', 'string'];
        $rules[] = ['passwordConfirm', 'required'];



        return $rules;
    }

    public function beforeValidate() {
        if ($this->passwordConfirm != $this->password)
            $this->addError('passwordConfirm', "Password confirm doesn't match");

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['name'] = \Yii::t('user', 'Name');
        return $labels;
    }

}
