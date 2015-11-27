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
use common\helpers\TransliteratorHelper;

class UrlRule extends Behavior {

    public $field = 'alias';
    private $alias;
    private $_url, $_pattern = NULL;
    public $transliterate = false;

    public function init() {

        if (!$this->_url) {
            throw new InvalidParamException('Invalid param property: route');
        }
    }

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'validateAlias',
            ActiveRecord::EVENT_AFTER_INSERT => 'insertAlias',
            ActiveRecord::EVENT_AFTER_UPDATE => 'updateAlias',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteAlias',
            ActiveRecord::EVENT_AFTER_FIND => 'findAlias'
        ];
    }

    public function getUrl() {

        $url = '';
        $query = '';
        $param = [];
        $data = $this->_url;

        $url = array_shift($data);

        foreach ($data as $r) {
            $param[$r] = $this->owner->{$r};
        }

        ksort($param);
        $query = http_build_query($param);

        if (!$query)
            return $url;

        return $url . '?' . $query;
    }

    public function setUrl($value) {

        $this->_url = $value;
    }

    public function getPattern() {

        $pattern = $this->_pattern;
        preg_match_all("/\{(\w+)\}/", $pattern, $results);

        if (isset($results)) {

            foreach ($results[1] as $itm) {
                $pattern = str_replace('{' . $itm . '}', $this->owner->{$itm}, $pattern);
            }
        }

        return $pattern;
    }

    public function setPattern($value) {
        $this->_pattern = $value;
    }

    private function prepareAlias($alias) {

        if ($this->transliterate)
            $alias = TransliteratorHelper::process($alias, '', 'en');

        $alias = preg_replace('/[^a-zа-яё0-9\/\s\-]+/iu', '', trim(strtolower($alias)));

        $alias = str_replace(' ', '-', $alias);

        return $alias;
    }

    public function findAlias($event) {

        if (Yii::$app->controller->action->id == 'index')
            return;

        $url = $this->url;

        $model = \backend\models\UrlRule::findByUrl($url);
        if (isset($model->alias))
            $this->owner->{$this->field} = $model->alias;
    }

    public function deleteAlias($event) {

        $alias = $this->owner->alias;

        $model = \backend\models\UrlRule::findByAlias($alias);
        if (isset($model->alias))
            $model->delete();
    }

    public function insertAlias($event) {
        self::updateAlias($event, true);
    }

    public function updateAlias($event, $insert = false) {

        $url = $this->url;
        $alias = $this->owner->{$this->field} ? $this->owner->{$this->field} : $this->pattern;
        $alias = $this->prepareAlias($alias);

        if ($alias) {

            $model = \backend\models\UrlRule::findByUrl($url);

            if (isset($model->url)) {
                $model->eid = $this->owner->id;
                $model->model = get_class($this->owner);
                $model->model = substr($model->model, strrpos($model->model, '\\') + strlen('\\'));
                $model->alias = $alias;
                $model->url = $url;
                $model->update();
            } else {

                $model = new \backend\models\UrlRule();
                $model->eid = $this->owner->id;
                $model->model = get_class($this->owner);
                $model->model = substr($model->model, strrpos($model->model, '\\') + strlen('\\'));
                $model->alias = $alias;
                $model->url = $url;
                $model->save();
            }
        } elseif (!$insert) {

            $model = \backend\models\UrlRule::findByUrl($url);
            if (isset($model->url))
                $model->delete();
        }
    }

    public function validateAlias($event) {

        if (Yii::$app->controller->action->id == 'index')
            return true;


        $url = $this->url;
        $alias = $this->owner->{$this->field} ? $this->owner->{$this->field} : $this->pattern;
        $alias = $this->prepareAlias($alias);


        if ($alias) {

            $model = \backend\models\UrlRule::findByAlias($alias);

            if (isset($model->alias) && $model->url != $url) {

                $this->owner->addError($this->field, "Alias already exist");
                return false;
            }
        }
    }

}
