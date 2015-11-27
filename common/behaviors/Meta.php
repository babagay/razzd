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

class Meta extends Behavior {

    private $_route;

    public function init() {

        if (!$this->_route) {
            throw new InvalidParamException('Invalid param property: route');
        }
    }

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveMeta',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveMeta',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteMeta',
            ActiveRecord::EVENT_AFTER_FIND => 'findMeta'
        ];
    }

    public function getRoute() {

        $route = '';
        $param = [];
        $data = $this->_route;

        $route = array_shift($data);

        foreach ($data as $r) {
            $param[$r] = (string) $this->owner->{$r};
        }

        ksort($param);

        return ['route' => $route, 'params' => $param];
    }

    public function setRoute($value) {

        $this->_route = $value;
    }

    public function deleteMeta($event) {

        $route = $this->route;
        $model = \backend\models\Meta::findByRoute($route);
        if (isset($model->route))
            $model->delete();
    }

    public function saveMeta($event, $insert = false) {


        $route = $this->route;
        $title = $this->owner->meta_title;
        $keywords = $this->owner->meta_keywords;
        $description = $this->owner->meta_description;

        if ($title || $keywords || $description) {

            $model = \backend\models\Meta::findByRoute($route);

            if (isset($model->id)) {

                $model->title = $title;
                $model->keywords = $keywords;
                $model->description = $description;
                $model->update();
            } else {

                $model = new \backend\models\Meta();
                $model->title = $title;
                $model->keywords = $keywords;
                $model->description = $description;
                $model->route = $route['route'];
                $model->params = serialize($route['params']);
                $model->save();
            }
        } elseif (!$insert) {

            $model = \backend\models\Meta::findByRoute($route);
            if (isset($model->id))
                $model->delete();
        }
    }

    public function findMeta($event) {
        if (Yii::$app->controller->action->id == 'index')
            return;

        $route = $this->route;
        $model = \backend\models\Meta::findByRoute($route);
        if (isset($model->id)) {
            $this->owner->meta_title = $model->title;
            $this->owner->meta_keywords = $model->keywords;
            $this->owner->meta_description = $model->description;
        }
    }

}
