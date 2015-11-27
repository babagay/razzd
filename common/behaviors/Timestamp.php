<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class Timestamp extends Behavior {

    public $create_attribute = 'created_at';
    public $update_attribute = 'updated_at';

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'prepareTime',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'prepareTime',
        ];
    }

    public function prepareTime() {
        self::initTime();
        $this->owner->{$this->update_attribute} = time();
        if (!$this->owner->{$this->create_attribute})
            $this->owner->{$this->create_attribute} = time();
    }

    public function initTime() {

        if (!$this->owner->{$this->create_attribute})
            $this->owner->{$this->create_attribute} = time();
    }

}
