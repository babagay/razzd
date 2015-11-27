<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\behaviors;

use yii;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;

class User extends Behavior {

    public $_username;

    public function init() {

    }

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'prepareUser',
            ActiveRecord::EVENT_AFTER_FIND => 'findUser'
        ];
    }

    public function prepareUser($event, $insert = false) {

        $className = array_pop(explode('\\', get_class($this->owner)));
        $post = Yii::$app->request->post($className);
        if (isset($post['username'])) {

            if (!$post['username'])
                $this->owner->uid = NULL;

            if (preg_match("/\[id:(\d+)\]$/", $post['username'], $matches)) {
                $this->owner->uid = $matches[1];
            }
        } else
            $this->owner->uid = NULL;
    }

    public function findUser($event) {

        $this->owner->_username = '';
        if (!$this->owner->id)
            return;

        $query = (new \yii\db\Query())
                ->select('id,username')
                ->from('{{%user}}')
                ->where([
            'id' => $this->owner->uid,
        ]);


        $data = $query->createCommand()->queryOne();

        if (isset($data['id']))
            $this->owner->_username = $data['username'] . ' [id:' . $data['id'] . ']';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsername() {

        return $this->_username;
    }

    public function setUsername($data) {
        $this->_username = $data;
    }

}
